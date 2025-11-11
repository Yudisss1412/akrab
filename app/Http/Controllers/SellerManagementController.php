<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerManagementController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'sellers');
        
        // Initialize variables
        $sellers = null;
        $buyers = null;
        $statusOptions = [];

        if ($tab === 'buyers') {
            // Super simple query to ensure buyers are returned
            try {
                // First, get all users with buyer role ID
                $buyerRoleId = Role::where('name', 'buyer')->value('id');
                
                if ($buyerRoleId) {
                    // Build query step by step
                    $query = User::query();
                    
                    // Filter by role_id
                    $query->where('role_id', $buyerRoleId);
                    
                    // Apply filters
                    if ($request->filled('search')) {
                        $searchTerm = $request->get('search');
                        $query->where(function($q) use ($searchTerm) {
                            $q->where('name', 'LIKE', "%{$searchTerm}%")
                              ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                        });
                    }

                    if ($request->filled('status')) {
                        $status = $request->get('status');
                        if ($status === 'aktif') {
                            $query->where('status', 'active');
                        } elseif ($status === 'ditangguhkan') {
                            $query->where('status', 'suspended');
                        }
                    }

                    if ($request->filled('join_date_from') || $request->filled('join_date_to')) {
                        if ($request->filled('join_date_from')) {
                            $query->whereDate('created_at', '>=', $request->get('join_date_from'));
                        }
                        if ($request->filled('join_date_to')) {
                            $query->whereDate('created_at', '<=', $request->get('join_date_to'));
                        }
                    }

                    $query->orderBy('created_at', 'desc');
                    $buyers = $query->paginate(15)->appends($request->query());

                    // Load relationships after pagination
                    if($buyers && $buyers->count() > 0) {
                        $buyers->getCollection()->load(['role']);
                        // Load orders separately to avoid issues
                        foreach($buyers as $buyer) {
                            $buyer->setRelation('orders', $buyer->orders);
                        }
                    }
                } else {
                    // Role 'buyer' tidak ditemukan, kembalikan pagination kosong
                    $buyers = collect()->paginate(15);
                }
            } catch (\Exception $e) {
                \Log::error('Error loading buyers: ' . $e->getMessage());
                $buyers = collect()->paginate(15);
            }

            $statusOptions = [
                'aktif' => 'Aktif',
                'ditangguhkan' => 'Ditangguhkan'
            ];
        } else {
            // For sellers tab
            $query = Seller::query();

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('store_name', 'LIKE', "%{$search}%")
                      ->orWhere('owner_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->filled('join_date_from') || $request->filled('join_date_to')) {
                if ($request->filled('join_date_from')) {
                    $query->whereDate('join_date', '>=', $request->get('join_date_from'));
                }
                if ($request->filled('join_date_to')) {
                    $query->whereDate('join_date', '<=', $request->get('join_date_to'));
                }
            }

            $query->orderBy('join_date', 'desc');
            $sellers = $query->paginate(15)->appends($request->query());

            $statusOptions = [
                'aktif' => 'Aktif',
                'ditangguhkan' => 'Ditangguhkan',
                'menunggu_verifikasi' => 'Menunggu Verifikasi',
                'baru' => 'Baru'
            ];
        }

        // Ensure that $buyers is never null when tab is buyers
        if ($tab === 'buyers' && is_null($buyers)) {
            $buyers = collect()->paginate(15);
        }

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
            'email' => 'required|email|max:255|unique:users,email|unique:sellers,email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->owner_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'status' => 'active',
        ]);

        $user->assignRole('seller');

        Seller::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'owner_name' => $request->owner_name,
            'email' => $request->email,
            'status' => 'aktif',
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
            $request->validate([
                'action' => 'required|in:suspend,activate,delete',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            $userIds = json_decode($request->input('user_ids'), true) ?: [];
            $action = $request->input('action');

            switch ($action) {
                case 'suspend':
                    User::whereIn('id', $userIds)->update(['status' => 'suspended']);
                    $message = 'Pembeli berhasil ditangguhkan.';
                    break;
                case 'activate':
                    User::whereIn('id', $userIds)->update(['status' => 'active']);
                    $message = 'Pembeli berhasil diaktifkan kembali.';
                    break;
                case 'delete':
                    User::whereIn('id', $userIds)->forceDelete();
                    $message = 'Pembeli berhasil dihapus.';
                    break;
                default:
                    return redirect()->back()->with('error', 'Aksi tidak valid.');
            }
        } else {
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
        $user->update(['status' => 'suspended']);

        return redirect()->back()->with('success', 'Pembeli berhasil ditangguhkan.');
    }

    public function activateUser(Request $request, User $user)
    {
        $user->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Pembeli berhasil diaktifkan kembali.');
    }

    public function userHistory(User $user)
    {
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
            $query->where('name', 'buyer');
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
                    $buyer->status,
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