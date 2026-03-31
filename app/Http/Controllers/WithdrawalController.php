<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// ========================================================================
// WITHDRAWAL CONTROLLER - PENARIKAN DANA SELLER
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani penarikan dana (withdrawal) untuk seller
// - Seller bisa tarik uang dari saldo yang mereka dapat dari penjualan
// - Seperti "tarik tunai" di bank dari rekening tabungan
//
// ANALOGI:
// Seperti tarik tunai di ATM:
// - Saldo = Uang hasil penjualan di rekening
// - Withdrawal = Tarik tunai di ATM
// - Bank Account = Rekening tujuan transfer
// - Admin = Bank yang proses transfer
//
// FITUR UTAMA:
// 1. Request Withdrawal - Seller ajukan penarikan dana
// 2. View Withdrawal History - Lihat riwayat penarikan
// 3. Cancel Withdrawal - Batalkan penarikan (jika masih pending)
// 4. Get Balance - Cek saldo seller
// 5. Transaction History - Riwayat transaksi (masuk/keluar)
//
// FLOW PENARIKAN DANA:
// 1. Seller klik "Tarik Dana" → Input jumlah & rekening
// 2. Sistem cek saldo (harus cukup!)
// 3. Buat withdrawal request dengan status 'pending'
// 4. Admin lihat request → Approve/Reject
// 5. Jika approve → Transfer dana ke rekening seller
// 6. Status update → Saldo berkurang
//
// VALIDASI PENTING:
// - Saldo harus cukup (anti-negative balance)
// - Minimal withdrawal (misal: Rp 10.000)
// - Hanya seller yang bisa withdrawal
// - Admin harus approve sebelum dana dikirim
// ========================================================================

