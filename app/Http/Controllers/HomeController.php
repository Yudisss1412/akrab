<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman landing page dengan 6 produk terlaris
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil 6 produk terlaris berdasarkan total quantity terjual dari order_items
        $products = Product::with(['seller', 'images'])
            ->select('products.*')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->where('products.status', 'active')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'desc')
            ->limit(6)
            ->get();

        return view('welcome', compact('products'));
    }
}
