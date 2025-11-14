<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get all orders for the authenticated user
        $orders = Order::with(['items.product', 'shipping_address', 'logs'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Format the orders data for the view
        $formattedOrders = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at->format('d M Y'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'image' => $item->product->main_image ? asset('storage/' . $item->product->main_image) : asset('src/placeholder_produk.png'),
                    ];
                }),
                'shipping_address' => $order->shipping_address ? [
                    'name' => $order->shipping_address->name,
                    'phone' => $order->shipping_address->phone,
                    'address' => $order->shipping_address->address,
                    'city' => $order->shipping_address->city,
                    'province' => $order->shipping_address->province,
                    'postal_code' => $order->shipping_address->postal_code,
                ] : null,
                'latest_log' => $order->logs->sortByDesc('created_at')->first(),
            ];
        });

        return view('customer.riwayat_pesanan.index', compact('formattedOrders'));
    }
}