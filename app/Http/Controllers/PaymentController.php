<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Proses pembayaran untuk pesanan
     */
    public function process(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|exists:orders,order_number',
            'payment_method' => 'required|in:bank_transfer,e_wallet,cod'
        ]);

        // Ambil order berdasarkan order number
        $order = Order::where('order_number', $request->order_number)
                     ->where('user_id', Auth::id())
                     ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        // Cek apakah order sudah dibayar sebelumnya
        if ($order->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah dibayar sebelumnya'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Buat atau update payment record
            $payment = Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method' => $request->payment_method,
                    'payment_status' => $request->payment_method === 'cod' ? 'success' : 'pending',
                    'amount' => $order->total_amount,
                    'paid_at' => $request->payment_method === 'cod' ? now() : null,
                ]
            );

            // Update status order
            $order->update([
                'status' => $request->payment_method === 'cod' ? 'confirmed' : 'pending',
                'paid_at' => $request->payment_method === 'cod' ? now() : null
            ]);

            // Jika pembayaran COD, tambahkan log bahwa pembayaran telah dikonfirmasi
            if ($request->payment_method === 'cod') {
                $order->logs()->create([
                    'status' => 'confirmed',
                    'description' => 'Pembayaran COD dikonfirmasi, pesanan siap diproses'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->payment_method === 'cod' ? 
                    'Pesanan berhasil dikonfirmasi. Pembayaran akan dilakukan saat pengiriman.' : 
                    'Silakan selesaikan pembayaran sesuai instruksi yang akan dikirimkan',
                'payment' => $payment,
                'order_number' => $order->order_number
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle callback dari payment gateway
     */
    public function callback(Request $request)
    {
        // Proses callback dari payment gateway
        $externalId = $request->get('external_id'); // ID transaksi dari payment gateway
        $status = $request->get('status'); // Status pembayaran dari payment gateway
        $orderId = $request->get('order_id');

        // Update payment berdasarkan response dari payment gateway
        $payment = Payment::where('transaction_id', $externalId)->first();

        if ($payment) {
            $payment->update([
                'payment_status' => $status,
                'paid_at' => $status === 'success' ? now() : null,
                'payment_gateway_response' => $request->all()
            ]);

            // Update status order jika pembayaran sukses
            if ($status === 'success') {
                $order = $payment->order;
                $order->update([
                    'status' => 'confirmed',
                    'paid_at' => now()
                ]);

                // Tambahkan log bahwa pembayaran telah diterima
                $order->logs()->create([
                    'status' => 'confirmed',
                    'description' => 'Pembayaran berhasil diterima'
                ]);
            }

            return response()->json(['message' => 'Callback berhasil diproses']);
        }

        return response()->json(['message' => 'Payment tidak ditemukan'], 404);
    }

    /**
     * Menampilkan status pembayaran
     */
    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
                     ->where('user_id', Auth::id())
                     ->with(['payment', 'shipping_address'])
                     ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'order' => $order,
            'payment' => $order->payment
        ]);
    }
}
