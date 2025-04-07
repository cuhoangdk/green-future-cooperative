<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
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
        'created_by_method',
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
}