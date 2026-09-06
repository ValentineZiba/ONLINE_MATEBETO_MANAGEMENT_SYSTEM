<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RestaurantTable>
 */
class RestaurantTableFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => (string) fake()->unique()->numberBetween(1, 999),
            'capacity' => fake()->numberBetween(2, 8),
            'location' => fake()->randomElement(['indoor', 'outdoor', 'bar', 'private']),
            'status' => 'available',
        ];
    }
}
