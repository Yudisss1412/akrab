<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    // Relasi ke pesanan
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke varian produk (jika ada)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}