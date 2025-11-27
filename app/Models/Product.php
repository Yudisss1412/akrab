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

    /**
     * Get the seller's bank account info
     */
    public function getSellerBankAccountAttribute()
    {
        return $this->seller ? $this->seller->bank_account_number : null;
    }

    /**
     * Get the seller's bank name
     */
    public function getSellerBankNameAttribute()
    {
        return $this->seller ? $this->seller->bank_name : null;
    }

    /**
     * Get the seller's account holder name
     */
    public function getSellerAccountHolderNameAttribute()
    {
        return $this->seller ? $this->seller->account_holder_name : null;
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
        // Coba akses kolom is_primary, tapi hindari error jika tidak ada
        try {
            // Periksa apakah kolom is_primary ada dalam schema
            $schema = \Illuminate\Support\Facades\Schema::hasColumn('product_images', 'is_primary');
            if ($schema) {
                $primaryImage = $this->images()->where('is_primary', true)->first();
                if ($primaryImage) {
                    return $primaryImage->image_path;
                }
            }
        } catch (\Exception $e) {
            // Kolom tidak ditemukan, abaikan error
        }

        // Fallback ke gambar pertama
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

    // Accessor untuk rating produk (sama dengan average_rating)
    public function getRatingAttribute()
    {
        return $this->average_rating;
    }

}