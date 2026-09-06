<?php

namespace App\Services\Payments\Gateways;

use App\Models\PaymentTransaction;
use App\Services\Payments\Contracts\PaymentGateway;
use App\Services\Payments\DTO\PaymentInitiationResult;
use App\Services\Payments\DTO\PaymentStatusResult;
use App\Services\Payments\DTO\WebhookEvent;
use Illuminate\Http\Request;

/**
 * Staff-attested payments: cash, in-person card (no reader hardware
 * integration), and bank transfer. No external API call — succeeds
 * immediately on the assumption staff has already collected payment.
 */
class ManualGateway implements PaymentGateway
{
    public function __construct(private readonly string $gatewayKey) {}

    public function key(): string
    {
        return $this->gatewayKey;
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function initiate(PaymentTransaction $transaction, array $payload = []): PaymentInitiationResult
    {
        return new PaymentInitiationResult(status: 'succeeded');
    }

    public function query(PaymentTransaction $transaction): PaymentStatusResult
    {
        return new PaymentStatusResult(status: $transaction->status);
    }

    public function refund(PaymentTransaction $original, float $amount, ?string $reason = null): PaymentStatusResult
    {
        return new PaymentStatusResult(status: 'succeeded');
    }

    public function verifyWebhook(Request $request): void
    {
        throw new \LogicException('Manual gateways do not receive webhooks.');
    }

    public function parseWebhook(Request $request): WebhookEvent
    {
        throw new \LogicException('Manual gateways do not receive webhooks.');
    }
}
