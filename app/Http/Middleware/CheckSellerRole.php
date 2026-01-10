<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memeriksa peran penjual
 *
 * Middleware ini memastikan bahwa hanya pengguna dengan peran penjual
 * yang dapat mengakses rute yang dilindungi oleh middleware ini.
 */
class CheckSellerRole
{
    /**
     * Menangani permintaan masuk
     *
     * @param Request $request Objek permintaan HTTP
     * @param Closure $next Fungsi closure untuk melanjutkan permintaan
     * @return Response Respons HTTP
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Periksa apakah pengguna memiliki peran penjual menggunakan sistem role_id
            if ($user->role && $user->role->name === 'seller') {
                return $next($request);
            }
        }

        return redirect('/')->with('error', 'Akses ditolak. Anda harus menjadi penjual untuk mengakses halaman ini.');
    }
}