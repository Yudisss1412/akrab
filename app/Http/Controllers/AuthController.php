<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Seller;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if it's an AJAX request
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => $this->getRoleBasedRedirect()
                ]);
            }

            // Redirect berdasarkan role pengguna
            return $this->redirectToRoleDashboard();
        }

        // Check if it's an AJAX request
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
                'errors' => [
                    'email' => ['Kredensial yang diberikan tidak cocok dengan catatan kami.']
                ]
            ], 422);
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ])->onlyInput('email');
    }

    // Get role-based redirect URL
    private function getRoleBasedRedirect()
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Cek role menggunakan role_id
            if ($user->role && $user->role->name === 'admin') {
                return route('admin.dashboard');
            }
            
            // Cek apakah user adalah penjual
            if ($user->role && $user->role->name === 'seller') {
                return route('seller.dashboard');
            }
            
            // Default ke customer dashboard for buyers
            return route('customer.dashboard');
        }
        
        return '/login';
    }

    // Menampilkan form register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Get the buyer role
        $buyerRole = \App\Models\Role::where('name', 'buyer')->first();
        if (!$buyerRole) {
            return back()->withErrors(['system' => 'Sistem role belum diatur. Silakan hubungi administrator.']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $buyerRole->id, // Assign buyer role to new customer
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }

    // Redirect berdasarkan role
    private function redirectToRoleDashboard()
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Cek role menggunakan role_id
            if ($user->role && $user->role->name === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            // Cek apakah user adalah penjual (both role and seller record)
            if (($user->role && $user->role->name === 'seller') || Seller::where('user_id', $user->id)->exists()) {
                return redirect()->route('seller.dashboard');
            }
            
            // Default ke customer dashboard for buyers
            return redirect()->route('customer.dashboard');
        }
        
        return redirect('/login');
    }
}
