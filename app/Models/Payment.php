<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_status',
        'transaction_id',
        'amount',
        'paid_at',
        'payment_gateway_response',
        'proof_image',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'payment_gateway_response' => 'array',
    ];

    // Relasi ke order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
