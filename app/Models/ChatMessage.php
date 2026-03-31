<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ========================================================================
// MODEL CHAT MESSAGE - PESAN ANTAR USER
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini merepresentasikan pesan dalam sistem chat
// - Setiap pesan punya sender (pengirim) dan receiver (penerima)
// - Fitur standar e-commerce untuk komunikasi user
//
// FUNGSI UTAMA:
// 1. Message Storage - Simpan pesan antar user
// 2. Read Tracking - Track apakah pesan sudah dibaca
// 3. Message Type - Support text, image, file (akan dikembangkan)
// 4. Conversation History - Riwayat chat antara 2 user
//
// FIELD PENTING:
// - sender_id: User yang kirim pesan
// - receiver_id: User yang terima pesan
// - message: Isi pesan (text, max 1000 karakter)
// - message_type: Tipe pesan (text, image, file)
// - read_status: Boolean (false = belum dibaca, true = sudah dibaca)
// - read_at: Timestamp saat pesan dibaca
//
// RELASI:
// - sender: User yang kirim pesan (BelongsTo)
// - receiver: User yang terima pesan (BelongsTo)
// ========================================================================

class ChatMessage extends Model
{
    protected $fillable = [
        'sender_id',        // ID user yang kirim pesan
        'receiver_id',      // ID user yang terima pesan
        'message',          // Isi pesan (text, max 1000 karakter)
        'message_type',     // Tipe pesan: text, image, file (default: text)
        'read_status',      // Status baca: false = belum dibaca, true = sudah dibaca
        'read_at',          // Timestamp saat pesan dibaca
    ];

    protected $casts = [
        // Casting read_at ke datetime object
        'read_at' => 'datetime',
    ];

    /**
     * Relasi ke pengirim
     * 
     * Setiap pesan dikirim oleh satu user (sender).
     * Satu user dapat mengirim banyak pesan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relasi ke penerima
     * 
     * Setiap pesan diterima oleh satu user (receiver).
     * Satu user dapat menerima banyak pesan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
