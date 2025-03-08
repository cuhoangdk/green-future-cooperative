<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderImage extends Model
{
    protected $fillable = [
        'title',
        'image_url',
        'link_url',
        'sort_order',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}