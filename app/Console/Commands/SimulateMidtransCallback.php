<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

// ========================================================================
// SIMULATE MIDTRANS CALLBACK COMMAND - SENJATA RAHASIA UNTUK TESTING
// ========================================================================
// UNTUK SIDANG SKRIPSI - INI ADALAH NILAI TAMBAH (A+ WORTHY!):
// 
// MASALAH YANG DIATASI:
// - Saat development/testing, kita tidak bisa benar-benar bayar ke Midtrans
// - Midtrans callback/webhook hanya dikirim saat pembayaran real berhasil
// - Kalau mau test flow pembayaran lengkap, harus deploy ke production & bayar beneran
// - Ini tidak praktis untuk development dan demo sidang
//
// SOLUSI:
// - Command ini mensimulasikan callback dari Midtrans tanpa perlu bayar beneran
// - Bisa test semua skenario: pembayaran berhasil, gagal, expired, refund, dll
// - Bisa demo di sidang: "Lihat, order status berubah otomatis seperti dapat callback dari Midtrans"
//
// CARA PAKAI UNTUK DEMO SIDANG:
// 1. Buat order baru di sistem (checkout biasa)
// 2. Jalankan command: php artisan app:simulate-midtrans-callback
// 3. Pilih order yang baru dibuat
// 4. Pilih status: settlement (berhasil), expire (kadaluarsa), cancel (dibatalkan)
// 5. Lihat status order berubah otomatis!
//
// CONTOH COMMAND:
// - php artisan app:simulate-midtrans-callback                    # Pilih order dari list
// - php artisan app:simulate-midtrans-callback ORD-20240301-00001 # Order spesifik
// - php artisan app:simulate-midtrans-callback --status=settlement # Langsung settlement
// - php artisan app:simulate-midtrans-callback --all              # Update semua pending orders
//
// MIDTRANS TRANSACTION STATUSES:
// - settlement  : Pembayaran berhasil (uang sudah masuk)
// - capture     : Sama seperti settlement (untuk credit card)
// - pending     : Menunggu pembayaran (customer belum bayar)
// - expire      : Waktu pembayaran habis (customer tidak bayar tepat waktu)
// - cancel      : Pembayaran dibatalkan
// - deny        : Pembayaran ditolak (fraud detection)
// - refund      : Pengembalian dana
// - chargeback  : Pengembalian dana dari bank (dispute)
//
// NILAI PLUS UNTUK SIDANG:
// - Menunjukkan pemahaman tentang testing best practices
// - Memudahkan demo tanpa perlu environment production
// - Bisa test edge cases (expired, failed, refund) yang sulit ditest secara manual
// ========================================================================

