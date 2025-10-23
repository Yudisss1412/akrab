<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'review_text',
        'status',
        'media',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
