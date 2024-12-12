<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * php artisan make:factory TransactionFactory --model=Transaction
     */
        
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vendedor = Seller::has('products')->get()->random();
        // $comprador = User::all()->except($vendedor->id)->random();
        $comprador = User::all()->except([$vendedor->id])->random();

        return [
            'quantity' => fake()->numberBetween(1, 3),
            'buyer_id' => $comprador->id,
            'product_id' => $vendedor->products->random()->id,
        ];
    }
}
