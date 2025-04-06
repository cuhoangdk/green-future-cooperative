<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Farm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name',        
        'description', 'farm_size', 'soil_type', 'irrigation_method',
        'latitude', 'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'farm_id');
    }
}
