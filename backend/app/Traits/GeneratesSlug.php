<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesSlug
{
    /**
     * Tạo slug duy nhất cho một mô hình và trường dữ liệu.
     *
     * @param  string $title
     * @param  string $modelClass Lớp mô hình để kiểm tra tính duy nhất của slug
     * @param  string $column Cột cần kiểm tra tính duy nhất (mặc định là 'slug')
     * @return string
     */
    public static function generateUniqueSlug(string $title, string $modelClass, string $column = 'slug')
    {
        // Tạo slug từ title
        $baseSlug = Str::slug($title);

        // Giới hạn độ dài của baseSlug còn 250 ký tự trừ đi độ dài của `-$count`
        $maxSlugLength = 200;
        $baseSlug = substr($baseSlug, 0, $maxSlugLength);

        $slug = $baseSlug;
        $count = 0;

        // Kiểm tra xem slug đã tồn tại chưa
        while ($modelClass::withTrashed()->where($column, $slug)->exists()) {
            $count++;
            $slug = $baseSlug . '-' . $count;
        }
        
        return $slug;
    }
}
