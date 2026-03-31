<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// ========================================================================
// PAYMENT CONTROLLER - PROSES PEMBAYARAN (MIDTRANS & NON-MIDTRANS)
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani PROSES PEMBAYARAN di sistem e-commerce
// - BERBEDA dengan CheckoutController & Midtrans:
//   * CheckoutController = Handle checkout (buat order)
//   * PaymentController = Handle pembayaran (bayar order)
//   * Midtrans = Payment gateway (tool eksternal untuk proses pembayaran)
// 
// ANALOGI:
// - CheckoutController = Kasir scan barang di supermarket
// - PaymentController = Proses pembayaran di mesin EDC
// - Midtrans = Bank/Visa yang benar-benar proses transfer uang
//
// FITUR UTAMA:
// 1. Process Payment - Proses pembayaran via Midtrans/non-Midtrans
// 2. Payment Callback - Handle callback dari payment gateway
// 3. Upload Proof - Upload bukti transfer bank
// 4. Payment Status - Cek status pembayaran
// 5. Verify Payment - Seller verifikasi pembayaran (untuk bank transfer)
// 6. Midtrans Notification - Handle webhook dari Midtrans server
//
// METODE PEMBAYARAN:
// - Bank Transfer (BCA, Mandiri, dll) - Upload bukti transfer
// - E-Wallet (Gopay, OVO, ShopeePay) - Via Midtrans
// - COD (Cash on Delivery) - Bayar saat barang diterima
// - Midtrans Direct - Pembayaran langsung via Midtrans
//
// FLOW PEMBAYARAN:
// 1. Customer pilih metode pembayaran di checkout
// 2. PaymentController process berdasarkan metode:
//    - Midtrans: Call Midtrans API → dapat Snap Token → redirect customer
//    - Bank Transfer: Upload bukti → seller verifikasi manual
//    - COD: Order confirmed → bayar saat delivery
// 3. Payment gateway kirim callback → update status order
// 4. Order diproses seller
// ========================================================================

class PaymentController extends Controller
{
    protected $midtransService;

    /**
     * Constructor - Inject MidtransService
     * 
     * ==========================================================================
     * DEPENDENCY INJECTION - MIDTRANS SERVICE
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini meng-inject MidtransService ke dalam controller
     * - MidtransService adalah wrapper untuk Midtrans API
     * - Memisahkan logic Midtrans dari controller (clean architecture)
     * 
     * DEPENDENCIES:
     * - MidtransService: Service untuk komunikasi dengan Midtrans API
     * - Middleware 'auth': Pastikan user sudah login
     */
    public function __construct(MidtransService $midtransService)
    {
        // Inject MidtransService untuk digunakan di semua method
        $this->midtransService = $midtransService;
        
        // Require authentication untuk semua method di controller ini
        $this->middleware('auth');
    }

