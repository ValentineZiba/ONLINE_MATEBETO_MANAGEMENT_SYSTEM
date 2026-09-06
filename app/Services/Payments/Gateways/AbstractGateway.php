<?php

namespace App\Services\Payments\Gateways;

use Illuminate\Support\Facades\Log;

abstract class AbstractGateway
{
    private const REDACTED_KEYS = [
        'authorization', 'secret', 'client_secret', 'api_key', 'apikey',
        'subscription_key', 'password', 'card_number', 'cvv', 'pin',
        'webhook_secret', 'access_token',
    ];

    protected function redact(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->redact($value);
            } elseif (in_array(strtolower((string) $key), self::REDACTED_KEYS, true)) {
                $payload[$key] = '[REDACTED]';
            }
        }

        return $payload;
    }

    protected function log(string $level, string $message, array $context = []): void
    {
        Log::channel('payments')->{$level}($message, $this->redact($context));
    }
}
