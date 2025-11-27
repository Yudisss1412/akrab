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
        // Gunakan whereHas untuk memastikan produk memiliki relasi seller dan category
        $productsQuery = Product::where('status', 'active')
            ->whereHas('seller')  // Hanya produk dengan seller yang valid
            ->whereHas('category') // Hanya produk dengan category yang valid
            ->with([
                'seller:id,user_id,store_name',
                'category:id,name',
                'images:id,product_id,image_path',
                'reviews:id,product_id,user_id,rating,status',
                'approvedReviews:id,product_id,user_id,rating,status'
            ]);

        // Tambahkan order by total_sales jika field ada di tabel
        if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'total_sales')) {
            $productsQuery = $productsQuery->orderBy('total_sales', 'desc');
        } else {
            // Fallback: urutkan berdasarkan jumlah order items terbanyak
            $productsQuery = $productsQuery
                ->withCount('orderItems')
                ->withAvg('approvedReviews', 'rating') // Load average rating from approved reviews
                ->orderBy('order_items_count', 'desc');
        }

        // Ambil produk-produk (ambil lebih banyak untuk akurasi rating)
        $products = $productsQuery->limit(20)->get();

        // Buat array produk dengan data yang dijamin aman
        $safeProducts = collect([]);
        foreach ($products as $product) {
            // Cek apakah produk memiliki relasi yang valid
            if ($product->seller && $product->category) {
                $safeProducts->push($product); // Tidak perlu cloning, cukup filter
            }
        }

        // Urutkan berdasarkan rating rata-rata (dihitung dari ulasan yang disetujui)
        $popularProducts = $safeProducts->sortByDesc(function ($product) {
            // Akses relasi yang sudah di-loaded
            if ($product->relationLoaded('approvedReviews')) {
                $approvedReviews = $product->approvedReviews;
            } else {
                $approvedReviews = $product->approvedReviews()->get();
            }

            if ($approvedReviews && $approvedReviews->count() > 0) {
                return $approvedReviews->avg('rating');
            }
            return 0;
        })->take(8)->values();

        // Ambil beberapa kategori untuk ditampilkan
        $categories = Category::limit(6)->get();

        return view('customer.cust_welcome', compact('user', 'popularProducts', 'categories'));
    }
}