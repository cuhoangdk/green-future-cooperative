<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\CooperativeMember;
use App\Models\PostCategory;
use App\Traits\GeneratesSlug;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    use GeneratesSlug;

    protected $model = Post::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence;
        return [
            'title' => $title,
            'slug' => GeneratesSlug::generateUniqueSlug($title, Post::class),
            'summary' => $this->faker->paragraph(1,true),
            'content' => $this->faker->paragraphs(5, true),
            'featured_image' => $this->faker->imageUrl(),
            'post_status' => $postStatus = $this->faker->randomElement(['draft', 'published', 'archived']),
            'is_hot' => $this->faker->boolean(40), // 10% bài viết là hot
            'is_featured' => $this->faker->boolean(50), // 20% bài viết là featured
            'published_at' => $postStatus !== 'draft' ? $this->faker->dateTime : null,
            'author_id' => CooperativeMember::factory(),
            'category_id' => PostCategory::factory(),
        ];
    }
}
