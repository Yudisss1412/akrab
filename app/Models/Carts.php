<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Carts - Keranjang Belanja
 * 
 * Model ini merepresentasikan item di keranjang belanja user.
 * Setiap record di tabel carts mewakili satu produk (dengan varian opsional)
 * yang ditambahkan user ke keranjang mereka.
 * 
 * Fitur:
 * - Menyimpan produk yang akan dibeli
 * - Menyimpan quantity produk
 * - Support varian produk (ukuran, warna, dll)
 * - Terlink ke user yang login
 */
class Carts extends Model
{
    use HasFactory;

    protected $table = 'carts';

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'user_id',                // ID user yang memiliki keranjang
        'product_id',             // ID produk yang ditambahkan
        'product_variant_id',     // ID varian produk (opsional, untuk ukuran/warna dll)
        'quantity',               // Jumlah produk yang dibeli
    ];

    /**
     * Relasi ke user (pemilik keranjang)
     * 
     * Setiap cart dimiliki oleh satu user.
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
     * Setiap cart item berisi satu produk.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke varian produk
     * 
     * Varian produk bersifat opsional. Digunakan jika produk
     * memiliki pilihan seperti ukuran, warna, dll.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}