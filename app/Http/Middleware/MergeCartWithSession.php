<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MergeCartWithSession
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika pengguna login dan memiliki item di session, pindahkan ke database
        if (Auth::check()) {
            $sessionCart = session('cart', []);
            
            if (!empty($sessionCart)) {
                foreach ($sessionCart as $item) {
                    $this->cartService->addItem(
                        $item['product_id'], 
                        $item['quantity'], 
                        $item['product_variant_id']
                    );
                }
                
                // Hapus cart dari session setelah dipindahkan
                session()->forget('cart');
            }
        }

        return $next($request);
    }
}