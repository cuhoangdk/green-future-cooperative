<?php

namespace App\Models;

use Http;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'full_name',
        'phone_number',
        'address_type',        
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'province' => 'string',
        'district' => 'string',
        'ward' => 'string',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    /**
     * Boot method để kiểm tra `is_default` duy nhất cho mỗi khách hàng.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (CustomerAddress $address) {
            if ($address->is_default) {
                // Hủy trạng thái is_default của các địa chỉ khác
                static::where('customer_id', $address->customer_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }
    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

}
