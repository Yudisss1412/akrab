<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'sub_total',
        'shipping_cost',
        'insurance_cost',
        'discount',
        'total_amount',
        'paid_at',
        'notes',
        'shipping_courier',
        'tracking_number',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    // Relasi ke user (pembeli)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user name safely
     */
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown User';
    }

    // Relasi ke item pesanan
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi ke alamat pengiriman
    public function shipping_address()
    {
        return $this->hasOne(ShippingAddress::class);
    }

    // Relasi ke log pesanan
    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }

    // Relasi ke pembayaran
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Relasi ke pengembalian dana
    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}