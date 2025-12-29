<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\ProductReturn;
use App\Models\ViolationReport;
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
        try {
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
                // Mapping status aplikasi ke status database
                $statusMapping = [
                    'pending_payment' => 'pending',    // Menunggu Pembayaran
                    'processing' => 'confirmed',       // Diproses
                    'shipping' => 'shipped',           // Dikirim
                    'completed' => 'delivered',        // Selesai
                    'cancelled' => 'cancelled'         // Dibatalkan
                ];

                $dbStatus = $statusMapping[$request->status] ?? $request->status;
                $query->where('status', $dbStatus);
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

            // Debug: lihat apakah seller_id valid dan berapa banyak produk yang dimiliki (fungsi index)
            \Log::info("Seller ID (index function): " . $seller->id);
            $productCount = DB::table('products')->where('seller_id', $seller->id)->count();
            \Log::info("Product count for seller (index function): " . $productCount);

            // Debug tambahan: cek apakah produk-produk milik seller tersedia di order_items
            $productsForSeller = DB::table('products')->where('seller_id', $seller->id)->get(['id']);
            $productIds = $productsForSeller->pluck('id')->toArray();
            \Log::info("Product IDs for seller (index function): " . json_encode($productIds));

            // Debug: cek apakah ada order_items yang terkait dengan produk milik seller
            $orderItemsForSellerProducts = DB::table('order_items')
                ->whereIn('product_id', $productIds)
                ->get();
            \Log::info("Order items count for seller products (index function): " . $orderItemsForSellerProducts->count());

            // Debug: query untuk mendapatkan semua pesanan yang terkait dengan penjual
            $allOrders = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.seller_id', $seller->id)
                ->select('orders.id', 'orders.status', 'orders.order_number')
                ->get();

            \Log::info("All orders for seller (index function): " . $seller->id . ", count: " . $allOrders->count());
            \Log::info("Order numbers for seller (index function): " . $allOrders->pluck('order_number')->toJson());
            \Log::info("Order statuses (index function): " . $allOrders->pluck('status')->toJson());

            // Hitung jumlah pesanan per status dari database
            $statusCounts = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.seller_id', $seller->id)
                ->selectRaw('orders.status, count(*) as count')
                ->groupBy('orders.status')
                ->pluck('count', 'orders.status');

            \Log::info("Raw status counts from database (index function): " . json_encode($statusCounts->toArray()));

            // Mapping status database ke status aplikasi
            $statusMapping = [
                'pending' => 'pending_payment',    // Menunggu Pembayaran
                'confirmed' => 'processing',       // Diproses
                'shipped' => 'shipping',           // Dikirim
                'delivered' => 'completed',        // Selesai
                'cancelled' => 'cancelled'         // Dibatalkan
            ];

            // Terapkan mapping dan hitung jumlah untuk setiap status aplikasi
            $allStatus = ['pending_payment', 'processing', 'shipping', 'completed', 'cancelled'];
            $statusData = [];
            foreach ($allStatus as $appStatus) {
                $statusData[$appStatus] = 0; // Inisialisasi dengan 0

                // Tambahkan jumlah dari setiap status database yang dipetakan ke status aplikasi ini
                foreach ($statusCounts as $dbStatus => $count) {
                    if ($statusMapping[$dbStatus] === $appStatus) {
                        $statusData[$appStatus] += $count;
                    }
                }
            }

            \Log::info("Mapped status counts (index function): " . json_encode($statusData));

            return view('penjual.manajemen_pesanan', compact('orders', 'statusData'));
        } catch (\Exception $e) {
            \Log::error("Error in index method: " . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data pesanan.');
        }
    }

    /**
     * API endpoint untuk mendapatkan jumlah pesanan per status
     */
    public function getOrderStatusCounts()
    {
        try {
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

            // Debug: lihat apakah seller_id valid dan berapa banyak produk yang dimiliki (fungsi getOrderStatusCounts)
            \Log::info("Seller ID (getOrderStatusCounts function): " . $seller->id);
            $productCount = DB::table('products')->where('seller_id', $seller->id)->count();
            \Log::info("Product count for seller (getOrderStatusCounts function): " . $productCount);

            // Debug: cek apakah produk-produk milik seller tersedia di order_items
            $productsForSeller = DB::table('products')->where('seller_id', $seller->id)->get(['id']);
            $productIds = $productsForSeller->pluck('id')->toArray();
            \Log::info("Product IDs for seller (getOrderStatusCounts function): " . json_encode($productIds));

            // Debug: query untuk mendapatkan semua pesanan yang terkait dengan penjual
            $allOrders = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.seller_id', $seller->id)
                ->select('orders.id', 'orders.status', 'orders.order_number')
                ->get();

            \Log::info("All orders for seller (getOrderStatusCounts function): " . $seller->id . ", count: " . $allOrders->count());
            \Log::info("Order numbers for seller (getOrderStatusCounts function): " . $allOrders->pluck('order_number')->toJson());
            \Log::info("Order statuses for seller (getOrderStatusCounts function): " . $allOrders->pluck('status')->toJson());

            // Hitung jumlah pesanan per status dari database
            $statusCounts = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.seller_id', $seller->id)
                ->selectRaw('orders.status, count(*) as count')
                ->groupBy('orders.status')
                ->pluck('count', 'orders.status');

            \Log::info("Raw status counts from database (getOrderStatusCounts function): " . json_encode($statusCounts->toArray()));

            // Mapping status database ke status aplikasi
            $statusMapping = [
                'pending' => 'pending_payment',    // Menunggu Pembayaran
                'confirmed' => 'processing',       // Diproses
                'shipped' => 'shipping',           // Dikirim
                'delivered' => 'completed',        // Selesai
                'cancelled' => 'cancelled'         // Dibatalkan
            ];

            // Terapkan mapping dan hitung jumlah untuk setiap status aplikasi
            $allStatus = ['pending_payment', 'processing', 'shipping', 'completed', 'cancelled'];
            $statusData = [];
            foreach ($allStatus as $appStatus) {
                $statusData[$appStatus] = 0; // Inisialisasi dengan 0

                // Tambahkan jumlah dari setiap status database yang dipetakan ke status aplikasi ini
                foreach ($statusCounts as $dbStatus => $count) {
                    if ($statusMapping[$dbStatus] === $appStatus) {
                        $statusData[$appStatus] += $count;
                    }
                }
            }

            \Log::info("Mapped status counts (getOrderStatusCounts function): " . json_encode($statusData));
            \Log::info("Returning status counts: " . json_encode($statusData));

            return response()->json([
                'success' => true,
                'status_counts' => $statusData
            ]);
        } catch (\Exception $e) {
            \Log::error("Error in getOrderStatusCounts: " . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung jumlah pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
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
        // Validasi input - menerima status dalam format database
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled'
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
     * Update the shipping status and tracking number of the specified order.
     */
    public function updateShipping(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'tracking_number' => 'required|string|max:255',
            'shipping_courier' => 'nullable|string|max:255',
            'shipping_carrier' => 'nullable|string|max:255'
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

        // Siapkan data untuk update
        $updateData = [
            'status' => 'shipped',
            'tracking_number' => $request->tracking_number,
        ];

        // Hanya update shipping_carrier jika disediakan
        if (!empty($request->shipping_carrier)) {
            $updateData['shipping_carrier'] = $request->shipping_carrier;
        }

        // Hanya update shipping_courier jika disediakan (jika tidak kosong)
        if (!empty($request->shipping_courier)) {
            $updateData['shipping_courier'] = $request->shipping_courier;
        }

        // Update status menjadi shipped dan tambahkan nomor resi
        $order->update($updateData);

        // Tambahkan log transisi status
        $order->logs()->create([
            'status' => 'shipped',
            'description' => "Pesanan dikirim dengan nomor resi {$request->tracking_number}",
            'updated_by' => 'seller',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dikirim dengan nomor resi',
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

        // Mapping status database ke status aplikasi
        $statusMapping = [
            'pending' => 'pending_payment',    // Menunggu Pembayaran
            'confirmed' => 'processing',       // Diproses
            'shipped' => 'shipping',           // Dikirim
            'delivered' => 'completed',        // Selesai
            'cancelled' => 'cancelled'         // Dibatalkan
        ];

        $formattedOrders = $recentOrders->map(function ($order) use ($statusMapping) {
            $appStatus = $statusMapping[$order->status] ?? $order->status;

            // Ambil item pertama dari pesanan untuk ditampilkan di card
            $firstItem = $order->items->first();
            $product = $firstItem ? $firstItem->product : null;

            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->getUserNameAttribute(),
                'status' => $appStatus,
                'status_display' => ucfirst(str_replace('_', ' ', $appStatus)),
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at->format('d M Y'),
                'tracking_number' => $order->tracking_number,
                // Tambahkan informasi produk untuk ditampilkan di card
                'product_image' => $product ? ($product->image ? asset('storage/' . $product->image) : asset('images/placeholder-product.jpg')) : asset('images/placeholder-product.jpg'),
                'product_name' => $product ? $product->name : 'Produk tidak ditemukan',
                'quantity' => $firstItem ? $firstItem->quantity : 0,
                'subtotal' => $firstItem ? $firstItem->subtotal : 0
            ];
        });

        return response()->json([
            'success' => true,
            'orders' => $formattedOrders
        ]);
    }

    /**
     * Display the seller's sales history
     */
    public function salesHistory(Request $request)
    {
        try {
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
                // Mapping status aplikasi ke status database
                $statusMapping = [
                    'pending_payment' => 'pending',    // Menunggu Pembayaran
                    'processing' => 'confirmed',       // Diproses
                    'shipping' => 'shipped',           // Dikirim
                    'completed' => 'delivered',        // Selesai
                    'cancelled' => 'cancelled'         // Dibatalkan
                ];

                $dbStatus = $statusMapping[$request->status] ?? $request->status;
                $query->where('status', $dbStatus);
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

            // Ambil semua pesanan milik penjual ini untuk menghitung statistik
            $allSellerOrders = Order::whereHas('items.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            });

            // Hitung jumlah pesanan per status dari database
            $statusCounts = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('products.seller_id', $seller->id)
                ->selectRaw('orders.status, count(*) as count')
                ->groupBy('orders.status')
                ->pluck('count', 'orders.status');

            // Mapping status database ke status aplikasi
            $statusMapping = [
                'pending' => 'pending_payment',    // Menunggu Pembayaran
                'confirmed' => 'processing',       // Diproses
                'shipped' => 'shipping',           // Dikirim
                'delivered' => 'completed',        // Selesai
                'cancelled' => 'cancelled'         // Dibatalkan
            ];

            // Terapkan mapping dan hitung jumlah untuk setiap status aplikasi
            $allStatus = ['pending_payment', 'processing', 'shipping', 'completed', 'cancelled'];
            $statusData = [];
            foreach ($allStatus as $appStatus) {
                $statusData[$appStatus] = 0; // Inisialisasi dengan 0

                // Tambahkan jumlah dari setiap status database yang dipetakan ke status aplikasi ini
                foreach ($statusCounts as $dbStatus => $count) {
                    if ($statusMapping[$dbStatus] === $appStatus) {
                        $statusData[$appStatus] += $count;
                    }
                }
            }

            // Hitung statistik penjualan
            $totalSales = $allSellerOrders->sum('total_amount'); // Total penjualan keseluruhan

            $totalTransactions = $allSellerOrders->count(); // Total jumlah transaksi

            $monthlyRevenue = $allSellerOrders->whereMonth('created_at', now()->month)
                                             ->whereYear('created_at', now()->year)
                                             ->sum('total_amount'); // Pendapatan bulan ini

            $avgPerTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0; // Rata-rata per transaksi

            // Ambil pesanan yang telah selesai untuk ditampilkan di daftar
            $completedOrdersQuery = Order::with(['user', 'items.product', 'items.variant', 'shipping_address', 'logs', 'payment'])
                ->whereHas('items.product', function ($q) use ($seller) {
                    $q->where('seller_id', $seller->id);
                })
                ->where('status', 'delivered'); // Status 'delivered' adalah pesanan yang selesai

            // Tambahkan filter yang sama seperti query utama
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $completedOrdersQuery->where(function($q) use ($search) {
                    $q->where('order_number', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }

            if ($request->has('date_filter') && $request->date_filter) {
                switch ($request->date_filter) {
                    case 'today':
                        $completedOrdersQuery->whereDate('created_at', today());
                        break;
                    case 'this_week':
                        $completedOrdersQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $completedOrdersQuery->whereMonth('created_at', now()->month)
                                             ->whereYear('created_at', now()->year);
                        break;
                    case 'this_year':
                        $completedOrdersQuery->whereYear('created_at', now()->year);
                        break;
                }
            }

            $completedOrders = $completedOrdersQuery->orderBy('created_at', 'desc')
                                                   ->paginate(10)
                                                   ->appends($request->query());

            return view('penjual.riwayat_penjualan', compact(
                'orders',
                'statusData',
                'totalSales',
                'totalTransactions',
                'monthlyRevenue',
                'avgPerTransaction',
                'completedOrders'
            ));
        } catch (\Exception $e) {
            \Log::error("Error in salesHistory method: " . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data riwayat penjualan.');
        }
    }

    // Fungsi-fungsi lainnya...
}