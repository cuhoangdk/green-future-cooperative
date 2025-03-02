<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CultivationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'activity',
        'fertilizer_used',
        'pesticide_used',
        'image_url',
        'video_url',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}