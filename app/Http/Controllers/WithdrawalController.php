<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    /**
     * Menampilkan daftar permintaan penarikan dana
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Hanya penjual yang bisa mengakses
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        $withdrawals = WithdrawalRequest::where('seller_id', $user->id)
                                       ->orderBy('created_at', 'desc')
                                       ->paginate(10);

        return response()->json([
            'withdrawals' => $withdrawals
        ]);
    }

    /**
     * Membuat permintaan penarikan dana baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000', // Minimal 10.000 IDR
            'bank_account' => 'required|string|max:255'
        ]);

        $user = Auth::user();

        // Hanya penjual yang bisa membuat permintaan
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        // Ambil saldo penjual (dalam implementasi nyata, ini akan dari data penjualan)
        // Untuk saat ini, kita asumsikan user memiliki metode getBalance
        $balance = $this->getSellerBalance($user->id);

        if ($request->amount > $balance) {
            return response()->json([
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $withdrawalRequest = WithdrawalRequest::create([
                'seller_id' => $user->id,
                'amount' => $request->amount,
                'bank_account' => $request->bank_account,
                'request_date' => now()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Permintaan penarikan dana berhasil dibuat',
                'withdrawal_request' => $withdrawalRequest
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat permintaan penarikan dana: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail permintaan penarikan dana
     */
    public function show($id)
    {
        $user = Auth::user();

        $withdrawal = WithdrawalRequest::where('id', $id)
                                      ->where('seller_id', $user->id)
                                      ->first();

        if (!$withdrawal) {
            return response()->json([
                'message' => 'Permintaan penarikan dana tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'withdrawal_request' => $withdrawal
        ]);
    }

    /**
     * Membatalkan permintaan penarikan dana
     */
    public function cancel($id)
    {
        $user = Auth::user();

        $withdrawal = WithdrawalRequest::where('id', $id)
                                      ->where('seller_id', $user->id)
                                      ->first();

        if (!$withdrawal) {
            return response()->json([
                'message' => 'Permintaan penarikan dana tidak ditemukan'
            ], 404);
        }

        // Hanya bisa dibatalkan jika status masih pending
        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'message' => 'Permintaan penarikan dana tidak bisa dibatalkan'
            ], 400);
        }

        $withdrawal->update([
            'status' => 'rejected',
            'notes' => 'Dibatalkan oleh penjual'
        ]);

        return response()->json([
            'message' => 'Permintaan penarikan dana berhasil dibatalkan'
        ]);
    }

    /**
     * Fungsi bantu untuk mendapatkan saldo penjual
     * Dalam implementasi nyata, ini akan berdasarkan penjualan, pengembalian, dll
     */
    private function getSellerBalance($sellerId)
    {
        // Dalam implementasi nyata, ini akan menghitung total penjualan
        // dikurangi komisi, pengembalian, dan penarikan sebelumnya
        // Untuk contoh, kita return saldo tetap 1000000
        return 1000000;
    }
}
