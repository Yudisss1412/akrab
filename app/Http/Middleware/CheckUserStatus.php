<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memeriksa status pengguna
 *
 * Middleware ini memastikan bahwa hanya pengguna dengan status aktif
 * yang dapat mengakses rute yang dilindungi oleh middleware ini.
 * Jika pengguna memiliki status 'suspended', mereka akan dikeluarkan
 * dari sistem dan diarahkan ke halaman login.
 */
class CheckUserStatus
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
        if (Auth::check() && Auth::user()->status === 'suspended') {
            // Logout pengguna dan arahkan ke login
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda telah ditangguhkan. Silakan hubungi administrator.',
                ], 401);
            }

            return redirect('/login')->with('error', 'Akun Anda telah ditangguhkan. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}
