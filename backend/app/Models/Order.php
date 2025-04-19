<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'status',
        'full_name',
        'phone_number',
        'address_type',
        'province',
        'district',
        'ward',
        'street_address',
        'total_price',
        'shipping_fee',
        'final_total_amount',
        'notes',
        'admin_note',
        'cancelled_reason',
        'cancelled_at',
        'cancelled_by',
        'expected_delivery_date',
        'email',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'expected_delivery_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $timestamp = now()->format('YmdHis');
                $randomSequence = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $model->id = 'ORD' . $timestamp . $randomSequence;
            }
        });
    }
}