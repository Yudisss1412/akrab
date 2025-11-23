<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Akan null jika belum login

        // Ambil produk populer - ambil dulu produk dan hitung rating masing-masing
        $productsQuery = Product::where('status', 'active')
            ->with(['seller', 'category', 'images', 'reviews']);

        // Tambahkan order by total_sales jika field ada di tabel
        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'total_sales')) {
            $productsQuery = $productsQuery->orderBy('total_sales', 'desc');
        } else {
            // Fallback: urutkan berdasarkan jumlah order items terbanyak
            $productsQuery = $productsQuery
                ->withCount('orderItems')
                ->orderBy('order_items_count', 'desc');
        }

        // Ambil produk dulu (ambil lebih banyak untuk akurasi rating)
        $products = $productsQuery->limit(20)->get();

        // Urutkan berdasarkan rating rata-rata (dihitung dari reviews)
        $popularProducts = $products->sortByDesc(function ($product) {
            return $product->average_rating;
        })->take(8)->values();

        // Ambil beberapa kategori untuk ditampilkan
        $categories = Category::limit(6)->get();

        return view('customer.cust_welcome', compact('user', 'popularProducts', 'categories'));
    }
}