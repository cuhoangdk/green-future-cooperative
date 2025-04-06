<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\User;
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

        // Tạo giá trị ngẫu nhiên cho is_hot và is_featured
        $isHot = $this->faker->boolean(40); // 40% bài viết là hot
        $isFeatured = $this->faker->boolean(50); // 50% bài viết là featured

        return [
            'title' => $title,
            'slug' => GeneratesSlug::generateUniqueSlug($title, Post::class),
            'summary' => $this->faker->paragraph(1, true),
            'content' => $this->faker->paragraphs(5, true),
            'featured_image' => $this->faker->imageUrl(),
            'post_status' => $postStatus = $this->faker->randomElement(['draft', 'published', 'archived']),
            'is_hot' => $isHot,
            'hot_order' => $isHot ? $this->faker->numberBetween(1, 10) : null, // Chỉ có giá trị khi is_hot = true
            'is_featured' => $isFeatured,
            'featured_order' => $isFeatured ? $this->faker->numberBetween(1, 10) : null, // Chỉ có giá trị khi is_featured = true
            'tags' => implode(', ', $this->faker->words(5)), // Tạo danh sách tags giả
            'meta_title' => $this->faker->sentence(),
            'meta_description' => $this->faker->text(160),
            'views' => $this->faker->numberBetween(0, 1000),
            'published_at' => $postStatus !== 'draft' ? $this->faker->dateTime : null,
            'user_id' => User::factory(),
            'category_id' => PostCategory::factory(),
        ];
    }
}
