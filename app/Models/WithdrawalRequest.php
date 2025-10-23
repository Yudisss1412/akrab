<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'seller_id',
        'amount',
        'status',
        'request_date',
        'processed_date',
        'bank_account',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'processed_date' => 'datetime',
    ];

    // Relasi ke seller
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
}
