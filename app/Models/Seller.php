<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Seller - Penjual/Toko
 * 
 * Model ini merepresentasikan entitas penjual/toko dalam sistem e-commerce AKRAB.
 * Setiap penjual terhubung ke pengguna (user) dan memiliki informasi seperti
 * nama toko, nama pemilik, email, status, tanggal bergabung, jumlah produk
 * aktif, total penjualan, rating, dan informasi bank.
 * 
 * Fitur Seller:
 * - Memiliki toko dengan nama unik (store_name)
 * - Memiliki koordinat lokasi (lat, lng) untuk fitur Get Directions
 * - Memiliki rating dari ulasan pembeli
 * - Terhubung ke user untuk autentikasi
 * - Memiliki informasi bank untuk pembayaran
 * 
 * Get Directions:
 * - Seller memiliki field 'lat' dan 'lng' untuk koordinat toko
 * - Koordinat ini digunakan untuk menampilkan lokasi di map
 * - Customer dapat mendapatkan arah ke toko menggunakan koordinat ini
 */
class Seller extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'user_id',                    // ID pengguna yang terkait dengan penjual
        'store_name',                 // Nama toko penjual (unik)
        'owner_name',                 // Nama pemilik toko
        'email',                      // Email penjual
        'status',                     // Status penjual (aktif, ditangguhkan, dll)
        'join_date',                  // Tanggal penjual bergabung
        'active_products_count',      // Jumlah produk aktif yang dijual
        'total_sales',                // Total penjualan (jumlah transaksi)
        'rating',                     // Rating rata-rata penjual (1-5)
        'bank_account_number',        // Nomor rekening bank untuk pembayaran
        'bank_name',                  // Nama bank penjual
        'account_holder_name',        // Nama pemilik rekening bank
        'profile_image',              // Gambar profil/logo toko
        'lat',                        // Latitude koordinat lokasi toko (untuk Get Directions)
        'lng',                        // Longitude koordinat lokasi toko (untuk Get Directions)
        'address',                    // Alamat lengkap toko
    ];

    /**
     * Konfigurasi casting atribut
     *
     * @var array
     */
    protected $casts = [
        'join_date' => 'date',        // Casting tanggal bergabung ke date object
        'total_sales' => 'decimal:2', // Casting total penjualan ke desimal dengan 2 angka di belakang koma
        'rating' => 'decimal:2',      // Casting rating ke desimal dengan 2 angka di belakang koma
    ];

    /**
     * Relasi ke pengguna (user)
     * 
     * Setiap seller dimiliki oleh satu user (akun login).
     * User digunakan untuk autentikasi, Seller untuk data toko.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke produk
     * 
     * Satu seller dapat memiliki banyak produk untuk dijual.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}