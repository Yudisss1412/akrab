<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerManagementController;
use App\Models\User;
use App\Models\Role;

// Route debugging untuk verifikasi sistem
Route::get('/debug-info', function() {
    return [
        'laravel_version' => app()->version(),
        'php_version' => phpversion(),
        'environment' => app()->environment(),
        'view_exists' => view()->exists('penjual.manajemen_promosi'),
        'file_exists' => file_exists(resource_path('views/penjual/manajemen_promosi.blade.view')),
        'route_works' => true
    ];
});

// Route debugging untuk mengecek data pembeli
Route::get('/debug-buyers', function() {
    $role = Role::where('name', 'buyer')->first();
    $roleExists = $role !== null;
    $buyerRoleId = $role ? $role->id : null;

    $allUsersCount = User::count();
    $usersWithBuyerRoleId = $role ? User::where('role_id', $role->id)->count() : 0;
    $usersWithBuyerRoleCheck = User::whereHas('role', function($q) {
        $q->where('name', 'buyer');
    })->count();

    $sampleBuyers = $role ? User::where('role_id', $role->id)->limit(5)->get(['id', 'name', 'email', 'role_id']) : collect([]);

    return [
        'role_exists' => $roleExists,
        'buyer_role_id' => $buyerRoleId,
        'total_users' => $allUsersCount,
        'users_with_buyer_role_id' => $usersWithBuyerRoleId,
        'users_with_buyer_role_check' => $usersWithBuyerRoleCheck,
        'sample_buyers' => $sampleBuyers
    ];
});

// Route untuk debugging controller
Route::get('/debug-controller', function() {
    $tab = request('tab', 'sellers');
    $buyerRole = \App\Models\Role::where('name', 'buyer')->first();
    $buyerRoleId = $buyerRole ? $buyerRole->id : null;

    if ($tab === 'buyers' && $buyerRoleId) {
        $buyersQuery = \App\Models\User::where('role_id', $buyerRoleId);
        $buyers = $buyersQuery->orderBy('created_at', 'desc')->with(['role', 'orders'])->paginate(15);
        return [
            'tab' => $tab,
            'buyer_role_id' => $buyerRoleId,
            'buyers_count' => $buyers->count(),
            'buyers_data' => $buyers->items()
        ];
    } else {
        return [
            'tab' => $tab,
            'buyer_role_id' => $buyerRoleId,
            'buyers_count' => 0,
            'message' => $tab !== 'buyers' ? 'Tab is not buyers' : 'Buyer role not found'
        ];
    }
});

// Route untuk Manajemen Promosi - ditempatkan di awal untuk menghindari konflik
Route::get('/penjual/promosi', [App\Http\Controllers\PromotionController::class, 'index'])->name('penjual.promosi');

Route::get('/penjual/promosi/diskon', [App\Http\Controllers\PromotionController::class,
'createDiscount'])->name('penjual.promosi.diskon');
Route::post('/penjual/promosi/diskon', [App\Http\Controllers\PromotionController::class,
'storeDiscount'])->name('penjual.promosi.diskon.store');
Route::get('/penjual/promosi/voucher', [App\Http\Controllers\PromotionController::class,
'createVoucher'])->name('penjual.promosi.voucher');
Route::post('/penjual/promosi/voucher', [App\Http\Controllers\PromotionController::class,
'storeVoucher'])->name('penjual.promosi.voucher.store');
Route::get('/penjual/promosi/{id}/edit', [App\Http\Controllers\PromotionController::class, 'edit'])->name('penjual.promosi.edit');
Route::put('/penjual/promosi/{id}/update', [App\Http\Controllers\PromotionController::class, 'updateVoucher'])->name('penjual.promosi.update');
Route::put('/penjual/promosi/{id}/update/discount', [App\Http\Controllers\PromotionController::class, 'updateDiscount'])->name('penjual.promosi.discount.update');
Route::post('/penjual/promosi/{id}/nonaktifkan', [App\Http\Controllers\PromotionController::class,
'nonaktifkan'])->name('penjual.promosi.nonaktifkan');
Route::delete('/penjual/promosi/{id}', [App\Http\Controllers\PromotionController::class,
'destroy'])->name('penjual.promosi.destroy');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Seller Management Routes
Route::prefix('admin')->name('sellers.')->group(function () {
    Route::get('/sellers', [SellerManagementController::class, 'index'])->name('index');
    Route::get('/sellers/buyers', function () {
        return redirect()->route('sellers.index', ['tab' => 'buyers']);
    })->name('buyers.index');  // Tautan langsung ke tab pembeli
    Route::get('/sellers/create', [SellerManagementController::class, 'create'])->name('create');
    Route::post('/sellers', [SellerManagementController::class, 'store'])->name('store');
    Route::get('/sellers/{seller}', [SellerManagementController::class, 'show'])->name('show');
    Route::get('/sellers/{seller}/edit', [SellerManagementController::class, 'edit'])->name('edit');
    Route::put('/sellers/{seller}', [SellerManagementController::class, 'update'])->name('update');
    Route::delete('/sellers/{seller}', [SellerManagementController::class, 'destroy'])->name('destroy');
    Route::post('/sellers/{seller}/suspend', [SellerManagementController::class, 'suspend'])->name('suspend');
    Route::post('/sellers/{seller}/activate', [SellerManagementController::class, 'activate'])->name('activate');
    Route::post('/sellers/bulk-action', [SellerManagementController::class, 'bulkAction'])->name('bulk_action');
    Route::get('/sellers/dashboard-stats', [SellerManagementController::class, 'getDashboardStats'])->name('dashboard_stats');

    // User management routes for buyers
    Route::post('/users/{user}/suspend', [SellerManagementController::class, 'suspendUser'])->name('suspend_user');
    Route::post('/users/{user}/activate', [SellerManagementController::class, 'activateUser'])->name('activate_user');
    Route::get('/users/{user}/history', [SellerManagementController::class, 'userHistory'])->name('user_history');
    Route::get('/users/{user}/edit', [SellerManagementController::class, 'editUser'])->name('edit_user');
    Route::put('/users/{user}', [SellerManagementController::class, 'updateUser'])->name('update_user');

    // Export routes
    Route::get('/sellers/export', [SellerManagementController::class, 'exportSellers'])->name('export_sellers');
    Route::get('/buyers/export', [SellerManagementController::class, 'exportBuyers'])->name('export_buyers');
});

