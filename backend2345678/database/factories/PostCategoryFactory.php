<?php

namespace Database\Factories;

use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Traits\GeneratesSlug;

class PostCategoryFactory extends Factory
{
    use GeneratesSlug;
    protected $model = PostCategory::class;

    public function definition(): array
    {
        $name = $this->faker->word;        
        return [
            'name' => $name,
            'slug' => GeneratesSlug::generateUniqueSlug($name, PostCategory::class),
            'description' => $this->faker->sentence,
        ];
    }
}
