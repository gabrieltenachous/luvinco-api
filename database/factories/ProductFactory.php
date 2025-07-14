<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
    public function definition(): array
    {
        return [
            'product_id' =>  (string) Str::uuid(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(8),
            'price' => fake()->randomFloat(2, 10, 1000),
            'category' => fake()->randomElement(['Roupas Masculinas', 'Eletrônicos', 'Livros']),
            'brand' => fake()->company(),
            'stock' => fake()->numberBetween(1, 50),
            'image_url' => fake()->imageUrl(),
        ];
    }

}
