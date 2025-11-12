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

Route::get('/cust_welcome', function () {
    return view('customer.cust_welcome');
})->name('cust.welcome');

Route::get('/halaman_produk', [App\Http\Controllers\ProductController::class, 'index'])->name('halaman.produk');
Route::get('/produk', [App\Http\Controllers\ProductController::class, 'getAllProducts'])->name('produk.api');
Route::get('/produk_detail/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('produk.detail');
Route::get('/produk/search', [App\Http\Controllers\ProductController::class, 'search'])->name('produk.search');
Route::get('/produk/kategori/{category}', [App\Http\Controllers\ProductController::class, 'byCategory'])->name('produk.kategori');

Route::get('/profil', function () {
    return view('customer.profil.profil_pembeli');
})->name('profil.pembeli');

Route::get('/edit_profil', function () {
    return view('customer.profil.edit_profil');
})->name('edit.profil');

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

Route::get('/pembayaran', [App\Http\Controllers\CheckoutController::class, 'showPayment'])->name('cust.pembayaran');
Route::post('/pembayaran/process', [App\Http\Controllers\CheckoutController::class, 'processPayment'])->name('payment.process');

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
                'product_image' => $review->product->image ? asset('storage/' . $review->product->image) : 
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
                        'image' => $item->product->image ? asset('storage/' . $item->product->image) : asset('src/placeholder_produk.png'),
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
Route::post('/api/tickets/{id}/replies', function ($id) {
    // In a real implementation, this would store ticket replies
    // For now, we return a success response
    return response()->json(['success' => true, 'message' => 'Balasan berhasil ditambahkan']);
})->name('api.tickets.replies');
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
                'img' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
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
                'img' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
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

Route::get('/profil_penjual', function () {
    return view('penjual.profil_penjual');
})->name('profil.penjual');

Route::get('/edit_profil_penjual', function () {
    return view('penjual.edit_profil_penjual');
})->name('edit.profil.penjual');

Route::get('/invoice', function () {
    return view('customer.transaksi.invoice');
})->name('invoice');

Route::get('/kategori', function () {
    return view('customer.kategori.kategori');
})->name('kategori');

Route::get('/kategori/kuliner', function () {
    // Ambil produk dari database sesuai kategori
    $category = \App\Models\Category::where('name', 'Kuliner')->first();
    
    $products = collect(); // Inisialisasi sebagai collection kosong
    if ($category) {
        $products = \App\Models\Product::with(['variants', 'seller', 'category'])
                     ->where('category_id', $category->id)
                     ->where('status', 'active')
                     ->get();
    }
    
    // Format data produk
    $formattedProducts = $products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
        ];
    });
    
    // Bagi produk ke dalam chunk untuk pagination
    $productChunks = $formattedProducts->chunk(8);
    
    // Siapkan data untuk setiap halaman
    $pageData = [];
    for ($i = 1; $i <= min(5, count($productChunks)); $i++) {
        $chunk = $productChunks->get($i - 1, collect([]))->values();
        $pageData["page_{$i}_products"] = $chunk->toArray();
    }
    
    return view('customer.kategori.kuliner', array_merge([
        'categoryTitle' => 'Kuliner',
        'categoryDescription' => 'Temukan berbagai produk kuliner menarik dari UMKM lokal',
    ], $pageData));
})->name('kategori.kuliner');

Route::get('/kategori/fashion', function () {
    // Ambil produk dari database sesuai kategori
    $category = \App\Models\Category::where('name', 'Fashion')->first();
    
    $products = collect(); // Inisialisasi sebagai collection kosong
    if ($category) {
        $products = \App\Models\Product::with(['variants', 'seller', 'category'])
                     ->where('category_id', $category->id)
                     ->where('status', 'active')
                     ->get();
    }
    
    // Format data produk
    $formattedProducts = $products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
        ];
    });
    
    // Bagi produk ke dalam chunk untuk pagination
    $productChunks = $formattedProducts->chunk(8);
    
    // Siapkan data untuk setiap halaman
    $pageData = [];
    for ($i = 1; $i <= min(5, count($productChunks)); $i++) {
        $chunk = $productChunks->get($i - 1, collect([]))->values();
        $pageData["page_{$i}_products"] = $chunk->toArray();
    }
    
    return view('customer.kategori.fashion', array_merge([
        'categoryTitle' => 'Fashion',
        'categoryDescription' => 'Temukan berbagai produk fashion menarik dari UMKM lokal',
    ], $pageData));
})->name('kategori.fashion');

