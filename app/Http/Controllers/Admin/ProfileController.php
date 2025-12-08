<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $admin = Auth::user();

        if (!$admin || $admin->role->name !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        // Ambil log aktivitas admin terbaru
        $activityLogs = AdminActivityLog::where('user_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.profil_admin', compact('admin', 'activityLogs'));
    }

    public function edit()
    {
        $admin = Auth::user();
        
        if (!$admin || $admin->role->name !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
        
        return view('admin.edit_profil_admin', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::user();
        
        if (!$admin || $admin->role->name !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profil.admin')->with('success', 'Profil admin berhasil diperbarui!');
    }
}