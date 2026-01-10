<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Order
 *
 * Model ini merepresentasikan entitas pesanan dalam sistem e-commerce AKRAB.
 * Setiap pesanan memiliki informasi seperti nomor pesanan, status, jumlah
 * subtotal, biaya pengiriman, asuransi, diskon, total jumlah, waktu pembayaran,
 * catatan, kurir pengiriman, dan nomor pelacakan. Model ini juga menyediakan
 * berbagai relasi ke model lain seperti pengguna, item pesanan, alamat pengiriman,
 * log pesanan, pembayaran, dan pengembalian dana.
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
        'order_number',         // Nomor unik pesanan
        'status',               // Status pesanan (pending, confirmed, shipped, delivered, cancelled)
        'sub_total',            // Jumlah subtotal pesanan
        'shipping_cost',        // Biaya pengiriman
        'insurance_cost',       // Biaya asuransi
        'discount',             // Diskon pesanan
        'total_amount',         // Total jumlah pembayaran
        'paid_at',              // Waktu pembayaran dilakukan
        'notes',                // Catatan tambahan dari pembeli
        'shipping_courier',     // Kurir pengiriman
        'shipping_carrier',     // Operator pengiriman
        'tracking_number',      // Nomor pelacakan pengiriman
    ];

    /**
     * Konfigurasi casting atribut
     *
     * @var array
     */
    protected $casts = [
        'paid_at' => 'datetime',  // Casting waktu pembayaran ke datetime
    ];

    /**
     * Relasi ke pengguna (pembeli)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan nama pengguna secara aman
     *
     * @return string Nama pengguna atau 'Unknown User' jika tidak ditemukan
     */
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown User';
    }

    /**
     * Relasi ke item pesanan
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shipping_address()
    {
        return $this->hasOne(ShippingAddress::class);
    }

    /**
     * Relasi ke log pesanan
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Relasi ke pengembalian dana
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}