Route::get('/kategori/kerajinan', function () {
    // Ambil produk dari database sesuai kategori
    $category = \App\Models\Category::where('name', 'Kerajinan Tangan')->first();
    
    $products = collect(); // Inisialisasi sebagai collection kosong
    if ($category) {
        $products = \App\Models\Product::with(['variants', 'seller', 'category'])
                     ->where('category_id', $category->id)
                     ->where('status', 'active')
                     ->get();
    }
    
    // Format data produk
    $formattedProducts = $products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
        ];
    });
    
    // Bagi produk ke dalam chunk untuk pagination
    $productChunks = $formattedProducts->chunk(8);
    
    // Siapkan data untuk setiap halaman
    $pageData = [];
    for ($i = 1; $i <= min(5, count($productChunks)); $i++) {
        $chunk = $productChunks->get($i - 1, collect([]))->values();
        $pageData["page_{$i}_products"] = $chunk->toArray();
    }
    
    return view('customer.kategori.kerajinan', array_merge([
        'categoryTitle' => 'Kerajinan Tangan',
        'categoryDescription' => 'Temukan berbagai produk kerajinan tangan unik dari UMKM lokal',
    ], $pageData));
})->name('kategori.kerajinan');

Route::get('/kategori/berkebun', function () {
    // Ambil produk dari database sesuai kategori
    $category = \App\Models\Category::where('name', 'Berkebun')->first();
    
    $products = collect(); // Inisialisasi sebagai collection kosong
    if ($category) {
        $products = \App\Models\Product::with(['variants', 'seller', 'category'])
                     ->where('category_id', $category->id)
                     ->where('status', 'active')
                     ->get();
    }
    
    // Format data produk
    $formattedProducts = $products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
        ];
    });
    
    // Bagi produk ke dalam chunk untuk pagination
    $productChunks = $formattedProducts->chunk(8);
    
    // Siapkan data untuk setiap halaman
    $pageData = [];
    for ($i = 1; $i <= min(5, count($productChunks)); $i++) {
        $chunk = $productChunks->get($i - 1, collect([]))->values();
        $pageData["page_{$i}_products"] = $chunk->toArray();
    }
    
    return view('customer.kategori.berkebun', array_merge([
        'categoryTitle' => 'Produk Berkebun',
        'categoryDescription' => 'Temukan berbagai produk berkebun alami dari UMKM lokal',
    ], $pageData));
})->name('kategori.berkebun');

Route::get('/kategori/kesehatan', function () {
    // Ambil produk dari database sesuai kategori
    $category = \App\Models\Category::where('name', 'Kesehatan')->first();
    
    $products = collect(); // Inisialisasi sebagai collection kosong
    if ($category) {
        $products = \App\Models\Product::with(['variants', 'seller', 'category'])
                     ->where('category_id', $category->id)
                     ->where('status', 'active')
                     ->get();
    }
    
    // Format data produk
    $formattedProducts = $products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
        ];
    });
    
    // Bagi produk ke dalam chunk untuk pagination
    $productChunks = $formattedProducts->chunk(8);
    
    // Siapkan data untuk setiap halaman
    $pageData = [];
    for ($i = 1; $i <= min(5, count($productChunks)); $i++) {
        $chunk = $productChunks->get($i - 1, collect([]))->values();
        $pageData["page_{$i}_products"] = $chunk->toArray();
    }
    
    return view('customer.kategori.kesehatan', array_merge([
        'categoryTitle' => 'Produk Kesehatan',
        'categoryDescription' => 'Temukan berbagai produk kesehatan alami dari UMKM lokal',
    ], $pageData));
})->name('kategori.kesehatan');

Route::get('/kategori/mainan', function () {
    // Ambil produk dari database sesuai kategori
    $category = \App\Models\Category::where('name', 'Mainan')->first();
    
    $products = collect(); // Inisialisasi sebagai collection kosong
    if ($category) {
        $products = \App\Models\Product::with(['variants', 'seller', 'category'])
                     ->where('category_id', $category->id)
                     ->where('status', 'active')
                     ->get();
    }
    
    // Format data produk
    $formattedProducts = $products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
        ];
    });
    
    // Bagi produk ke dalam chunk untuk pagination
    $productChunks = $formattedProducts->chunk(8);
    
    // Siapkan data untuk setiap halaman
    $pageData = [];
    for ($i = 1; $i <= min(5, count($productChunks)); $i++) {
        $chunk = $productChunks->get($i - 1, collect([]))->values();
        $pageData["page_{$i}_products"] = $chunk->toArray();
    }
    
    return view('customer.kategori.mainan', array_merge([
        'categoryTitle' => 'Mainan',
        'categoryDescription' => 'Temukan berbagai produk mainan edukatif dari UMKM lokal',
    ], $pageData));
})->name('kategori.mainan');