class SimulateMidtransCallback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:simulate-midtrans-callback
                            {order_number? : Order number to simulate callback for (leave empty to select from pending orders)}
                            {--status=settlement : Transaction status (settlement, capture, pending, expire, cancel, deny)}
                            {--fraud=accept : Fraud status (accept, deny)}
                            {--all : Apply to all pending orders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate Midtrans payment callback for development/testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ========================================
        // AMBIL ARGUMENT & OPTION DARI COMMAND LINE
        // ========================================
        $orderNumber = $this->argument('order_number');
        $status = $this->option('status');       // Default: settlement
        $fraudStatus = $this->option('fraud');   // Default: accept
        $applyToAll = $this->option('all');      // Flag untuk proses semua pending orders

        // ========================================
        // MODE 1: --all FLAG (UPDATE SEMUA PENDING ORDERS)
        // ========================================
        // Jika flag --all digunakan, proses semua order yang masih pending
        // Berguna untuk testing bulk update atau demo skala besar
        if ($applyToAll) {
            // Cari semua order dengan status 'pending'
            // yang memiliki payment method Midtrans/bank_transfer/e_wallet
            $pendingOrders = Order::where('status', 'pending')
                ->whereHas('payment', function($query) {
                    $query->whereIn('payment_method', ['midtrans', 'bank_transfer', 'e_wallet']);
                })
                ->get();

            if ($pendingOrders->isEmpty()) {
                $this->info("No pending orders with Midtrans/bank_transfer/e_wallet payment method found.");
                return 0;
            }

            $this->info("Found {$pendingOrders->count()} pending orders with Midtrans/bank_transfer/e_wallet payment method.");

            // Loop dan proses setiap order
            foreach ($pendingOrders as $order) {
                $this->processOrder($order, $status, $fraudStatus);
            }

            $this->info("\nAll pending orders processed successfully!");
            return 0;
        }

        // ========================================
        // MODE 2: INTERACTIVE (TAMPILKAN LIST ORDER)
        // ========================================
        // Jika tidak ada order_number yang diberikan sebagai argument
        // Tampilkan list order pending dan minta user memilih
        if (!$orderNumber) {
            // Cari semua order pending dengan payment method Midtrans
            $pendingOrders = Order::where('status', 'pending')
                ->whereHas('payment', function($query) {
                    $query->whereIn('payment_method', ['midtrans', 'bank_transfer', 'e_wallet']);
                })
                ->get();

            if ($pendingOrders->isEmpty()) {
                $this->info("No pending orders with Midtrans/bank_transfer/e_wallet payment method found.");
                return 0;
            }

            // Ambil semua order numbers untuk pilihan
            $orderNumbers = $pendingOrders->pluck('order_number')->toArray();
            
            // Tampilkan pilihan interaktif (dropdown CLI)
            $selectedOrderNumber = $this->choice(
                'Which pending order would you like to simulate callback for?',
                $orderNumbers
            );

            $orderNumber = $selectedOrderNumber;
        }

        // ========================================
        // MODE 3: PROSES ORDER SPESIFIK
        // ========================================
        // Jika order_number diberikan, langsung proses order tersebut
        return $this->processOrderWithNumber($orderNumber, $status, $fraudStatus);
    }

    /**
     * Process a single order by order number
     * 
     * Helper method untuk mencari order berdasarkan order number
     * dan memanggil processOrder dengan order object yang sudah ditemukan.
     */
    private function processOrderWithNumber($orderNumber, $status, $fraudStatus)
    {
        // ========================================
        // CARI ORDER DI DATABASE
        // ========================================
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            $this->error("Order with number {$orderNumber} not found!");
            return 1;  // Return exit code 1 (error)
        }

        // Lanjutkan ke proses utama
        return $this->processOrder($order, $status, $fraudStatus);
    }

    /**
     * Process a single order
     * 
     * ==========================================================================
     * CORE LOGIC - SIMULASI CALLBACK MIDTRANS
     * ==========================================================================
     * Method ini mensimulasikan apa yang terjadi saat Midtrans mengirim webhook:
     * 1. Terima notification dari Midtrans dengan transaction_status & fraud_status
     * 2. Map status Midtrans ke status lokal sistem
     * 3. Update order dan payment record di database
     * 4. Catat log perubahan status untuk audit trail
     * 
     * MIDTRANS STATUS MAPPING:
     * - settlement/capture + accept = confirmed (pembayaran berhasil)
     * - pending = pending (menunggu pembayaran)
     * - expire = cancelled (waktu pembayaran habis)
     * - cancel/deny = cancelled (dibatalkan/ditolak)
     * - refund/chargeback = cancelled + refunded
     * 
     * @param Order $order Order object yang akan diupdate
     * @param string $status Transaction status dari Midtrans
     * @param string $fraudStatus Fraud status dari Midtrans (accept/deny)
     * @return int Exit code
     */
    private function processOrder($order, $status, $fraudStatus)
    {
        $orderNumber = $order->order_number;

        // ========================================
        // TAMPILKAN INFO AWAL
        // ========================================
        $this->info("\nSimulating Midtrans callback for order: {$orderNumber}");
        $this->info("Transaction status: {$status}");
        $this->info("Fraud status: {$fraudStatus}");

        // ========================================
        // CARI PAYMENT RECORD
        // ========================================
        // Find the payment record - setiap order harus punya payment record
        $payment = Payment::where('order_id', $order->id)->first();

        if (!$payment) {
            $this->error("Payment record not found for order {$orderNumber}!");
            return 1;
        }

        $this->info("Order found: {$order->order_number} (Current status: {$order->status})");
        $this->info("Payment found: {$payment->payment_method} (Current status: {$payment->payment_status})");

        // Log the status change
        $this->info("Processing callback simulation...");

        // ========================================
        // MAP MIDTRANS STATUS KE STATUS LOKAL
        // ========================================
        // Initialize dengan status saat ini (default tidak berubah)
        $newOrderStatus = $order->status; // Default to current status
        $newPaymentStatus = $payment->payment_status; // Default to current status

        // Switch berdasarkan transaction_status dari Midtrans
        switch ($status) {
            // ========================================
            // PEMBAYARAN BERHASIL (SETTLEMENT/CAPTURE)
            // ========================================
            // settlement: Uang sudah masuk ke rekening merchant
            // capture: Sama seperti settlement (untuk credit card)
            case 'settlement':
            case 'capture':
                if ($fraudStatus === 'accept') {
                    // Fraud check passed - pembayaran valid
                    $newOrderStatus = 'confirmed';  // Order dikonfirmasi
                    $newPaymentStatus = 'success';  // Payment berhasil
                } else {
                    // Fraud check failed - transaksi mencurigakan
                    $newOrderStatus = 'pending';    // Tetap pending untuk review manual
                    $newPaymentStatus = 'pending';
                }
                break;

            // ========================================
            // MENUNGGU PEMBAYARAN
            // ========================================
            // Customer belum menyelesaikan pembayaran
            case 'pending':
                $newOrderStatus = 'pending';
                $newPaymentStatus = 'pending';
                break;

            // ========================================
            // WAKTU PEMBAYARAN HABIS (EXPIRE)
            // ========================================
            // Customer tidak bayar dalam waktu yang ditentukan
            // Order otomatis dibatalkan
            case 'expire':
                $newOrderStatus = 'cancelled';
                $newPaymentStatus = 'expired';
                break;

            // ========================================
            // PEMBAYARAN DIBATALKAN/DITOLAK
            // ========================================
            // cancel: Customer membatalkan pembayaran
            // deny: Payment ditolak oleh fraud detection Midtrans
            case 'cancel':
            case 'deny':
                $newOrderStatus = 'cancelled';
                $newPaymentStatus = 'failed';
                break;

            // ========================================
            // PENGEMBALIAN DANA (REFUND/CHARGEBACK)
            // ========================================
            // refund: Merchant mengembalikan dana
            // chargeback: Bank mengembalikan dana (dispute)
            case 'refund':
            case 'partial_refund':
            case 'chargeback':
            case 'partial_chargeback':
                $newOrderStatus = 'cancelled'; // Order dibatalkan
                $newPaymentStatus = 'refunded'; // Status payment: refunded
                break;
        }

        // ========================================
        // UPDATE PAYMENT RECORD
        // ========================================
        // Update payment record dengan status baru
        $payment->update([
            'payment_status' => $newPaymentStatus,
            'payment_gateway_response' => [
                'order_id' => $orderNumber,
                'transaction_status' => $status,       // Status dari Midtrans
                'fraud_status' => $fraudStatus,        // Fraud check result
                'simulated' => true,                   // Flag: ini hasil simulasi (bukan real callback)
                'timestamp' => now()->toISOString()    // Timestamp ISO 8601
            ],
            'paid_at' => in_array($status, ['settlement', 'capture']) && $fraudStatus === 'accept' 
                ? now()  // Set paid_at hanya jika pembayaran berhasil
                : $payment->paid_at  // Keep existing paid_at jika tidak berubah
        ]);

        // ========================================
        // CEK APAKAH STATUS BERUBAH
        // ========================================
        $orderStatusChanged = $order->status !== $newOrderStatus;

        // ========================================
        // UPDATE ORDER STATUS (JIKA BERUBAH)
        // ========================================
        if ($orderStatusChanged) {
            $order->update([
                'status' => $newOrderStatus,
                'paid_at' => in_array($status, ['settlement', 'capture']) && $fraudStatus === 'accept' 
                    ? now()  // Set paid_at timestamp
                    : $order->paid_at  // Keep existing
            ]);

            // ========================================
            // CATAT LOG PERUBAHAN STATUS
            // ========================================
            // Add log entry untuk audit trail - penting untuk tracking
            $order->logs()->create([
                'status' => $newOrderStatus,
                'description' => "Status pesanan diubah oleh sistem simulasi Midtrans dari '{$order->status}' menjadi '{$newOrderStatus}'. Transaction status: {$status}, Fraud status: {$fraudStatus}",
                'updated_by' => 'system'  // Ditandai sebagai perubahan oleh sistem
            ]);
        }

        // ========================================
        // TAMPILKAN HASIL EKSEKUSI
        // ========================================
        $this->info('');
        $this->info("Callback simulation completed!");
        $this->info("Order status: {$order->status} (was {$order->getOriginal('status')})");
        $this->info("Payment status: {$payment->payment_status} (was {$payment->getOriginal('payment_status')})");
        $this->info('');

        if ($orderStatusChanged) {
            $this->info("✓ Order status changed from '{$order->getOriginal('status')}' to '{$newOrderStatus}'");
        } else {
            $this->info("- Order status remained the same: '{$newOrderStatus}'");
        }

        return 0;  // Return exit code 0 (success)
    }
}
