<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ========================================================================
// MODEL TICKET - SISTEM TIKET BANTUAN (HELP DESK)
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini merepresentasikan tiket bantuan yang dibuat oleh user
// - Setiap tiket punya status flow: open → in_progress → resolved → closed
// - Digunakan untuk customer service & support tracking
//
// FUNGSI UTAMA:
// 1. Tracking Masalah - Setiap masalah user dicatat sebagai tiket
// 2. Priority Management - Prioritas tiket (low/medium/high/urgent)
// 3. Status Tracking - Monitor progress penyelesaian
// 4. Assignment - Assign tiket ke admin/CS tertentu
// 5. Audit Trail - Record kapan tiket dibuat, direspon, diselesaikan
//
// FIELD PENTING:
// - user_id        : User yang buat tiket (pelapor masalah)
// - subject        : Judul/subject tiket
// - message        : Deskripsi detail masalah
// - category       : Kategori masalah (technical, billing, product, dll)
// - priority       : Tingkat urgensi (low, medium, high, urgent)
// - status         : Status tiket (open, in_progress, resolved, closed)
// - assignee_id    : Admin/CS yang ditugaskan handle tiket ini
// - resolved_at    : Timestamp saat tiket diselesaikan
// - resolution_notes : Catatan penyelesaian dari admin
//
// RELASI:
// - user       : User yang buat tiket (BelongsTo)
// - assignee   : Admin yang handle tiket (BelongsTo)
// - replies    : Balasan/konversasi dalam tiket (HasMany)
// ========================================================================

/**
 * Model Ticket
 * 
 * Model ini merepresentasikan tiket bantuan yang dibuat oleh user
 * untuk mendapatkan dukungan dari admin/customer service.
 * 
 * @package App\Models
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'category',
        'priority',
        'status',
        'assignee_id',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Relasi ke user pembuat tiket
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke user yang ditugaskan
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    // Relasi ke balasan tiket
    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }
}
