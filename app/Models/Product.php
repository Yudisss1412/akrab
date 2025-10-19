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
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the seller name safely
     */
    public function getSellerNameAttribute()
    {
        return $this->seller ? $this->seller->name : 'Unknown Seller';
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

    // Relasi ke ulasan
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relasi ke wishlist
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // Relasi ke gambar produk
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}