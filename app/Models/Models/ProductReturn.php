<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReturn extends Model
{
    protected $table = 'returns';
    
    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'reason',
        'description',
        'status',
        'refund_amount',
        'return_method',
        'requested_at',
        'processed_at',
        'processed_by',
        'admin_notes',
        'tracking_number'
    ];
    
    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'refund_amount' => 'decimal:2'
    ];
    
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
    
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(\App\Models\OrderItem::class, 'order_item_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }
}
