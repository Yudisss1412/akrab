<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'weight',
        'image',
        'category_id',
        'seller_id',
        'status',
    ];

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke penjual
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    // Relasi ke varian produk
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Relasi ke item keranjang
    public function cartItems()
    {
        return $this->hasMany(Carts::class);
    }

    // Relasi ke item pesanan
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}