Route::get('/kategori/hampers', function () {
    // Ambil produk dari database sesuai kategori
    $category = \App\Models\Category::where('name', 'Hampers')->first();
    
    $products = collect(); // Inisialisasi sebagai collection kosong
    if ($category) {
        $products = \App\Models\Product::with(['variants', 'seller', 'category'])
                     ->where('category_id', $category->id)
                     ->where('status', 'active')
                     ->get();
    }
    
    // Format data produk
    $formattedProducts = $products->map(function($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
        ];
    });
    
    // Bagi produk ke dalam chunk untuk pagination
    $productChunks = $formattedProducts->chunk(8);
    
    // Siapkan data untuk setiap halaman
    $pageData = [];
    for ($i = 1; $i <= min(5, count($productChunks)); $i++) {
        $chunk = $productChunks->get($i - 1, collect([]))->values();
        $pageData["page_{$i}_products"] = $chunk->toArray();
    }
    
    return view('customer.kategori.hampers', array_merge([
        'categoryTitle' => 'Hampers',
        'categoryDescription' => 'Temukan berbagai produk hampers menarik dari UMKM lokal',
    ], $pageData));
})->name('kategori.hampers');

Route::get('/dashboard_admin', function () {
    return view('admin.dashboard');
})->name('dashboard.admin');

Route::get('/profil_admin', function () {
    return view('admin.profil_admin');
})->name('profil.admin');

Route::get('/edit_profil_admin', function () {
    return view('admin.edit_profil_admin');
})->name('edit.profil.admin');

Route::put('/admin/profil/update', function () {
    // Logika untuk menyimpan perubahan profil admin
    return redirect()->route('profil.admin')->with('success', 'Profil admin berhasil diperbarui!');
})->name('profil.admin.update');

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
Route::get('/penjual/komplain-retur', [App\Http\Controllers\Seller\SellerOrderController::class, 'complaintsAndReturns'])->name('penjual.komplain.retur');
Route::post('/api/returns/{id}/approve', [App\Http\Controllers\Seller\SellerOrderController::class, 'approveReturn'])->name('api.returns.approve');
Route::post('/api/returns/{id}/reject', [App\Http\Controllers\Seller\SellerOrderController::class, 'rejectReturn'])->name('api.returns.reject');
Route::post('/api/returns/{id}/complete', [App\Http\Controllers\Seller\SellerOrderController::class, 'completeReturn'])->name('api.returns.complete');

Route::get('/penjual/saldo', function () {
    return view('penjual.saldo_penarikan');
})->name('penjual.saldo');





// Order Detail Route - parameter sebagai string karena menggunakan order number
Route::get('/penjual/pesanan/{order}', [App\Http\Controllers\Seller\SellerOrderController::class, 'show'])->name('penjual.pesanan.show');
Route::put('/penjual/pesanan/{order}/status', [App\Http\Controllers\Seller\SellerOrderController::class, 'updateStatus'])->name('penjual.pesanan.status.update');

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
    
    // API endpoints for admin dashboard
    Route::get('/api/admin/dashboard/stats', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getDashboardStats'])->name('api.admin.dashboard.stats');
    Route::get('/api/admin/dashboard/users', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getUserStats'])->name('api.admin.dashboard.users');
    Route::get('/api/admin/dashboard/products', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getProductStats'])->name('api.admin.dashboard.products');
    
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
                'min_order' => 1,
                'ready_stock' => 10
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
    Route::get('/penjual/dashboard', function () {
        return view('penjual.dashboard');
    })->name('seller.dashboard');
});

Route::middleware(['auth', 'customer.role'])->group(function () {
    Route::get('/customer/dashboard', [App\Http\Controllers\RoleDashboardController::class, 
'showCustomerDashboard'])->name('customer.dashboard');
    Route::get('/customer/riwayat-pesanan', [App\Http\Controllers\OrderHistoryController::class, 'index'])->name('customer.order.history');
    
    // Customer ticket routes
    Route::get('/customer/tickets', [App\Http\Controllers\TicketController::class, 'getTicketsByUser'])->name('customer.tickets');
    Route::get('/tickets/create', [App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
});

// Authentication Routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// General dashboard route that shows different content based on role
Route::middleware(['auth'])->get('/dashboard', [App\Http\Controllers\RoleDashboardController::class, 
'showRoleBasedDashboard'])->name('dashboard');

// API route for filtering products
Route::get('/api/products/filter', [App\Http\Controllers\ProductController::class, 'filter'])->name('api.products.filter');

// API routes for categories
Route::post('/api/categories', [App\Http\Controllers\Api\CategoryController::class, 'store']);
Route::get('/api/categories/{id}/product-count', [App\Http\Controllers\Api\CategoryController::class, 'getProductCount']);
Route::delete('/api/categories/{id}', [App\Http\Controllers\Api\CategoryController::class, 'destroy']);

// API routes for subcategories
Route::get('/api/categories/{id}/subcategories', [App\Http\Controllers\Api\SubcategoryController::class, 'getSubcategoriesByCategory']);
Route::get('/api/subcategories/{id}/product-count', [App\Http\Controllers\Api\SubcategoryController::class, 'getProductCount']);
Route::delete('/api/subcategories/{id}', [App\Http\Controllers\Api\SubcategoryController::class, 'destroy']);


