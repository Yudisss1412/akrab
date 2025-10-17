<?php

namespace App\Services;

use App\Models\Carts;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected $sessionKey = 'cart';

    /**
     * Mendapatkan item-item di keranjang
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getCartItems()
    {
        if (Auth::check()) {
            // Jika pengguna login, ambil dari database dan kembalikan dalam format konsisten
            $cartItems = Carts::with(['product', 'productVariant'])
                ->where('user_id', Auth::id())
                ->get();
            
            // Format data agar konsisten dengan format session (array)
            return $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'product' => $item->product,  // Relasi product sudah dimuat dengan with()
                    'product_variant' => $item->productVariant,  // Relasi productVariant
                    'subtotal' => ($item->product->price + ($item->productVariant->additional_price ?? 0)) * $item->quantity
                ];
            });
        } else {
            // Jika pengguna tidak login, ambil dari session
            $cartItems = collect(Session::get($this->sessionKey, []));
            
            // Tambahkan informasi produk ke masing-masing item
            $cartItems = $cartItems->map(function ($item) {
                $product = Product::find($item['product_id']);
                $productVariant = null;
                
                if ($item['product_variant_id']) {
                    $productVariant = ProductVariant::find($item['product_variant_id']);
                }
                
                $item['product'] = $product;
                $item['product_variant'] = $productVariant;
                
                // Hitung subtotal
                $basePrice = $product ? $product->price : 0;
                $variantPrice = $productVariant ? $productVariant->additional_price : 0;
                $item['subtotal'] = ($basePrice + $variantPrice) * $item['quantity'];
                
                return $item;
            });
            
            return $cartItems;
        }
    }

    /**
     * Menambahkan item ke keranjang
     * 
     * @param int $productId
     * @param int $quantity
     * @param int|null $productVariantId
     * @return mixed
     */
    public function addItem($productId, $quantity, $productVariantId = null)
    {
        if (Auth::check()) {
            // Jika pengguna login, simpan ke database
            return Carts::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'product_variant_id' => $productVariantId,
                ],
                [
                    'quantity' => $quantity,
                ]
            );
        } else {
            // Jika pengguna tidak login, simpan ke session
            $cart = Session::get($this->sessionKey, []);
            
            // Cek apakah produk sudah ada di keranjang
            $existingKey = null;
            foreach ($cart as $key => $item) {
                if ($item['product_id'] == $productId && $item['product_variant_id'] == $productVariantId) {
                    $existingKey = $key;
                    break;
                }
            }
            
            if ($existingKey !== null) {
                // Update kuantitas jika produk sudah ada
                $cart[$existingKey]['quantity'] = min(99, $cart[$existingKey]['quantity'] + $quantity);
            } else {
                // Tambahkan item baru
                $cart[] = [
                    'product_id' => $productId,
                    'product_variant_id' => $productVariantId,
                    'quantity' => $quantity,
                ];
            }
            
            Session::put($this->sessionKey, $cart);
            
            // Return format yang mirip dengan model Carts
            return (object)[
                'id' => count($cart) - 1, // Gunakan index sebagai ID untuk session
                'product_id' => $productId,
                'product_variant_id' => $productVariantId,
                'quantity' => $quantity,
                'product' => Product::find($productId),
                'product_variant' => ProductVariant::find($productVariantId),
            ];
        }
    }

    /**
     * Memperbarui kuantitas item di keranjang
     * 
     * @param mixed $itemId
     * @param int $quantity
     * @return bool
     */
    public function updateItem($itemId, $quantity)
    {
        if (Auth::check()) {
            // Jika pengguna login, update di database
            $cartItem = Carts::where('id', $itemId)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$cartItem) {
                return false;
            }
            
            if ($quantity > 0) {
                $cartItem->update(['quantity' => $quantity]);
            } else {
                $cartItem->delete();
            }
            
            return true;
        } else {
            // Jika pengguna tidak login, update di session
            $cart = Session::get($this->sessionKey, []);
            
            if (isset($cart[$itemId])) {
                if ($quantity > 0) {
                    $cart[$itemId]['quantity'] = min(99, max(1, $quantity));
                } else {
                    unset($cart[$itemId]);
                    // Re-index array
                    $cart = array_values($cart);
                }
                
                Session::put($this->sessionKey, $cart);
                return true;
            }
            
            return false;
        }
    }

    /**
     * Menghapus item dari keranjang
     * 
     * @param mixed $itemId
     * @return bool
     */
    public function removeItem($itemId)
    {
        if (Auth::check()) {
            // Jika pengguna login, hapus dari database
            $cartItem = Carts::where('id', $itemId)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$cartItem) {
                return false;
            }
            
            $cartItem->delete();
            return true;
        } else {
            // Jika pengguna tidak login, hapus dari session
            $cart = Session::get($this->sessionKey, []);
            
            if (isset($cart[$itemId])) {
                unset($cart[$itemId]);
                // Re-index array
                $cart = array_values($cart);
                Session::put($this->sessionKey, $cart);
                return true;
            }
            
            return false;
        }
    }

    /**
     * Mengosongkan keranjang
     * 
     * @return bool
     */
    public function clearCart()
    {
        if (Auth::check()) {
            // Jika pengguna login, hapus dari database
            return Carts::where('user_id', Auth::id())->delete();
        } else {
            // Jika pengguna tidak login, hapus dari session
            Session::forget($this->sessionKey);
            return true;
        }
    }

    /**
     * Menghitung total subtotal keranjang
     * 
     * @return float
     */
    public function getSubtotal()
    {
        $cartItems = $this->getCartItems();
        $subtotal = 0;
        
        foreach ($cartItems as $item) {
            $basePrice = $item['product'] ? $item['product']->price : 0;
            $variantPrice = $item['product_variant'] ? $item['product_variant']->additional_price : 0;
            $subtotal += ($basePrice + $variantPrice) * $item['quantity'];
        }
        
        return $subtotal;
    }

    /**
     * Menghitung jumlah total item di keranjang
     * 
     * @return int
     */
    public function getTotalItems()
    {
        $cartItems = $this->getCartItems();
        return $cartItems->sum('quantity');
    }
}