<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Payment
 *
 * Model ini merepresentasikan entitas pembayaran dalam sistem e-commerce AKRAB.
 * Setiap pembayaran memiliki informasi seperti ID pesanan terkait, metode
 * pembayaran, status pembayaran, ID transaksi, jumlah pembayaran, waktu
 * pembayaran, respons gateway pembayaran, dan bukti pembayaran. Model ini
 * juga menyediakan relasi ke model pesanan.
 */
class Payment extends Model
{
    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'order_id',                    // ID pesanan terkait dengan pembayaran
        'payment_method',              // Metode pembayaran (bank_transfer, e_wallet, cod, midtrans)
        'payment_status',              // Status pembayaran (pending, success, failed, expired)
        'transaction_id',              // ID transaksi dari gateway pembayaran
        'amount',                      // Jumlah pembayaran
        'paid_at',                     // Waktu pembayaran dilakukan
        'payment_gateway_response',    // Respons dari gateway pembayaran (array)
        'proof_image',                 // Bukti pembayaran (gambar)
    ];

    /**
     * Konfigurasi casting atribut
     *
     * @var array
     */
    protected $casts = [
        'paid_at' => 'datetime',                    // Casting waktu pembayaran ke datetime
        'payment_gateway_response' => 'array',      // Casting respons gateway pembayaran ke array
    ];

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
