<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

Route::get('/halaman_produk', function () {
    return view('customer.produk.halaman_produk');
})->name('halaman.produk');

Route::get('/produk_detail', function () {
    return view('customer.produk.produk_detail');
})->name('produk.detail');

Route::get('/profil', function () {
    return view('customer.profil.profil_pembeli');
})->name('profil.pembeli');

Route::get('/edit_profil', function () {
    return view('customer.profil.edit_profil');
})->name('edit.profil');

// halaman keranjang
Route::get('/keranjang', function () {
    return view('customer.keranjang');
})->name('keranjang');

Route::get('/checkout', function () {
    return view('customer.transaksi.checkout');
})->name('checkout');

Route::get('/pengiriman', function () {
    return view('customer.transaksi.pengiriman');
})->name('cust.pengiriman');

Route::get('/pembayaran', function () {
    return view('customer.transaksi.pembayaran');
})->name('cust.pembayaran');

Route::get('/halaman_ulasan', function () {
    return view('customer.koleksi.halaman_ulasan');
})->name('halaman_ulasan');

Route::view('/halaman-ulasan', 'customer.koleksi.halaman_ulasan')->name('halaman-ulasan');

Route::get('/halaman_wishlist', function () {
    return view('customer.koleksi.halaman_wishlist');
})->name('halaman_wishlist');

Route::get('/dashboard_penjual', function () {
    return view('penjual.dashboard_penjual');
})->name('dashboard.penjual');

Route::get('/profil_penjual', function () {
    return view('penjual.profil_penjual');
})->name('profil.penjual');

Route::get('/invoice', function () {
    return view('customer.transaksi.invoice');
})->name('invoice');

Route::get('/dashboard_admin', function () {
    return view('admin.dashboard_admin');
})->name('dashboard.admin');
