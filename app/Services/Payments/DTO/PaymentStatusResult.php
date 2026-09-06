<?php

namespace App\Services\Payments\DTO;

final class PaymentStatusResult
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $gatewayReference = null,
        public readonly array $rawResponse = [],
        public readonly ?string $failureReason = null,
    ) {}
}
