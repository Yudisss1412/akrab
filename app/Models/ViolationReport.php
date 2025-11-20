<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationReport extends Model
{
    use HasFactory;

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
        'fine_amount',
    ];

    protected $casts = [
        'evidence' => 'array', // Karena evidence disimpan sebagai JSON
        'handled_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function violator()
    {
        return $this->belongsTo(User::class, 'violator_user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}