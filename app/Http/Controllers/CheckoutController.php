<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\ShippingAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// ========================================================================
// CHECKOUT CONTROLLER - JANTUNG E-COMMERCE SYSTEM
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani seluruh alur checkout dari keranjang sampai pembayaran
// - Terintegrasi dengan Midtrans Payment Gateway untuk proses pembayaran online
// - Mendukung multiple payment methods: Bank Transfer, E-Wallet, COD
//
// ALUR CHECKOUT:
// 1. index()         - Tampilkan halaman checkout dengan ringkasan pesanan
// 2. process()       - Validasi & buat order baru (Order + ShippingAddress + OrderItems)
// 3. showShipping()  - Tampilkan halaman pengiriman
// 4. showPayment()   - Tampilkan halaman pembayaran
// 5. processPayment()- Proses pembayaran (update status order)
//
// INTEGRASI MIDTRANS:
// - Midtrans adalah payment gateway yang mendukung berbagai metode pembayaran di Indonesia
// - Payment methods: BCA Virtual Account, Mandiri Virtual Account, Gopay, OVO, ShopeePay, dll
// - Flow: Customer bayar → Midtrans proses → Midtrans kirim callback/webhook → Update status order
//
// PENTING UNTUK SIDANG:
// - Callback/Webhook Midtrans biasanya ada di endpoint terpisah (route: /midtrans/callback)
// - Callback ini yang akan mengupdate status order secara otomatis saat pembayaran berhasil
// - Untuk development, ada command artisan untuk simulate callback (SimulateMidtransCallback.php)
// ========================================================================

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->middleware('auth');
    }

    /**
     * Menampilkan halaman checkout
     */
    public function index()
    {
        // Ambil item keranjang
        $cartItems = $this->cartService->getCartItems();

        // Hitung subtotal
        $subTotal = $this->cartService->getSubtotal();

        // Biaya pengiriman default
        $shippingCost = 15000; // Harga default untuk REGULER
        $total = $subTotal + $shippingCost;

        // Ambil alamat pengguna (misalnya dari profil pengguna)
        $user = Auth::user();

        // Data untuk checkout page
        $checkoutData = [
            'cartItems' => $cartItems,
            'subTotal' => $subTotal,
            'shippingCost' => $shippingCost,
            'total' => $total,
            'user' => $user
        ];

        return view('customer.transaksi.checkout', $checkoutData);
    }

    /**
     * Proses checkout dan buat pesanan
     */
    public function process(Request $request)
    {
        \Log::info('Checkout process started', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'full_address' => 'required|string|max:500',
            'shipping_method' => 'required|in:reguler,express,same_day',
        ]);

        \Log::info('Checkout validation passed');

        DB::beginTransaction();

        try {
            // Ambil item keranjang dari user
            $cartItems = $this->cartService->getCartItems();
            if ($cartItems->isEmpty()) {
                \Log::warning('Cart is empty during checkout process');
                return redirect()->back()->withErrors(['cart' => 'Keranjang Anda kosong.']);
            }

            \Log::info('Cart items retrieved', ['count' => $cartItems->count()]);

            // Hitung subtotal
            $subTotal = $this->cartService->getSubtotal();

            // Determine shipping cost based on method
            $shippingCostMap = [
                'reguler' => 15000,
                'express' => 25000,
                'same_day' => 50000,
            ];

            $shippingCost = $shippingCostMap[$request->shipping_method] ?? 15000;

            // Buat nomor pesanan
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

            // Buat pesanan
            $insuranceCost = 1500; // Insurance cost
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'status' => 'pending', // Awalnya pesanan pending
                'sub_total' => $subTotal,
                'shipping_cost' => $shippingCost,
                'insurance_cost' => $insuranceCost,
                'total_amount' => $subTotal + $shippingCost + $insuranceCost,
                'notes' => $request->notes ?? '',
                'shipping_courier' => $request->shipping_method
            ]);

            \Log::info('Order created successfully', ['order_id' => $order->id]);

            // Buat alamat pengiriman
        // Ambil data alamat dari request - jika user mengedit di form checkout
        $recipientName = $request->recipient_name ?: $user->name;
        $phone = $request->phone ?: $user->phone;
        $province = $request->province ?: old('province', '');
        $city = $request->city ?: old('city', '');
        $district = $request->district ?: old('district', '');
        $ward = $request->ward ?: old('ward', '');
        $fullAddress = $request->full_address ?: $user->address;

        // Buat alamat pengiriman
        \Log::info('Creating shipping address with data:', [
            'order_id' => $order->id,
            'recipient_name' => $recipientName,
            'phone' => $phone,
            'province' => $province,
            'city' => $city,
            'district' => $district,
            'ward' => $ward,
            'full_address' => $fullAddress,
        ]);

        try {
            $shippingAddress = ShippingAddress::create([
                'order_id' => $order->id,
                'recipient_name' => $recipientName,
                'phone' => $phone,
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'ward' => $ward,
                'full_address' => $fullAddress,
            ]);

            \Log::info('Shipping address created successfully with ID: ' . $shippingAddress->id);

            // Kosongkan session temporary address setelah digunakan
            session()->forget('temp_shipping_address');
        } catch (\Exception $e) {
            \Log::error('Failed to create shipping address: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'request_data' => $request->all()
            ]);

            // Rollback transaksi jika pembuatan alamat gagal
            DB::rollback();

            return redirect()->back()->withErrors(['error' => 'Gagal membuat alamat pengiriman: ' . $e->getMessage()]);
        }

            // Buat item pesanan dan kurangi stok
            foreach ($cartItems as $item) {
                $product = $item['product'];
                $productVariant = $item['product_variant'];
                
                // Hitung harga berdasarkan produk dan varian
                $basePrice = $product ? $product->price : 0;
                $variantPrice = $productVariant ? $productVariant->additional_price : 0;
                $unitPrice = $basePrice + $variantPrice;
                $subtotal = $unitPrice * $item['quantity'];

                // Buat item pesanan
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Kurangi stok produk
                if ($productVariant) {
                    $productVariant->decrement('stock', $item['quantity']);
                } else {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Kosongkan keranjang setelah checkout
            $this->cartService->clearCart();

            DB::commit();

            \Log::info('Checkout process completed successfully', [
                'order_id' => $order->id,
                'shipping_address_id' => $shippingAddress->id ?? 'undefined',
                'order_number' => $order->order_number
            ]);

            // Redirect ke halaman pengiriman dengan nomor order
            return redirect()->route('cust.pengiriman.order', ['order' => $order->order_number])
                            ->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan ke pembayaran.');

        } catch (\Exception $e) {
            // Hanya rollback jika transaksi masih aktif
            if (DB::transactionLevel() > 0) {
                DB::rollback();
            }

            \Log::error('Checkout process error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine() . ' Trace: ' . $e->getTraceAsString());

            // Log error tambahan untuk debugging
            \Log::error('Error during shipping address creation:', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat proses checkout: Silakan coba lagi atau hubungi admin jika masalah terus berlanjut.']);
        }
    }

    /**
     * Menampilkan halaman pengiriman
     */
    public function showShipping($orderNumber = null)
    {
        if ($orderNumber) {
            // Jika ada order number, tampilkan detail order
            $order = Order::where('order_number', $orderNumber)
                         ->where('user_id', Auth::id())
                         ->with(['shipping_address', 'items.product', 'items.variant'])
                         ->firstOrFail();
            
            return view('customer.transaksi.pengiriman', compact('order'));
        } else {
            // Jika tidak ada order number, mungkin tampilkan pesanan terakhir
            $latestOrder = Order::where('user_id', Auth::id())
                              ->latest()
                              ->with(['shipping_address', 'items.product', 'items.variant'])
                              ->first();
            
            return view('customer.transaksi.pengiriman', compact('latestOrder'));
        }
    }

    /**
     * Update shipping method and total for an order
     */
    public function updateShipping(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'shipping_method' => 'required|in:reguler,express,same_day',
        ]);

        $order = Order::where('order_number', $request->order_number)
                      ->where('user_id', Auth::id())
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.'
            ], 404);
        }

        // Determine shipping cost based on method
        $shippingCostMap = [
            'reguler' => 15000,
            'express' => 25000,
            'same_day' => 50000,
        ];

        $shippingCost = $shippingCostMap[$request->shipping_method];
        $insuranceCost = 1500; // Insurance cost remains constant
        
        // Calculate new total
        $newTotal = $order->sub_total + $shippingCost + $insuranceCost;

        // Update the order
        $order->update([
            'shipping_cost' => $shippingCost,
            'insurance_cost' => $insuranceCost, // Store insurance cost for consistency
            'total_amount' => $newTotal,
            'shipping_courier' => $request->shipping_method
        ]);

        // Update the order
        $order->update([
            'shipping_cost' => $shippingCost,
            'total_amount' => $newTotal,
            'shipping_courier' => $request->shipping_method
        ]);

        // Refresh the order data to return
        $order->refresh();
        $order->load(['shipping_address', 'items.product', 'items.variant']);

        return response()->json([
            'success' => true,
            'message' => 'Metode pengiriman berhasil diperbarui',
            'order' => $order
        ]);
    }

    /**
     * Update shipping address for an order
     */
    public function updateShippingAddress(Request $request, $orderId)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'full_address' => 'required|string|max:500',
        ]);

        // Find the order
        $order = Order::where('id', $orderId)
                      ->where('user_id', Auth::id())
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.'
            ], 404);
        }

        // Find and update the shipping address
        $shippingAddress = $order->shipping_address;

        if (!$shippingAddress) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat pengiriman tidak ditemukan.'
            ], 404);
        }

        $shippingAddress->update([
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'ward' => $request->ward,
            'full_address' => $request->full_address,
        ]);

        // Refresh the data
        $order->refresh();
        $order->load(['shipping_address', 'items.product', 'items.variant']);

        return response()->json([
            'success' => true,
            'message' => 'Alamat pengiriman berhasil diperbarui',
            'order' => $order
        ]);
    }

    /**
     * Update user's default shipping address permanently
     */
    public function updateUserAddress(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'full_address' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        // Update user's address fields permanently
        $user->update([
            'name' => $request->recipient_name,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'ward' => $request->ward,
            'full_address' => $request->full_address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alamat pengguna berhasil diperbarui secara permanen',
            'data' => [
                'recipient_name' => $user->name,
                'phone' => $user->phone,
                'full_address' => $user->address,
            ]
        ]);
    }

    /**
     * Menampilkan halaman pembayaran
     */
    public function showPayment(Request $request)
    {
        // Check if an order number is passed as query parameter
        $orderNumber = $request->get('order');
        
        if ($orderNumber) {
            // Get specific order by order number
            $order = Order::where('order_number', $orderNumber)
                         ->where('user_id', Auth::id())
                         ->with(['shipping_address', 'items.product', 'items.variant'])
                         ->first();
        } else {
            // Get the latest order for the user
            $order = Order::where('user_id', Auth::id())
                         ->latest()
                         ->with(['shipping_address', 'items.product', 'items.variant'])
                         ->first();
        }
        
        // Data untuk ditampilkan di halaman pembayaran
        $paymentData = [
            'order' => $order,
        ];
        
        if (!$order) {
            return redirect()->back()->withErrors(['error' => 'Pesanan tidak ditemukan. Silakan kembali ke checkout.']);
        }
        
        return view('customer.transaksi.pembayaran', $paymentData);
    }
    
    /**
     * Process the payment for an order
     * 
     * ==========================================================================
     * FUNGSI INI ADALAH INTI DARI PROSES PEMBAYARAN
     * ==========================================================================
     * UNTUK SIDANG SKRIPSI:
     * - Fungsi ini menangani pembayaran untuk berbagai metode: Bank Transfer, E-Wallet, COD
     * - Untuk Bank Transfer & E-Wallet: Order status jadi 'pending', tunggu callback dari Midtrans
     * - Untuk COD: Order langsung 'confirmed', customer bayar saat barang diterima
     * 
     * FLOW PEMBAYARAN MIDTRANS:
     * 1. Customer pilih metode pembayaran (Bank Transfer/E-Wallet)
     * 2. Sistem buat transaksi di Midtrans (via Snap token di view)
     * 3. Customer bayar melalui halaman Midtrans
     * 4. Midtrans kirim webhook/callback ke endpoint /midtrans/callback
     * 5. Callback handler update status order berdasarkan response Midtrans
     * 
     * STATUS ORDER:
     * - 'pending'    : Menunggu pembayaran (customer belum bayar)
     * - 'confirmed'  : Pembayaran berhasil / COD dikonfirmasi
     * - 'processing' : Pesanan sedang diproses seller
     * - 'shipped'    : Pesanan dikirim
     * - 'delivered'  : Pesanan diterima customer
     * - 'cancelled'  : Pesanan dibatalkan (pembayaran expired/ditolak)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPayment(Request $request)
    {
        // ========================================
        // STEP 1: VALIDASI INPUT
        // ========================================
        // Validasi parameter yang diperlukan untuk proses pembayaran
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,e_wallet,cod,midtrans',
            'order_number' => 'required|string'  // Order number untuk identifikasi pesanan
        ]);

        // ========================================
        // STEP 2: CARI ORDER BERDASARKAN ORDER NUMBER
        // ========================================
        // Find order by order number and user ID
        // CATATAN: Tidak filter by status untuk avoid issue jika status sudah berubah
        $order = Order::where('order_number', $request->order_number)
                      ->where('user_id', Auth::id())
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada pesanan yang ditemukan.'
            ], 404);
        }

        // ========================================
        // STEP 3: HANDLE KHUSUS UNTUK COD
        // ========================================
        // For COD, if the order is already in a final state, consider it successful
        // COD tidak perlu tunggu payment gateway - customer bayar saat delivery
        if ($request->payment_method === 'cod' && in_array($order->status, ['confirmed', 'paid', 'processing', 'shipped', 'delivered'])) {
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses',
                'order_number' => $order->order_number
            ]);
        }

        // ========================================
        // STEP 4: CEK PENCEGAHAN DUPLICATE PROCESSING
        // ========================================
        // Check if order is already processed to prevent duplicate processing for non-COD methods
        // Status final: paid, processing, shipped, delivered, cancelled
        if (in_array($order->status, ['paid', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah diproses sebelumnya.'
            ], 400);
        }

        // ========================================
        // STEP 5: TENTUKAN STATUS BARU BERDASARKAN PAYMENT METHOD
        // ========================================
        // Update the order status based on payment method
        $newStatus = 'paid'; // Default status
        $paidAt = null; // Timestamp pembayaran - null berarti belum bayar

        switch ($request->payment_method) {
            case 'bank_transfer':
                // ========================================
                // BANK TRANSFER (VIA MIDTRANS)
                // ========================================
                // Untuk bank transfer, redirect ke halaman pembayaran Midtrans
                // Status akan diupdate via callback/webhook dari Midtrans
                // Customer dapat virtual account number untuk transfer
                $newStatus = 'pending';
                break;
                
            case 'e_wallet':
                // ========================================
                // E-WALLET (VIA MIDTRANS)
                // ========================================
                // Untuk e-wallet (Gopay, OVO, ShopeePay), redirect ke Midtrans
                // Status akan diupdate via callback/webhook dari Midtrans
                // Customer akan diarahkan ke aplikasi e-wallet untuk pembayaran
                $newStatus = 'pending';
                break;
                
            case 'cod':
                // ========================================
                // COD (CASH ON DELIVERY)
                // ========================================
                // Untuk COD, set status ke 'confirmed' langsung
                // Pembayaran akan dilakukan customer saat kurir mengantar barang
                // Risiko: customer bisa batal terima paket saat delivery
                $newStatus = 'confirmed';
                break;
        }

        // ========================================
        // STEP 6: UPDATE STATUS ORDER
        // ========================================
        // Update the order status and payment information
        $order->update([
            'status' => $newStatus,
            'paid_at' => $paidAt  // null untuk pending, akan diisi saat callback Midtrans
        ]);

        // ========================================
        // STEP 7: CATAT LOG PERUBAHAN STATUS
        // ========================================
        // Add order log to track payment - untuk audit trail
        $statusForLog = $request->payment_method === 'cod' ? 'confirmed' : 'paid';
        $description = 'Pembayaran ' . $request->payment_method . ' diproses. Status: ' . $newStatus;

        $order->logs()->create([
            'status' => $statusForLog,
            'description' => $description
        ]);

        // ========================================
        // STEP 8: RETURN RESPONSE KE CLIENT
        // ========================================
        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diproses',
            'order_number' => $order->order_number
        ]);
    }

    /**
     * Show payment confirmation page based on payment method
     */
    public function showPaymentConfirmation(Request $request)
    {
        $request->validate([
            'order' => 'required|string|exists:orders,order_number',
            'method' => 'required|in:e_wallet,cod,midtrans'
        ]);

        $order = Order::where('order_number', $request->order)
                      ->where('user_id', Auth::id())
                      ->with(['items.product.seller', 'shipping_address'])
                      ->firstOrFail();

        $viewPath = "customer.transaksi.payment.{$request->method}";

        // Return the appropriate view based on payment method
        switch ($request->method) {
            case 'e_wallet':
                return view('customer.transaksi.payment.e_wallet', compact('order'));
            case 'cod':
                return view($viewPath, compact('order'));
            case 'midtrans':
                return view('customer.transaksi.payment.midtrans', compact('order'));
            default:
                return redirect()->route('cust.pembayaran')
                    ->withErrors(['method' => 'Metode pembayaran tidak valid.']);
        }
    }
}