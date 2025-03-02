<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CultivationLog>
 */
class CultivationLogFactory extends Factory
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
            'activity' => $this->faker->randomElement(['Gieo hạt', 'Tưới nước', 'Bón phân', 'Thu hoạch']),
            'fertilizer_used' => $this->faker->optional()->word,
            'pesticide_used' => $this->faker->optional()->word,
            'image_url' => $this->faker->imageUrl(),
            'video_url' => $this->faker->url,
            'notes' => $this->faker->paragraph,
            'created_at' => $this->faker->dateTimeThisYear,
        ];
    }
}
