<?php

namespace App\Services\Payments\Contracts;

use App\Models\PaymentTransaction;
use App\Services\Payments\DTO\PaymentInitiationResult;
use App\Services\Payments\DTO\PaymentStatusResult;
use App\Services\Payments\DTO\WebhookEvent;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function key(): string;

    public function isConfigured(): bool;

    public function initiate(PaymentTransaction $transaction, array $payload = []): PaymentInitiationResult;

    public function query(PaymentTransaction $transaction): PaymentStatusResult;

    public function refund(PaymentTransaction $original, float $amount, ?string $reason = null): PaymentStatusResult;

    public function verifyWebhook(Request $request): void;

    public function parseWebhook(Request $request): WebhookEvent;
}
