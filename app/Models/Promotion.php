<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ========================================================================
// MODEL PROMOTION - VOUCHER & DISKON
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini merepresentasikan promosi/voucher di e-commerce
// - Seller bisa buat voucher (kode promo) atau diskon produk
// - Fitur standar e-commerce untuk meningkatkan penjualan
//
// JENIS PROMOSI:
// 1. Voucher - Kode promo yang dipakai customer (DISKON50, GRATISONGKIR)
// 2. Product Discount - Diskon langsung untuk produk tertentu
//
// TIPE DISKON:
// - percentage: Diskon persen (50%, 20%, dll)
// - fixed_amount: Diskon nominal (Rp 50.000, Rp 100.000)
// - free_shipping: Gratis ongkir (akan dikembangkan)
//
// FIELD PENTING:
// - code: Kode voucher unik (untuk tracking)
// - discount_value: Nilai diskon (persen atau nominal)
// - min_order_amount: Minimal pembelian untuk pakai voucher
// - max_discount_amount: Max diskon untuk percentage type
// - usage_limit: Batas penggunaan voucher (0 = unlimited)
// - start_date & end_date: Periode berlaku promosi
// - status: active, inactive, expired
// ========================================================================

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'type',
        'category',
        'code',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'status',
        'seller_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    
    /**
     * Get the seller that owns the promotion.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
    
    /**
     * Get the product promotions for this promotion.
     */
    public function productPromotions()
    {
        return $this->hasMany(ProductPromotion::class);
    }
}
