<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

/**
 * OrderDetailController - Detail Pesanan
 * 
 * Controller ini menangani operasi terkait detail pesanan:
 * - Menampilkan detail pesanan (show)
 * - Menampilkan invoice pesanan (invoice)
 * - Melaporkan pesanan yang belum diterima meski status delivered (reportUndelivered)
 * 
 * User dapat melihat detail pesanan berdasarkan order_number.
 */
class OrderDetailController extends Controller
{
    /**
     * Menampilkan detail pesanan
     * 
     * Menampilkan informasi lengkap pesanan termasuk:
     * - Data pesanan (status, total, tanggal, dll)
     * - Item-item yang dipesan
     * - Alamat pengiriman
     * - Log/history status pesanan
     * 
     * @param string $order Order number (bukan ID)
     * @return \Illuminate\View\View View detail pesanan
     */
    public function show($order)
    {
        // Cari pesanan berdasarkan order_number (bukan ID)
        $order = Order::where('order_number', $order)->firstOrFail();

        // Load relasi yang diperlukan untuk menampilkan detail lengkap
        $order->load(['items.product', 'items.variant', 'user', 'shipping_address', 'logs']);

        return view('orders.show', compact('order'));
    }

    /**
     * Menampilkan invoice pesanan
     * 
     * Invoice adalah ringkasan pesanan yang dapat di-download/print
     * untuk keperluan pembayaran atau arsip pembeli.
     * 
     * @param string $order Order number (bukan ID)
     * @return \Illuminate\View\View View invoice
     */
    public function invoice($order)
    {
        // Cari pesanan berdasarkan order_number
        $order = Order::where('order_number', $order)->firstOrFail();

        // Load relasi yang diperlukan untuk invoice
        $order->load(['items.product', 'items.variant', 'user', 'shipping_address']);

        return view('customer.transaksi.invoice', compact('order'));
    }

    /**
     * Melaporkan pesanan yang ditandai delivered tapi belum diterima
     * 
     * Fitur ini memungkinkan customer melaporkan jika status pesanan
     * sudah 'delivered' tapi barang belum diterima secara fisik.
     * Status akan dikembalikan ke 'shipped' untuk investigasi.
     * 
     * @param string $order Order number
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function reportUndelivered($order)
    {
        try {
            // Cari pesanan berdasarkan order_number
            $orderData = Order::where('order_number', $order)->firstOrFail();

            // Cek apakah user yang login adalah pemilik pesanan
            if ($orderData->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melaporkan pesanan ini'
                ], 403);
            }

            // Hanya boleh report jika status saat ini adalah 'delivered'
            if ($orderData->status !== 'delivered') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini belum ditandai sebagai diterima'
                ], 400);
            }

            // Simpan status asli sebelum diubah (untuk logging)
            $originalStatus = $orderData->status;
            
            // Kembalikan status ke 'shipped' untuk investigasi
            $orderData->update(['status' => 'shipped']);

            // Catat log perubahan status
            $orderData->logs()->create([
                'status' => 'shipped',
                'description' => 'Status pesanan dikembalikan ke shipped karena pelapor mengatakan belum menerima barang',
                'updated_by' => auth()->user()->name ?? 'customer',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diproses. Status pesanan telah diperbarui.',
                'order' => $orderData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in reportUndelivered: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses laporan'
            ], 500);
        }
    }
}