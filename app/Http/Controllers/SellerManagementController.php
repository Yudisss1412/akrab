<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerManagementController extends Controller
{
    public function index(Request $request)
    {
        // Search and filter logic
        $query = Seller::query();
        
        // Search by store name, owner name, or email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('store_name', 'LIKE', "%{$search}%")
                  ->orWhere('owner_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        
        // Filter by join date range
        if ($request->filled('join_date_from') || $request->filled('join_date_to')) {
            if ($request->filled('join_date_from')) {
                $query->whereDate('join_date', '>=', $request->get('join_date_from'));
            }
            if ($request->filled('join_date_to')) {
                $query->whereDate('join_date', '<=', $request->get('join_date_to'));
            }
        }
        
        // Sort by join date descending by default
        $query->orderBy('join_date', 'desc');
        
        $sellers = $query->paginate(15)->appends($request->query());
        
        // Get status filter options
        $statusOptions = [
            'aktif' => 'Aktif',
            'ditangguhkan' => 'Ditangguhkan',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'baru' => 'Baru'
        ];
        
        return view('sellers.index', compact('sellers', 'statusOptions'));
    }
    
    public function create()
    {
        return view('sellers.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:sellers,email',
        ]);
        
        Seller::create([
            'store_name' => $request->store_name,
            'owner_name' => $request->owner_name,
            'email' => $request->email,
            'status' => 'aktif', // Set default status as active
            'join_date' => now(),
            'active_products_count' => 0,
            'total_sales' => 0,
            'rating' => 0
        ]);
        
        return redirect()->route('sellers.index')->with('success', 'Penjual baru berhasil ditambahkan.');
    }
    
    public function show(Seller $seller)
    {
        return view('sellers.show', compact('seller'));
    }
    
    public function edit(Seller $seller)
    {
        return view('sellers.edit', compact('seller'));
    }
    
    public function update(Request $request, Seller $seller)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:sellers,email,' . $seller->id,
            'status' => 'required|in:aktif,ditangguhkan,menunggu_verifikasi,baru'
        ]);
        
        $seller->update([
            'store_name' => $request->store_name,
            'owner_name' => $request->owner_name,
            'email' => $request->email,
            'status' => $request->status
        ]);
        
        return redirect()->route('sellers.index')->with('success', 'Data penjual berhasil diperbarui.');
    }
    
    public function destroy(Seller $seller)
    {
        $seller->delete();
        
        return redirect()->route('sellers.index')->with('success', 'Penjual berhasil dihapus.');
    }
    
    public function suspend(Seller $seller)
    {
        $seller->update(['status' => 'ditangguhkan']);
        
        return redirect()->back()->with('success', 'Penjual berhasil ditangguhkan.');
    }
    
    public function activate(Seller $seller)
    {
        $seller->update(['status' => 'aktif']);
        
        return redirect()->back()->with('success', 'Penjual berhasil diaktifkan kembali.');
    }
    
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:suspend,activate,delete',
            'seller_ids' => 'required|array',
            'seller_ids.*' => 'exists:sellers,id'
        ]);
        
        $sellerIds = $request->input('seller_ids');
        $action = $request->input('action');
        
        switch ($action) {
            case 'suspend':
                Seller::whereIn('id', $sellerIds)->update(['status' => 'ditangguhkan']);
                $message = 'Penjual berhasil ditangguhkan.';
                break;
            case 'activate':
                Seller::whereIn('id', $sellerIds)->update(['status' => 'aktif']);
                $message = 'Penjual berhasil diaktifkan kembali.';
                break;
            case 'delete':
                Seller::whereIn('id', $sellerIds)->delete();
                $message = 'Penjual berhasil dihapus.';
                break;
            default:
                return redirect()->back()->with('error', 'Aksi tidak valid.');
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    public function getDashboardStats()
    {
        $stats = [
            'active_sellers' => Seller::where('status', 'aktif')->count(),
            'new_sellers' => Seller::where('status', 'baru')->where('join_date', '>=', now()->subDays(7))->count(),
            'suspended_sellers' => Seller::where('status', 'ditangguhkan')->count(),
            'pending_verification' => Seller::where('status', 'menunggu_verifikasi')->count(),
        ];
        
        return response()->json($stats);
    }
}
