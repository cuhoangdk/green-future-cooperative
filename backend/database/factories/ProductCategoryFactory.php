<?php

namespace Database\Factories;

use App\Traits\GeneratesSlug;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word;
        return [
            'name' => $name,
            'slug' => GeneratesSlug::generateUniqueSlug($name, ProductCategoryFactory::class),
            'description' => $this->faker->paragraph,
        ];
    }
}
