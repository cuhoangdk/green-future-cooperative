<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductQuantityPrice>
 */
class ProductQuantityPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'quantity' => $this->faker->randomFloat(2, 0, 10), // Số lượng từ 0 đến 10kg
            'price' => $this->faker->randomFloat(2, 10000, 1000000), // Giá từ 10,000 đến 1,000,000 VND
            'created_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
