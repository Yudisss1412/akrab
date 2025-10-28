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
        $withdrawalRequests = WithdrawalRequest::with(['seller.user', 'seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.withdrawal_requests', compact('withdrawalRequests'));
    }

    public function show($id)
    {
        $request = WithdrawalRequest::with(['seller.user'])->find($id);
        
        if (!$request) {
            return response()->json([
                'error' => 'Permintaan penarikan dana tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'id' => $request->id,
            'seller_name' => $request->seller->name ?? 'N/A',
            'seller_email' => $request->seller->user->email ?? 'N/A',
            'amount' => $request->amount,
            'amount_formatted' => number_format($request->amount, 0, ',', '.'),
            'status' => ucfirst(str_replace('_', ' ', $request->status)),
            'payment_method' => $request->payment_method,
            'payment_method_display' => $this->getPaymentMethodDisplay($request),
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'ewallet_number' => $request->ewallet_number,
            'notes' => $request->notes,
            'rejection_reason' => $request->rejection_reason,
            'created_at' => $request->created_at->format('d M Y H:i'),
            'updated_at' => $request->updated_at->format('d M Y H:i'),
        ]);
    }
    
    private function getPaymentMethodDisplay($request)
    {
        switch ($request->payment_method) {
            case 'bank_transfer':
                return "Transfer Bank ({$request->bank_name} - {$request->account_number})";
            case 'ewallet':
                return "E-Wallet ({$request->ewallet_number})";
            default:
                return ucfirst(str_replace('_', ' ', $request->payment_method));
        }
    }

    public function approve(Request $request, $id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        $withdrawal->update([
            'status' => 'approved'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan penarikan dana berhasil disetujui'
        ]);
    }

    public function reject(Request $request, $id)
    {
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

        $withdrawal = WithdrawalRequest::findOrFail($id);
        
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
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        $withdrawal->update([
            'status' => 'processing'
        ]);

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
                    $withdrawal->seller->name ?? 'N/A',
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
}
