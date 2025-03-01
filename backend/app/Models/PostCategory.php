<?php

namespace App\Models;

use App\Traits\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
    use HasFactory, SoftDeletes, GeneratesSlug;

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
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($postCategory) {
            if (empty($postCategory->slug)) {
                $postCategory->slug = static::generateUniqueSlug($postCategory->name, static::class);
            }
        });

        static::updating(function ($postCategory) {
            if ($postCategory->isDirty('name')) {
                $postCategory->slug = static::generateUniqueSlug($postCategory->name, static::class);
            }
        });
    }
}
