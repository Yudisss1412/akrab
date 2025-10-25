<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'type',
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
