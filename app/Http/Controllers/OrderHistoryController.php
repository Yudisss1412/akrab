<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ========================================================================
// ORDER HISTORY CONTROLLER - RIWAYAT PEMBELIAN CUSTOMER
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani "Riwayat Pembelian" dari perspektif BUYER
// - Customer bisa lihat semua order yang pernah mereka buat
// - Seperti "tumpukan struk belanja" yang disimpan di dompet
//
// FITUR UTAMA:
// 1. Order List - Daftar semua pesanan customer dengan filter
// 2. Order Detail - Lihat detail lengkap setiap pesanan
// 3. Status Tracking - Lihat progress pesanan (pending → delivered)
// 4. Reorder - Beli lagi produk yang sama
// 5. Review - Kasih rating & ulasan setelah terima produk
// 6. Complaint - Buat tiket bantuan jika ada masalah
//
// FLOW CUSTOMER:
// 1. Customer belanja → Checkout → Bayar
// 2. Order masuk ke riwayat pembelian
// 3. Customer bisa lihat riwayat kapan saja
// 4. Lacak paket, kasih review, beli lagi
//
// PERBEDAAN DENGAN MONITORING TRANSAKSI:
// - Order History = Buyer perspective (yang saya beli)
// - Seller Order Monitoring = Seller perspective (yang saya jual)
// ========================================================================

/**
 * OrderHistoryController - Riwayat Pesanan
 *
 * Controller ini menangani tampilan riwayat pesanan untuk customer:
 * - Menampilkan daftar semua pesanan user yang login
 * - Format data pesanan untuk tampilan yang user-friendly
 * - Support filter & pencarian order
 *
 * Hanya user yang sudah login yang dapat mengakses fitur ini.
 * 
 * @package App\Http\Controllers
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class OrderHistoryController extends Controller
{
    /**
     * Constructor - require authentication untuk semua method
     * 
     * Middleware 'auth' memastikan hanya user yang login yang bisa akses.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan halaman riwayat pesanan
     * 
     * ==========================================================================
     * FITUR: RIWAYAT PEMBELIAN CUSTOMER (STRUK BELANJA DIGITAL)
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menampilkan semua order yang pernah dibuat customer
     * - Data diformat agar mudah ditampilkan di view
     * - Urutkan dari yang terbaru (seperti struk paling atas = paling baru)
     * 
     * ANALOGI:
     * Seperti buka dompet dan lihat tumpukan struk belanja:
     * - Struk paling atas = belanja terbaru
     * - Struk di bawah = belanja lama
     * - Setiap struk berisi: apa yang dibeli, total, tanggal, status
     *
     * @return \Illuminate\View\View View riwayat pesanan
     */
    public function index()
    {
        // ========================================
        // STEP 1: QUERY ORDER MILIK USER LOGIN
        // ========================================
        // Ambil semua pesanan milik user yang login
        // Load relasi untuk menampilkan info lengkap
        $orders = Order::with(['items.product', 'shipping_address', 'logs'])
            ->where('user_id', Auth::id())  // Filter: hanya order milik user ini
            ->orderBy('created_at', 'desc')  // Urutkan dari yang terbaru (seperti struk di dompet)
            ->get();

        // ========================================
        // STEP 2: FORMAT DATA UNTUK VIEW
        // ========================================
        // Format data pesanan agar mudah ditampilkan di view
        // Seperti "merapikan struk" agar mudah dibaca
        $formattedOrders = $orders->map(function ($order) {
            return [
                // ========================================
                // INFO DASAR ORDER (SEPERTI HEADER STRUK)
                // ========================================
                'id' => $order->id,
                'order_number' => $order->order_number,  // Contoh: ORD-20240301-00001
                'status' => $order->status,  // pending, confirmed, shipped, delivered
                'total_amount' => $order->total_amount,  // Total yang dibayar
                'created_at' => $order->created_at->format('d M Y'),  // Format: 01 Jan 2024
                
                // ========================================
                // PRODUK YANG DIBELI (ISI KERANJANG)
                // ========================================
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product->name,  // Nama produk
                        'quantity' => $item->quantity,  // Jumlah beli
                        'price' => $item->price,  // Harga per unit
                        'image' => $item->product->main_image 
                            ? asset('storage/' . $item->product->main_image)  // Gambar produk
                            : asset('src/placeholder_produk.png'),  // Placeholder jika tidak ada gambar
                    ];
                }),
                
                // ========================================
                // ALAMAT PENGIRIMAN (TUJUAN PAKET)
                // ========================================
                'shipping_address' => $order->shipping_address ? [
                    'name' => $order->shipping_address->name,  // Nama penerima
                    'phone' => $order->shipping_address->phone,  // Nomor HP penerima
                    'address' => $order->shipping_address->address,  // Alamat lengkap
                    'city' => $order->shipping_address->city,  // Kota
                    'province' => $order->shipping_address->province,  // Provinsi
                    'postal_code' => $order->shipping_address->postal_code,  // Kode pos
                ] : null,
                
                // ========================================
                // LOG STATUS TERAKHIR (TRACKING PROGRESS)
                // ========================================
                'latest_log' => $order->logs->sortByDesc('created_at')->first(),  // Log status paling terbaru
            ];
        });

        // ========================================
        // STEP 3: RETURN VIEW DENGAN DATA
        // ========================================
        // Return view dengan data yang sudah diformat
        // View akan menampilkan seperti "daftar struk belanja"
        return view('customer.riwayat_pesanan.index', compact('formattedOrders'));
    }
}