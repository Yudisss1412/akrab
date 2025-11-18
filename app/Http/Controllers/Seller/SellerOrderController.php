<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
        }

        // Query builder untuk pesanan berdasarkan produk penjual
        $query = Order::with(['user', 'items.product', 'items.variant', 'shipping_address', 'logs', 'payment'])
            ->whereHas('items.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan pencarian jika ada
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has('date_filter') && $request->date_filter) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Paginate hasil
        $orders = $query->paginate(10)->appends($request->query());

        // Hitung jumlah pesanan per status
        $statusCounts = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $seller->id)
            ->selectRaw('orders.status, count(*) as count')
            ->groupBy('orders.status')
            ->pluck('count', 'orders.status');

        $allStatus = ['pending_payment', 'processing', 'shipping', 'completed', 'cancelled'];
        $statusData = [];
        foreach ($allStatus as $status) {
            $statusData[$status] = $statusCounts[$status] ?? 0;
        }

        return view('penjual.manajemen_pesanan', compact('orders', 'statusData'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
        }

        // Ambil pesanan dengan produk milik penjual ini
        $order = Order::with(['user', 'items.product', 'items.variant', 'shipping_address', 'logs', 'payment'])
            ->where('id', $id)
            ->whereHas('items.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->firstOrFail();

        return view('penjual.detail_pesanan', compact('order'));
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|in:pending_payment,processing,shipping,completed,cancelled'
        ]);

        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
        }

        // Ambil pesanan dengan produk milik penjual ini
        $order = Order::where('id', $id)
            ->whereHas('items.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->firstOrFail();

        // Update status
        $order->update(['status' => $request->status]);

        // Tambahkan log transisi status
        $order->logs()->create([
            'status' => $request->status,
            'description' => "Status pesanan diubah menjadi {$request->status} oleh penjual",
            'updated_by' => 'seller',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui',
            'order' => $order
        ]);
    }
    
    /**
     * API endpoint untuk mendapatkan pesanan terbaru untuk ditampilkan di profil penjual
     */
    public function getRecentOrders()
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller record tidak ditemukan'], 403);
        }

        // Ambil 3 pesanan terbaru dengan produk milik penjual ini
        $recentOrders = Order::with(['user', 'items.product'])
            ->whereHas('items.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Format data untuk frontend
        $formattedOrders = $recentOrders->map(function($order) {
            // Ambil item pertama untuk ditampilkan (bisa disesuaikan)
            $firstItem = $order->items->first();
            
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'created_at' => $order->created_at->format('d M Y'),
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'customer_name' => $order->user->name ?? 'Pelanggan',
                'product_name' => $firstItem && $firstItem->product ? $firstItem->product->name : 'Produk tidak ditemukan',
                'product_image' => $firstItem && $firstItem->product && $firstItem->product->main_image ?
                    asset('storage/' . $firstItem->product->main_image) :
                    asset('src/placeholder_produk.png'),
                'quantity' => $firstItem ? $firstItem->quantity : 1,
                'subtotal' => $firstItem ? $firstItem->subtotal : 0
            ];
        });

        return response()->json([
            'success' => true,
            'orders' => $formattedOrders
        ]);
    }
    
    /**
     * Display the sales history page for sellers
     */
    public function salesHistory(Request $request)
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
        }

        // Query builder untuk pesanan selesai berdasarkan produk penjual
        $query = Order::with(['user', 'items.product', 'items.variant', 'shipping_address', 'logs', 'payment'])
            ->whereHas('items.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->where('status', 'completed') // Hanya tampilkan pesanan yang selesai
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan pencarian jika ada
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has('date_filter') && $request->date_filter) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Pagination hasil
        $completedOrders = $query->paginate(10)->appends($request->query());

        // Hitung total penjualan (jumlah produk terjual)
        $totalSales = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $seller->id)
            ->where('orders.status', 'completed')
            ->sum('order_items.quantity');

        // Hitung total transaksi selesai
        $totalTransactions = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $seller->id)
            ->where('orders.status', 'completed')
            ->distinct('orders.id')
            ->count();

        // Hitung pendapatan bulan ini
        $monthlyRevenue = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $seller->id)
            ->where('orders.status', 'completed')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->sum('order_items.subtotal');

        // Hitung rata-rata per transaksi
        $avgPerTransaction = $totalTransactions > 0 ? $monthlyRevenue / $totalTransactions : 0;

        return view('penjual.riwayat_penjualan', compact(
            'completedOrders', 
            'totalSales', 
            'totalTransactions', 
            'monthlyRevenue', 
            'avgPerTransaction'
        ));
    }
    
    /**
     * API endpoint to fetch urgent tasks counts for the seller dashboard
     */
    public function getUrgentTasks()
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller record tidak ditemukan'], 403);
        }

        // Hitung pesanan yang perlu diproses (status processing atau pending_payment)
        $pendingPaymentCount = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $seller->id)
            ->whereIn('orders.status', ['pending_payment'])
            ->count();

        $processingCount = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_id', $seller->id)
            ->where('orders.status', 'processing')
            ->count();

        $unrepliedChatsCount = 0; // Placeholder - bisa diintegrasikan dengan sistem chat
        
        // Hitung jumlah komplain (ulasan dengan rating 2 ke bawah)
        $newComplaintsCount = \App\Models\Review::whereHas('product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->where('rating', '<=', 2)
            ->count();
            
        // Hitung jumlah permintaan retur yang belum diproses (status pending)
        $pendingReturnsCount = \App\Models\Models\ProductReturn::whereHas('orderItem.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'success' => true,
            'urgent_tasks' => [
                'pending_orders' => $pendingPaymentCount + $processingCount,
                'unreplied_chats' => $unrepliedChatsCount,
                'new_complaints' => $newComplaintsCount,
                'pending_returns' => $pendingReturnsCount
            ]
        ]);
    }
    
    /**
     * Display the complaints and returns page
     */
    public function complaintsAndReturns()
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
        }

        // Kita tetap kirim data kosong karena sekarang data akan dimuat secara dinamis
        $complaints = [];
        $returns = [];

        return view('penjual.komplain_retur', compact('complaints', 'returns'));
    }
    
    /**
     * Approve a return request
     */
    public function approveReturn($id)
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller record tidak ditemukan'], 403);
        }

        // Ambil permintaan retur
        $return = \App\Models\Models\ProductReturn::where('id', $id)
            ->whereHas('orderItem.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->first();

        if (!$return) {
            return response()->json(['success' => false, 'message' => 'Permintaan retur tidak ditemukan'], 404);
        }

        if ($return->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Permintaan retur sudah diproses sebelumnya'], 400);
        }

        // Update status menjadi approved
        $return->update([
            'status' => 'approved',
            'processed_by' => $user->id,
            'processed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan retur berhasil disetujui'
        ]);
    }
    
    /**
     * Reject a return request
     */
    public function rejectReturn($id)
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller record tidak ditemukan'], 403);
        }

        // Ambil permintaan retur
        $return = \App\Models\Models\ProductReturn::where('id', $id)
            ->whereHas('orderItem.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->first();

        if (!$return) {
            return response()->json(['success' => false, 'message' => 'Permintaan retur tidak ditemukan'], 404);
        }

        if ($return->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Permintaan retur sudah diproses sebelumnya'], 400);
        }

        // Update status menjadi rejected
        $return->update([
            'status' => 'rejected',
            'processed_by' => $user->id,
            'processed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan retur berhasil ditolak'
        ]);
    }
    
    /**
     * Complete a return request
     */
    public function completeReturn($id)
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller record tidak ditemukan'], 403);
        }

        // Ambil permintaan retur
        $return = \App\Models\Models\ProductReturn::where('id', $id)
            ->whereHas('orderItem.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->first();

        if (!$return) {
            return response()->json(['success' => false, 'message' => 'Permintaan retur tidak ditemukan'], 404);
        }

        if ($return->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Hanya permintaan retur yang disetujui yang bisa diselesaikan'], 400);
        }

        // Update status menjadi completed
        $return->update([
            'status' => 'completed',
            'processed_by' => $user->id,
            'processed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan retur berhasil diselesaikan'
        ]);
    }

    /**
     * API endpoint untuk mendapatkan data retur produk untuk ditampilkan di halaman komplain & retur
     */
    public function getReturnsData(Request $request)
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['error' => 'Seller record tidak ditemukan'], 403);
        }

        // Query untuk permintaan retur
        $returnsQuery = \App\Models\Models\ProductReturn::with(['user', 'orderItem.product'])
            ->whereHas('orderItem.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->orderBy('requested_at', 'desc');

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status) {
            $returnsQuery->where('status', $request->status);
        }

        $returns = $returnsQuery->paginate(10);

        $formattedReturns = $returns->map(function($return) {
            return [
                'id' => $return->id,
                'customer_name' => $return->user->name ?? 'Pelanggan',
                'reason' => $return->reason,
                'description' => $return->description ?? 'Tidak ada deskripsi',
                'product_name' => $return->orderItem->product->name ?? 'Produk Tidak Ditemukan',
                'status' => $return->status,
                'status_label' => ucfirst(str_replace('_', ' ', $return->status)),
                'created_at' => $return->requested_at->format('d M Y, H:i'),
                'refund_amount' => $return->refund_amount,
                'tracking_number' => $return->tracking_number
            ];
        });

        return response()->json([
            'returns' => $formattedReturns,
            'pagination' => [
                'current_page' => $returns->currentPage(),
                'last_page' => $returns->lastPage(),
                'total' => $returns->total(),
                'per_page' => $returns->perPage()
            ]
        ]);
    }
}