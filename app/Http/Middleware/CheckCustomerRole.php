<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use Symfony\Component\HttpFoundation\Response;

class CheckCustomerRole
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
            
            // Customer is user with buyer role using the role_id system
            if ($user->role && $user->role->name === 'buyer') {
                return $next($request);
            }
        }
        
        // Jika user tidak memenuhi syarat sebagai customer, redirect
        return redirect('/')->with('error', 'Akses ditolak. Anda harus masuk sebagai pembeli untuk mengakses halaman ini.');
    }
}