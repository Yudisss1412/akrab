<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ========================================================================
// MODEL ORDER - JANTUNG SISTEM E-COMMERCE
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini adalah CORE dari sistem transaksi e-commerce
// - Setiap order merepresentasikan satu pesanan lengkap dari customer
// - Memiliki relasi ke: User, OrderItems, ShippingAddress, Payment, OrderLog
//
// ORDER STATUS FLOW (STATE MACHINE):
// 1. pending     - Order dibuat, menunggu pembayaran dari customer
// 2. confirmed   - Pembayaran dikonfirmasi (via Midtrans callback)
// 3. processing  - Seller sedang mempersiapkan pesanan
// 4. shipped     - Pesanan dikirim (seller input resi)
// 5. delivered   - Pesanan diterima customer (selesai)
// 6. cancelled   - Order dibatalkan (expired, gagal bayar, refund)
//
// KOMPONEN HARGA:
// - sub_total      : Total harga produk (quantity × unit_price)
// - shipping_cost  : Ongkos kirim (REGULER/EXPRESS/SAME_DAY)
// - insurance_cost : Biaya asuransi (opsional, default 1500)
// - discount       : Diskon/voucher (opsional)
// - total_amount   : Final = sub_total + shipping_cost + insurance_cost - discount
//
// TRACKING & AUDIT:
// - OrderLog model mencatat setiap perubahan status
// - Payment model menyimpan status pembayaran dari Midtrans
// - ShippingAddress model menyimpan alamat pengiriman
// ========================================================================

/**
 * Model Order - Pesanan Pembelian
 *
 * Model ini merepresentasikan entitas pesanan dalam sistem e-commerce AKRAB.
 * Setiap pesanan memiliki informasi seperti nomor pesanan, status, jumlah
 * subtotal, biaya pengiriman, asuransi, diskon, total jumlah, waktu pembayaran,
 * catatan, kurir pengiriman, dan nomor pelacakan.
 *
 * Status order flow:
 * 1. pending - Order dibuat, menunggu pembayaran
 * 2. confirmed - Pembayaran dikonfirmasi
 * 3. processing - Pesanan diproses seller
 * 4. shipped - Pesanan dikirim
 * 5. delivered - Pesanan diterima
 * 6. cancelled - Order dibatalkan
 *
 * Model ini juga menyediakan berbagai relasi ke model lain seperti pengguna,
 * item pesanan, alamat pengiriman, log pesanan, pembayaran, dan pengembalian dana.
 * 
 * @package App\Models
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class Order extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'user_id',              // ID pengguna yang membuat pesanan
        'order_number',         // Nomor unik pesanan (format: ORD-YYYYMMDD-XXXX)
        'status',               // Status pesanan (pending, confirmed, shipped, delivered, cancelled)
        'sub_total',            // Jumlah subtotal pesanan (total harga produk sebelum ongkir & diskon)
        'shipping_cost',        // Biaya pengiriman (ongkos kirim)
        'insurance_cost',       // Biaya asuransi (opsional)
        'discount',             // Diskon pesanan (promo/voucher)
        'total_amount',         // Total jumlah pembayaran (sub_total + shipping_cost + insurance_cost - discount)
        'paid_at',              // Waktu pembayaran dilakukan
        'notes',                // Catatan tambahan dari pembeli (untuk seller/kurir)
        'shipping_courier',     // Kurir pengiriman (JNE, J&T, POS Indonesia, dll)
        'shipping_carrier',     // Operator pengiriman (same day, regular, cargo, dll)
        'tracking_number',      // Nomor pelacakan pengiriman (resi)
    ];

    /**
     * Konfigurasi casting atribut
     *
     * @var array
     */
    protected $casts = [
        'paid_at' => 'datetime',  // Casting waktu pembayaran ke datetime object
    ];

    /**
     * Relasi ke pengguna (pembeli)
     * 
     * Setiap order dimiliki oleh satu user (pembeli).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk mendapatkan nama pengguna secara aman
     * 
     * Mengembalikan nama user atau 'Unknown User' jika relasi tidak ditemukan.
     * 
     * @return string Nama pengguna atau 'Unknown User'
     */
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown User';
    }

    /**
     * Relasi ke item pesanan
     * 
     * Satu order dapat memiliki banyak item produk.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke alamat pengiriman
     * 
     * Satu order memiliki satu alamat pengiriman.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shipping_address()
    {
        return $this->hasOne(ShippingAddress::class);
    }

    /**
     * Relasi ke log pesanan
     * 
     * Log digunakan untuk tracking perubahan status order.
     * Satu order dapat memiliki banyak log (history).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }

    /**
     * Relasi ke pembayaran
     * 
     * Satu order memiliki satu record pembayaran.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Relasi ke pengembalian dana (refund)
     * 
     * Satu order dapat memiliki satu refund (jika ada pengembalian dana).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}