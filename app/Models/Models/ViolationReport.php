<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViolationReport extends Model
{
    protected $table = 'violation_reports';
    
    protected $fillable = [
        'report_number',
        'reporter_user_id',
        'violator_user_id',
        'product_id',
        'order_id',
        'violation_type',
        'description',
        'evidence',
        'status',
        'admin_notes',
        'handled_by',
        'handled_at',
        'resolution',
        'fine_amount'
    ];
    
    protected $casts = [
        'evidence' => 'array',
        'handled_at' => 'datetime',
        'fine_amount' => 'decimal:2'
    ];
    
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reporter_user_id');
    }
    
    public function violator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'violator_user_id');
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
    
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
    
    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'handled_by');
    }
    
    protected static function boot()
    {
        parent::boot();
        
        // Generate unique report number when creating
        static::creating(function ($model) {
            if (empty($model->report_number)) {
                $timestamp = now()->format('Y-m-d');
                $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
                $model->report_number = "VR-{$timestamp}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
