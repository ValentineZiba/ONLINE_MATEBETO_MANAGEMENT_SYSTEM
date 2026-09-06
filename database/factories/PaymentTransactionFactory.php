<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transactionable_type' => Order::class,
            'transactionable_id' => Order::factory(),
            'reference' => 'TXN-' . strtoupper(Str::ulid()),
            'gateway' => 'cash',
            'type' => 'payment',
            'status' => 'pending',
            'amount' => fake()->randomFloat(2, 50, 500),
            'currency' => 'ZMW',
        ];
    }

    public function succeeded(): static
    {
        return $this->state(fn () => ['status' => 'succeeded', 'confirmed_at' => now()]);
    }

    public function forFlutterwave(): static
    {
        return $this->state(fn () => [
            'gateway' => 'flutterwave',
            'gateway_reference' => (string) fake()->numberBetween(1000000, 9999999),
        ]);
    }
}
