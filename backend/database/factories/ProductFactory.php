<?php
namespace Database\Factories;

use App\Traits\GeneratesSlug;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->word;
        return [
            'product_code' => 'ITEM_' . strtoupper($this->faker->userName) . '_' . str_pad($this->faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id' => 1,
            'farm_id' => 1,
            'category_id' => 1,
            'unit_id' => 1,
            'name' => $name,
            'slug' => GeneratesSlug::generateUniqueSlug($name, ProductFactory::class),
            'description' => $this->faker->paragraph,
            'seed_supplier' => $this->faker->company,
            'cultivated_area' => $this->faker->randomFloat(2, 1, 100),
            'sown_at' => $this->faker->dateTimeThisYear,
            'harvested_at' => $this->faker->dateTimeThisYear,
            'pricing_type' => $this->faker->randomElement(['fix', 'flexible', 'contact']),
            'base_price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'is_active' => $this->faker->boolean,
            'sold_quantity' => $this->faker->numberBetween(0, 500),
            'views' => $this->faker->numberBetween(0, 10000),
            'meta_title' => $this->faker->sentence,
            'meta_description' => $this->faker->paragraph,
            'meta_keyword' => $this->faker->word,
        ];
    }
}
