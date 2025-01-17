<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostCategory extends Model
{
    use HasFactory;

    /**
     * Các cột có thể được gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * Các cột sẽ được tự động cast sang kiểu dữ liệu mong muốn.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Quan hệ với model Post.
     * Một danh mục có thể chứa nhiều bài viết.
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }
}
