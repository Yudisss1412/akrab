<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerManagementController;

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

// Route untuk Manajemen Promosi - ditempatkan di awal untuk menghindari konflik
Route::get('/penjual/promosi', function() {
    if(file_exists(resource_path('views/penjual/manajemen_promosi.blade.view'))) {
        return view('penjual.manajemen_promosi');
    } else {
        return response()->json(['error' => 'View file tidak ditemukan', 'path' => resource_path('views/penjual/manajemen_promosi.blade.view')], 404);
    }
})->name('penjual.promosi');

Route::get('/penjual/promosi/diskon', [App\Http\Controllers\PromotionController::class, 'createDiscount'])->name('penjual.promosi.diskon');
Route::get('/penjual/promosi/voucher', [App\Http\Controllers\PromotionController::class, 'createVoucher'])->name('penjual.promosi.voucher');
Route::get('/penjual/promosi/{id}/edit', [App\Http\Controllers\PromotionController::class, 'edit'])->name('penjual.promosi.edit');
Route::post('/penjual/promosi/{id}/nonaktifkan', [App\Http\Controllers\PromotionController::class, 'nonaktifkan'])->name('penjual.promosi.nonaktifkan');
Route::delete('/penjual/promosi/{id}', [App\Http\Controllers\PromotionController::class, 'destroy'])->name('penjual.promosi.destroy');

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
});

Route::get('/welcome', function () {
    return view('welcome');
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

Route::get('/pembayaran', [App\Http\Controllers\CheckoutController::class, 'showPayment'])->name('cust.pembayaran');
Route::post('/pembayaran/process', [App\Http\Controllers\CheckoutController::class, 'processPayment'])->name('payment.process');

Route::get('/halaman_ulasan', function () {
    return view('customer.koleksi.halaman_ulasan');
})->name('halaman_ulasan');

Route::view('/halaman-ulasan', 'customer.koleksi.halaman_ulasan')->name('halaman-ulasan');

Route::get('/halaman_wishlist', function () {
    return view('customer.koleksi.halaman_wishlist');
})->name('halaman_wishlist');

// API endpoint untuk wishlist di halaman profil
Route::get('/api/customer/wishlist', function () {
    // Contoh data dummy wishlist
    // Dalam implementasi sebenarnya, ini akan mengambil data dari database
    $wishlist = [
        ['id' => 1, 'title' => 'Xbox Series S With Series X', 'price' => 1008000, 'img' => asset('src/CangkirKeramik1.png'), 'shop' => 'Toko Elektronik Jaya', 'date' => '24 Agu 2025', 'liked' => true, 'url' => '/produk/1'],
        ['id' => 2, 'title' => 'iPhone 15 128GB', 'price' => 8670000, 'img' => asset('src/PiringKayu.png'), 'shop' => 'iStore Official', 'date' => '21 Agu 2025', 'liked' => true, 'url' => '/produk/2'],
    ];
    
    return response()->json($wishlist);
})->name('api.wishlist');

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
    return view('customer.kategori.kuliner');
})->name('kategori.kuliner');

Route::get('/kategori/fashion', function () {
    return view('customer.kategori.fashion');
})->name('kategori.fashion');

Route::get('/kategori/kerajinan', function () {
    return view('customer.kategori.kerajinan');
})->name('kategori.kerajinan');

Route::get('/kategori/berkebun', function () {
    return view('customer.kategori.berkebun');
})->name('kategori.berkebun');

Route::get('/kategori/kesehatan', function () {
    return view('customer.kategori.kesehatan');
})->name('kategori.kesehatan');

Route::get('/kategori/mainan', function () {
    return view('customer.kategori.mainan');
})->name('kategori.mainan');

Route::get('/kategori/hampers', function () {
    return view('customer.kategori.hampers');
})->name('kategori.hampers');

