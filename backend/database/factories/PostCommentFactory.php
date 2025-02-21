<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostCommentFactory extends Factory
{
    public function definition()
    {
        return [
            'post_id' => Post::factory(),
            'customer_id' => Customer::factory(),
            'content' => $this->faker->paragraph(),
        ];
    }
}