class WithdrawalController extends Controller
{
    /**
     * Menampilkan daftar permintaan penarikan dana
     * 
     * ==========================================================================
     * FITUR: WITHDRAWAL HISTORY - RIWAYAT PENARIKAN DANA
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menampilkan semua withdrawal request milik seller
     * - Seller bisa lihat history penarikan mereka (pending/completed/rejected)
     * - Pagination 10 request per halaman
     * 
     * VALIDASI:
     * - Hanya seller yang bisa akses (owner validation)
     * - Filter: hanya withdrawal milik seller yang login
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // ========================================
        // STEP 1: AMBIL USER YANG LOGIN
        // ========================================
        $user = Auth::user();

        // ========================================
        // STEP 2: VALIDASI ROLE (SELLER ONLY)
        // ========================================
        // Hanya penjual yang bisa mengakses
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            // Handle untuk request JSON vs View
            if ($request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak'
                ], 403);
            }
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // ========================================
        // STEP 3: QUERY WITHDRAWAL REQUESTS
        // ========================================
        // Ambil semua withdrawal request milik seller ini
        // Urutkan dari yang terbaru (desc)
        $withdrawals = WithdrawalRequest::where('seller_id', $user->id)
                                       ->orderBy('created_at', 'desc')
                                       ->paginate(10);  // Pagination 10 per halaman

        // ========================================
        // STEP 4: RETURN RESPONSE
        // ========================================
        // Handle untuk request JSON vs View
        if ($request->isJson() || $request->wantsJson()) {
            return response()->json([
                'withdrawals' => $withdrawals
            ]);
        } else {
            // Jika tidak request JSON, kembalikan view (untuk route /penjual/saldo)
            // Ambil data seller untuk ditampilkan di view
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            return view('penjual.saldo_penarikan', compact('seller'));
        }
    }

    /**
     * Mengambil data penarikan dalam format JSON (untuk AJAX)
     * 
     * ==========================================================================
     * FITUR: API GET WITHDRAWAL HISTORY - ENDPOINT AJAX
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini adalah API endpoint untuk frontend
     * - Frontend call via AJAX untuk load withdrawal history tanpa reload
     * - Return JSON dengan daftar withdrawal requests
     * 
     * USE CASE:
     * - Frontend load withdrawal history via AJAX
     * - Infinite scroll untuk withdrawal requests
     * - Real-time update withdrawal status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithdrawalHistory()
    {
        // ========================================
        // STEP 1: AMBIL USER YANG LOGIN
        // ========================================
        $user = Auth::user();

        // ========================================
        // STEP 2: VALIDASI ROLE (SELLER ONLY)
        // ========================================
        // Hanya penjual yang bisa mengakses
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        // ========================================
        // STEP 3: QUERY WITHDRAWAL REQUESTS
        // ========================================
        // Ambil semua withdrawal request milik seller ini
        // Pagination untuk performa (10 per halaman)
        $withdrawals = WithdrawalRequest::where('seller_id', $user->id)
                                       ->orderBy('created_at', 'desc')
                                       ->paginate(10);

        // ========================================
        // STEP 4: RETURN JSON RESPONSE
        // ========================================
        return response()->json([
            'withdrawals' => $withdrawals
        ]);
    }

    /**
     * Membuat permintaan penarikan dana baru
     * 
     * ==========================================================================
     * FITUR: REQUEST WITHDRAWAL - AJUKAN PENARIKAN DANA
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle seller ajukan withdrawal request
     * - Validasi saldo cukup (anti-negative balance)
     * - Minimal withdrawal Rp 10.000
     * - Buat withdrawal request + transaction record
     * 
     * FLOW:
     * 1. Seller input amount & bank account
     * 2. Validasi: amount >= 10.000, bank account valid
     * 3. Cek saldo seller (harus cukup!)
     * 4. Buat withdrawal request (status: pending)
     * 5. Buat transaction record (status: pending)
     * 6. Saldo dikurangi (balance_after)
     * 
     * VALIDASI:
     * - amount: Min 10.000 (minimal penarikan)
     * - bank_account: Rekening tujuan valid
     * - Saldo harus cukup (anti-negative balance)
     * - Hanya seller yang bisa withdrawal
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // ========================================
            // STEP 1: VALIDASI INPUT
            // ========================================
            // Validasi amount & bank account
            $request->validate([
                'amount' => 'required|numeric|min:10000', // Minimal 10.000 IDR
                'bank_account' => 'required|string|max:255'
            ]);

            // ========================================
            // STEP 2: AMBIL USER & VALIDASI ROLE
            // ========================================
            $user = Auth::user();

            // Hanya penjual yang bisa membuat permintaan
            if (!$user || !$user->role || $user->role->name !== 'seller') {
                return response()->json([
                    'message' => 'Akses ditolak'
                ], 403);
            }

            // ========================================
            // STEP 3: AMBIL SELLER RECORD
            // ========================================
            // Ambil ID penjual dari tabel sellers berdasarkan user_id
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                return response()->json([
                    'message' => 'Seller record tidak ditemukan'
                ], 403);
            }

            // ========================================
            // STEP 4: CEK SALDO
            // ========================================
            // Ambil saldo penjual
            \Log::info('Menghitung saldo untuk seller ID: ' . $seller->id);
            $balance = $this->getSellerBalance($seller->id);
            \Log::info('Saldo ditemukan: ' . $balance);

            // ========================================
            // STEP 5: VALIDASI SALDO CUKUP
            // ========================================
            // Cek apakah amount <= balance (anti-negative balance)
            if ($request->amount > $balance) {
                \Log::info('Saldo tidak mencukupi. Saldo: ' . $balance . ', Diminta: ' . $request->amount);
                return response()->json([
                    'message' => 'Saldo tidak mencukupi'
                ], 400);
            }

            // ========================================
            // STEP 6: MULAI DATABASE TRANSACTION
            // ========================================
            // DB transaction untuk consistency
            DB::beginTransaction();

            // ========================================
            // STEP 7: BUAT WITHDRAWAL REQUEST
            // ========================================
            // Buat withdrawal request dengan status default 'pending'
            $withdrawalRequest = WithdrawalRequest::create([
                'seller_id' => $seller->id,  // Seller yang ajukan withdrawal
                'amount' => $request->amount,  // Jumlah yang ditarik
                'bank_account' => $request->bank_account,  // Rekening tujuan
                'request_date' => now()  // Tanggal request
            ]);

            // ========================================
            // STEP 8: HITUNG SALDO SEBELUM & SESUDAH
            // ========================================
            // Hitung saldo sebelum (berdasarkan transaksi sebelumnya)
            $previousTransactions = \App\Models\SellerTransaction::where('seller_id', $seller->id)
                ->where('transaction_date', '<=', now())
                ->get();

            $balanceBefore = 0;
            foreach ($previousTransactions as $prevTrans) {
                if ($prevTrans->transaction_type === 'sale') {
                    $balanceBefore += $prevTrans->amount;  // Pemasukan dari penjualan
                } elseif ($prevTrans->transaction_type === 'withdrawal' && $prevTrans->status === 'completed') {
                    $balanceBefore -= $prevTrans->amount;  // Pengeluaran dari withdrawal sebelumnya
                }
            }

            // Saldo setelah = Saldo sebelum - Amount withdrawal
            $balanceAfter = $balanceBefore - $request->amount;

            // ========================================
            // STEP 9: BUAT TRANSACTION RECORD
            // ========================================
            // Buat transaksi penarikan dengan status pending
            // Ini akan mengurangi saldo seller
            \App\Models\SellerTransaction::create([
                'seller_id' => $seller->id,
                'withdrawal_request_id' => $withdrawalRequest->id,  // Link ke withdrawal request
                'transaction_type' => 'withdrawal',  // Tipe: withdrawal (pengeluaran)
                'amount' => $request->amount,
                'balance_before' => $balanceBefore,  // Saldo sebelum withdrawal
                'balance_after' => $balanceAfter,  // Saldo setelah withdrawal
                'description' => "Permintaan penarikan dana",
                'reference_type' => 'withdrawal',
                'reference_id' => $withdrawalRequest->id,
                'status' => 'pending',  // Status pending sampai admin approve
                'transaction_date' => now(),
            ]);

            // ========================================
            // STEP 10: COMMIT TRANSACTION
            // ========================================
            DB::commit();

            // ========================================
            // STEP 11: RETURN RESPONSE
            // ========================================
            return response()->json([
                'message' => 'Permintaan penarikan dana berhasil dibuat',
                'withdrawal_request' => $withdrawalRequest
            ]);
        } catch (\Exception $e) {
            // ========================================
            // STEP 12: ROLLBACK JIKA ERROR
            // ========================================
            // Rollback transaksi jika ada error
            DB::rollback();

            \Log::error('Error in store withdrawal: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

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

        // Perbarui status transaksi terkait
        $transaction = \App\Models\SellerTransaction::where('withdrawal_request_id', $withdrawal->id)
            ->where('transaction_type', 'withdrawal')
            ->first();

        if ($transaction) {
            // Hitung ulang saldo setelah pembatalan
            $previousTransactions = \App\Models\SellerTransaction::where('seller_id', $transaction->seller_id)
                ->where('transaction_date', '<=', now())
                ->where('id', '!=', $transaction->id) // Kecualikan transaksi yang dibatalkan
                ->get();

            $balanceBefore = 0;
            foreach ($previousTransactions as $prevTrans) {
                if ($prevTrans->transaction_type === 'sale') {
                    $balanceBefore += $prevTrans->amount;
                } elseif ($prevTrans->transaction_type === 'withdrawal' && $prevTrans->status === 'completed') {
                    $balanceBefore -= $prevTrans->amount;
                }
            }

            $balanceAfter = $balanceBefore; // Karena transaksi dibatalkan, saldo kembali ke sebelum transaksi ini

            $transaction->update([
                'status' => 'cancelled',
                'balance_after' => $balanceAfter,
                'description' => 'Permintaan penarikan dana dibatalkan oleh penjual'
            ]);
        }

        return response()->json([
            'message' => 'Permintaan penarikan dana berhasil dibatalkan'
        ]);
    }

    /**
     * Menampilkan halaman saldo penjual
     */
    public function showBalancePage()
    {
        $user = Auth::user();

        // Hanya penjual yang bisa mengakses
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // Ambil seller record
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
        }