    /**
     * Proses pembayaran untuk pesanan
     * 
     * ==========================================================================
     * FITUR: PROCESS PAYMENT - PROSES PEMBAYARAN ORDER
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini adalah MAIN METHOD untuk proses pembayaran
     * - Handle 2 jenis pembayaran: Midtrans & Non-Midtrans
     * - Midtrans: Bank Transfer, E-Wallet via Midtrans API
     * - Non-Midtrans: COD, manual bank transfer
     * 
     * FLOW MIDTRANS:
     * 1. Validasi order & payment method
     * 2. Call MidtransService → dapat Snap Token
     * 3. Simpan payment record dengan status 'pending'
     * 4. Return Snap Token ke frontend → redirect customer ke Midtrans
     * 5. Midtrans handle pembayaran (BCA, Gopay, dll)
     * 6. Midtrans kirim callback → update status (di method callback)
     * 
     * FLOW COD:
     * 1. Validasi order & payment method
     * 2. Set payment status = 'success', order status = 'confirmed'
     * 3. Customer bayar saat barang diterima
     * 
     * FLOW BANK TRANSFER (MANUAL):
     * 1. Customer upload bukti transfer (di method uploadProof)
     * 2. Seller verifikasi manual (di method updateStatus)
     * 3. Update status order
     * 
     * VALIDASI:
     * - order_number: Harus ada di database & milik user ini
     * - payment_method: bank_transfer, e_wallet, cod, midtrans
     * - Order belum dibayar sebelumnya (anti-double payment)
     *
     * @param Request $request Objek request HTTP
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function process(Request $request)
    {
        // ========================================
        // STEP 1: VALIDASI INPUT
        // ========================================
        // Validasi order number & payment method
        $request->validate([
            'order_number' => 'required|string|exists:orders,order_number',  // Order harus valid
            'payment_method' => 'required|in:bank_transfer,e_wallet,cod,midtrans'  // Metode pembayaran valid
        ]);

        // ========================================
        // STEP 2: AMBIL ORDER
        // ========================================
        // Ambil order berdasarkan order number
        // Pastikan order milik user yang login (security)
        $order = Order::where('order_number', $request->order_number)
                     ->where('user_id', Auth::id())
                     ->with(['user', 'shipping_address', 'items.product', 'items.variant'])  // Load relasi untuk info lengkap
                     ->first();

        if (!$order) {
            // Order tidak ditemukan atau bukan milik user ini
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        // ========================================
        // STEP 3: CEK STATUS PEMBAYARAN
        // ========================================
        // Cek apakah order sudah dibayar sebelumnya
        // Anti-double payment: Order yang sudah paid tidak bisa dibayar lagi
        if ($order->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah dibayar sebelumnya'
            ], 400);
        }

        // ========================================
        // STEP 4: PROSES BERDASARKAN PAYMENT METHOD
        // ========================================
        // Cek apakah payment method menggunakan Midtrans
        if ($request->payment_method === 'bank_transfer' || $request->payment_method === 'e_wallet' || $request->payment_method === 'midtrans') {
            // ========================================
            // CABANG A: PEMBAYARAN VIA MIDTRANS
            // ========================================
            // Proses pembayaran Midtrans
            try {
                // Load relasi produk dan varian sebelum mengirim ke Midtrans
                // Data ini diperlukan untuk detail transaksi di Midtrans
                $orderItems = $order->load('items.product', 'items.variant')->items;

                // Format items untuk dikirim ke Midtrans
                // Midtrans memerlukan detail produk untuk setiap item
                $itemsForMidtrans = [];
                foreach ($orderItems as $item) {
                    $itemsForMidtrans[] = [
                        'product' => $item->product,  // Info produk
                        'product_variant' => $item->variant,  // Info varian (jika ada)
                        'quantity' => $item->quantity,  // Jumlah beli
                        'unit_price' => $item->unit_price,  // Harga per unit
                    ];
                }

                // ========================================
                // CALL MIDTRANS API
                // ========================================
                // Dapatkan Snap Token dari MidtransService
                // Snap Token digunakan untuk redirect customer ke halaman pembayaran Midtrans
                $snapToken = $this->midtransService->getSnapToken($order, $itemsForMidtrans);

                // ========================================
                // SIMPAN PAYMENT RECORD
                // ========================================
                // Simpan transaction_id di payment record
                // UpdateOrCreate: Update jika sudah ada, Create jika belum
                $paymentMethod = $request->payment_method; // Use the original payment method requested
                $payment = Payment::updateOrCreate(
                    ['order_id' => $order->id],  // Cari berdasarkan order_id
                    [
                        'payment_method' => $paymentMethod,  // Metode pembayaran
                        'transaction_id' => $order->order_number,  // ID transaksi = order number
                        'payment_status' => 'pending',  // Status pending sampai Midtrans konfirmasi
                        'amount' => $order->total_amount,  // Total amount yang harus dibayar
                    ]
                );

                // ========================================
                // RETURN SNAP TOKEN KE FRONTEND
                // ========================================
                // Frontend akan gunakan Snap Token untuk redirect ke Midtrans
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran Midtrans berhasil diproses',
                    'snap_token' => $snapToken,  // Token untuk redirect ke Midtrans
                    'order_number' => $order->order_number,
                    'payment_method' => $request->payment_method // Return the original payment method requested
                ]);

            } catch (\Exception $e) {
                // ========================================
                // HANDLE ERROR
                // ========================================
                // Jika ada error saat call Midtrans API
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses pembayaran Midtrans: ' . $e->getMessage()
                ], 500);
            }
        } else {
            // ========================================
            // CABANG B: PEMBAYARAN NON-MIDTRANS (COD/MANUAL)
            // ========================================
            // Proses pembayaran metode lama (non-Midtrans)
            DB::beginTransaction();

            try {
                // ========================================
                // STEP B1: BUAT/UPDATE PAYMENT RECORD
                // ========================================
                // Buat atau update payment record
                // COD: Status langsung 'success', paid_at = now
                // Non-COD: Status 'pending', tunggu konfirmasi
                $payment = Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'payment_method' => $request->payment_method,
                        'payment_status' => $request->payment_method === 'cod' ? 'success' : 'pending',
                        'amount' => $order->total_amount,
                        'paid_at' => $request->payment_method === 'cod' ? now() : null,
                    ]
                );

                // ========================================
                // STEP B2: UPDATE STATUS ORDER
                // ========================================
                // Update status order berdasarkan payment method
                // COD: Status 'confirmed' (seller bisa langsung proses)
                // Non-COD: Status 'pending' (tunggu pembayaran)
                $order->update([
                    'status' => $request->payment_method === 'cod' ? 'confirmed' : 'pending',
                    'paid_at' => $request->payment_method === 'cod' ? now() : null
                ]);

                // ========================================
                // STEP B3: LOG UNTUK COD
                // ========================================
                // Jika pembayaran COD, tambahkan log bahwa pembayaran telah dikonfirmasi
                if ($request->payment_method === 'cod') {
                    $order->logs()->create([
                        'status' => 'confirmed',
                        'description' => 'Pembayaran COD dikonfirmasi, pesanan siap diproses'
                    ]);
                }

                DB::commit();

                // ========================================
                // STEP B4: RETURN RESPONSE
                // ========================================
                return response()->json([
                    'success' => true,
                    'message' => $request->payment_method === 'cod' ?
                        'Pesanan berhasil dikonfirmasi. Pembayaran akan dilakukan saat pengiriman.' :
                        'Silakan selesaikan pembayaran sesuai instruksi yang akan dikirimkan',
                    'payment' => $payment,
                    'order_number' => $order->order_number
                ]);

            } catch (\Exception $e) {
                // ========================================
                // STEP B5: HANDLE ERROR (ROLLBACK)
                // ========================================
                // Rollback transaksi jika ada error
                DB::rollback();

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage()
                ], 500);
            }
        }
    }

    /**
     * Handle callback dari payment gateway (untuk metode pembayaran selain Midtrans)
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
                        // Gunakan 'confirmed' agar konsisten dengan sistem penjual
                        $newOrderStatus = 'confirmed';
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

    /**
     * Endpoint untuk mensimulasikan callback pembayaran (development only)
     */
    public function simulateCallback(Request $request)
    {
        // Hanya untuk development/testing
        if (app()->environment('production')) {
            return response()->json(['error' => 'Endpoint ini hanya tersedia di lingkungan development'], 403);
        }

        $request->validate([
            'order_number' => 'required|string|exists:orders,order_number',
            'status' => 'required|in:settlement,capture,success,paid,pending,waiting,cancel,expire,failure,declined',
            'fraud_status' => 'required|in:accept,deny'
        ]);

        $order = Order::where('order_number', $request->order_number)
                     ->where('user_id', Auth::id())
                     ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        $payment = $order->payment;

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pembayaran tidak ditemukan'], 404);
        }

