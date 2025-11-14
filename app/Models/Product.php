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
        'subcategory',
        'seller_id',
        'status',
        'specifications',
        'material',
        'size',
        'color',
        'brand',
        'features',
        'min_order',
        'origin',
        'warranty',
        'view_count',
        'discount_price',
        'discount_start_date',
        'discount_end_date',
        'sku',
    ];

    protected $casts = [
        'specifications' => 'array',
        'features' => 'array',
        'additional_images' => 'array',
        'discount_start_date' => 'date',
        'discount_end_date' => 'date',
    ];

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke subkategori
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
    
    // Relasi ke penjual
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    /**
     * Get the seller name safely
     */
    public function getSellerNameAttribute()
    {
        return $this->seller ? $this->seller->store_name : 'Unknown Seller';
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
    
    // Relasi ke ulasan yang disetujui
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
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

    // Method untuk mendapatkan rata-rata rating
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('status', 'approved')->avg('rating') ?? 0;
    }

    // Method untuk mendapatkan jumlah ulasan
    public function getReviewsCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    // Relasi ke promosi produk
    public function productPromotions()
    {
        return $this->hasMany(ProductPromotion::class);
    }

    // Method untuk mendapatkan promosi aktif untuk produk ini
    public function getActivePromotionAttribute()
    {
        return $this->productPromotions()
                   ->where('status', 'active')
                   ->where('start_date', '<=', now())
                   ->where(function($query) {
                       $query->whereNull('end_date')
                             ->orWhere('end_date', '>=', now());
                   })
                   ->first();
    }
    
    // Method untuk mendapatkan gambar utama dari product_images
    public function getMainImageAttribute()
    {
        $primaryImage = $this->images()->where('is_primary', true)->first();
        if ($primaryImage) {
            return $primaryImage->image_path;
        }

        // Fallback ke gambar pertama jika tidak ada yang ditandai sebagai utama
        $firstImage = $this->images()->first();
        return $firstImage ? $firstImage->image_path : null;
    }

    // Method untuk mendapatkan semua gambar (utama dan tambahan)
    public function getAllImagesAttribute()
    {
        $images = [];

        foreach ($this->images as $image) {
            $images[] = $image->image_path;
        }

        return $images;
    }
    
}