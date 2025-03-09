<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'email',
        'password',
        'remember_token',
        'full_name',
        'phone_number',
        'avatar_url', 
        'date_of_birth',
        'gender',
        'total_orders',
        'total_spending',
        'last_order_date',
        'newsletter_subscribed',
        'is_banned',
        'verified_at',
    ];

    protected $hidden = ['password', 'remember_token',];

    protected $casts = [
        'date_of_birth' => 'date',
        'newsletter_subscribed' => 'boolean',
        'is_banned' => 'boolean',
        'verified_at' => 'datetime',
        'last_order_date' => 'datetime',
    ];

    /**
     * Quan hệ với bảng `customer_addresses`.
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
     * Hook để hash password trước khi lưu vào database.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($customer) {
            if ($customer->isDirty('password')) {
                $customer->password = bcrypt($customer->password);
            }
        });
    }
}
