<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Review
 *
 * Model ini merepresentasikan entitas ulasan dalam sistem e-commerce AKRAB.
 * Setiap ulasan memiliki informasi seperti ID pengguna yang memberi ulasan,
 * ID produk yang diulas, ID pesanan terkait, rating, teks ulasan, status,
 * media (gambar/video), balasan dari penjual, dan waktu balasan. Model ini
 * juga menyediakan relasi ke model pengguna, produk, dan pesanan.
 */
class Review extends Model
{
    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'user_id',        // ID pengguna yang memberi ulasan
        'product_id',     // ID produk yang diulas
        'order_id',       // ID pesanan terkait dengan ulasan
        'rating',         // Rating yang diberikan (biasanya 1-5)
        'review_text',    // Teks ulasan dari pengguna
        'status',         // Status ulasan (pending, approved, rejected)
        'media',          // Media yang dilampirkan dalam ulasan (array)
        'reply',          // Balasan dari penjual terhadap ulasan
        'replied_at',     // Waktu penjual memberi balasan
    ];

    /**
     * Konfigurasi casting atribut
     *
     * @var array
     */
    protected $casts = [
        'media' => 'array',  // Casting field media ke array karena disimpan sebagai JSON
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke pesanan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