        return view('penjual.saldo_penarikan', compact('seller'));
    }

    /**
     * Mengambil data saldo penjual dalam bentuk JSON
     */
    public function getBalanceData()
    {
        $user = Auth::user();

        // Hanya penjual yang bisa mengakses
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json([
                'success' => false,
                'message' => 'Seller record tidak ditemukan'
            ], 403);
        }

        $balance = $this->getSellerBalance($seller->id);

        return response()->json([
            'success' => true,
            'balance' => $balance
        ]);
    }

    /**
     * Mengambil data riwayat transaksi penjual
     */
    public function getTransactionHistory()
    {
        $user = Auth::user();

        // Hanya penjual yang bisa mengakses
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            \Log::info('Akses ditolak di getTransactionHistory: user tidak valid');
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        \Log::info('User ID: ' . $user->id);

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            \Log::info('Seller record tidak ditemukan untuk user_id: ' . $user->id);
            return response()->json(['message' => 'Seller record tidak ditemukan'], 403);
        }

        \Log::info('Seller ID: ' . $seller->id);

        // Ambil transaksi dari tabel seller_transactions
        $transactions = $this->generateTransactionHistory($seller->id);

        \Log::info('Jumlah transaksi ditemukan: ' . count($transactions));

        return response()->json([
            'transactions' => $transactions
        ]);
    }

    /**
     * Fungsi bantu untuk mendapatkan saldo penjual
     * Dalam implementasi nyata, ini akan berdasarkan penjualan, pengembalian, dll
     */
    private function getSellerBalance($sellerId)
    {
        // Gunakan service untuk menghitung saldo
        $statsService = new \App\Services\SellerStatisticsService();
        return $statsService->calculateSellerBalance($sellerId);
    }

    /**
     * Fungsi bantu untuk menghasilkan riwayat transaksi
     */
    private function generateTransactionHistory($sellerId)
    {
        try {
            // Ambil transaksi terbaru dari model SellerTransaction
            $transactions = \App\Models\SellerTransaction::where('seller_id', $sellerId)
                ->orderBy('transaction_date', 'desc')
                ->limit(10)
                ->get();

            $formattedTransactions = collect();

            foreach ($transactions as $transaction) {
                $description = $transaction->description;

                // Tambahkan informasi tambahan berdasarkan tipe transaksi
                if ($transaction->transaction_type === 'sale') {
                    $description = "Penjualan Produk";
                } elseif ($transaction->transaction_type === 'withdrawal') {
                    $description = "Penarikan Dana";
                } elseif ($transaction->transaction_type === 'commission') {
                    $description = "Biaya Komisi";
                }

                $formattedTransactions->push([
                    'date' => $transaction->transaction_date,
                    'description' => $description,
                    'type' => $transaction->transaction_type === 'sale' ? 'Pemasukan' :
                              ($transaction->transaction_type === 'withdrawal' ? 'Pengeluaran' : 'Biaya'),
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                ]);
            }

            // Urutkan berdasarkan tanggal terbaru
            $formattedTransactions = $formattedTransactions->sortByDesc('date')->values();

            return $formattedTransactions;
        } catch (\Exception $e) {
            \Log::error('Error dalam generateTransactionHistory: ' . $e->getMessage());
            return collect(); // Kembalikan collection kosong jika ada error
        }
    }
}
