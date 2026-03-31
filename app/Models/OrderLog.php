<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ========================================================================
// MODEL ORDERLOG - AUDIT TRAIL PERUBAHAN STATUS ORDER
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini mencatat SETIAP perubahan status order (audit trail)
// - Berguna untuk tracking, monitoring, dan dispute resolution
// - Setiap kali status berubah, record baru dibuat di tabel ini
//
// FUNGSI UTAMA:
// 1. Customer Tracking - Customer bisa lihat progress pesanan
// 2. Admin Monitoring - Audit semua perubahan status
// 3. Dispute Resolution - Bukti history jika ada sengketa
// 4. System Debugging - Track perubahan yang dilakukan sistem
//
// CONTOH PENGGUNAAN:
// - Seller update status jadi 'shipped' → Log dibuat
// - Midtrans callback update status jadi 'confirmed' → Log dibuat
// - Sistem auto-cancel order expired → Log dibuat
//
// FIELD PENTING:
// - order_id     : Order yang diubah
// - user_id      : User yang mengubah (nullable jika sistem)
// - status       : Status baru order
// - description  : Detail perubahan (auto-generated atau manual)
// - updated_by   : Text representation (admin/seller/system)
// ========================================================================

/**
 * Model OrderLog - Log/Riwayat Perubahan Status Pesanan
 *
 * Model ini merepresentasikan history/log dari perubahan status pesanan.
 * Setiap kali status order berubah, sebuah record baru dibuat di tabel ini
 * untuk tracking dan audit trail.
 *
 * Informasi yang disimpan:
 * - Order yang diubah
 * - User yang melakukan perubahan (jika ada)
 * - Status baru
 * - Deskripsi perubahan
 * - Waktu perubahan
 *
 * Berguna untuk:
 * - Customer tracking: melihat progress pesanan
 * - Admin monitoring: audit perubahan status
 * - Dispute resolution: bukti history perubahan
 * 
 * @package App\Models
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class OrderLog extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array
     */
    protected $fillable = [
        'order_id',       // ID pesanan yang diubah
        'user_id',        // ID user yang melakukan perubahan (opsional)
        'status',         // Status baru pesanan
        'description',    // Deskripsi perubahan (opsional, untuk catatan)
        'updated_by',     // Nama/nama role yang melakukan update (text representation)
    ];

    /**
     * Relasi ke pesanan (order)
     * 
     * Setiap log dimiliki oleh satu order.
     * Satu order dapat memiliki banyak log (history changes).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke user yang melakukan aksi
     * 
     * User yang mengubah status pesanan (bisa admin, seller, atau sistem).
     * Relasi ini opsional karena beberapa perubahan bisa dilakukan oleh sistem.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}