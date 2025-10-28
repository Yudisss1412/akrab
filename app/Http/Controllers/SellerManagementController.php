<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerManagementController extends Controller
{
    public function index(Request $request)
    {
        // Check which tab is active
        $tab = $request->get('tab', 'sellers');
        
        // Initialize variables
        $sellers = null;
        $buyers = null;
        $statusOptions = [];
        
        if ($tab === 'buyers') {
            // Search and filter logic for buyers
            $query = User::with(['role', 'orders'])->whereHas('role', function($q) {
                $q->where('name', 'customer'); // Assuming customer role identifies buyers
            });
            
            // Search by name or email
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }
            
            // Filter by status (assuming active/inactive based on account status)
            if ($request->filled('status')) {
                $status = $request->get('status');
                if ($status === 'aktif') {
                    $query->whereNull('deleted_at'); // active users not deleted
                } elseif ($status === 'ditangguhkan') {
                    $query->whereNotNull('deleted_at'); // suspended users (soft deleted)
                }
            }
            
            // Filter by join date range
            if ($request->filled('join_date_from') || $request->filled('join_date_to')) {
                if ($request->filled('join_date_from')) {
                    $query->whereDate('created_at', '>=', $request->get('join_date_from'));
                }
                if ($request->filled('join_date_to')) {
                    $query->whereDate('created_at', '<=', $request->get('join_date_to'));
                }
            }
            
            // Sort by created_at descending by default
            $query->orderBy('created_at', 'desc');
            
            $buyers = $query->paginate(15)->appends($request->query());
            
            // Get status filter options for buyers
            $statusOptions = [
                'aktif' => 'Aktif',
                'ditangguhkan' => 'Ditangguhkan'
            ];
        } else {
            // Search and filter logic for sellers
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
            
            // Get status filter options for sellers
            $statusOptions = [
                'aktif' => 'Aktif',
                'ditangguhkan' => 'Ditangguhkan',
                'menunggu_verifikasi' => 'Menunggu Verifikasi',
                'baru' => 'Baru'
            ];
        }
        
        // Return view with all necessary variables
        return view('sellers.index', compact('sellers', 'buyers', 'statusOptions', 'tab'));
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
        $userType = $request->input('user_type', 'seller');
        
        if ($userType === 'buyer') {
            // Handle bulk actions for buyers
            $request->validate([
                'action' => 'required|in:suspend,activate,delete',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);
            
            $userIds = json_decode($request->input('user_ids'), true) ?: [];
            $action = $request->input('action');
            
            switch ($action) {
                case 'suspend':
                    User::whereIn('id', $userIds)->delete(); // Soft delete to suspend
                    $message = 'Pembeli berhasil ditangguhkan.';
                    break;
                case 'activate':
                    User::whereIn('id', $userIds)->restore(); // Restore to activate
                    $message = 'Pembeli berhasil diaktifkan kembali.';
                    break;
                case 'delete':
                    User::whereIn('id', $userIds)->forceDelete(); // Permanent delete
                    $message = 'Pembeli berhasil dihapus.';
                    break;
                default:
                    return redirect()->back()->with('error', 'Aksi tidak valid.');
            }
        } else {
            // Handle bulk actions for sellers
            $request->validate([
                'action' => 'required|in:suspend,activate,delete',
                'seller_ids' => 'required|array',
                'seller_ids.*' => 'exists:sellers,id'
            ]);
            
            $sellerIds = json_decode($request->input('seller_ids'), true) ?: [];
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
    
    public function suspendUser(Request $request, User $user)
    {
        // Soft delete the user to suspend
        $user->delete();
        
        return redirect()->back()->with('success', 'Pembeli berhasil ditangguhkan.');
    }

    public function activateUser(Request $request, User $user)
    {
        // Restore the user to activate
        $user->restore();
        
        return redirect()->back()->with('success', 'Pembeli berhasil diaktifkan kembali.');
    }

    public function userHistory(User $user)
    {
        // Get user's order history
        $orders = $user->orders()->with(['items', 'shipping_address'])->latest()->paginate(10);
        
        return view('admin.user_history', compact('user', 'orders'));
    }

    public function editUser(User $user)
    {
        return view('admin.edit_user', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        return redirect()->route('sellers.index', ['tab' => 'buyers'])->with('success', 'Data pembeli berhasil diperbarui.');
    }

    public function exportSellers()
    {
        $sellers = Seller::all();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sellers.csv"',
        ];

        $callback = function() use ($sellers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Nama Toko',
                'Nama Pemilik',
                'Email',
                'Status',
                'Tanggal Bergabung',
                'Jumlah Produk Aktif',
                'Total Penjualan',
                'Rating'
            ]);

            foreach ($sellers as $seller) {
                fputcsv($file, [
                    $seller->id,
                    $seller->store_name,
                    $seller->owner_name,
                    $seller->email,
                    $seller->status,
                    $seller->join_date ? $seller->join_date->format('Y-m-d') : '-',
                    $seller->active_products_count,
                    $seller->total_sales,
                    $seller->rating
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportBuyers()
    {
        $buyers = User::whereHas('role', function($query) {
            $query->where('name', 'customer');
        })->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="buyers.csv"',
        ];

        $callback = function() use ($buyers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Nama',
                'Email',
                'Status',
                'Tanggal Bergabung',
                'Jumlah Transaksi',
                'Total Pengeluaran'
            ]);

            foreach ($buyers as $buyer) {
                $totalOrders = $buyer->orders()->count();
                $totalSpending = $buyer->orders()->sum('total_amount');
                
                fputcsv($file, [
                    $buyer->id,
                    $buyer->name,
                    $buyer->email,
                    $buyer->deleted_at ? 'Ditangguhkan' : 'Aktif',
                    $buyer->created_at->format('Y-m-d'),
                    $totalOrders,
                    $totalSpending
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
