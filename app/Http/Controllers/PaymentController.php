<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
        $this->middleware('auth');
    }

    /**
     * Proses pembayaran untuk pesanan
     */
    public function process(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|exists:orders,order_number',
            'payment_method' => 'required|in:bank_transfer,e_wallet,cod,midtrans'
        ]);

        // Ambil order berdasarkan order number
        $order = Order::where('order_number', $request->order_number)
                     ->where('user_id', Auth::id())
                     ->with(['user', 'shipping_address', 'items.product', 'items.variant'])
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

        if ($request->payment_method === 'bank_transfer' || $request->payment_method === 'e_wallet' || $request->payment_method === 'midtrans') {
            // Proses pembayaran Midtrans
            try {
                // Load relasi produk dan varian sebelum mengirim ke Midtrans
                $orderItems = $order->load('items.product', 'items.variant')->items;

                // Format items untuk dikirim ke Midtrans
                $itemsForMidtrans = [];
                foreach ($orderItems as $item) {
                    $itemsForMidtrans[] = [
                        'product' => $item->product,
                        'product_variant' => $item->variant,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                    ];
                }

                $snapToken = $this->midtransService->getSnapToken($order, $itemsForMidtrans);

                // Simpan transaction_id di payment record
                $paymentMethod = $request->payment_method; // Use the original payment method requested
                $payment = Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'payment_method' => $paymentMethod,
                        'transaction_id' => $order->order_number,
                        'payment_status' => 'pending',
                        'amount' => $order->total_amount,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran Midtrans berhasil diproses',
                    'snap_token' => $snapToken,
                    'order_number' => $order->order_number,
                    'payment_method' => $request->payment_method // Return the original payment method requested
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses pembayaran Midtrans: ' . $e->getMessage()
                ], 500);
            }
        } else {
            // Proses pembayaran metode lama
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
    }

    /**
     * Handle callback dari payment gateway
     */
    public function callback(Request $request)
    {
        // Proses callback dari payment gateway
        $externalId = $request->get('external_id') ?: $request->get('transaction_id'); // ID transaksi dari payment gateway
        $status = $request->get('status') ?: $request->get('transaction_status'); // Status pembayaran dari payment gateway
        $orderId = $request->get('order_id');
        $statusCode = $request->get('status_code');
        $fraudStatus = $request->get('fraud_status', 'accept');

        \Log::info('Payment callback received', [
            'external_id' => $externalId,
            'status' => $status,
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'fraud_status' => $fraudStatus,
            'request_data' => $request->all()
        ]);

        // Update payment berdasarkan response dari payment gateway
        $payment = Payment::where('transaction_id', $externalId)->first();

        if (!$payment) {
            // Jika tidak ditemukan dengan transaction_id, coba cari berdasarkan order_id
            if ($orderId) {
                $payment = Payment::where('order_id', $orderId)->first();
            }
        }

        if ($payment) {
            // Update payment record
            $payment->update([
                'payment_status' => $status,
                'paid_at' => in_array($status, ['settlement', 'capture', 'success', 'paid']) ? now() : null,
                'payment_gateway_response' => $request->all()
            ]);

            // Update status order jika pembayaran sukses
            $order = $payment->order;
            if ($order) {
                $newOrderStatus = $order->status; // Default status tidak berubah

                if (in_array($status, ['settlement', 'capture', 'success', 'paid'])) {
                    if ($fraudStatus === 'accept') {
                        $newOrderStatus = 'paid';
                        $order->update([
                            'status' => $newOrderStatus,
                            'paid_at' => now()
                        ]);
                    } else {
                        $newOrderStatus = 'pending';
                        $order->update([
                            'status' => $newOrderStatus
                        ]);
                    }
                } elseif (in_array($status, ['pending', 'waiting'])) {
                    $newOrderStatus = 'waiting_payment_verification';
                    $order->update([
                        'status' => $newOrderStatus
                    ]);
                } elseif (in_array($status, ['cancel', 'expire', 'failure', 'declined'])) {
                    $newOrderStatus = 'cancelled';
                    $order->update([
                        'status' => $newOrderStatus
                    ]);
                }

                // Tambahkan log bahwa pembayaran telah diterima
                $order->logs()->create([
                    'status' => $newOrderStatus,
                    'description' => 'Pembayaran berhasil diterima melalui gateway. Status: ' . $status . ', Fraud Status: ' . $fraudStatus
                ]);
            }

            return response()->json(['message' => 'Callback berhasil diproses']);
        }

        return response()->json(['message' => 'Payment tidak ditemukan'], 404);
    }

    /**
     * Upload proof of payment for bank transfer
     */
    public function uploadProof(Request $request)
    {
        \Log::info('Upload proof request received', [
            'user_id' => Auth::id(),
            'order_number' => $request->order_number,
            'has_file' => $request->hasFile('proof_image'),
            'file_info' => $request->hasFile('proof_image') ? [
                'name' => $request->file('proof_image')->getClientOriginalName(),
                'size' => $request->file('proof_image')->getSize(),
                'mime' => $request->file('proof_image')->getMimeType()
            ] : null
        ]);

        $request->validate([
            'order_number' => 'required|string|exists:orders,order_number',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB max
        ]);

        $order = Order::where('order_number', $request->order_number)
                     ->where('user_id', Auth::id())
                     ->first();

        if (!$order) {
            \Log::warning('Order not found for user', [
                'user_id' => Auth::id(),
                'order_number' => $request->order_number
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Upload the proof image
            $proofPath = null;
            if ($request->hasFile('proof_image')) {
                $file = $request->file('proof_image');

                \Log::info('Attempting to store proof image', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'client_mime' => $file->getClientMimeType(),
                    'real_path' => $file->getRealPath(),
                    'is_valid' => $file->isValid()
                ]);

                // Coba store file
                $proofPath = $file->store('payment-proofs', 'public');

                \Log::info('Proof image stored successfully', ['path' => $proofPath]);
            }

            // Create or update payment record
            $payment = Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method' => 'bank_transfer',
                    'payment_status' => 'pending_verification',
                    'amount' => $order->total_amount,
                    'proof_image' => $proofPath,
                ]
            );

            // Update order status to waiting for payment verification
            // Log status sebelum update untuk debugging
            \Log::info('Updating order status', [
                'order_id' => $order->id,
                'previous_status' => $order->status,
                'new_status' => 'waiting_payment_verification'
            ]);

            $order->update([
                'status' => 'waiting_payment_verification'
            ]);

            \Log::info('Order status updated successfully', [
                'order_id' => $order->id,
                'new_status' => $order->fresh()->status
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah. Pesanan Anda akan segera diproses setelah verifikasi.',
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Error in uploadProof', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'order_number' => $request->order_number ?? 'Not provided',
                'file_info' => $request->hasFile('proof_image') ? [
                    'name' => $request->file('proof_image')?->getClientOriginalName(),
                    'size' => $request->file('proof_image')?->getSize(),
                    'mime' => $request->file('proof_image')?->getMimeType()
                ] : 'No file uploaded'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunggah bukti pembayaran: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Update payment status for seller verification
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:paid,rejected'
        ]);

        $user = auth()->user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya penjual yang dapat memverifikasi pembayaran.'
            ], 403);
        }

        // Get seller info
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json([
                'success' => false,
                'message' => 'Seller record tidak ditemukan.'
            ], 403);
        }

        // Get the order and verify it belongs to this seller
        $order = \App\Models\Order::where('id', $request->order_id)
                      ->whereHas('items.product', function ($q) use ($seller) {
                          $q->where('seller_id', $seller->id);
                      })
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan atau tidak terkait dengan penjual ini.'
            ], 404);
        }

        // Get payment record
        $payment = $order->payment;
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Catatan pembayaran tidak ditemukan.'
            ], 404);
        }

        // Update payment status based on action
        DB::beginTransaction();

        try {
            $newStatus = $request->status;
            $payment->update([
                'payment_status' => $newStatus,
                'paid_at' => $request->status === 'paid' ? now() : $payment->paid_at
            ]);

            // Update order status based on payment status
            if ($request->status === 'paid') {
                $order->update([
                    'status' => 'confirmed',  // Change to confirmed so seller can process
                    'paid_at' => now()
                ]);

                // Add log entry
                $order->logs()->create([
                    'status' => 'confirmed',
                    'description' => 'Pembayaran terverifikasi oleh penjual',
                    'updated_by' => 'seller'
                ]);
            } else {  // rejected
                $order->update([
                    'status' => 'cancelled'
                ]);

                // Add log entry
                $order->logs()->create([
                    'status' => 'cancelled',
                    'description' => 'Pembayaran ditolak oleh penjual',
                    'updated_by' => 'seller'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->status === 'paid' ?
                    'Pembayaran berhasil diverifikasi' :
                    'Pembayaran berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
