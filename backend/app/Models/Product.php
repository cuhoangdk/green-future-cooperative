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
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
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
        'stock_quantity',
        'status',
        'sold_quantity',
        'views',
        'expired',
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
    public function quantityPrices()
    {
        return $this->hasMany(ProductQuantityPrice::class);
    }
    
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function prices(){
        return $this->hasMany(ProductQuantityPrice::class);
    }
    protected static function boot()
    {
        parent::boot();

        // Tạo slug khi creating
        static::creating(function ($product) {
            if (empty($product->id)) {
                $product->id = $product->generateProductCode();
            }
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name, static::class);
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

        // Chuyển đổi tên người dùng thành không dấu
        $userName = Str::ascii($userName);
        $userNameParts = explode(' ', trim($userName));
        $userCodePart = '';
        foreach ($userNameParts as $part) {
            $userCodePart .= strtoupper(substr($part, 0, 1)); // Lấy chữ cái đầu mỗi từ
        }
        $userCodePart = substr($userCodePart, 0, 4); // Giới hạn tối đa 4 ký tự cho tên người dùng

        // Tạo tiền tố
        $prefix = "{$productCodePart}_{$userCodePart}";

        // Tạo số thứ tự 4 chữ số
        $latestProduct = static::where('id', 'like', "{$prefix}_%")
            ->orderBy('id', 'desc')->withTrashed()
            ->first();

        $sequence = 1;
        if ($latestProduct) {
            $parts = explode('_', $latestProduct->id);
            $sequence = (int)end($parts) + 1;
        }

        $sequenceStr = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Ghép lại (tổng độ dài có thể vượt 200, nhưng database sẽ tự cắt nếu cần)
        return "{$prefix}_{$sequenceStr}";
    }
}
