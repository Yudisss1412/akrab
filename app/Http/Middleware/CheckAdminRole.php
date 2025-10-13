<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dalam kasus ini, kita asumsikan bahwa admin adalah user dengan akses ke rute admin
        // Kita bisa mengecek apakah user adalah admin berdasarkan logika aplikasi
        // Misalnya: cek apakah user memiliki email tertentu atau id tertentu sebagai admin
        if (Auth::check()) {
            // Di sini Anda bisa menambahkan logika untuk memeriksa apakah user adalah admin
            // Misalnya dengan mengecek email atau field khusus di tabel users
            // Contoh sederhana: cek apakah email mengandung 'admin'
            $user = Auth::user();
            
            // Logika untuk menentukan apakah user adalah admin
            // Anda bisa mengganti ini dengan logika sesuai kebutuhan aplikasi Anda
            if ($this->isAdmin($user)) {
                return $next($request);
            }
        }
        
        return redirect('/')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
    
    /**
     * Cek apakah user adalah admin
     */
    private function isAdmin($user)
    {
        // Check if user has admin role using the role_id system
        return $user->role && $user->role->name === 'admin';
    }
}