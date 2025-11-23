<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartItems = $this->cartService->getCartItems();

        // Hitung subtotal untuk tiap item dan total keseluruhan
        $cartSubtotal = $this->cartService->getSubtotal();

        // Hitung total berat produk di keranjang
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $product = $item['product'] ?? $item->product;
            if ($product) {
                $weightPerUnit = $product->weight ?? 0;
                $quantity = $item['quantity'] ?? $item->quantity;
                $totalWeight += $weightPerUnit * $quantity;
            }
        }

        // Nilai-nilai lain yang mungkin ditampilkan di keranjang
        $discount = 0; // Placeholder untuk diskon
        $cartTotal = $cartSubtotal - $discount; // Total akhir

        $totalItems = $this->cartService->getTotalItems();

        return view('customer.keranjang', compact('cartItems', 'cartSubtotal', 'discount', 'totalWeight', 'cartTotal', 'totalItems'));
    }
    
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id', // Tambahkan validasi untuk varian produk
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cartItem = $this->cartService->addItem(
            $request->product_id,
            $request->quantity,
            $request->product_variant_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'data' => $cartItem
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        $success = $this->cartService->updateItem($id, $request->quantity);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kuantitas'
            ], 404);
        }

        // If quantity is 0, the item is deleted, so we don't return updated item data
        if ($request->quantity <= 0) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang'
            ]);
        }

        // Ambil ulang item yang diperbarui untuk ditampilkan
        $cartItems = $this->cartService->getCartItems();
        $updatedItem = $cartItems->firstWhere(function ($item) use ($id) {
            return $item['id'] == $id;
        });

        return response()->json([
            'success' => true,
            'message' => 'Kuantitas berhasil diperbarui',
            'data' => $updatedItem
        ]);
    }

    public function remove($id)
    {
        $success = $this->cartService->removeItem($id);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang'
        ]);
    }

    public function clear()
    {
        $this->cartService->clearCart();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }
}