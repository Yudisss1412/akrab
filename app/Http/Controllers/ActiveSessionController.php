<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ActiveSessionController extends Controller
{
    /**
     * Menampilkan daftar sesi login aktif pengguna
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Pastikan pengguna sudah login
        if (!Auth::check()) {
            return response()->json(['sessions' => []]);
        }
        
        // Dalam implementasi nyata, kita akan mengambil data sesi dari database
        // Untuk saat ini, kita akan membuat data dummy yang menyerupai struktur nyata
        
        $currentSessionId = Session::getId();
        $userAgent = $request->userAgent();
        $ipAddress = $request->ip();
        
        // Untuk implementasi yang lebih lengkap, kita bisa menggunakan tabel sessions
        // atau membuat tabel khusus user_sessions untuk melacak sesi pengguna
        
        $sessions = [
            [
                'id' => $currentSessionId,
                'device' => $this->parseUserAgent($userAgent),
                'location' => 'Jakarta, Indonesia', // Dalam implementasi nyata, ini bisa diambil dari IP geolocation
                'last_active' => 'Baru saja',
                'ip' => $ipAddress,
                'current' => true,
                'user_agent' => substr($userAgent ?? 'Unknown Browser', 0, 50)
            ],
            [
                'id' => 'abc123def456',
                'device' => 'Safari di iPhone',
                'location' => 'Bandung, Indonesia',
                'last_active' => '2 jam yang lalu',
                'ip' => '203.123.45.67',
                'current' => false,
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)'
            ],
            [
                'id' => 'xyz789uvw012',
                'device' => 'Firefox di Mac',
                'location' => 'Surabaya, Indonesia',
                'last_active' => '1 hari yang lalu',
                'ip' => '118.90.12.34',
                'current' => false,
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:89.0)'
            ]
        ];
        
        return response()->json(['sessions' => $sessions]);
    }
    
    /**
     * Mengakhiri sesi tertentu
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $sessionId
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $sessionId)
    {
        // Pastikan pengguna sudah login
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        // Dalam implementasi nyata, kita akan menghapus sesi dari database
        // dan membatalkan sesi aktif jika sesi tersebut bukan sesi saat ini
        
        if ($sessionId === Session::getId()) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat mengakhiri sesi saat ini'], 400);
        }
        
        // Untuk contoh ini, kita hanya akan mengembalikan respons sukses
        return response()->json([
            'success' => true, 
            'message' => 'Sesi berhasil diakhiri'
        ]);
    }
    
    /**
     * Mengakhiri semua sesi kecuali sesi saat ini
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        // Pastikan pengguna sudah login
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        // Dalam implementasi nyata, kita akan menghapus semua sesi pengguna
        // kecuali sesi saat ini dari database
        
        // Untuk contoh ini, kita hanya akan mengembalikan respons sukses
        return response()->json([
            'success' => true, 
            'message' => 'Semua sesi lain berhasil diakhiri'
        ]);
    }
    
    /**
     * Parse user agent untuk mendapatkan informasi device
     *
     * @param string|null $userAgent
     * @return string
     */
    private function parseUserAgent($userAgent)
    {
        if (!$userAgent) {
            return 'Unknown Device';
        }
        
        // Contoh parsing sederhana - dalam implementasi nyata bisa lebih kompleks
        if (strpos($userAgent, 'Chrome') !== false) {
            if (strpos($userAgent, 'Windows') !== false) {
                return 'Chrome di Windows';
            } elseif (strpos($userAgent, 'Mac') !== false) {
                return 'Chrome di Mac';
            } else {
                return 'Chrome di Device';
            }
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            if (strpos($userAgent, 'Windows') !== false) {
                return 'Firefox di Windows';
            } elseif (strpos($userAgent, 'Mac') !== false) {
                return 'Firefox di Mac';
            } else {
                return 'Firefox di Device';
            }
        } elseif (strpos($userAgent, 'Safari') !== false) {
            if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
                return 'Safari di iPhone/iPad';
            } else {
                return 'Safari di Device';
            }
        } else {
            return 'Browser di Device';
        }
    }
}