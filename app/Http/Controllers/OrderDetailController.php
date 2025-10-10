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
    
}