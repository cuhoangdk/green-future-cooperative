<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GeneratesSlug;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes, GeneratesSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($productCategory) {
            // Kiểm tra và tạo slug nếu chưa có
            if (empty($productCategory->slug)) {
                $productCategory->slug = static::generateUniqueSlug($productCategory->name, static::class);
            }
        });

        static::updating(function ($productCategory) {
            // Kiểm tra xem tiêu đề có thay đổi và slug chưa có, nếu đúng thì tạo lại slug
            if ($productCategory->isDirty('name')) {
                $productCategory->slug = static::generateUniqueSlug($productCategory->name, static::class);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}