<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use Symfony\Component\HttpFoundation\Response;

class CheckSellerRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user has seller role using the role_id system
            if ($user->role && $user->role->name === 'seller') {
                return $next($request);
            }
        }
        
        return redirect('/')->with('error', 'Akses ditolak. Anda harus menjadi penjual untuk mengakses halaman ini.');
    }
}