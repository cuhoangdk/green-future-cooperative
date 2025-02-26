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
        'province', 'district', 'ward', 'street_address',
        'description', 'farm_size', 'soil_type', 'irrigation_method',
        'latitude', 'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
