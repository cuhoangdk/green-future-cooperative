<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class CooperativeMember extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;
    protected $table = 'cooperative_members';

    protected $fillable = [
        'username', 'slug', 'email', 'password', 'full_name', 'phone_number',
        'address', 'farm_location', 'farm_size', 'bank_account_number',
        'bank_name', 'avatar_url', 'bio', 'is_active', 'verified_at',
        'joined_at', 'last_login_at'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'verified_at' => 'datetime',
        'joined_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }
}
