<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['post_id', 'customer_id', 'content'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
