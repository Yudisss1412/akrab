<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ========================================================================
// MODEL REVIEW - ULASAN & RATING PRODUK (SOCIAL PROOF)
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini merepresentasikan ulasan/rating produk dari customer
// - Setiap review berisi: rating (1-5⭐), teks ulasan, media (foto/video)
// - Review berfungsi sebagai "social proof" untuk customer lain
//
// FUNGSI UTAMA:
// 1. Rating System - Customer kasih rating 1-5 bintang
// 2. Review Text - Customer tulis pengalaman mereka
// 3. Media Upload - Customer upload foto/video produk real
// 4. Social Proof - Customer lain baca sebelum beli
// 5. Seller Feedback - Seller tahu kualitas produk mereka
//
// FIELD PENTING:
// - user_id      : User yang memberi ulasan (pembeli)
// - product_id   : Produk yang diulas
// - order_id     : Order referensi (bukti sudah beli)
// - rating       : Rating 1-5 bintang
// - review_text  : Teks ulasan dari customer
// - status       : Status moderasi (pending/approved/rejected)
// - media        : Array foto/video yang diupload
// - reply        : Balasan dari seller
// - replied_at   : Waktu seller membalas
//
// VALIDASI:
// - Hanya user yang SUDAH BELI bisa review (via order_id)
// - Satu produk = Satu review per user per order (anti-spam)
// - Order harus status "delivered" (sudah diterima)
//
// MANFAAT REVIEW:
// - Social proof: Customer lain percaya untuk beli
// - Feedback untuk seller: Improve kualitas produk
// - Rating average: Menentukan kualitas produk di marketplace
// - SEO: User-generated content membantu SEO
// ========================================================================

/**
 * Model Review
 *
 * Model ini merepresentasikan entitas ulasan dalam sistem e-commerce AKRAB.
 * Setiap ulasan memiliki informasi seperti ID pengguna yang memberi ulasan,
 * ID produk yang diulas, ID pesanan terkait, rating, teks ulasan, status,
 * media (gambar/video), balasan dari penjual, dan waktu balasan. Model ini
 * juga menyediakan relasi ke model pengguna, produk, dan pesanan.
 * 
 * @package App\Models
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class Review extends Model
{
    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'user_id',        // ID pengguna yang memberi ulasan (pembeli)
        'product_id',     // ID produk yang diulas
        'order_id',       // ID pesanan terkait dengan ulasan (bukti sudah beli)
        'rating',         // Rating yang diberikan (1-5 bintang)
        'review_text',    // Teks ulasan dari pengguna (pengalaman mereka)
        'status',         // Status ulasan (pending, approved, rejected)
        'media',          // Media yang dilampirkan dalam ulasan (array path foto/video)
        'reply',          // Balasan dari penjual terhadap ulasan
        'replied_at',     // Waktu penjual memberi balasan
    ];

    /**
     * Konfigurasi casting atribut
     *
     * @var array
     */
    protected $casts = [
        // Casting field media ke array karena disimpan sebagai JSON di database
        // Contoh: ["path/to/image1.jpg", "path/to/image2.jpg"]
        'media' => 'array',
    ];

    /**
     * Relasi ke pengguna
     * 
     * Setiap review dimiliki oleh satu user (pembeli yang memberi ulasan).
     * Satu user dapat memiliki banyak review (untuk produk berbeda).
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
     * Setiap review adalah untuk satu produk tertentu.
     * Satu produk dapat memiliki banyak review (dari pembeli berbeda).
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
     * Setiap review terkait dengan satu order (bukti sudah beli).
     * Ini memastikan hanya pembeli asli yang bisa memberi ulasan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
