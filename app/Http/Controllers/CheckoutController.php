<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\ShippingAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->middleware('auth');
    }

    /**
     * Menampilkan halaman checkout
     */
    public function index()
    {
        // Ambil item keranjang
        $cartItems = $this->cartService->getCartItems();
        
        // Hitung subtotal
        $subTotal = $this->cartService->getSubtotal();
        
        // Biaya pengiriman default
        $shippingCost = 15000; // Harga default untuk REGULER
        $total = $subTotal + $shippingCost;
        
        // Ambil alamat pengguna (misalnya dari profil pengguna)
        $user = Auth::user();
        
        // Data untuk checkout page
        $checkoutData = [
            'cartItems' => $cartItems,
            'subTotal' => $subTotal,
            'shippingCost' => $shippingCost,
            'total' => $total,
            'user' => $user
        ];
        
        return view('customer.transaksi.checkout', $checkoutData);
    }

    /**
     * Proses checkout dan buat pesanan
     */
    public function process(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'full_address' => 'required|string|max:500',
            'shipping_method' => 'required|in:reguler,express',
        ]);

        DB::beginTransaction();

        try {
            // Ambil item keranjang dari user
            $cartItems = $this->cartService->getCartItems();
            if ($cartItems->isEmpty()) {
                return redirect()->back()->withErrors(['cart' => 'Keranjang Anda kosong.']);
            }

            // Hitung subtotal
            $subTotal = $this->cartService->getSubtotal();

            // Tentukan biaya pengiriman berdasarkan metode
            $shippingCost = $request->shipping_method === 'express' ? 25000 : 15000;

            // Buat nomor pesanan
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

            // Buat pesanan
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'status' => 'pending', // Awalnya pesanan pending
                'sub_total' => $subTotal,
                'shipping_cost' => $shippingCost,
                'total_amount' => $subTotal + $shippingCost,
                'notes' => $request->notes ?? '',
                'shipping_courier' => $request->shipping_method
            ]);

            // Buat alamat pengiriman
            $shippingAddress = ShippingAddress::create([
                'order_id' => $order->id,
                'recipient_name' => $request->recipient_name,
                'phone' => $request->phone,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'ward' => $request->ward,
                'full_address' => $request->full_address,
            ]);

            // Buat item pesanan dan kurangi stok
            foreach ($cartItems as $item) {
                $product = $item['product'];
                $productVariant = $item['product_variant'];
                
                // Hitung harga berdasarkan produk dan varian
                $basePrice = $product ? $product->price : 0;
                $variantPrice = $productVariant ? $productVariant->additional_price : 0;
                $unitPrice = $basePrice + $variantPrice;
                $subtotal = $unitPrice * $item['quantity'];

                // Buat item pesanan
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Kurangi stok produk
                if ($productVariant) {
                    $productVariant->decrement('stock', $item['quantity']);
                } else {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Kosongkan keranjang setelah checkout
            $this->cartService->clearCart();

            DB::commit();

            // Redirect ke halaman pengiriman dengan order number
            return redirect()->route('cust.pengiriman.order', ['order' => $order->order_number])
                            ->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan ke pembayaran.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat proses checkout: ' . $e->getMessage()]);
        }
    }

    /**
     * Menampilkan halaman pengiriman
     */
    public function showShipping($orderNumber = null)
    {
        if ($orderNumber) {
            // Jika ada order number, tampilkan detail order
            $order = Order::where('order_number', $orderNumber)
                         ->where('user_id', Auth::id())
                         ->with(['shipping_address', 'items.product', 'items.variant'])
                         ->firstOrFail();
            
            return view('customer.transaksi.pengiriman', compact('order'));
        } else {
            // Jika tidak ada order number, mungkin tampilkan pesanan terakhir
            $latestOrder = Order::where('user_id', Auth::id())
                              ->latest()
                              ->with(['shipping_address', 'items.product', 'items.variant'])
                              ->first();
            
            return view('customer.transaksi.pengiriman', compact('latestOrder'));
        }
    }

    /**
     * Menampilkan halaman pembayaran
     */
    public function showPayment()
    {
        // Ambil pesanan terakhir pengguna
        $latestOrder = Order::where('user_id', Auth::id())
                          ->latest()
                          ->with(['shipping_address', 'items.product', 'items.variant'])
                          ->first();
        
        // Data untuk ditampilkan di halaman pembayaran
        $paymentData = [
            'order' => $latestOrder,
        ];
        
        return view('customer.transaksi.pembayaran', $paymentData);
    }
}