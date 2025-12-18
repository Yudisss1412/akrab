<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;

class DashboardController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        if (!$user || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }
        
        // Ambil data penjual
        $seller = Seller::where('user_id', $user->id)->firstOrFail();
        
        // Hitung statistik
        $stats = [
            'this_month_revenue' => $this->getRevenueThisMonth($seller->id),
            'new_orders' => $this->getNewOrdersCount($seller->id),
            'viewed_products' => $this->getViewedProductsCount($seller->id),
            'store_rating' => $this->getStoreRating($seller->id),
            'pending_orders' => $this->getPendingOrdersCount($seller->id),
            'urgent_complaints' => $this->getUrgentComplaintsCount($seller->id),
        ];

        return view('penjual.dashboard_penjual', compact('user', 'seller', 'stats'));
    }
    
    private function getRevenueThisMonth($sellerId)
    {
        // Ambil produk milik penjual
        $productIds = Product::where('seller_id', $sellerId)->pluck('id');

        // Hitung total pendapatan dari order items yang produknya milik penjual
        $revenue = Order::whereHas('items', function($query) use ($productIds) {
                $query->whereIn('product_id', $productIds);
            })
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        return $revenue;
    }
    
    private function getNewOrdersCount($sellerId)
    {
        // Ambil produk milik penjual
        $productIds = Product::where('seller_id', $sellerId)->pluck('id');

        // Hitung jumlah order baru (belum diproses) yang produknya milik penjual
        $newOrders = Order::whereHas('items', function($query) use ($productIds) {
                $query->whereIn('product_id', $productIds);
            })
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        return $newOrders;
    }
    
    private function getViewedProductsCount($sellerId)
    {
        // Ambil produk milik penjual dan hitung total view
        $viewCount = Product::where('seller_id', $sellerId)
            ->sum('view_count');
            
        return $viewCount;
    }
    
    private function getStoreRating($sellerId)
    {
        // Ambil produk milik penjual
        $productIds = Product::where('seller_id', $sellerId)->pluck('id');

        // Hitung rata-rata rating dari ulasan produk
        $averageRating = \DB::table('reviews')
            ->whereIn('product_id', $productIds)
            ->avg('rating');

        return $averageRating ? round($averageRating, 1) : 0;
    }

    private function getPendingOrdersCount($sellerId)
    {
        // Ambil produk milik penjual
        $productIds = Product::where('seller_id', $sellerId)->pluck('id');

        \Log::info("getPendingOrdersCount called with sellerId: " . $sellerId . ", productIds count: " . $productIds->count());

        // Hitung jumlah order pending yang produknya milik penjual ini
        // Berdasarkan mapping status di SellerOrderController, status database adalah:
        // pending (mapping dari pending_payment), confirmed (mapping dari processing)
        $pendingOrdersCount = Order::whereHas('items', function($query) use ($productIds) {
                $query->whereIn('product_id', $productIds);
            })
            ->whereIn('status', ['pending', 'confirmed']) // Hanya pending dan confirmed sebagai pesanan yang perlu diproses
            ->count();

        \Log::info("getPendingOrdersCount returning: " . $pendingOrdersCount);

        return $pendingOrdersCount;
    }

    private function getUrgentComplaintsCount($sellerId)
    {
        // Hitung jumlah review dengan rating 2 ke bawah (komplain/retur)
        // yang terkait dengan produk milik penjual
        \Log::info("getUrgentComplaintsCount called with sellerId: " . $sellerId);

        $urgentComplaintsCount = \App\Models\Review::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })
            ->where('rating', '<=', 2)
            ->count();

        \Log::info("getUrgentComplaintsCount returning: " . $urgentComplaintsCount);

        return $urgentComplaintsCount;
    }
}