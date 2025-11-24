<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the customer's profile.
     */
    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        return view('customer.profil.profil_pembeli', compact('user'));
    }

    /**
     * Show the form for editing the customer's profile.
     */
    public function edit()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        return view('customer.profil.edit_profil', compact('user'));
    }

    /**
     * Update the customer's profile in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'full_address' => 'required|string|max:500',
            'bio' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'ward' => $request->ward,
            'full_address' => $request->full_address,
            'bio' => $request->bio,
        ]);

        return redirect()->route('profil.pembeli')->with('success', 'Profil berhasil diperbarui');
    }
}