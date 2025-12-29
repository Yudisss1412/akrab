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
            if ($request->isJson() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak'
                ], 403);
            }
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        $withdrawals = WithdrawalRequest::where('seller_id', $user->id)
                                       ->orderBy('created_at', 'desc')
                                       ->paginate(10);

        if ($request->isJson() || $request->wantsJson()) {
            return response()->json([
                'withdrawals' => $withdrawals
            ]);
        } else {
            // Jika tidak request JSON, kembalikan view (untuk route /penjual/saldo)
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            return view('penjual.saldo_penarikan', compact('seller'));
        }
    }

    /**
     * Mengambil data penarikan dalam format JSON (untuk AJAX)
     */
    public function getWithdrawalHistory()
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
                                       ->paginate(10); // Sesuaikan jumlah per halaman

        return response()->json([
            'withdrawals' => $withdrawals
        ]);
    }

    /**
     * Membuat permintaan penarikan dana baru
     */
    public function store(Request $request)
    {
        try {
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

            // Ambil ID penjual dari tabel sellers berdasarkan user_id
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                return response()->json([
                    'message' => 'Seller record tidak ditemukan'
                ], 403);
            }

            // Ambil saldo penjual
            \Log::info('Menghitung saldo untuk seller ID: ' . $seller->id);
            $balance = $this->getSellerBalance($seller->id);
            \Log::info('Saldo ditemukan: ' . $balance);

            if ($request->amount > $balance) {
                \Log::info('Saldo tidak mencukupi. Saldo: ' . $balance . ', Diminta: ' . $request->amount);
                return response()->json([
                    'message' => 'Saldo tidak mencukupi'
                ], 400);
            }

            DB::beginTransaction();

            $withdrawalRequest = WithdrawalRequest::create([
                'seller_id' => $seller->id,
                'amount' => $request->amount,
                'bank_account' => $request->bank_account,
                'request_date' => now()
            ]);

            // Hitung saldo sebelum (kita hitung berdasarkan transaksi sebelumnya untuk penjual ini)
            $previousTransactions = \App\Models\SellerTransaction::where('seller_id', $seller->id)
                ->where('transaction_date', '<=', now())
                ->get();

            $balanceBefore = 0;
            foreach ($previousTransactions as $prevTrans) {
                if ($prevTrans->transaction_type === 'sale') {
                    $balanceBefore += $prevTrans->amount;
                } elseif ($prevTrans->transaction_type === 'withdrawal' && $prevTrans->status === 'completed') {
                    $balanceBefore -= $prevTrans->amount;
                }
            }

            $balanceAfter = $balanceBefore - $request->amount;

            // Buat transaksi penarikan dengan status pending
            \App\Models\SellerTransaction::create([
                'seller_id' => $seller->id,
                'withdrawal_request_id' => $withdrawalRequest->id,
                'transaction_type' => 'withdrawal',
                'amount' => $request->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => "Permintaan penarikan dana",
                'reference_type' => 'withdrawal',
                'reference_id' => $withdrawalRequest->id,
                'status' => 'pending',
                'transaction_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Permintaan penarikan dana berhasil dibuat',
                'withdrawal_request' => $withdrawalRequest
            ]);
        } catch (\Exception $e) {
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
