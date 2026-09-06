<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 500);
        $tax = round($subtotal * 0.16, 2);

        return [
            'user_id' => User::factory(),
            'order_type' => fake()->randomElement(['dine_in', 'takeaway', 'delivery']),
            'status' => 'pending',
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->numerify('097#######'),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'delivery_fee' => 0,
            'discount' => 0,
            'total' => $subtotal + $tax,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['payment_status' => 'paid']);
    }
}
