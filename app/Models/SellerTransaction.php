<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerTransaction extends Model
{
    protected $fillable = [
        'seller_id',
        'order_id',
        'order_item_id',
        'withdrawal_request_id',
        'transaction_type', // 'sale', 'withdrawal', 'commission', 'refund', etc.
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'reference_type', // 'order', 'withdrawal', etc.
        'reference_id',
        'status', // 'completed', 'pending', 'cancelled', etc.
        'transaction_date',
        'metadata',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Relasi ke penjual
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    // Relasi ke pesanan (jika transaksi terkait dengan pesanan)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke item pesanan (jika transaksi terkait dengan item pesanan)
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Relasi ke permintaan penarikan (jika transaksi terkait dengan penarikan)
    public function withdrawalRequest(): BelongsTo
    {
        return $this->belongsTo(WithdrawalRequest::class);
    }

    // Scope untuk transaksi penjualan
    public function scopeSales($query)
    {
        return $query->where('transaction_type', 'sale');
    }

    // Scope untuk transaksi penarikan
    public function scopeWithdrawals($query)
    {
        return $query->where('transaction_type', 'withdrawal');
    }

    // Scope untuk transaksi komisi
    public function scopeCommissions($query)
    {
        return $query->where('transaction_type', 'commission');
    }

    // Scope untuk transaksi yang selesai
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope untuk transaksi penjual tertentu
    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }
}
