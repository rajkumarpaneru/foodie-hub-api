<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'category_id' => Category::factory()->create(),
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'rank' => $this->faker->numberBetween(1, 20),
            'price' => $this->faker->numberBetween(1, 20),
        ];
    }
}
