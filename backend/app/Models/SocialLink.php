<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = [
        'platform',
        'url',
        'is_active',
    ];
    protected $cast = [
        'is_active' => 'boolean',
    ];
}
