<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    /**
     * Display the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($order)
    {
        // Find order by order number instead of ID
        $order = Order::where('order_number', $order)->firstOrFail();
        
        // Load the order with its relationships
        $order->load(['items.product', 'items.variant', 'user', 'shipping_address', 'logs']);
        
        return view('orders.show', compact('order'));
    }
    
    /**
     * Display the invoice for the specified order.
     *
     * @param  string  $order
     * @return \Illuminate\Http\Response
     */
    public function invoice($order)
    {
        // Find order by order number instead of ID
        $order = Order::where('order_number', $order)->firstOrFail();
        
        // Load the order with its relationships
        $order->load(['items.product', 'items.variant', 'user', 'shipping_address']);
        
        return view('customer.transaksi.invoice', compact('order'));
    }

    /**
     * Report that an order marked as delivered was not actually received
     */
    public function reportUndelivered($order)
    {
        try {
            // Find the order by order number
            $orderData = Order::where('order_number', $order)->firstOrFail();

            // Check if user is authorized to report this order
            if ($orderData->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk melaporkan pesanan ini'
                ], 403);
            }

            // Only allow reporting if the status is 'delivered'
            if ($orderData->status !== 'delivered') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini belum ditandai sebagai diterima'
                ], 400);
            }

            // Update status back to 'shipped' to indicate it needs to be checked
            $originalStatus = $orderData->status;
            $orderData->update(['status' => 'shipped']);

            // Add log entry for the status change
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