Route::get('/dashboard_admin', function () {
    return view('admin.dashboard_admin');
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
Route::get('/reports/violations', function () {
    return '<h1>Daftar Laporan Pelanggaran Penjual</h1><p>Halaman ini menampilkan daftar laporan pelanggaran dari penjual.</p>';
})->name('reports.violations');

Route::get('/support/tickets', function () {
    return '<h1>Daftar Tiket Bantahan</h1><p>Halaman ini menampilkan daftar tiket bantuan dari pengguna.</p>';
})->name('support.tickets');

Route::get('/withdrawal/requests', function () {
    return '<h1>Daftar Permintaan Penarikan Dana</h1><p>Halaman ini menampilkan daftar permintaan penarikan dana dari penjual.</p>';
})->name('withdrawal.requests');



Route::get('/commission/settings', function () {
    return '<h1>Pengaturan Komisi</h1><p>Halaman untuk mengatur pengaturan komisi platform.</p>';
})->name('commission.settings');

Route::get('/send/announcement', function () {
    return '<h1>Kirim Pengumuman</h1><p>Halaman untuk mengirim pengumuman ke pengguna.</p>';
})->name('send.announcement');

Route::get('/reports/violations', function () {
    return view('admin.reports_violations');
})->name('reports.violations');

Route::get('/reports/violations/{id}', function ($id) {
    return view('admin.report_detail', ['reportId' => $id]);
})->name('reports.violations.detail');

Route::get('/support/tickets', function () {
    return view('admin.support_tickets');
})->name('support.tickets');

Route::get('/support/tickets/{id}', function ($id) {
    return view('admin.ticket_detail', ['ticketId' => $id]);
})->name('support.tickets.detail');

Route::get('/withdrawal/requests', function () {
    return view('admin.withdrawal_requests');
})->name('withdrawal.requests');

// Product Management Routes
Route::get('/admin/produk', function () {
    return view('admin.produk.index');
})->name('produk.index');

Route::get('/penjual/produk', function () {
    return view('penjual.manajemen_produk');
})->name('penjual.produk');

Route::get('/penjual/pesanan', function () {
    return view('penjual.manajemen_pesanan');
})->name('penjual.pesanan');

Route::get('/penjual/saldo', function () {
    return view('penjual.saldo_penarikan');
})->name('penjual.saldo');

Route::get('/penjual/ulasan', function () {
    return view('penjual.manajemen_ulasan');
})->name('penjual.ulasan');



// Order Detail Route - parameter sebagai string karena menggunakan order number
Route::get('/penjual/pesanan/{order}', [App\Http\Controllers\OrderDetailController::class, 'show'])->name('orders.show');

// Order Invoice Route
Route::get('/invoice/{order}', [App\Http\Controllers\OrderDetailController::class, 'invoice'])->name('order.invoice');

// Shipping Label Route - Simple approach
Route::get('/shipping-label/{order}', function ($order) {
    $orderData = App\Models\Order::where('order_number', $order)->firstOrFail();
    $orderData->load(['items.product', 'items.variant', 'user', 'shipping_address']);
    return view('shipping_label', ['order' => $orderData]);
})->name('shipping.label');

// Role-based Dashboards
Route::middleware(['auth', 'admin.role'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\RoleDashboardController::class, 'showAdminDashboard'])->name('admin.dashboard');
});

Route::middleware(['auth', 'seller.role'])->group(function () {
    Route::get('/penjual/dashboard', function () {
        return view('penjual.dashboard_penjual');
    })->name('seller.dashboard');
});

Route::middleware(['auth', 'customer.role'])->group(function () {
    Route::get('/customer/dashboard', [App\Http\Controllers\RoleDashboardController::class, 'showCustomerDashboard'])->name('customer.dashboard');
});

// Authentication Routes
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// General dashboard route that shows different content based on role
Route::middleware(['auth'])->get('/dashboard', [App\Http\Controllers\RoleDashboardController::class, 'showRoleBasedDashboard'])->name('dashboard');