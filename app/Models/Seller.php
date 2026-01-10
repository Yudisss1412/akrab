<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Seller
 *
 * Model ini merepresentasikan entitas penjual dalam sistem e-commerce AKRAB.
 * Setiap penjual terhubung ke pengguna (user) dan memiliki informasi seperti
 * nama toko, nama pemilik, email, status, tanggal bergabung, jumlah produk
 * aktif, total penjualan, rating, dan informasi bank. Model ini juga
 * menyediakan relasi ke model pengguna dan produk.
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
        'store_name',                 // Nama toko penjual
        'owner_name',                 // Nama pemilik toko
        'email',                      // Email penjual
        'status',                     // Status penjual (aktif, ditangguhkan, dll)
        'join_date',                  // Tanggal penjual bergabung
        'active_products_count',      // Jumlah produk aktif
        'total_sales',                // Total penjualan
        'rating',                     // Rating penjual
        'bank_account_number',        // Nomor rekening bank
        'bank_name',                  // Nama bank
        'account_holder_name',        // Nama pemilik rekening
        'profile_image',              // Gambar profil penjual
    ];

    /**
     * Konfigurasi casting atribut
     *
     * @var array
     */
    protected $casts = [
        'join_date' => 'date',        // Casting tanggal bergabung ke date
        'total_sales' => 'decimal:2', // Casting total penjualan ke desimal dengan 2 angka di belakang koma
        'rating' => 'decimal:2',      // Casting rating ke desimal dengan 2 angka di belakang koma
    ];

    /**
     * Relasi ke pengguna
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}