        $status = $request->status;
        $fraudStatus = $request->fraud_status;

        // Update payment record
        $payment->update([
            'payment_status' => $status,
            'paid_at' => in_array($status, ['settlement', 'capture', 'success', 'paid']) ? now() : null,
            'payment_gateway_response' => [
                'status' => $status,
                'fraud_status' => $fraudStatus,
                'simulated' => true,
                'timestamp' => now()->toISOString()
            ]
        ]);

        // Update status order
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
            'description' => 'Pembayaran berhasil disimulasikan melalui endpoint testing. Status: ' . $status . ', Fraud Status: ' . $fraudStatus
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Callback berhasil disimulasikan',
            'order' => $order->fresh(),
            'payment' => $payment->fresh()
        ]);
    }

    /**
     * Handle Midtrans payment notification
     * This method is called by Midtrans server when payment status changes
     */
    public function midtransNotification(Request $request)
    {
        // Log the incoming notification
        \Log::info('Midtrans notification received', [
            'request_data' => $request->all()
        ]);

        // Get payload from Midtrans
        $payload = $request->getContent();
        $notification = json_decode($payload, true);

        if (!$notification) {
            \Log::error('Invalid JSON payload received from Midtrans');
            return response()->json(['message' => 'Invalid JSON'], 400);
        }

        $orderId = $notification['order_id'];
        $transactionStatus = $notification['transaction_status'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        $signatureKey = $notification['signature_key'] ?? '';
        $fraudStatus = $notification['fraud_status'] ?? 'accept';

        // Verify signature key for security
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (!hash_equals($expectedSignature, $signatureKey)) {
            \Log::error('Midtrans signature verification failed', [
                'received_signature' => $signatureKey,
                'expected_signature' => $expectedSignature,
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'gross_amount' => $grossAmount
            ]);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // Find the order based on order_number (which is sent as order_id in Midtrans)
        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            \Log::error('Order not found for Midtrans notification', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus
            ]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Find the payment record associated with this order
        $payment = Payment::where('order_id', $order->id)->first();

        if (!$payment) {
            \Log::error('Payment not found for order', [
                'order_id' => $order->id,
                'order_number' => $orderId
            ]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Log the status change
        \Log::info('Processing Midtrans notification for order', [
            'order_number' => $orderId,
            'old_order_status' => $order->status,
            'old_payment_status' => $payment->payment_status,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus
        ]);

        // Map Midtrans status to our local order status
        $newOrderStatus = $order->status; // Default to current status
        $newPaymentStatus = $payment->payment_status; // Default to current status

        switch ($transactionStatus) {
            case 'settlement':
            case 'capture':
                if ($fraudStatus === 'accept') {
                    $newOrderStatus = 'confirmed'; // Changed from 'paid' to 'confirmed' to match seller system
                    $newPaymentStatus = 'success';
                } else {
                    $newOrderStatus = 'pending'; // Keep pending if fraud check fails
                    $newPaymentStatus = 'pending';
                }
                break;

            case 'pending':
                $newOrderStatus = 'pending';
                $newPaymentStatus = 'pending';
                break;

            case 'expire':
                $newOrderStatus = 'cancelled';
                $newPaymentStatus = 'expired';
                break;

            case 'cancel':
            case 'deny':
                $newOrderStatus = 'cancelled';
                $newPaymentStatus = 'failed';
                break;

            case 'refund':
            case 'partial_refund':
            case 'chargeback':
            case 'partial_chargeback':
                $newOrderStatus = 'cancelled'; // Or 'refunded' if you have that status
                $newPaymentStatus = 'refunded';
                break;
        }

        // Update payment record
        $payment->update([
            'payment_status' => $newPaymentStatus,
            'transaction_id' => $notification['transaction_id'] ?? $payment->transaction_id,
            'payment_gateway_response' => $notification,
            'paid_at' => in_array($transactionStatus, ['settlement', 'capture']) && $fraudStatus === 'accept' ? now() : $payment->paid_at
        ]);

        // Update order status if it's changing
        $orderStatusChanged = $order->status !== $newOrderStatus;

        if ($orderStatusChanged) {
            $order->update([
                'status' => $newOrderStatus,
                'paid_at' => in_array($transactionStatus, ['settlement', 'capture']) && $fraudStatus === 'accept' ? now() : $order->paid_at
            ]);

            // Add log entry for the status change
            $order->logs()->create([
                'status' => $newOrderStatus,
                'description' => "Status pesanan diubah otomatis oleh sistem Midtrans dari '{$order->status}' menjadi '{$newOrderStatus}'. Transaction status: {$transactionStatus}, Fraud status: {$fraudStatus}",
                'updated_by' => 'system'
            ]);
        }

        // Log the result
        \Log::info('Midtrans notification processed successfully', [
            'order_number' => $orderId,
            'new_order_status' => $newOrderStatus,
            'new_payment_status' => $newPaymentStatus,
            'order_status_changed' => $orderStatusChanged
        ]);

        return response()->json(['message' => 'Notification received and processed'], 200);
    }
}
