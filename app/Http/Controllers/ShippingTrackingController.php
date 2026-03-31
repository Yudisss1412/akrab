<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// ========================================================================
// SHIPPING TRACKING CONTROLLER - LACAK PENGIRIMAN PAKET
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani fitur tracking pengiriman untuk customer
// - Customer bisa monitor progress pengiriman dari seller ke rumah mereka
// - Seperti lacak paket JNE/J&T tapi berbasis status order (bukan API kurir)
//
// FITUR UTAMA:
// 1. Show Tracking - Tampilkan halaman tracking pengiriman
// 2. Timeline Pengiriman - Progress dari order sampai diterima
// 3. Status-Based Timeline - Timeline disesuaikan status order
// 4. Location Info - Info lokasi setiap tahap pengiriman
//
// TIMELINE PENGIRIMAN:
// 1. Pesanan Diterima → Seller terima order dari customer
// 2. Dikemas → Seller packing barang
// 3. Dikirim dari Kota Asal → Paket diserahkan ke kurir
// 4. Dalam Perjalanan → Paket di jalan ke kota tujuan
// 5. Sampai di Kota Tujuan → Paket tiba di kota customer
// 6. Diantar ke Alamat → Kurir antar ke rumah customer
// 7. Diterima → Customer terima paket (selesai)
//
// CATATAN PENTING:
// - Timeline saat ini ESTIMASI berdasarkan waktu order
// - Bukan real-time tracking dari API kurir (JNE/J&T)
// - Status update manual oleh seller
// - Untuk production: dapat integrasi API kurir untuk real tracking
//
// ARSITEKTUR:
// - Support upgrade ke real tracking (tinggal tambah API integration)
// - Flexible design untuk berbagai metode pengiriman
// - Status-based: mengikuti status order di database
// ========================================================================

