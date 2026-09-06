<?php

namespace App\Services\Payments;

use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\DTO\PaymentInitiationResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * The only code path that writes payment_status on Order/BarTab. Every
 * payment surface (POS, checkout, bar tabs, admin) goes through here so
 * "is this paid" always reflects a real PaymentTransaction.
 */
class PaymentManager
{
    /** @var array<string, PaymentGateway> */
    private array $gateways = [];

    public function register(PaymentGateway $gateway): void
    {
        $this->gateways[$gateway->key()] = $gateway;
    }

    public function gateway(string $key): PaymentGateway
    {
        if (! isset($this->gateways[$key])) {
            throw new \InvalidArgumentException("Unknown payment gateway [{$key}].");
        }

        return $this->gateways[$key];
    }

    public function initiate(
        Model $transactionable,
        string $gatewayKey,
        float $amount,
        array $payload = [],
        ?User $initiatedBy = null,
        ?string $phoneNumber = null,
    ): PaymentTransaction {
        $gateway = $this->gateway($gatewayKey);

        $transaction = PaymentTransaction::create([
            'transactionable_type' => $transactionable::class,
            'transactionable_id' => $transactionable->getKey(),
            'reference' => $this->generateReference(),
            'gateway' => $gatewayKey,
            'type' => 'payment',
            'status' => 'pending',
            'amount' => $amount,
            'currency' => 'ZMW',
            'phone_number' => $phoneNumber,
            'initiated_by' => $initiatedBy?->id,
            'meta' => array_filter($payload, fn ($v) => $v !== null && $v !== ''),
        ]);

        $result = $gateway->initiate($transaction, $payload);

        return match ($result->status) {
            'succeeded' => $this->markSucceeded($transaction, $result->rawResponse, $result->gatewayReference),
            'failed' => $this->markFailed($transaction, 'Gateway rejected the payment', $result->rawResponse),
            'cancelled' => $this->markCancelled($transaction, 'Gateway cancelled the payment'),
            default => $this->markProcessing($transaction, $result),
        };
    }

    public function markSucceeded(PaymentTransaction $transaction, array $rawResponse = [], ?string $gatewayReference = null): PaymentTransaction
    {
        return DB::transaction(function () use ($transaction, $rawResponse, $gatewayReference) {
            $locked = PaymentTransaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();

            if ($locked->isTerminal()) {
                return $locked;
            }

            $locked->update([
                'status' => 'succeeded',
                'gateway_reference' => $gatewayReference ?? $locked->gateway_reference,
                'gateway_response' => $rawResponse ?: $locked->gateway_response,
                'confirmed_at' => now(),
            ]);

            if ($locked->type === 'payment') {
                $locked->transactionable?->onPaymentSucceeded($locked);
            }

            $this->syncParentStatus($locked);

            Log::channel('payments')->info('Payment transaction succeeded', [
                'reference' => $locked->reference,
                'gateway' => $locked->gateway,
                'type' => $locked->type,
            ]);

            return $locked;
        });
    }

    public function markFailed(PaymentTransaction $transaction, string $reason, array $rawResponse = []): PaymentTransaction
    {
        return DB::transaction(function () use ($transaction, $reason, $rawResponse) {
            $locked = PaymentTransaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();

            if ($locked->isTerminal()) {
                return $locked;
            }

            $locked->update([
                'status' => 'failed',
                'failure_reason' => $reason,
                'gateway_response' => $rawResponse ?: $locked->gateway_response,
                'confirmed_at' => now(),
            ]);

            $this->syncParentStatus($locked);

            Log::channel('payments')->warning('Payment transaction failed', [
                'reference' => $locked->reference,
                'reason' => $reason,
            ]);

            return $locked;
        });
    }

    public function markCancelled(PaymentTransaction $transaction, string $reason): PaymentTransaction
    {
        return DB::transaction(function () use ($transaction, $reason) {
            $locked = PaymentTransaction::whereKey($transaction->id)->lockForUpdate()->firstOrFail();

            if ($locked->isTerminal()) {
                return $locked;
            }

            $locked->update([
                'status' => 'cancelled',
                'failure_reason' => $reason,
                'confirmed_at' => now(),
            ]);

            $this->syncParentStatus($locked);

            return $locked;
        });
    }

    public function refund(PaymentTransaction $original, ?float $amount, ?string $reason, ?User $initiatedBy): PaymentTransaction
    {
        $gateway = $this->gateway($original->gateway);
        $refundAmount = $amount ?? (float) $original->amount;

        $refundTransaction = PaymentTransaction::create([
            'transactionable_type' => $original->transactionable_type,
            'transactionable_id' => $original->transactionable_id,
            'reference' => $this->generateReference(),
            'gateway' => $original->gateway,
            'type' => 'refund',
            'status' => 'pending',
            'amount' => $refundAmount,
            'currency' => $original->currency,
            'initiated_by' => $initiatedBy?->id,
            'meta' => ['original_transaction_id' => $original->id, 'reason' => $reason],
        ]);

        $result = $gateway->refund($original, $refundAmount, $reason);

        return match ($result->status) {
            'succeeded' => $this->markSucceeded($refundTransaction, $result->rawResponse, $result->gatewayReference),
            default => $this->markFailed($refundTransaction, $reason ?? 'Refund declined by gateway', $result->rawResponse),
        };
    }

    private function markProcessing(PaymentTransaction $transaction, PaymentInitiationResult $result): PaymentTransaction
    {
        $transaction->update([
            'status' => $result->status,
            'gateway_reference' => $result->gatewayReference,
            'gateway_response' => $result->rawResponse,
        ]);

        $this->syncParentStatus($transaction);

        return $transaction;
    }

    private function syncParentStatus(PaymentTransaction $transaction): void
    {
        $parent = $transaction->transactionable;
        if (! $parent) {
            return;
        }

        $newStatus = match (true) {
            $transaction->status === 'succeeded' && $transaction->type === 'refund' => 'refunded',
            $transaction->status === 'succeeded' => 'paid',
            $transaction->status === 'failed' => 'failed',
            $transaction->status === 'cancelled' => 'cancelled',
            default => 'processing',
        };

        $parent->update(['payment_status' => $newStatus]);
    }

    private function generateReference(): string
    {
        return 'TXN-'.strtoupper((string) Str::ulid());
    }
}
