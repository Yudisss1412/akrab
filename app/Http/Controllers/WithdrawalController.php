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

        $balance = $this->getSellerBalance($user->id);

        return response()->json([
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
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Dalam implementasi nyata, ini akan mengambil data dari tabel transaksi
        // Untuk sekarang, kita buat data dummy yang dinamis berdasarkan penjualan
        $transactions = $this->generateTransactionHistory($user->id);

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
        // Ambil penjual
        $seller = \App\Models\Seller::find($sellerId);
        if (!$seller) {
            return 0;
        }

        // Hitung total penjualan yang selesai dari produk penjual ini
        $completedOrdersAmount = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $sellerId)
            ->where('orders.status', 'completed')
            ->sum('order_items.subtotal');

        // Hitung total penarikan yang sudah diproses
        $processedWithdrawals = DB::table('withdrawal_requests')
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('amount');

        // Hitung total biaya komisi (dalam implementasi nyata)
        $commissionAmount = 0; // Untuk implementasi sementara

        // Saldo = total penjualan - komisi - penarikan yang sudah diproses
        $balance = $completedOrdersAmount - $commissionAmount - $processedWithdrawals;

        return max(0, $balance); // Pastikan tidak minus
    }

    /**
     * Fungsi bantu untuk menghasilkan riwayat transaksi
     */
    private function generateTransactionHistory($sellerId)
    {
        // Dalam implementasi nyata, ini akan mengambil dari tabel transaksi yang sesuai
        // Untuk sekarang, kita gabungkan data dari order yang selesai dan penarikan
        $completedOrders = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('orders.created_at', 'products.name', 'order_items.subtotal as amount')
            ->where('products.seller_id', $sellerId)
            ->where('orders.status', 'completed')
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();

        $withdrawals = DB::table('withdrawal_requests')
            ->select('created_at', 'notes as name', 'amount')
            ->selectRaw("'-Penarikan Dana' as name")
            ->where('seller_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Gabungkan dan urutkan
        $transactions = collect();

        foreach ($completedOrders as $order) {
            $transactions->push([
                'date' => $order->created_at,
                'description' => 'Penjualan Produk "' . ($order->name ?? 'Produk') . '"',
                'type' => 'Pemasukan',
                'amount' => $order->amount,
            ]);
        }

        foreach ($withdrawals as $withdrawal) {
            $transactions->push([
                'date' => $withdrawal->created_at,
                'description' => 'Penarikan Dana',
                'type' => 'Pengeluaran',
                'amount' => $withdrawal->amount,
            ]);
        }

        // Urutkan berdasarkan tanggal terbaru
        $transactions = $transactions->sortByDesc('date')->values();

        return $transactions;
    }
}
