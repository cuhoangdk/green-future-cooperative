<?php

namespace App\Models;

use App\Traits\GeneratesSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, GeneratesSlug;

    protected $fillable = [
        'product_code',
        'user_id',
        'farm_id',
        'category_id',
        'unit_id',
        'name',
        'slug',
        'description',
        'seed_supplier',
        'cultivated_area',
        'sown_at',
        'harvested_at',
        'pricing_type',
        'base_price',
        'stock_quantity',
        'is_active',
        'sold_quantity',
        'views',
        'meta_title',
        'meta_description',
        'meta_keyword',
    ];

    protected $dates = ['sown_at', 'harvested_at', 'deleted_at'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class); 
    }
    public function farm()
    {
        return $this->belongsTo(Farm::class); 
    }
    
    protected static function boot()
    {
        parent::boot();

        // Tạo slug khi creating
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name, static::class);
            }
            if (empty($product->product_code)) {
                $product->product_code = $product->generateProductCode();
            }
        });

        // Cập nhật slug khi updating
        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name, static::class);
            }
            // Không tự động tạo lại product_code khi updating để tránh thay đổi mã đã tồn tại
        });

    }    
    /**
     * Tạo mã sản phẩm theo định dạng [TÊNSẢNPHẨM]_[TÊNNGƯỜIDÙNG]_[4SOTHUTU]
     * Ví dụ: Cà chua cherry đỏ, Nguyễn Trần Việt Hoàng -> CACHUACHERRYDO_NTVH_0001
     * Giới hạn productCodePart tối đa 200 ký tự
     */
    public function generateProductCode()
    {
        // Lấy full tên sản phẩm dạng slug và giới hạn 200 ký tự
        $productCodePart = substr(strtoupper(Str::slug($this->name, '')), 0, 200);

        // Lấy tên người dùng từ quan hệ user
        $user = $this->user ?? auth()->user();
        $userName = $user ? $user->full_name : 'UNKNOWN';
        $userNameParts = explode(' ', trim($userName));
        $userCodePart = '';
        foreach ($userNameParts as $part) {
            $userCodePart .= strtoupper(substr($part, 0, 1)); // Lấy chữ cái đầu mỗi từ
        }
        $userCodePart = substr($userCodePart, 0, 4); // Giới hạn tối đa 4 ký tự cho tên người dùng

        // Tạo tiền tố
        $prefix = "{$productCodePart}_{$userCodePart}";

        // Tạo số thứ tự 4 chữ số
        $latestProduct = static::where('product_code', 'like', "{$prefix}_%")
            ->orderBy('product_code', 'desc')
            ->first();

        $sequence = 1;
        if ($latestProduct) {
            $parts = explode('_', $latestProduct->product_code);
            $sequence = (int)end($parts) + 1;
        }

        $sequenceStr = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Ghép lại (tổng độ dài có thể vượt 200, nhưng database sẽ tự cắt nếu cần)
        return "{$prefix}_{$sequenceStr}";
    }
}
