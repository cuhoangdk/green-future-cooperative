<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductQuantityPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'price',
    ];

    protected $dates = ['deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // Thêm quan hệ gián tiếp đến ProductUnit
    public function unit()
    {
        return $this->hasOneThrough(ProductUnit::class, Product::class, 'id', 'id', 'product_id', 'unit_id');
    }
}