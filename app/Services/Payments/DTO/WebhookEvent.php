<?php

namespace App\Services\Payments\DTO;

final class WebhookEvent
{
    public function __construct(
        public readonly string $eventId,
        public readonly ?string $gatewayReference,
        public readonly string $status,
        public readonly array $rawPayload,
    ) {}
}
