<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ========================================================================
// MODEL TICKET REPLY - SISTEM BALASAN TIKET (KONVERSASI)
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini merepresentasikan balasan/konversasi dalam tiket
// - Seperti chat antara user dan admin dalam thread tiket
// - Setiap balasan disimpan sebagai record terpisah
//
// FUNGSI UTAMA:
// 1. Conversation Thread - Chat bolak-balik antara user & admin
// 2. Communication Track - Semua komunikasi tercatat rapi
// 3. Evidence - Bukti komunikasi jika ada dispute
// 4. Internal Notes - Admin bisa catat internal (tidak terlihat user)
//
// FIELD PENTING:
// - ticket_id      : Tiket yang dibalas (relasi ke Ticket)
// - user_id        : User yang kirim balasan (pelapor atau admin)
// - message        : Isi balasan/pesan
// - is_internal_note : Flag apakah ini catatan internal admin (hidden dari user)
//
// CONTOH PENGGUNAAN:
// 1. User buat tiket → "Saya tidak bisa login"
// 2. Admin balas → "Baik, kami cek dulu akun Anda"
// 3. User balas → "Oke, terima kasih"
// 4. Admin internal note → "User ini VIP, prioritas tinggi"
// 5. Admin balas → "Sudah kami reset password, silakan coba login lagi"
//
// RELASI:
// - ticket   : Tiket yang dibalas (BelongsTo)
// - user     : User yang kirim balasan (BelongsTo)
// ========================================================================

/**
 * Model TicketReply
 * 
 * Model ini merepresentasikan balasan atau pesan dalam thread tiket.
 * Setiap kali ada komunikasi antara user dan admin, record baru dibuat
 * di tabel ini untuk tracking conversation.
 * 
 * @package App\Models
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal_note',
    ];

    protected $casts = [
        'is_internal_note' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
