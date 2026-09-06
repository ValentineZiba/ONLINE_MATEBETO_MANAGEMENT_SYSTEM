<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarTab>
 */
class BarTabFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 30, 400);
        $tax = round($subtotal * 0.16, 2);

        return [
            'opened_by' => User::factory(),
            'customer_name' => fake()->name(),
            'status' => 'open',
            'payment_status' => 'pending',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
        ];
    }
}