class ShippingTrackingController extends Controller
{
    /**
     * Menampilkan halaman tracking pengiriman untuk order
     * 
     * ==========================================================================
     * FITUR: SHOW SHIPPING TRACKING - LACAK PAKET
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini tampilkan halaman tracking untuk customer
     * - Customer bisa monitor progress pengiriman paket mereka
     * - Timeline disesuaikan dengan status order saat ini
     * 
     * TIMELINE PENGIRIMAN (7 TAHAP):
     * 1. Pesanan Diterima (pending) → Seller terima order
     * 2. Dikemas (confirmed) → Seller packing barang
     * 3. Dikirim dari Kota Asal (confirmed) → Paket dikirim
     * 4. Dalam Perjalanan (shipped) → Paket di jalan
     * 5. Sampai di Kota Tujuan (shipped) → Paket tiba di kota
     * 6. Diantar ke Alamat (shipped) → Kurir antar
     * 7. Diterima (delivered) → Customer terima paket
     * 
     * STATUS MAPPING:
     * - pending     → Max tahap 1 (Pesanan Diterima)
     * - confirmed   → Max tahap 3 (Dikemas & Dikirim)
     * - shipped     → Max tahap 6 (Dalam Perjalanan & Diantar)
     * - delivered   → Max tahap 7 (Diterima - Selesai)
     * 
     * CATATAN:
     * - Timeline adalah ESTIMASI berdasarkan waktu order
     * - Bukan real-time tracking dari kurir
     * - Timestamp di-generate berdasarkan created_at order
     * - Lokasi berdasarkan shipping_address
     * 
     * @param string $order Order number yang akan dilacak
     * @return \Illuminate\View\View View tracking pengiriman
     */
    public function show($order)
    {
        try {
            // ========================================
            // STEP 1: LOAD ORDER & SHIPPING ADDRESS
            // ========================================
            // Ambil order berdasarkan order number
            // Load relasi shipping_address untuk info lokasi
            $orderData = Order::with(['shipping_address'])
                ->where('order_number', $order)
                ->firstOrFail();

            // ========================================
            // STEP 2: BUAT BASE TIMELINE (7 TAHAP)
            // ========================================
            // Base timeline untuk shipping tracking
            // Ini adalah timeline lengkap dari order sampai diterima
            $baseTimeline = [
                [
                    'status' => 'Pesanan Diterima',
                    'description' => 'Pesanan Anda telah diterima oleh penjual',
                    'timestamp' => $orderData->created_at->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->city ?? 'Kota Asal',
                    'status_code' => 'accepted'
                ],
                [
                    'status' => 'Dikemas',
                    'description' => 'Pesanan sedang dikemas oleh penjual',
                    'timestamp' => $orderData->created_at->addHours(2)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->city ?? 'Kota Asal',
                    'status_code' => 'packed'
                ],
                [
                    'status' => 'Dikirim dari Kota Asal',
                    'description' => 'Pesanan telah dikirim dari kota asal',
                    'timestamp' => $orderData->created_at->addHours(4)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->city ?? 'Kota Asal',
                    'status_code' => 'shipped_origin'
                ],
                [
                    'status' => 'Dalam Perjalanan',
                    'description' => 'Pesanan sedang dalam perjalanan menuju kota tujuan',
                    'timestamp' => $orderData->created_at->addHours(24)->format('Y-m-d H:i:s'),
                    'location' => 'Dalam perjalanan',
                    'status_code' => 'in_transit'
                ],
                [
                    'status' => 'Sampai di Kota Tujuan',
                    'description' => 'Pesanan telah tiba di kota tujuan',
                    'timestamp' => $orderData->created_at->addHours(48)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->city ?? 'Kota Tujuan',
                    'status_code' => 'arrived_destination'
                ],
                [
                    'status' => 'Diantar ke Alamat',
                    'description' => 'Pesanan sedang diantar ke alamat tujuan',
                    'timestamp' => $orderData->created_at->addHours(72)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->full_address ?? 'Alamat Tujuan',
                    'status_code' => 'out_for_delivery'
                ],
                [
                    'status' => 'Diterima',
                    'description' => 'Pesanan telah diterima oleh penerima',
                    'timestamp' => $orderData->created_at->addHours(73)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->full_address ?? 'Alamat Tujuan',
                    'status_code' => 'delivered'
                ]
            ];

            // ========================================
            // STEP 3: ADJUST TIMELINE BERDASARKAN STATUS
            // ========================================
            // Adjust timeline berdasarkan status order saat ini
            // Hanya tampilkan tahap yang sudah dilalui
            $adjustedTimeline = [];
            $maxIndexToShow = 0;

            // ========================================
            // STEP 4: TENTukan PROGRESS BERDASARKAN STATUS
            // ========================================
            // Determine how far the order has progressed based on its status
            // Mapping status order ke tahap timeline maksimal
            switch ($orderData->status) {
                case 'pending':
                    // Order baru dibuat, belum dikonfirmasi
                    $maxIndexToShow = 0;  // Hanya tahap 1: Pesanan Diterima
                    break;
                case 'confirmed':
                    // Order dikonfirmasi, seller siap kirim
                    $maxIndexToShow = 2;  // Sampai tahap 3: Dikirim dari Kota Asal
                    break;
                case 'shipped':
                    // Order sudah dikirim, dalam perjalanan
                    $maxIndexToShow = 5;  // Sampai tahap 6: Diantar ke Alamat
                    break;
                case 'delivered':
                    // Order sudah diterima customer
                    $maxIndexToShow = 6;  // Sampai tahap 7: Diterima (selesai)
                    break;
                default:
                    // Status tidak dikenali, tampilkan tahap 1 saja
                    $maxIndexToShow = 0;
            }

            // ========================================
            // STEP 5: BUILD ADJUSTED TIMELINE
            // ========================================
            // Show only steps up to the current status
            // Loop dari tahap 1 sampai tahap maksimal berdasarkan status
            for ($i = 0; $i <= $maxIndexToShow && $i < count($baseTimeline); $i++) {
                $item = $baseTimeline[$i];
                
                // Adjust timestamp to be relative to order creation time
                // Setiap tahap ditambah 5 jam dari tahap sebelumnya (estimasi)
                $item['timestamp'] = $orderData->created_at->addHours($i * 5)->format('Y-m-d H:i:s');
                
                $adjustedTimeline[] = $item;
            }

            // ========================================
            // STEP 6: RETURN VIEW
            // ========================================
            // Return view tracking dengan data order & timeline
            return view('customer.transaksi.shipping_track', compact('orderData', 'adjustedTimeline'));
        } catch (\Exception $e) {
            // ========================================
            // STEP 7: HANDLE ERROR
            // ========================================
            // Log error untuk debugging
            Log::error('Error in shipping tracking: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());
            
            // Tampilkan error 404 jika order tidak ditemukan
            abort(404, 'Pesanan tidak ditemukan');
        }
    }
}