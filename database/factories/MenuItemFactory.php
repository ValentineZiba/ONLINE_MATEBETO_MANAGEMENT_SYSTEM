<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 20, 300),
            'is_available' => true,
            'is_featured' => false,
            'preparation_time' => fake()->numberBetween(5, 30),
            'stock_quantity' => fake()->numberBetween(10, 100),
            'station' => 'kitchen',
            'sort_order' => 0,
        ];
    }
}
