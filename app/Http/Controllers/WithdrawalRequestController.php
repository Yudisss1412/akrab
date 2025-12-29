<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WithdrawalRequestController extends Controller
{
    public function index()
    {
        // Check if there are any real withdrawal requests in the database
        $hasRealRequests = WithdrawalRequest::exists();

        if (!$hasRealRequests) {
            // No real withdrawal requests exist, show filtered dummy data
            return $this->showFilteredDummyRequests(request());
        }

        $withdrawalRequests = WithdrawalRequest::with(['seller.user', 'seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.withdrawal_requests', compact('withdrawalRequests'));
    }

    public function show($id)
    {
        // Check if there are any real withdrawal requests in the database
        $hasRealRequests = WithdrawalRequest::exists();

        if (!$hasRealRequests) {
            // If no real requests exist, we need to create dummy data for the specific ID
            return $this->showDummyRequest($id);
        }

        $request = WithdrawalRequest::with(['seller.user'])->find($id);

        if (!$request) {
            return response()->json([
                'error' => 'Permintaan penarikan dana tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'id' => $request->id,
            'seller_name' => $request->seller->store_name ?? 'N/A',
            'seller_email' => $request->seller->user->email ?? 'N/A',
            'amount' => $request->amount,
            'amount_formatted' => number_format($request->amount, 0, ',', '.'),
            'status' => ucfirst(str_replace('_', ' ', $request->status)),
            'payment_method' => 'bank_transfer', // Default for compatibility
            'payment_method_display' => $this->getPaymentMethodDisplayForDB($request),
            'bank_name' => $this->getBankNameFromBankAccount($request->bank_account),
            'account_number' => $this->getAccountNumberFromBankAccount($request->bank_account),
            'account_name' => 'N/A', // Not stored in DB but for compatibility
            'ewallet_number' => null, // Not stored in DB but for compatibility
            'notes' => $request->notes,
            'rejection_reason' => $request->notes, // Use notes field for rejection reason (for compatibility)
            'created_at' => $request->created_at->format('d M Y H:i'),
            'updated_at' => $request->updated_at->format('d M Y H:i'),
        ]);
    }

    /**
     * Show dummy request for a specific ID when no real requests exist
     */
    private function showDummyRequest($id)
    {
        // Generate dummy data with the specific ID
        $sellers = \App\Models\Seller::with('user')->limit(10)->get();
        if ($sellers->isEmpty()) {
            $sellers = collect([
                (object) ['id' => 1, 'store_name' => 'Toko Elektronik Jaya', 'user' => (object)['email' => 'toko.jaya@example.com']],
                (object) ['id' => 2, 'store_name' => 'Fashion Modern', 'user' => (object)['email' => 'fashion.modern@example.com']],
            ]);
        }

        $amountOptions = [500000, 1000000, 1500000, 2000000, 2500000, 3000000, 5000000];
        $statuses = ['pending', 'processing', 'completed', 'rejected', 'approved'];
        $banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'BTN', 'CIMB', 'Danamon'];

        $seller = $sellers->get(($id - 1) % $sellers->count());
        $amount = $amountOptions[array_rand($amountOptions)];
        $status = $statuses[array_rand($statuses)];
        $bank = $banks[array_rand($banks)];
        $accountNumber = str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        $bankAccount = $bank . ' - ' . $accountNumber;

        $dummyRequest = [
            'id' => $id,
            'seller' => $seller,
            'amount' => $amount,
            'status' => $status,
            'bank_account' => $bankAccount,
            'notes' => $status === 'rejected' ? 'Dokumen tidak lengkap' : null,
            'created_at' => now()->subDays(rand(0, 30)),
            'updated_at' => now()->subDays(rand(0, 30)),
        ];

        return response()->json([
            'id' => $dummyRequest['id'],
            'seller_name' => $dummyRequest['seller']->store_name ?? 'N/A',
            'seller_email' => $dummyRequest['seller']->user->email ?? 'N/A',
            'amount' => $dummyRequest['amount'],
            'amount_formatted' => number_format($dummyRequest['amount'], 0, ',', '.'),
            'status' => ucfirst(str_replace('_', ' ', $dummyRequest['status'])),
            'payment_method' => 'bank_transfer', // Default for compatibility
            'payment_method_display' => "Transfer Bank ({$bank} - {$accountNumber})",
            'bank_name' => $bank,
            'account_number' => $accountNumber,
            'account_name' => $seller->owner_name ?? 'N/A',
            'ewallet_number' => null, // Not applicable for bank transfer
            'notes' => $dummyRequest['notes'],
            'rejection_reason' => $dummyRequest['notes'],
            'created_at' => $dummyRequest['created_at']->format('d M Y H:i'),
            'updated_at' => $dummyRequest['updated_at']->format('d M Y H:i'),
        ]);
    }

    private function getPaymentMethodDisplayForDB($request)
    {
        $bankName = $this->getBankNameFromBankAccount($request->bank_account);
        $accountNumber = $this->getAccountNumberFromBankAccount($request->bank_account);

        return "Transfer Bank ({$bankName} - {$accountNumber})";
    }

    private function getBankNameFromBankAccount($bankAccount)
    {
        if (!$bankAccount) return 'N/A';

        // Extract bank name from format like "BCA - 1234567"
        $parts = explode(' - ', $bankAccount);
        return $parts[0] ?? 'N/A';
    }

    private function getAccountNumberFromBankAccount($bankAccount)
    {
        if (!$bankAccount) return 'N/A';

        // Extract account number from format like "BCA - 1234567"
        $parts = explode(' - ', $bankAccount);
        return isset($parts[1]) ? $parts[1] : 'N/A';
    }

    public function approve(Request $request, $id)
    {
        try {
            \Log::info('Approve function called for ID: ' . $id);

            $user = Auth::user();
            \Log::info('User: ' . ($user ? $user->name . ' (Role: ' . ($user->role ? $user->role->name : 'null') . ')' : 'null'));

            // Hanya admin yang bisa menyetujui permintaan penarikan
            if (!$user || !$user->role || $user->role->name !== 'admin') {
                \Log::info('Access denied - not admin or no role');
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak - hanya admin yang bisa menyetujui permintaan penarikan'
                ], 403);
            }

            $withdrawal = WithdrawalRequest::with('seller')->findOrFail($id);
            \Log::info('Withdrawal found: ' . $withdrawal->id . ', Seller: ' . ($withdrawal->seller ? $withdrawal->seller->id : 'null'));

            // Pastikan seller ada
            if (!$withdrawal->seller) {
                \Log::info('Seller not found for withdrawal ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Seller tidak ditemukan untuk permintaan penarikan ini'
                ], 404);
            }

            $withdrawal->update([
                'status' => 'completed'
            ]);

            // Hitung saldo sebelum (kita hitung berdasarkan transaksi sebelumnya untuk penjual ini)
            $previousTransactions = \App\Models\SellerTransaction::where('seller_id', $withdrawal->seller->id)
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

            $balanceAfter = $balanceBefore - $withdrawal->amount;
            \Log::info('Balance calculated - Before: ' . $balanceBefore . ', After: ' . $balanceAfter);

            // Buat transaksi penarikan
            $transaction = \App\Models\SellerTransaction::create([
                'seller_id' => $withdrawal->seller->id,
                'withdrawal_request_id' => $withdrawal->id,
                'transaction_type' => 'withdrawal',
                'amount' => $withdrawal->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => "Penarikan dana selesai",
                'reference_type' => 'withdrawal',
                'reference_id' => $withdrawal->id,
                'status' => 'completed',
                'transaction_date' => now(),
            ]);

            \Log::info('Transaction created: ' . $transaction->id);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan penarikan dana berhasil diselesaikan'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in approve function: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui permintaan penarikan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        // Hanya admin yang bisa menolak permintaan penarikan
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak - hanya admin yang bisa menolak permintaan penarikan'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $withdrawal = WithdrawalRequest::with('seller')->findOrFail($id);

        // Pastikan seller ada
        if (!$withdrawal->seller) {
            return response()->json([
                'success' => false,
                'message' => 'Seller tidak ditemukan untuk permintaan penarikan ini'
            ], 404);
        }

        $withdrawal->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan penarikan dana berhasil ditolak'
        ]);
    }

    public function process(Request $request, $id)
    {
        $user = Auth::user();

        // Hanya admin yang bisa memproses permintaan penarikan
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak - hanya admin yang bisa memproses permintaan penarikan'
            ], 403);
        }

        $withdrawal = WithdrawalRequest::with('seller')->findOrFail($id);

        // Pastikan seller ada
        if (!$withdrawal->seller) {
            return response()->json([
                'success' => false,
                'message' => 'Seller tidak ditemukan untuk permintaan penarikan ini'
            ], 404);
        }

        $withdrawal->update([
            'status' => 'processing'
        ]);

        // Buat transaksi penarikan jika belum ada
        $existingTransaction = \App\Models\SellerTransaction::where('withdrawal_request_id', $withdrawal->id)
            ->where('transaction_type', 'withdrawal')
            ->first();

        if (!$existingTransaction) {
            // Hitung saldo sebelum (kita hitung berdasarkan transaksi sebelumnya untuk penjual ini)
            $previousTransactions = \App\Models\SellerTransaction::where('seller_id', $withdrawal->seller->id)
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

            $balanceAfter = $balanceBefore - $withdrawal->amount;

            \App\Models\SellerTransaction::create([
                'seller_id' => $withdrawal->seller->id,
                'withdrawal_request_id' => $withdrawal->id,
                'transaction_type' => 'withdrawal',
                'amount' => $withdrawal->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => "Penarikan dana sedang diproses",
                'reference_type' => 'withdrawal',
                'reference_id' => $withdrawal->id,
                'status' => 'completed', // Status transaksi tetap completed karena dana sudah dikurangi dari saldo
                'transaction_date' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permintaan penarikan dana berhasil diproses'
        ]);
    }
    
    public function approveBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:withdrawal_requests,id'
        ]);

        $withdrawals = WithdrawalRequest::whereIn('id', $request->ids)->get();
        
        foreach ($withdrawals as $withdrawal) {
            $withdrawal->update(['status' => 'approved']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menyetujui ' . count($withdrawals) . ' permintaan'
        ]);
    }

    public function rejectBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:withdrawal_requests,id',
            'reason' => 'required|string|max:500'
        ]);

        $withdrawals = WithdrawalRequest::whereIn('id', $request->ids)->get();
        
        foreach ($withdrawals as $withdrawal) {
            $withdrawal->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menolak ' . count($withdrawals) . ' permintaan'
        ]);
    }

    public function export()
    {
        // Check if there are any real withdrawal requests in the database
        $hasRealRequests = WithdrawalRequest::exists();

        if (!$hasRealRequests) {
            // No real withdrawal requests exist, return filtered dummy data for export
            return $this->exportDummyRequests();
        }

        // Logic untuk export data withdrawal ke file CSV atau Excel
        $withdrawals = WithdrawalRequest::with(['seller.user'])->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="withdrawal_requests.csv"',
        ];

        $callback = function() use ($withdrawals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID Permintaan',
                'Nama Penjual',
                'Email Penjual',
                'Jumlah',
                'Status',
                'Tanggal Diajukan',
                'Tanggal Diperbarui',
                'Nama Bank',
                'Nomor Rekening',
                'Catatan Penolakan'
            ]);

            foreach ($withdrawals as $withdrawal) {
                fputcsv($file, [
                    $withdrawal->id,
                    $withdrawal->seller->store_name ?? 'N/A',
                    $withdrawal->seller->user->email ?? 'N/A',
                    $withdrawal->amount,
                    ucfirst(str_replace('_', ' ', $withdrawal->status)),
                    $withdrawal->created_at->format('Y-m-d H:i:s'),
                    $withdrawal->updated_at->format('Y-m-d H:i:s'),
                    $withdrawal->bank_name ?? 'N/A',
                    $withdrawal->account_number ?? 'N/A',
                    $withdrawal->rejection_reason ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export dummy withdrawal requests data for CSV export when there are no real requests
     */
    private function exportDummyRequests()
    {
        // Get seller data for dummy requests
        $sellers = \App\Models\Seller::with('user')->limit(10)->get();
        if ($sellers->isEmpty()) {
            // If no sellers exist, create default sellers
            $sellers = collect([
                (object) ['id' => 1, 'store_name' => 'Toko Elektronik Jaya', 'user' => (object)['email' => 'toko.jaya@example.com']],
                (object) ['id' => 2, 'store_name' => 'Fashion Modern', 'user' => (object)['email' => 'fashion.modern@example.com']],
                (object) ['id' => 3, 'store_name' => 'Buku dan Alat Tulis', 'user' => (object)['email' => 'buku.tulis@example.com']],
                (object) ['id' => 4, 'store_name' => 'Olahraga Nusantara', 'user' => (object)['email' => 'olahraga.nusantara@example.com']],
                (object) ['id' => 5, 'store_name' => 'Mainan Anak Ceria', 'user' => (object)['email' => 'mainan.ceria@example.com']],
            ]);
        }

        // Generate dummy withdrawal requests
        $dummyRequests = collect();
        $amountOptions = [500000, 1000000, 1500000, 2000000, 2500000, 3000000, 5000000];
        $statuses = ['pending', 'approved', 'rejected', 'processing', 'completed'];
        $banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'BTN', 'CIMB', 'Danamon'];

        for ($i = 0; $i < 15; $i++) {
            $seller = $sellers->get($i % $sellers->count());

            $dummyRequest = [
                'id' => $i + 1,
                'seller_name' => $seller->store_name ?? 'N/A',
                'seller_email' => $seller->user->email ?? 'N/A',
                'amount' => $amountOptions[array_rand($amountOptions)],
                'status' => $statuses[array_rand($statuses)],
                'created_at' => now()->subDays(rand(0, 30))->format('Y-m-d H:i:s'),
                'updated_at' => now()->subDays(rand(0, 30))->format('Y-m-d H:i:s'),
                'bank_name' => $banks[array_rand($banks)],
                'account_number' => 'xxxx', // Simplified for export
                'rejection_reason' => in_array($statuses[array_rand($statuses)], ['rejected']) ? 'Dokumen tidak lengkap' : '-'
            ];

            $dummyRequests->push($dummyRequest);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="withdrawal_requests.csv"',
        ];

        $callback = function() use ($dummyRequests) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID Permintaan',
                'Nama Penjual',
                'Email Penjual',
                'Jumlah',
                'Status',
                'Tanggal Diajukan',
                'Tanggal Diperbarui',
                'Nama Bank',
                'Nomor Rekening',
                'Catatan Penolakan'
            ]);

            foreach ($dummyRequests as $request) {
                fputcsv($file, [
                    $request['id'],
                    $request['seller_name'],
                    $request['seller_email'],
                    $request['amount'],
                    ucfirst(str_replace('_', ' ', $request['status'])),
                    $request['created_at'],
                    $request['updated_at'],
                    $request['bank_name'],
                    $request['account_number'],
                    $request['rejection_reason']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show filtered dummy withdrawal requests when there are no real requests in the database
     */
    private function showFilteredDummyRequests($request)
    {
        // Create dummy withdrawal requests collection with consistent data
        $dummyRequests = collect();

        // Fixed seed to make the randomization consistent across requests
        mt_srand(12345); // Using a fixed seed to make data consistent

        // Get sellers from the database to use for dummy requests
        $sellers = \App\Models\Seller::with('user')->limit(10)->get();
        if ($sellers->isEmpty()) {
            // If no sellers exist, create default sellers
            $sellers = collect([
                (object) ['id' => 1, 'store_name' => 'Toko Elektronik Jaya', 'user' => (object)['email' => 'toko.jaya@example.com']],
                (object) ['id' => 2, 'store_name' => 'Fashion Modern', 'user' => (object)['email' => 'fashion.modern@example.com']],
                (object) ['id' => 3, 'store_name' => 'Buku dan Alat Tulis', 'user' => (object)['email' => 'buku.tulis@example.com']],
                (object) ['id' => 4, 'store_name' => 'Olahraga Nusantara', 'user' => (object)['email' => 'olahraga.nusantara@example.com']],
                (object) ['id' => 5, 'store_name' => 'Mainan Anak Ceria', 'user' => (object)['email' => 'mainan.ceria@example.com']],
                (object) ['id' => 6, 'store_name' => 'Makanan Sehat', 'user' => (object)['email' => 'makanan.sehat@example.com']],
                (object) ['id' => 7, 'store_name' => 'Perlengkapan Bayi', 'user' => (object)['email' => 'perlengkapan.bayi@example.com']],
                (object) ['id' => 8, 'store_name' => 'Rumah Tangga', 'user' => (object)['email' => 'rumah.tangga@example.com']],
                (object) ['id' => 9, 'store_name' => 'Otomotif Nusantara', 'user' => (object)['email' => 'otomotif.nusantara@example.com']],
                (object) ['id' => 10, 'store_name' => 'Kecantikan Alami', 'user' => (object)['email' => 'kecantikan.alami@example.com']],
            ]);
        }

        // Amount options and statuses
        $amountOptions = [500000, 1000000, 1500000, 2000000, 2500000, 3000000, 5000000, 7500000, 10000000];
        $statuses = ['pending', 'processing', 'completed', 'rejected', 'approved'];
        $banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'BTN', 'CIMB', 'Danamon'];

        // Determine filters
        $filteredStatus = $request->get('status');

        for ($i = 0; $i < 30; $i++) {
            $seller = $sellers->get($i % $sellers->count());

            $dummyRequest = new \stdClass();
            $dummyRequest->id = $i + 1;
            $dummyRequest->seller = $seller;
            $dummyRequest->seller_id = $seller->id;
            $dummyRequest->amount = $amountOptions[mt_rand(0, count($amountOptions)-1)];
            $dummyRequest->status = $filteredStatus ?? $statuses[mt_rand(0, count($statuses)-1)];
            $dummyRequest->created_at = now()->subDays(mt_rand(0, 30))->subHours(mt_rand(0, 24))->subMinutes(mt_rand(0, 60));
            $dummyRequest->updated_at = $dummyRequest->created_at->addHours(mt_rand(0, 48));
            $dummyRequest->request_date = $dummyRequest->created_at;
            $dummyRequest->processed_date = $dummyRequest->status === 'completed' ? $dummyRequest->updated_at : null;
            $bank = $banks[mt_rand(0, count($banks)-1)];
            $accountNumber = str_pad(mt_rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
            $dummyRequest->bank_account = $bank . ' - ' . $accountNumber;
            $dummyRequest->notes = $dummyRequest->status === 'rejected' ? 'Dokumen tidak lengkap' : null;

            // Apply status filter if specified
            $includeRequest = true;
            if ($filteredStatus && $dummyRequest->status !== $filteredStatus) {
                $includeRequest = false;
            }

            if ($includeRequest) {
                $dummyRequests->push($dummyRequest);
            }
        }

        // Reset the random seed to avoid affecting other parts of the application
        mt_srand();

        // Create a paginator for dummy data
        $currentPage = $request->get('page', 1);
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;
        $items = $dummyRequests->slice($offset, $perPage)->values();

        $withdrawalRequests = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $dummyRequests->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        return view('admin.withdrawal_requests', compact('withdrawalRequests'));
    }
}
