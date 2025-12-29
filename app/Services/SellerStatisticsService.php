<?php

namespace App\Services;

use App\Models\SellerTransaction;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

class SellerStatisticsService
{
    /**
     * Menghitung statistik penjualan untuk penjual
     */
    public function calculateSellerStats($sellerId)
    {
        // Hitung total penjualan (pemasukan dari penjualan yang selesai)
        $totalSales = SellerTransaction::forSeller($sellerId)
            ->sales()
            ->completed()
            ->sum('amount');

        // Hitung total transaksi (jumlah penjualan yang selesai)
        $totalTransactions = SellerTransaction::forSeller($sellerId)
            ->sales()
            ->completed()
            ->count();

        // Hitung pendapatan bulan ini
        $monthlyRevenue = SellerTransaction::forSeller($sellerId)
            ->sales()
            ->completed()
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        // Hitung rata-rata per transaksi
        $avgPerTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        return [
            'totalSales' => $totalSales,
            'totalTransactions' => $totalTransactions,
            'monthlyRevenue' => $monthlyRevenue,
            'avgPerTransaction' => $avgPerTransaction
        ];
    }

    /**
     * Menghitung saldo penjual
     */
    public function calculateSellerBalance($sellerId)
    {
        $totalIncome = SellerTransaction::forSeller($sellerId)
            ->sales()
            ->completed()
            ->sum('amount');

        $totalWithdrawals = SellerTransaction::forSeller($sellerId)
            ->withdrawals()
            ->completed()
            ->sum('amount');

        $totalCommissions = SellerTransaction::forSeller($sellerId)
            ->commissions()
            ->completed()
            ->sum('amount');

        $balance = $totalIncome - $totalCommissions - $totalWithdrawals;

        return max(0, $balance);
    }

    /**
     * Menghitung jumlah pesanan per status
     */
    public function calculateOrderStatusCounts($sellerId)
    {
        $statusCounts = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $sellerId)
            ->selectRaw('orders.status, count(*) as count')
            ->groupBy('orders.status')
            ->pluck('count', 'orders.status');

        // Mapping status database ke status aplikasi
        $statusMapping = [
            'pending' => 'pending_payment',    // Menunggu Pembayaran
            'confirmed' => 'processing',       // Diproses
            'shipped' => 'shipping',           // Dikirim
            'delivered' => 'completed',        // Selesai
            'cancelled' => 'cancelled'         // Dibatalkan
        ];

        // Terapkan mapping dan hitung jumlah untuk setiap status aplikasi
        $allStatus = ['pending_payment', 'processing', 'shipping', 'completed', 'cancelled'];
        $statusData = [];
        foreach ($allStatus as $appStatus) {
            $statusData[$appStatus] = 0; // Inisialisasi dengan 0

            // Tambahkan jumlah dari setiap status database yang dipetakan ke status aplikasi ini
            foreach ($statusCounts as $dbStatus => $count) {
                if ($statusMapping[$dbStatus] === $appStatus) {
                    $statusData[$appStatus] += $count;
                }
            }
        }

        return $statusData;
    }
}