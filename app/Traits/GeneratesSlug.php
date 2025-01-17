<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\Post; 

trait GeneratesSlug
{
    /**
     * Generate a unique slug for a given model and field.
     *
     * @param  string $title
     * @param  string $modelClass
     * @param  string $field
     * @return string
     */
    public function generateUniqueSlug(string $title, string $column = 'slug')
    {
        // Tạo slug từ title
        $slug = Str::slug($title);
        $count = 0;

        // Kiểm tra xem slug đã tồn tại chưa
        while (Post::withTrashed()->where($column, $slug)->exists()) {
            $count++;
            $slug = Str::slug($title) . '-' . $count;
        }

        return $slug;
    }
}
