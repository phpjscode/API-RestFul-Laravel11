<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * php artisan make:factory ProductFactory --model=Product
     */
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'description' => fake()->paragraph(1),
            // 'quantity' => fake()->numberBetween(1, 10),
            // 'status' => fake()->randomElement([Product::PRODUCTO_DISPONIBLE, Product::PRODUCTO_NO_DISPONIBLE]), 
            'quantity' => $quantity = fake()->numberBetween(1, 10),
            'status' => $quantity == 0 ? Product::PRODUCTO_NO_DISPONIBLE : Product::PRODUCTO_DISPONIBLE,
            'image' => randomElement(['1.jpg', '2.jpg', '3.jpg']),
            // 'seller_id' => User::inRandomOrder()->first()->id,
            // 'seller_id' => User::all()->random()->id,
            'seller_id' => User::inRandomOrder()->value('id'),
        ];
    }
}
