<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CooperativeMember;
use App\Models\Post;
use App\Models\PostCategory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = PostCategory::factory(5)->create();
            
        // Tạo 1 thành viên hợp tác xã và lấy đối tượng đầu tiên
        $member = CooperativeMember::factory()->create(); // Không cần factory(1)

        // Tạo 10 bài viết với cooperative_member_id và post_category_id ngẫu nhiên
        Post::factory(10)->create([
            'author_id' => $member->id,
            'category_id' => $categories->random()->id,
        ]);
    }
}