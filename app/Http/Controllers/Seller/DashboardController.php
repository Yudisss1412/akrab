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
}