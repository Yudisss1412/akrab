<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Model Product
 *
 * Model ini merepresentasikan entitas produk dalam sistem e-commerce AKRAB.
 * Setiap produk memiliki informasi seperti nama, deskripsi, harga, stok, berat,
 * kategori, penjual, dan informasi tambahan seperti spesifikasi, warna, ukuran,
 * bahan, dan fitur. Model ini juga menangani pembuatan SKU unik otomatis
 * dan menyediakan berbagai relasi ke model lain seperti kategori, penjual,
 * ulasan, dan gambar produk.
 */
class Product extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'name',                      // Nama produk
        'description',               // Deskripsi produk
        'price',                     // Harga produk
        'stock',                     // Stok produk
        'weight',                    // Berat produk dalam gram
        'category_id',               // ID kategori produk
        'subcategory',               // Subkategori produk (string)
        'seller_id',                 // ID penjual yang menjual produk
        'status',                    // Status produk (aktif, tidak aktif, draft)
        'specifications',            // Spesifikasi produk (array)
        'material',                  // Bahan produk
        'size',                      // Ukuran produk
        'color',                     // Warna produk
        'brand',                     // Merek produk
        'features',                  // Fitur-fitur produk (array)
        'min_order',                 // Jumlah minimum pemesanan
        'origin',                    // Asal produk
        'warranty',                  // Garansi produk
        'view_count',                // Jumlah dilihat
        'discount_price',            // Harga diskon
        'discount_start_date',       // Tanggal mulai diskon
        'discount_end_date',         // Tanggal akhir diskon
        'sku',                       // Kode SKU produk
    ];

    /**
     * Boot model dan atur listener event
     *
     * Metode ini dijalankan saat model di-boot, digunakan untuk mengatur
     * listener event seperti creating dan updating untuk menghasilkan
     * SKU unik secara otomatis.
     */
    protected static function boot()
    {
        parent::boot();

        // Listener saat membuat produk baru
        static::creating(function ($product) {
            // Jika SKU belum diisi, hasilkan SKU unik
            if (empty($product->sku)) {
                $product->sku = $product->generateUniqueSku();
            }
        });

        // Listener saat memperbarui produk
        static::updating(function ($product) {
            // Jika SKU diubah atau ingin digenerate ulang, pastikan tetap unik
            if (!empty($product->sku)) {
                $existing = self::where('sku', $product->sku)
                                ->where('id', '!=', $product->id)
                                ->first();
                if ($existing) {
                    $product->sku = $product->generateUniqueSku();
                }
            } elseif (empty($product->sku)) {
                // Jika SKU kosong saat update, generate yang baru
                $product->sku = $product->generateUniqueSku();
            }
        });
    }

    /**
     * Menghasilkan SKU unik untuk produk
     *
     * @return string SKU unik yang dihasilkan
     */
    private function generateUniqueSku()
    {
        $baseSku = $this->generateBaseSku();

        // Cek apakah SKU sudah ada di database, jika ya tambahkan angka
        $counter = 1;
        $sku = $baseSku;

        while (self::where('sku', $sku)->exists()) {
            $sku = $baseSku . '-' . $counter;
            $counter++;
        }

        return $sku;
    }

    /**
     * Menghasilkan format dasar SKU
     *
     * @return string Format dasar SKU
     */
    private function generateBaseSku()
    {
        // Ambil 3 huruf pertama dari nama produk (dikonversi ke uppercase)
        $namePart = Str::upper(Str::substr(Str::slug($this->name, ''), 0, 3));

        // Ambil ID penjual untuk identifikasi unik
        $sellerId = $this->seller_id ? str_pad($this->seller_id, 3, '0', STR_PAD_LEFT) : '001';

        // Tambahkan timestamp untuk variasi (ambil 4 digit terakhir dari timestamp)
        $timestamp = substr(time(), -4);

        // Format: [3HURUF][SELLERID][TIMESTAMP]
        return $namePart . $sellerId . $timestamp;
    }

    /**
     * Konfigurasi casting atribut
     *
     * @var array
     */
    protected $casts = [
        'specifications' => 'array',      // Casting spesifikasi ke array
        'features' => 'array',            // Casting fitur ke array
        'additional_images' => 'array',   // Casting gambar tambahan ke array
        'discount_start_date' => 'date',  // Casting tanggal mulai diskon ke date
        'discount_end_date' => 'date',    // Casting tanggal akhir diskon ke date
    ];

    /**
     * Relasi ke kategori produk
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke subkategori produk
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    /**
     * Relasi ke penjual produk
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    /**
     * Mendapatkan nama penjual secara aman
     *
     * @return string Nama toko penjual atau 'Unknown Seller' jika tidak ditemukan
     */
    public function getSellerNameAttribute()
    {
        return $this->seller ? $this->seller->store_name : 'Unknown Seller';
    }

    /**
     * Mendapatkan informasi nomor rekening bank penjual
     *
     * @return string|null Nomor rekening bank penjual atau null jika tidak ditemukan
     */
    public function getSellerBankAccountAttribute()
    {
        return $this->seller ? $this->seller->bank_account_number : null;
    }

    /**
     * Mendapatkan nama bank penjual
     *
     * @return string|null Nama bank penjual atau null jika tidak ditemukan
     */
    public function getSellerBankNameAttribute()
    {
        return $this->seller ? $this->seller->bank_name : null;
    }

    /**
     * Mendapatkan nama pemilik rekening bank penjual
     *
     * @return string|null Nama pemilik rekening bank penjual atau null jika tidak ditemukan
     */
    public function getSellerAccountHolderNameAttribute()
    {
        return $this->seller ? $this->seller->account_holder_name : null;
    }

    /**
     * Relasi ke varian produk
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Relasi ke item keranjang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(Carts::class);
    }

    /**
     * Relasi ke item pesanan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke ulasan produk
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relasi ke ulasan yang disetujui
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    /**
     * Relasi ke wishlist
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Relasi ke gambar produk
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Mendapatkan rata-rata rating produk
     *
     * @return float Rata-rata rating dari ulasan yang disetujui
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('status', 'approved')->avg('rating') ?? 0;
    }

    /**
     * Mendapatkan jumlah ulasan produk
     *
     * @return int Jumlah ulasan yang disetujui
     */
    public function getReviewsCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Relasi ke promosi produk
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productPromotions()
    {
        return $this->hasMany(ProductPromotion::class);
    }

    /**
     * Mendapatkan promosi aktif untuk produk ini
     *
     * @return \App\Models\ProductPromotion|null Promosi aktif atau null jika tidak ada
     */
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

    /**
     * Mendapatkan gambar utama dari tabel product_images
     *
     * @return string|null Path gambar utama atau null jika tidak ditemukan
     */
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

    /**
     * Mendapatkan semua gambar produk (utama dan tambahan)
     *
     * @return array Array path gambar produk
     */
    public function getAllImagesAttribute()
    {
        $images = [];

        foreach ($this->images as $image) {
            $images[] = $image->image_path;
        }

        return $images;
    }

    /**
     * Accessor untuk rating produk (sama dengan average_rating)
     *
     * @return float Rata-rata rating produk
     */
    public function getRatingAttribute()
    {
        return $this->average_rating;
    }

}