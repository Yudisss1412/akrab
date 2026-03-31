<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ========================================================================
// MODEL WITHDRAWAL REQUEST - PENARIKAN DANA SELLER
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Model ini merepresentasikan permintaan penarikan dana dari seller
// - Seller tarik uang dari saldo mereka (hasil penjualan)
// - Seperti "formulir penarikan" di bank
//
// ANALOGI:
// Seperti penarikan tunai di bank:
// - WithdrawalRequest = Formulir penarikan yang diisi
// - seller_id = Pemilik rekening
// - amount = Jumlah uang yang ditarik
// - bank_account = Rekening tujuan transfer
// - status = Status penarikan (pending/approved/rejected/completed)
//
// FLOW PENARIKAN:
// 1. Seller ajukan withdrawal (status: pending)
// 2. Admin review & approve/reject
// 3. Jika approve → Transfer dana (status: completed)
// 4. Jika reject → Saldo kembali (status: rejected)
//
// FIELD PENTING:
// - seller_id      : Seller yang ajukan withdrawal
// - amount         : Jumlah dana yang ditarik (Rp)
// - status         : Status request (pending/processing/completed/rejected)
// - bank_account   : Rekening tujuan transfer
// - request_date   : Tanggal pengajuan
// - processed_date : Tanggal diproses (oleh admin)
// - notes          : Catatan (alasan reject, dll)
//
// STATUS WITHDRAWAL:
// - pending    : Menunggu persetujuan admin
// - processing : Sedang diproses (transfer sedang dilakukan)
// - completed  : Selesai (dana sudah ditransfer)
// - rejected   : Ditolak (saldo kembali ke seller)
// ========================================================================

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'seller_id',        // ID seller yang ajukan withdrawal
        'amount',           // Jumlah dana yang ditarik (dalam Rupiah)
        'status',           // Status request: pending, processing, completed, rejected
        'payment_method',   // Metode pembayaran (bank_transfer/ewallet)
        'bank_name',        // Nama bank (BCA, Mandiri, dll)
        'account_number',   // Nomor rekening
        'account_name',     // Nama pemilik rekening
        'ewallet_number',   // Nomor e-wallet (Gopay, OVO, dll)
        'rejection_reason', // Alasan penolakan (jika rejected)
        'request_date',     // Tanggal pengajuan withdrawal
        'processed_date',   // Tanggal diproses oleh admin
        'bank_account',     // Rekening tujuan (format: "BCA - 1234567")
        'notes',            // Catatan tambahan
    ];

    protected $casts = [
        // Casting amount ke decimal 2 digit (untuk Rupiah)
        'amount' => 'decimal:2',
        // Casting tanggal ke datetime object
        'request_date' => 'datetime',
        'processed_date' => 'datetime',
    ];

    /**
     * Relasi ke seller
     * 
     * Setiap withdrawal request dimiliki oleh satu seller.
     * Satu seller dapat memiliki banyak withdrawal request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    /**
     * Relasi ke user melalui seller
     * 
     * Withdrawal request terhubung ke user melalui seller.
     * Ini memudahkan untuk mendapatkan info user yang login.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'seller_id', 'seller_id');
    }
}