Route::get('/welcome', function () {
    return view('customer.cust_welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('auth.login');
})->name('view');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/cust_welcome', [App\Http\Controllers\Customer\WelcomeController::class, 'index'])->name('cust.welcome');

Route::get('/halaman_produk', [App\Http\Controllers\ProductController::class, 'index'])->name('halaman.produk');
Route::get('/produk', [App\Http\Controllers\ProductController::class, 'getAllProducts'])->name('produk.api');
Route::get('/produk_detail/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('produk.detail');
Route::get('/produk/search', [App\Http\Controllers\ProductController::class, 'search'])->name('produk.search');
Route::get('/produk/kategori/{category}', [App\Http\Controllers\ProductController::class, 'byCategory'])->name('produk.kategori');
Route::get('/toko/{seller}', [App\Http\Controllers\SellerController::class, 'show'])->name('toko.show');

Route::get('/profil', [App\Http\Controllers\Customer\ProfileController::class, 'show'])->name('profil.pembeli');
Route::get('/edit_profil', [App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('edit.profil');
Route::put('/profil_pembeli/update', [App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profil.pembeli.update');

// halaman keranjang
Route::get('/keranjang', [App\Http\Controllers\CartController::class, 'index'])->name('keranjang');
Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

// API routes for products
Route::get('/api/products/popular', [App\Http\Controllers\ProductController::class, 'popular']);
Route::get('/api/products/{id}', [App\Http\Controllers\ProductController::class, 'apiShow']);

Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');

Route::get('/pengiriman', [App\Http\Controllers\CheckoutController::class, 'showShipping'])->name('cust.pengiriman');
Route::get('/pengiriman/{order}', [App\Http\Controllers\CheckoutController::class, 'showShipping'])->name('cust.pengiriman.order');
Route::post('/pengiriman/update-shipping', [App\Http\Controllers\CheckoutController::class,
'updateShipping'])->name('cust.pengiriman.update');

Route::put('/pengiriman/update-address/{orderId}', [App\Http\Controllers\CheckoutController::class,
'updateShippingAddress'])->name('cust.pengiriman.update.address');


Route::get('/pembayaran', [App\Http\Controllers\CheckoutController::class, 'showPayment'])->name('cust.pembayaran');
Route::post('/pembayaran/process', [App\Http\Controllers\CheckoutController::class, 'processPayment'])->name('payment.process');
Route::get('/pembayaran/confirm', [App\Http\Controllers\CheckoutController::class, 'showPaymentConfirmation'])->name('payment.confirmation');
Route::post('/payment/upload-proof', [App\Http\Controllers\PaymentController::class, 'uploadProof'])->name('payment.upload-proof');
Route::post('/payment/update-status', [App\Http\Controllers\PaymentController::class, 'updateStatus'])->name('payment.update-status');

// Debug route untuk melihat informasi penjual
Route::get('/debug/order-seller/{orderNumber}', function($orderNumber) {
    $order = App\Models\Order::where('order_number', $orderNumber)
                ->with(['items.product.seller'])
                ->first();

    if (!$order) {
        return 'Order not found';
    }

    $seller = $order->items->first() ? $order->items->first()->product->seller : null;

    return [
        'order' => $order->order_number,
        'seller' => $seller ? [
            'id' => $seller->id,
            'store_name' => $seller->store_name,
            'bank_account_number' => $seller->bank_account_number,
            'bank_name' => $seller->bank_name,
            'account_holder_name' => $seller->account_holder_name
        ] : 'No seller found'
    ];
})->name('debug.order.seller');

// Routes untuk ulasan
Route::get('/ulasan', [App\Http\Controllers\ReviewController::class, 'index'])->name('ulasan.index');
Route::post('/ulasan', [App\Http\Controllers\ReviewController::class, 'store'])->name('ulasan.store');
Route::get('/ulasan/{orderItemId}/create', [App\Http\Controllers\ReviewController::class, 'create'])->name('ulasan.create');
Route::get('/ulasan/produk/{productId}', [App\Http\Controllers\ReviewController::class,
'showByProduct'])->name('ulasan.show_by_product');

// API endpoint untuk mengambil ulasan pengguna
Route::get('/api/reviews', function() {
    if (!auth()->check()) {
        return response()->json(['reviews' => []]);
    }

    $reviews = \App\Models\Review::with(['product', 'product.seller'])
        ->where('user_id', auth()->id())
        ->latest()
        ->get()
        ->map(function($review) {
            return [
                'id' => $review->id,
                'product_name' => $review->product->name ?? 'Produk Tidak Ditemukan',
                'shop_name' => $review->product->seller->name ?? 'Toko Tidak Diketahui',
                'product_image' => $review->product->main_image ? asset('storage/' . $review->product->main_image) :
asset('src/placeholder_produk.png'),
                'rating' => $review->rating,
                'review_text' => $review->review_text,
                'media' => $review->media ? array_map(function($path) {
                    return asset('storage/' . $path);
                }, json_decode($review->media, true)) : null,
                'created_at' => $review->created_at->format('d M Y')
            ];
        });

    return response()->json(['reviews' => $reviews]);
})->name('api.reviews');

// API endpoint untuk mengambil riwayat pesanan pengguna
Route::get('/api/orders', function() {
    if (!auth()->check()) {
        return response()->json(['orders' => []]);
    }

    $orders = \App\Models\Order::with(['items.product', 'shipping_address', 'logs'])
        ->where('user_id', auth()->id())
        ->latest()
        ->limit(5) // Ambil 5 pesanan terbaru
        ->get()
        ->map(function($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at->format('d M Y'),
                'items' => $order->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'image' => $item->product->main_image ? asset('storage/' . $item->product->main_image) : asset('src/placeholder_produk.png'),
                    ];
                }),
                'shipping_address' => $order->shipping_address ? [
                    'name' => $order->shipping_address->name,
                    'phone' => $order->shipping_address->phone,
                    'address' => $order->shipping_address->address,
                    'city' => $order->shipping_address->city,
                    'province' => $order->shipping_address->province,
                    'postal_code' => $order->shipping_address->postal_code,
                ] : null,
                'latest_log' => $order->logs->sortByDesc('created_at')->first(),
            ];
        });

    return response()->json(['orders' => $orders]);
})->name('api.orders');

// API endpoint untuk mengambil sesi login aktif pengguna
Route::get('/api/active-sessions', [App\Http\Controllers\ActiveSessionController::class, 'index'])->name('api.active_sessions')->middleware('auth');

// API endpoint untuk mengakhiri sesi tertentu
Route::delete('/api/active-sessions/{sessionId}', [App\Http\Controllers\ActiveSessionController::class, 'destroy'])->name('api.active_sessions.destroy')->middleware('auth');

// API endpoint untuk mengakhiri semua sesi kecuali sesi saat ini
Route::delete('/api/active-sessions', [App\Http\Controllers\ActiveSessionController::class, 'destroyAll'])->name('api.active_sessions.destroy_all')->middleware('auth');

Route::get('/halaman_ulasan', [App\Http\Controllers\ReviewController::class, 'halamanUlasan'])->name('halaman_ulasan');

Route::view('/halaman-ulasan', 'customer.koleksi.halaman_ulasan')->name('halaman-ulasan');

// API endpoint for fetching user's reviews
Route::get('/api/user-reviews', [App\Http\Controllers\ReviewController::class, 'getUserReviews'])->name('api.user_reviews');

// API endpoints for tickets
Route::get('/api/tickets', [App\Http\Controllers\TicketController::class, 'apiGetTickets'])->name('api.tickets');
Route::post('/api/tickets/{id}/replies', [App\Http\Controllers\TicketController::class, 'addTicketReply'])->name('api.tickets.replies');
Route::delete('/api/tickets/{ticketId}/replies/{replyId}', [App\Http\Controllers\TicketController::class, 'deleteTicketReply'])->name('api.tickets.replies.delete');
Route::get('/api/staff', function () {
    // Return staff/admin users for ticket assignment
    $staff = \App\Models\User::whereHas('role', function($query) {
        $query->whereIn('name', ['admin', 'staff', 'support']);
    })->get(['id', 'name']);

    return response()->json(['staff' => $staff]);
})->name('api.staff');

// API endpoint for updating user's review
Route::put('/api/reviews/{reviewId}', [App\Http\Controllers\ReviewController::class, 'updateReview'])->name('api.update_review');

// API endpoint for deleting user's review
Route::delete('/api/reviews/{reviewId}', [App\Http\Controllers\ReviewController::class,
'deleteReview'])->name('api.delete_review');

Route::get('/halaman_wishlist', function () {
    // Ambil data wishlist dari database untuk user yang sedang login
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $wishlists = \App\Models\Wishlist::with(['product.seller', 'product.images'])
        ->where('user_id', auth()->id())
        ->get()
        ->map(function($wishlist) {
            $product = $wishlist->product;
            return [
                'id' => $wishlist->id,
                'product_id' => $product->id,
                'title' => $product->name,
                'price' => $product->price,
                'img' => $product->main_image ? asset('storage/' . $product->main_image) : asset('src/placeholder.png'),
                'shop' => $product->seller ? $product->seller->name : 'Toko Tidak Diketahui',
                'date' => $wishlist->created_at->format('d M Y'),
                'liked' => true,
                'url' => '/produk/' . $product->id
            ];
        });

    return view('customer.koleksi.halaman_wishlist', compact('wishlists'));
})->name('halaman_wishlist')->middleware('auth');

// API endpoint untuk wishlist di halaman profil
Route::get('/api/customer/wishlist', function () {
    // Ambil data wishlist dari database untuk user yang sedang login
    if (!auth()->check()) {
        return response()->json([]);
    }

    $wishlists = \App\Models\Wishlist::with(['product.seller'])
        ->where('user_id', auth()->id())
        ->get()
        ->map(function($wishlist) {
            $product = $wishlist->product;
            return [
                'id' => $wishlist->id,
                'product_id' => $product->id,
                'title' => $product->name,
                'price' => $product->price,
                'img' => $product->main_image ? asset('storage/' . $product->main_image) : asset('src/placeholder.png'),
                'shop' => $product->seller ? $product->seller->name : 'Toko Tidak Diketahui',
                'date' => $wishlist->created_at->format('d M Y'),
                'liked' => true,
                'url' => '/produk/' . $product->id
            ];
        });

    return response()->json($wishlists);
})->name('api.wishlist')->middleware('auth');

Route::get('/dashboard_penjual', function () {
    return view('penjual.dashboard_penjual');
})->name('dashboard.penjual');

Route::middleware(['auth', 'seller.role'])->group(function () {
    Route::get('/profil_penjual', [App\Http\Controllers\Seller\ProfileController::class, 'show'])->name('profil.penjual');

    Route::get('/edit_profil_penjual', [App\Http\Controllers\Seller\ProfileController::class, 'edit'])->name('edit.profil.penjual');

    Route::put('/profil_penjual', [App\Http\Controllers\Seller\ProfileController::class, 'update'])->name('profil.penjual.update');

    Route::post('/geocode-address', [App\Http\Controllers\Seller\ProfileController::class, 'geocodeAddress'])->name('seller.geocode.address');
});

Route::get('/invoice', function () {
    return view('customer.transaksi.invoice');
})->name('invoice');

Route::get('/kategori', function () {
    return view('customer.kategori.kategori');
})->name('kategori');

// Route untuk masing-masing kategori yang langsung menuju controller
Route::get('/kategori/kuliner', function () {
    $controller = new App\Http\Controllers\ProductController();
    return $controller->showCategoryPage('kuliner', request());
})->name('kategori.kuliner');

Route::get('/kategori/fashion', function () {
    $controller = new App\Http\Controllers\ProductController();
    return $controller->showCategoryPage('fashion', request());
})->name('kategori.fashion');

Route::get('/kategori/kerajinan', function () {
    $controller = new App\Http\Controllers\ProductController();
    return $controller->showCategoryPage('kerajinan', request());
})->name('kategori.kerajinan');

Route::get('/kategori/berkebun', function () {
    $controller = new App\Http\Controllers\ProductController();
    return $controller->showCategoryPage('berkebun', request());
})->name('kategori.berkebun');

Route::get('/kategori/kesehatan', function () {
    $controller = new App\Http\Controllers\ProductController();
    return $controller->showCategoryPage('kesehatan', request());
})->name('kategori.kesehatan');

Route::get('/kategori/mainan', function () {
    $controller = new App\Http\Controllers\ProductController();
    return $controller->showCategoryPage('mainan', request());
})->name('kategori.mainan');

Route::get('/kategori/hampers', function () {
    $controller = new App\Http\Controllers\ProductController();
    return $controller->showCategoryPage('hampers', request());
})->name('kategori.hampers');

Route::get('/dashboard_admin', function () {
    return view('admin.dashboard');
})->name('dashboard.admin');

Route::get('/profil_admin', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profil.admin');

Route::get('/edit_profil_admin', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('edit.profil.admin');

Route::put('/admin/profil/update', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profil.admin.update');

// Admin dashboard related routes
Route::middleware(['auth', 'admin.role'])->group(function () {
    Route::get('/support/tickets', [App\Http\Controllers\TicketController::class, 'index'])->name('support.tickets');
    Route::get('/support/tickets/{id}', [App\Http\Controllers\TicketController::class, 'show'])->name('support.tickets.detail');
    Route::post('/support/tickets/{id}/update-status', [App\Http\Controllers\TicketController::class, 'updateStatus'])->name('support.tickets.update-status');
    Route::get('/api/tickets/{id}/messages', [App\Http\Controllers\TicketController::class, 'getTicketMessages'])->name('api.tickets.messages');

    // Withdrawal Request routes
    Route::get('/withdrawal-requests', [App\Http\Controllers\WithdrawalRequestController::class, 'index'])->name('withdrawal.requests');
    Route::get('/api/withdrawals/{id}', [App\Http\Controllers\WithdrawalRequestController::class, 'show'])->name('api.withdrawals.show');
    Route::post('/api/withdrawals/{id}/approve', [App\Http\Controllers\WithdrawalRequestController::class, 'approve'])->name('api.withdrawals.approve');
    Route::post('/api/withdrawals/{id}/reject', [App\Http\Controllers\WithdrawalRequestController::class, 'reject'])->name('api.withdrawals.reject');
    Route::post('/api/withdrawals/{id}/process', [App\Http\Controllers\WithdrawalRequestController::class, 'process'])->name('api.withdrawals.process');
    Route::post('/admin/withdrawals/approve-bulk', [App\Http\Controllers\WithdrawalRequestController::class, 'approveBulk'])->name('admin.withdrawals.approve-bulk');
    Route::post('/admin/withdrawals/reject-bulk', [App\Http\Controllers\WithdrawalRequestController::class, 'rejectBulk'])->name('admin.withdrawals.reject-bulk');
    Route::get('/admin/withdrawals/export', [App\Http\Controllers\WithdrawalRequestController::class, 'export'])->name('admin.withdrawals.export');
});





Route::get('/commission/settings', function () {
    return '<h1>Pengaturan Komisi</h1><p>Halaman untuk mengatur pengaturan komisi platform.</p>';
})->name('commission.settings');

Route::get('/send/announcement', function () {
    return '<h1>Kirim Pengumuman</h1><p>Halaman untuk mengirim pengumuman ke pengguna.</p>';
})->name('send.announcement');

Route::get('/reports/violations', [App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.violations');
Route::get('/reports/violations/filter', [App\Http\Controllers\Admin\ReportsController::class, 'filter'])->name('reports.violations.filter');
Route::get('/reports/violations/{id}', [App\Http\Controllers\Admin\ReportsController::class, 'show'])->name('reports.violations.detail');
Route::put('/reports/violations/{id}/status', [App\Http\Controllers\Admin\ReportsController::class, 'updateStatus'])->name('reports.violations.update_status');

// Product Management Routes
// Product Management Routes for Admin
Route::middleware(['auth', 'admin.role'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/produk', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('produk.index');
    Route::put('/produk/{id}/approve', [App\Http\Controllers\Admin\ProductController::class, 'approveProduct'])->name('produk.approve');
    Route::put('/produk/{id}/reject', [App\Http\Controllers\Admin\ProductController::class, 'rejectProduct'])->name('produk.reject');
    Route::put('/produk/{id}/suspend', [App\Http\Controllers\Admin\ProductController::class, 'suspendProduct'])->name('produk.suspend');
    Route::put('/produk/{id}/status', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('produk.update_status');
    Route::delete('/produk/{id}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('produk.delete');

    // Review Management Routes
    Route::put('/reviews/{id}/approve', [App\Http\Controllers\Admin\ProductController::class, 'approveReview'])->name('reviews.approve');
    Route::put('/reviews/{id}/reject', [App\Http\Controllers\Admin\ProductController::class, 'rejectReview'])->name('reviews.reject');
    Route::delete('/reviews/{id}', [App\Http\Controllers\Admin\ProductController::class, 'deleteReview'])->name('reviews.delete');
});


Route::get('/penjual/produk', [App\Http\Controllers\Seller\ProductController::class, 'index'])->name('penjual.produk');
Route::get('/penjual/produk/create', [App\Http\Controllers\Seller\ProductController::class,
'create'])->name('penjual.produk.create');
Route::post('/penjual/produk', [App\Http\Controllers\Seller\ProductController::class, 'store'])->name('penjual.produk.store');
Route::get('/penjual/produk/{id}', [App\Http\Controllers\Seller\ProductController::class, 'show'])->name('penjual.produk.show');
Route::get('/penjual/produk/{id}/edit', [App\Http\Controllers\Seller\ProductController::class,
'edit'])->name('penjual.produk.edit');
Route::put('/penjual/produk/{id}', [App\Http\Controllers\Seller\ProductController::class,
'update'])->name('penjual.produk.update');
Route::delete('/penjual/produk/{id}', [App\Http\Controllers\Seller\ProductController::class,
'destroy'])->name('penjual.produk.destroy');
Route::put('/penjual/produk/{id}/stock', [App\Http\Controllers\Seller\ProductController::class,
'updateStock'])->name('penjual.produk.stock.update');
Route::delete('/penjual/product-image/{id}', [App\Http\Controllers\Seller\ProductController::class,
'destroyImage'])->name('penjual.product.image.delete');

// Route untuk manajemen ulasan penjual
Route::get('/penjual/ulasan', [App\Http\Controllers\Seller\ReviewController::class, 'index'])->name('seller.reviews.index');
Route::post('/penjual/reviews/{reviewId}/reply', [App\Http\Controllers\Seller\ReviewController::class, 'reply'])->name('seller.reviews.reply');
Route::get('/penjual/reviews/api', [App\Http\Controllers\Seller\ReviewController::class, 'getReviewsJson'])->name('seller.reviews.api');
Route::get('/penjual/reviews/recent', [App\Http\Controllers\Seller\ReviewController::class, 'getRecentReviews'])->name('seller.reviews.recent');

Route::get('/penjual/pesanan', [App\Http\Controllers\Seller\SellerOrderController::class, 'index'])->name('penjual.pesanan');
Route::get('/penjual/pesanan/recent', [App\Http\Controllers\Seller\SellerOrderController::class, 'getRecentOrders'])->name('penjual.pesanan.recent');
Route::get('/penjual/riwayat-penjualan', [App\Http\Controllers\Seller\SellerOrderController::class, 'salesHistory'])->name('penjual.riwayat.penjualan');
Route::get('/penjual/urgent-tasks', [App\Http\Controllers\Seller\SellerOrderController::class, 'getUrgentTasks'])->name('penjual.urgent.tasks');
Route::get('/penjual/order-status-counts', [App\Http\Controllers\Seller\SellerOrderController::class, 'getOrderStatusCounts'])->name('penjual.order.status.counts');
Route::get('/penjual/komplain-retur', [App\Http\Controllers\Seller\SellerOrderController::class, 'complaintsAndReturns'])->name('penjual.komplain.retur');

// API route untuk komplain & retur
Route::get('/api/complaints', [App\Http\Controllers\Seller\ReviewController::class, 'getLowRatingReviews'])->name('api.complaints');
Route::get('/api/returns', [App\Http\Controllers\Seller\SellerOrderController::class, 'getReturnsData'])->name('api.returns');
Route::post('/api/returns/{id}/approve', [App\Http\Controllers\Seller\SellerOrderController::class, 'approveReturn'])->name('api.returns.approve');
Route::post('/api/returns/{id}/reject', [App\Http\Controllers\Seller\SellerOrderController::class, 'rejectReturn'])->name('api.returns.reject');
Route::post('/api/returns/{id}/complete', [App\Http\Controllers\Seller\SellerOrderController::class, 'completeReturn'])->name('api.returns.complete');

// API endpoint untuk komplain-retur yang digunakan oleh frontend
Route::get('/penjual/komplain-retur/api', [App\Http\Controllers\Seller\SellerOrderController::class, 'getComplaintsAndReturnsJson'])->name('seller.complaints.returns.api');

Route::get('/penjual/saldo', [App\Http\Controllers\WithdrawalController::class, 'showBalancePage'])->name('penjual.saldo');

// Debug route for filtering issue - with proper auth
Route::middleware(['auth'])->get('/debug-filtering', function(\Illuminate\Http\Request $request) {
    // Cek parameter
    $filterStar = $request->get('filter_star');
    $filterReply = $request->get('filter_reply');
    $sortBy = $request->get('sort_by');

    // Ambil user penjual yang sedang login
    $user = auth()->user();
    if (!$user || $user->role->name !== 'seller') {
        return response()->json(['error' => 'Seller not authenticated'], 403);
    }

    $seller = \App\Models\Seller::where('user_id', $user->id)->first();
    if (!$seller) {
        return response()->json(['error' => 'Seller profile not found'], 403);
    }

    // Ambil produk milik seller
    $products = $seller->products()->pluck('id')->toArray();

    // Bangun query
    $query = \App\Models\Review::with(['user', 'product'])->whereIn('product_id', $products);

    // Log query sebelum filter
    $beforeCount = $query->count();

    // Terapkan filter
    if ($filterStar) {
        $query->where('rating', $filterStar);
    }

    if ($filterReply) {
        if ($filterReply === 'replied') {
            $query->whereNotNull('reply');
        } else if ($filterReply === 'pending') {
            $query->whereNull('reply');
        }
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
        default: // newest
            $query->orderBy('created_at', 'desc');
            break;
    }

    $reviews = $query->get();
    $afterCount = $reviews->count();

    return response()->json([
        'debug_info' => [
            'filter_star' => $filterStar,
            'filter_reply' => $filterReply,
            'sort_by' => $sortBy,
            'product_ids' => $products,
            'total_products' => count($products),
            'total_reviews_before_filter' => $beforeCount,
            'total_reviews_after_filter' => $afterCount,
            'query_debug' => [
                'filter_star_applied' => (bool)$filterStar,
                'filter_reply_applied' => (bool)$filterReply,
                'sort_applied' => $sortBy ?: 'newest'
            ]
        ],
        'reviews' => $reviews->map(function($review) {
            return [
                'id' => $review->id,
                'user_name' => $review->user->name,
                'product_name' => $review->product->name,
                'rating' => $review->rating,
                'has_reply' => !empty($review->reply),
                'reply' => $review->reply,
                'status' => $review->status
            ];
        })
    ]);
});





// Order Detail Route - parameter sebagai string karena menggunakan order number
Route::get('/penjual/pesanan/{order}', [App\Http\Controllers\Seller\SellerOrderController::class, 'show'])->name('penjual.pesanan.show');
Route::put('/penjual/pesanan/{order}/status', [App\Http\Controllers\Seller\SellerOrderController::class, 'updateStatus'])->name('penjual.pesanan.status.update');
Route::get('/penjual/pembayaran/verifikasi', [App\Http\Controllers\Seller\SellerOrderController::class, 'paymentVerification'])->name('penjual.pembayaran.verifikasi');
Route::get('/api/penjual/pembayaran/pending', [App\Http\Controllers\Seller\SellerOrderController::class, 'getPendingPayments'])->name('api.penjual.pembayaran.pending');

// Order Invoice Route
Route::get('/invoice/{order}', [App\Http\Controllers\OrderDetailController::class, 'invoice'])->name('order.invoice');

// Shipping Label Route - Simple approach
Route::get('/shipping-label/{order}', function ($order) {
    $orderData = App\Models\Order::where('order_number', $order)->firstOrFail();
    $orderData->load(['items.product', 'items.variant', 'user', 'shipping_address']);
    return view('shipping_label', ['order' => $orderData]);
})->name('shipping.label');

// Review Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/products/{productId}/reviews', [App\Http\Controllers\ReviewController::class, 'show'])->name('reviews.show');
});

// Wishlist Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [App\Http\Controllers\WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{id}', [App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::delete('/wishlist/product/{productId}', [App\Http\Controllers\WishlistController::class,
'destroyByProductId'])->name('wishlist.destroy-by-product');
    Route::post('/wishlist/{id}/move-to-cart', [App\Http\Controllers\WishlistController::class,
'moveToCart'])->name('wishlist.move-to-cart');
});

// Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/process', [App\Http\Controllers\PaymentController::class, 'process'])->name('payment.process.api');
    Route::post('/payment/callback', [App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
    Route::get('/payment/{order}', [App\Http\Controllers\PaymentController::class, 'show'])->name('payment.show');
});

// Withdrawal Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/withdrawal', [App\Http\Controllers\WithdrawalController::class, 'index'])->name('withdrawal.index');
    Route::post('/withdrawal', [App\Http\Controllers\WithdrawalController::class, 'store'])->name('withdrawal.store');
    Route::get('/withdrawal/{id}', [App\Http\Controllers\WithdrawalController::class, 'show'])->name('withdrawal.show');
    Route::post('/withdrawal/{id}/cancel', [App\Http\Controllers\WithdrawalController::class,
'cancel'])->name('withdrawal.cancel');

    // API endpoints untuk saldo dan riwayat transaksi
    Route::get('/api/withdrawal/balance', [App\Http\Controllers\WithdrawalController::class, 'getBalanceData'])->name('api.withdrawal.balance');
    Route::get('/api/withdrawal/transactions', [App\Http\Controllers\WithdrawalController::class, 'getTransactionHistory'])->name('api.withdrawal.transactions');
    Route::get('/api/withdrawal/history', [App\Http\Controllers\WithdrawalController::class, 'getWithdrawalHistory'])->name('api.withdrawal.history');
});

// Chat Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{userId}', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/contacts', [App\Http\Controllers\ChatController::class, 'getContacts'])->name('chat.contacts');
    Route::post('/chat/{userId}/mark-as-read', [App\Http\Controllers\ChatController::class,
'markAsRead'])->name('chat.mark-as-read');
});

