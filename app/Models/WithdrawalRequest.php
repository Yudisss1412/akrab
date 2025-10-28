<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'seller_id',
        'amount',
        'status',
        'payment_method',
        'bank_name',
        'account_number',
        'account_name',
        'ewallet_number',
        'rejection_reason',
        'request_date',
        'processed_date',
        'bank_account',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_date' => 'datetime',
        'processed_date' => 'datetime',
    ];

    // Relasi ke seller
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    // Relasi ke user melalui seller
    public function user()
    {
        return $this->belongsTo(User::class, 'seller_id', 'seller_id');
    }
}
