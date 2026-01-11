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

            // Filter items hanya untuk produk penjual ini
            foreach ($orders as $order) {
                $order->items = $order->items->filter(function ($item) use ($seller) {
                    return $item->product->seller_id == $seller->id;
                });
            }

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

        // Filter items hanya untuk produk penjual ini
        $order->items = $order->items->filter(function ($item) use ($seller) {
            return $item->product->seller_id == $seller->id;
        });

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

        // Filter items hanya untuk produk penjual ini
        $order->items = $order->items->filter(function ($item) use ($seller) {
            return $item->product->seller_id == $seller->id;
        });

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
            ->with(['items', 'items.product']) // Load items untuk membuat transaksi
            ->firstOrFail();

        // Filter items hanya untuk produk penjual ini
        $order->items = $order->items->filter(function ($item) use ($seller) {
            return $item->product->seller_id == $seller->id;
        });

        // Simpan status lama sebelum update
        $oldStatus = $order->status;

        // Update status
        $order->update(['status' => $request->status]);

        // Tambahkan log transisi status
        $order->logs()->create([
            'status' => $request->status,
            'description' => "Status pesanan diubah menjadi {$request->status} oleh penjual",
            'updated_by' => 'seller',
        ]);

        // Jika status berubah menjadi 'delivered' (artinya pesanan selesai), buat transaksi penjualan
        if ($request->status === 'delivered' && $oldStatus !== 'delivered') {
            $this->createSalesTransactions($order, $seller->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui',
            'order' => $order
        ]);
    }

    /**
     * Membuat transaksi penjualan ketika pesanan selesai
     */
    private function createSalesTransactions($order, $sellerId)
    {
        // Buat transaksi untuk setiap item dalam pesanan
        foreach ($order->items as $item) {
            // Pastikan item ini milik penjual yang benar
            if ($item->product && $item->product->seller_id == $sellerId) {
                \App\Models\SellerTransaction::create([
                    'seller_id' => $sellerId,
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'transaction_type' => 'sale',
                    'amount' => $item->subtotal,
                    'description' => "Penjualan produk: {$item->product->name}",
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'status' => 'completed',
                    'transaction_date' => now(),
                ]);
            }
        }
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
        $order = Order::with(['items.product']) // Load items untuk filtering
            ->where('id', $id)
            ->whereHas('items.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->firstOrFail();

        // Filter items hanya untuk produk penjual ini
        $order->items = $order->items->filter(function ($item) use ($seller) {
            return $item->product->seller_id == $seller->id;
        });

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
        $recentOrders = Order::with(['user', 'items.product', 'logs'])
            ->whereHas('items.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Filter items hanya untuk produk penjual ini
        foreach ($recentOrders as $order) {
            $order->items = $order->items->filter(function ($item) use ($seller) {
                return $item->product->seller_id == $seller->id;
            });
        }

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

            // Buat timeline dari log pesanan
            $timeline = [];
            foreach ($order->logs as $log) {
                $statusLabel = $statusMapping[$log->status] ?? $log->status;

                // Mapping status ke dalam bahasa Indonesia
                $statusIndonesia = [
                    'pending_payment' => 'Menunggu Pembayaran',
                    'processing' => 'Diproses',
                    'shipping' => 'Dikirim',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan'
                ];

                $statusDisplay = $statusIndonesia[$statusLabel] ?? ucfirst(str_replace('_', ' ', $statusLabel));
                $timeline[] = [
                    'status' => $statusLabel,
                    'status_display' => $statusDisplay,
                    'timestamp' => $log->created_at->format('d M Y H:i'),
                    'description' => $log->description
                ];
            }

            // Urutkan timeline berdasarkan waktu
            usort($timeline, function ($a, $b) {
                return strtotime($a['timestamp']) - strtotime($b['timestamp']);
            });

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
                'subtotal' => $firstItem ? $firstItem->subtotal : 0,
                // Tambahkan timeline
                'timeline' => $timeline
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

            // Filter items hanya untuk produk penjual ini
            foreach ($orders as $order) {
                $order->items = $order->items->filter(function ($item) use ($seller) {
                    return $item->product->seller_id == $seller->id;
                });
            }

            // Gunakan service untuk menghitung statistik
            $statsService = new \App\Services\SellerStatisticsService();

            // Hitung statistik penjualan
            $stats = $statsService->calculateSellerStats($seller->id);
            $totalSales = $stats['totalSales'];
            $totalTransactions = $stats['totalTransactions'];
            $monthlyRevenue = $stats['monthlyRevenue'];
            $avgPerTransaction = $stats['avgPerTransaction'];

            // Hitung jumlah pesanan per status
            $statusData = $statsService->calculateOrderStatusCounts($seller->id);

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

            // Filter items hanya untuk produk penjual ini
            foreach ($completedOrders as $order) {
                $order->items = $order->items->filter(function ($item) use ($seller) {
                    return $item->product->seller_id == $seller->id;
                });
            }

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

        /**
     * Display complaints and returns page
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

        return view('penjual.komplain_retur');
    }

    /**
     * Get complaints and returns data in JSON format for the page
     */
    public function getComplaintsAndReturnsJson(Request $request)
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

        // Ambil parameter filter
        $filterStar = $request->input('filter_star');
        $filterReply = $request->input('filter_reply');
        $sortBy = $request->input('sort_by', 'newest');
        $page = $request->input('page', 1);
        $perPage = 10;

        // Bangun query untuk reviews
        $query = Review::with(['user', 'product'])
            ->whereHas('product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            });

        // Filter berdasarkan bintang (rating)
        if ($filterStar) {
            $query->where('rating', $filterStar);
        } else {
            // Secara default hanya tampilkan rating rendah (1-2 bintang) sebagai komplain
            $query->where('rating', '<=', 2);
        }

        // Filter berdasarkan status balasan
        if ($filterReply === 'replied') {
            $query->whereNotNull('reply')->where('reply', '!=', '');
        } elseif ($filterReply === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('reply')->orWhere('reply', '');
            });
        }

        // Urutkan
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $query->orderBy('rating', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $reviews = $query->paginate($perPage);

        // Format data untuk frontend
        $formattedReviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'name' => $review->user->name ?? 'Pelanggan',
                'date' => $review->created_at->format('d M Y H:i'),
                'rating' => $review->rating,
                'comment' => $review->review_text,
                'replied' => !empty($review->reply),
                'reply' => $review->reply,
                'product' => [
                    'id' => $review->product->id,
                    'name' => $review->product->name,
                    'image' => $review->product->image ? asset('storage/' . $review->product->image) : asset('images/placeholder-product.jpg'),
                ]
            ];
        });

        return response()->json([
            'reviews' => $formattedReviews,
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Get returns data for the page
     */
    public function getReturnsData(Request $request)
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

        // Ambil parameter filter
        $filterStatus = $request->input('filter_status');
        $page = $request->input('page', 1);
        $perPage = 10;

        // Bangun query untuk returns
        $query = ProductReturn::with(['user', 'orderItem.product'])
            ->whereHas('orderItem.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            });

        // Filter berdasarkan status
        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Format data untuk frontend
        $formattedReturns = $returns->map(function ($return) {
            // Mapping status ke label
            $statusLabels = [
                'pending' => 'Menunggu',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'completed' => 'Selesai'
            ];

            return [
                'id' => $return->id,
                'customer_name' => $return->user->name ?? 'Pelanggan',
                'created_at' => $return->created_at->format('d M Y H:i'),
                'status' => $return->status,
                'status_label' => $statusLabels[$return->status] ?? ucfirst($return->status),
                'product_name' => $return->orderItem->product->name ?? 'Produk tidak ditemukan',
                'reason' => $return->reason,
                'description' => $return->description,
                'refund_amount' => (float)$return->refund_amount,
            ];
        });

        return response()->json([
            'returns' => $formattedReturns,
            'pagination' => [
                'current_page' => $returns->currentPage(),
                'last_page' => $returns->lastPage(),
                'per_page' => $returns->perPage(),
                'total' => $returns->total(),
            ]
        ]);
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
        $return = ProductReturn::with(['orderItem.product'])
            ->where('id', $id)
            ->whereHas('orderItem.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->firstOrFail();

        // Perbarui status menjadi approved
        $return->update([
            'status' => 'approved',
            'processed_at' => now(),
            'processed_by' => $user->id
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
        $return = ProductReturn::with(['orderItem.product'])
            ->where('id', $id)
            ->whereHas('orderItem.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->firstOrFail();

        // Perbarui status menjadi rejected
        $return->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'processed_by' => $user->id
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
        $return = ProductReturn::with(['orderItem.product'])
            ->where('id', $id)
            ->whereHas('orderItem.product', function ($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })
            ->firstOrFail();

        // Perbarui status menjadi completed
        $return->update([
            'status' => 'completed',
            'processed_at' => now(),
            'processed_by' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Retur berhasil diselesaikan'
        ]);
    }
}