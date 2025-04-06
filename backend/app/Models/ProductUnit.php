<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'allow_decimal',
    ];
    protected $casts = [
        'allow_decimal' => 'boolean', // Ép kiểu sang boolean
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}