// Role-based Dashboards
Route::middleware(['auth', 'admin.role'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\RoleDashboardController::class,
'showAdminDashboard'])->name('admin.dashboard');

    // Payment verification routes
    Route::get('/admin/payment/verification', [App\Http\Controllers\Admin\AdminDashboardController::class, 'paymentVerification'])->name('admin.payment.verification');

    // API endpoints for admin dashboard
    Route::get('/api/admin/dashboard/stats', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getDashboardStats'])->name('api.admin.dashboard.stats');
    Route::get('/api/admin/dashboard/users', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getUserStats'])->name('api.admin.dashboard.users');
    Route::get('/api/admin/dashboard/products', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getProductStats'])->name('api.admin.dashboard.products');
    Route::get('/api/admin/payment/pending', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getPendingPayments'])->name('api.admin.payment.pending');

    // Test endpoint for creating a product
    Route::post('/api/admin/test-create-product', function () {
        try {
            $product = \App\Models\Product::create([
                'name' => 'Test Produk',
                'description' => 'Deskripsi test produk',
                'price' => 100000,
                'category_id' => 1,
                'stock' => 10,
                'weight' => 500,
                'seller_id' => 2,
                'status' => 'aktif',
                'min_order' => 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dibuat',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat produk: ' . $e->getMessage()
            ], 500);
        }
    })->name('api.admin.test.create.product');
});

Route::middleware(['auth', 'seller.role'])->group(function () {
    Route::get('/penjual/dashboard', [App\Http\Controllers\Seller\DashboardController::class, 'show'])->name('seller.dashboard');
});

Route::middleware(['auth', 'customer.role'])->group(function () {
    Route::get('/customer/dashboard', [App\Http\Controllers\RoleDashboardController::class,
'showCustomerDashboard'])->name('customer.dashboard');
    Route::get('/customer/riwayat-pesanan', [App\Http\Controllers\OrderHistoryController::class, 'index'])->name('customer.order.history');

    // Customer ticket routes
    Route::get('/customer/tickets', [App\Http\Controllers\TicketController::class, 'getTicketsByUser'])->name('customer.tickets');
    Route::get('/customer/tickets/{id}', [App\Http\Controllers\TicketController::class, 'show'])->name('customer.tickets.detail');
    Route::get('/tickets/create', [App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
});

// Authentication Routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/reset', [App\Http\Controllers\ResetPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\ResetPasswordController::class, 'reset'])->name('password.update');

// General dashboard route that shows different content based on role
Route::middleware(['auth'])->get('/dashboard', [App\Http\Controllers\RoleDashboardController::class,
'showRoleBasedDashboard'])->name('dashboard');

// API routes for categories
Route::middleware(['auth', 'admin.role'])->group(function () {
    Route::post('/api/categories', [App\Http\Controllers\Api\CategoryController::class, 'store']);
    Route::get('/api/categories/{id}/product-count', [App\Http\Controllers\Api\CategoryController::class, 'getProductCount']);
    Route::delete('/api/categories/{id}', [App\Http\Controllers\Api\CategoryController::class, 'destroy']);
    Route::put('/api/categories/{id}', [App\Http\Controllers\Api\CategoryController::class, 'update']);
    Route::get('/api/categories/{id}', [App\Http\Controllers\Api\CategoryController::class, 'show']);
});

// API routes for subcategories
Route::middleware(['auth', 'admin.role'])->group(function () {
    Route::get('/api/categories/{id}/subcategories', [App\Http\Controllers\Api\SubcategoryController::class, 'getSubcategoriesByCategory']);
    Route::get('/api/subcategories/{id}/product-count', [App\Http\Controllers\Api\SubcategoryController::class, 'getProductCount']);
    Route::delete('/api/subcategories/{id}', [App\Http\Controllers\Api\SubcategoryController::class, 'destroy']);
    Route::post('/api/subcategories', [App\Http\Controllers\Api\SubcategoryController::class, 'store']);
    Route::put('/api/subcategories/{id}', [App\Http\Controllers\Api\SubcategoryController::class, 'update']);
    Route::get('/api/subcategories/{id}', [App\Http\Controllers\Api\SubcategoryController::class, 'show']);
});


