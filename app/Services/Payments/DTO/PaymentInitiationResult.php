<?php

namespace App\Services\Payments\DTO;

final class PaymentInitiationResult
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $redirectUrl = null,
        public readonly ?string $gatewayReference = null,
        public readonly array $rawResponse = [],
    ) {}
}
