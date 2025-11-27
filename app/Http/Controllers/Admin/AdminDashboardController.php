<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Get dashboard statistics for admin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardStats(Request $request)
    {
        // Get time range filter
        $range = $request->get('range', '7days');
        
        // Calculate date range
        $startDate = $this->calculateStartDate($range);
        
        // Get ecosystem metrics
        $metrics = $this->getEcosystemMetrics($startDate);
        
        // Get seller statistics
        $sellerStats = $this->getSellerStatistics($startDate);
        
        // Get moderation counts
        $moderationCounts = $this->getModerationCounts();
        
        // Return JSON response
        return response()->json([
            'success' => true,
            'data' => [
                'metrics' => $metrics,
                'seller_stats' => $sellerStats,
                'moderation_counts' => $moderationCounts
            ]
        ]);
    }
    
    /**
     * Calculate start date based on range
     *
     * @param  string  $range
     * @return \Carbon\Carbon
     */
    private function calculateStartDate($range)
    {
        switch ($range) {
            case 'today':
                return now()->startOfDay();
            case '7days':
                return now()->subDays(7)->startOfDay();
            case 'month':
                return now()->startOfMonth();
            case 'year':
                return now()->startOfYear();
            default:
                return now()->subDays(7)->startOfDay();
        }
    }
    
    /**
     * Get ecosystem metrics
     *
     * @param  \Carbon\Carbon  $startDate
     * @return array
     */
    private function getEcosystemMetrics($startDate)
    {
        // Gross Merchandise Value (GMV) - Total order amounts
        $gmv = Order::where('created_at', '>=', $startDate)
                    ->where('status', '!=', 'cancelled')
                    ->sum('total_amount');
        
        // Platform revenue (assumed to be 5% of GMV)
        $revenue = $gmv * 0.05;
        
        // Order statistics
        $totalOrders = Order::where('created_at', '>=', $startDate)
                             ->where('status', '!=', 'cancelled')
                             ->count();
        
        $completedOrders = Order::where('created_at', '>=', $startDate)
                                 ->where('status', 'completed')
                                 ->count();
        
        // Growth percentage calculation (simplified)
        $previousPeriodStart = $startDate->copy()->subDays($startDate->diffInDays(now()));
        $previousGmv = Order::whereBetween('created_at', [$previousPeriodStart, $startDate])
                            ->where('status', '!=', 'cancelled')
                            ->sum('total_amount');
        
        $growthPercentage = $previousGmv > 0 ? (($gmv - $previousGmv) / $previousGmv) * 100 : 0;
        
        return [
            'gmv' => $gmv,
            'revenue' => $revenue,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'growth_percentage' => round($growthPercentage, 2),
            'chart_data' => $this->generateChartData($startDate)
        ];
    }
    
    /**
     * Generate chart data for the given period
     *
     * @param  \Carbon\Carbon  $startDate
     * @return array
     */
    private function generateChartData($startDate)
    {
        // Get daily GMV for the last 7 days
        $dailyData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as gmv')
            )
            ->where('created_at', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        
        return [
            'labels' => $dailyData->pluck('date')->toArray(),
            'gmv' => $dailyData->pluck('gmv')->toArray()
        ];
    }
    
    /**
     * Get seller statistics
     *
     * @param  \Carbon\Carbon  $startDate
     * @return array
     */
    private function getSellerStatistics($startDate)
    {
        // Total sellers
        $totalSellers = Seller::count();
        
        // New sellers in the period
        $newSellers = Seller::where('created_at', '>=', $startDate)->count();
        
        // Active sellers (sellers with orders in the period)
        $activeSellers = Seller::whereHas('orders', function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        })->count();
        
        // Top selling sellers
        $topSellers = Seller::withCount(['orders as total_orders'])
                            ->withSum(['orders as total_sales'], 'total_amount')
                            ->orderBy('total_sales_sum', 'desc')
                            ->limit(5)
                            ->get()
                            ->map(function ($seller) {
                                return [
                                    'id' => $seller->id,
                                    'name' => $seller->name,
                                    'total_orders' => $seller->total_orders_count,
                                    'total_sales' => $seller->total_sales_sum
                                ];
                            });
        
        return [
            'total' => $totalSellers,
            'new' => $newSellers,
            'active' => $activeSellers,
            'top_sellers' => $topSellers
        ];
    }
    
    /**
     * Get moderation counts
     *
     * @return array
     */
    private function getModerationCounts()
    {
        // For now, using dummy data since we don't have actual moderation systems
        // In a real implementation, these would come from actual reports/moderation tables
        
        return [
            'violations_reported' => 24,
            'support_tickets' => 42,
            'withdrawal_requests' => 18,
            'pending_verifications' => 24
        ];
    }
    
    /**
     * Get user statistics
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserStats(Request $request)
    {
        $range = $request->get('range', '7days');
        $startDate = $this->calculateStartDate($range);
        
        // Total users
        $totalUsers = User::count();
        
        // New users in period
        $newUsers = User::where('created_at', '>=', $startDate)->count();
        
        // Active users (users with orders)
        $activeUsers = User::whereHas('orders', function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        })->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'new_users' => $newUsers,
                'active_users' => $activeUsers
            ]
        ]);
    }
    
    /**
     * Get product statistics
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductStats(Request $request)
    {
        $range = $request->get('range', '7days');
        $startDate = $this->calculateStartDate($range);
        
        // Total products
        $totalProducts = Product::count();
        
        // New products in period
        $newProducts = Product::where('created_at', '>=', $startDate)->count();
        
        // Active products (products with orders)
        $activeProducts = Product::whereHas('orderItems', function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        })->count();
        
        // Top selling products
        $topProducts = Product::withCount(['orderItems as total_sold'])
                              ->withSum(['orderItems as total_revenue'], 'price')
                              ->orderBy('total_revenue_sum', 'desc')
                              ->limit(5)
                              ->get()
                              ->map(function ($product) {
                                  return [
                                      'id' => $product->id,
                                      'name' => $product->name,
                                      'total_sold' => $product->total_sold_count,
                                      'total_revenue' => $product->total_revenue_sum
                                  ];
                              });
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_products' => $totalProducts,
                'new_products' => $newProducts,
                'active_products' => $activeProducts,
                'top_products' => $topProducts
            ]
        ]);
    }

    /**
     * Display payment verification page for admin
     */
    public function paymentVerification(Request $request)
    {
        // Query orders that need payment verification (with proof of payment)
        $query = Order::with(['user', 'seller', 'items.product', 'items.variant', 'shipping_address', 'payment'])
            ->whereHas('payment', function ($q) {
                $q->where('payment_status', 'pending_verification')
                  ->whereNotNull('proof_image');
            })
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan pencarian jika ada
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('seller', function ($sellerQuery) use ($search) {
                      $sellerQuery->where('store_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $pendingPayments = $query->paginate(10)->appends($request->query());

        return view('admin.payment_verification', compact('pendingPayments'));
    }

    /**
     * API endpoint to get pending payments that need verification
     */
    public function getPendingPayments(Request $request)
    {
        // Query orders that need payment verification (with proof of payment)
        $query = Order::with(['user', 'seller', 'items.product', 'payment'])
            ->whereHas('payment', function ($q) {
                $q->where('payment_status', 'pending_verification')
                  ->whereNotNull('proof_image');
            })
            ->orderBy('created_at', 'desc');

        // Apply search filter if provided
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('seller', function ($sellerQuery) use ($search) {
                      $sellerQuery->where('store_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $pendingPayments = $query->paginate($request->get('per_page', 10));

        // Format data for API response
        $formattedPayments = $pendingPayments->map(function($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name ?? 'Pelanggan Tidak Ditemukan',
                'customer_phone' => $order->user->phone ?? 'Tidak Tersedia',
                'total_amount' => $order->total_amount,
                'formatted_amount' => 'Rp' . number_format($order->total_amount, 0, ',', '.'),
                'proof_image' => $order->payment ? asset('storage/' . $order->payment->proof_image) : null,
                'created_at' => $order->created_at->format('d M Y H:i'),
                'payment_status' => $order->payment ? $order->payment->payment_status : 'Tidak Ditemukan',
                'seller_name' => $order->items->first() ? ($order->items->first()->product->seller->store_name ?? 'Penjual Tidak Ditemukan') : 'Tidak Ada Produk',
                'items' => $order->items->map(function($item) {
                    return [
                        'product_name' => $item->product->name ?? 'Produk Tidak Ditemukan',
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                        'formatted_subtotal' => 'Rp' . number_format($item->subtotal, 0, ',', '.')
                    ];
                })->toArray()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedPayments,
            'pagination' => [
                'current_page' => $pendingPayments->currentPage(),
                'last_page' => $pendingPayments->lastPage(),
                'total' => $pendingPayments->total(),
                'per_page' => $pendingPayments->perPage(),
                'from' => $pendingPayments->firstItem(),
                'to' => $pendingPayments->lastItem()
            ]
        ]);
    }
}