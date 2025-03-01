<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GeneratesSlug;

class Post extends Model
{
    use HasFactory, SoftDeletes, GeneratesSlug;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'featured_image',
        'category_id',
        'user_id',
        'post_status',
        'is_hot',
        'hot_order',
        'is_featured',
        'featured_order',
        'tags',
        'meta_title',
        'meta_description',
        'views',
        'published_at',
    ];
    protected $casts = [
        'published_at' => 'datetime',
        'is_hot' => 'boolean',
        'is_featured' => 'boolean',
    ];
    // Quan hệ với PostCategory (Giả sử mỗi post có một category)
    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    // Quan hệ với User (Giả sử mỗi post có một user)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            // Kiểm tra và tạo slug nếu chưa có
            if (empty($post->slug)) {
                $post->slug = static::generateUniqueSlug($post->title, static::class);
            }
        });

        static::updating(function ($post) {
            // Kiểm tra xem tiêu đề có thay đổi và slug chưa có, nếu đúng thì tạo lại slug
            if ($post->isDirty('title')) {
                $post->slug = static::generateUniqueSlug($post->title, static::class);
            }
        });
    }
}
