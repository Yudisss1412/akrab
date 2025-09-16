<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('login');
})->name('view');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/cust_welcome', function () {
    return view('cust_welcome');
})->name('cust.welcome');

Route::get('/halaman_produk', function () {
    return view('halaman_produk');
})->name('halaman.produk');

Route::get('/produk_detail', function () {
    return view('produk_detail');
})->name('produk.detail');

Route::get('/profil', function () {
    return view('profil_pembeli');
})->name('profil.pembeli');

Route::get('/edit_profil', function () {
    return view('edit_profil');
})->name('edit.profil');

// halaman keranjang
Route::get('/keranjang', function () {
    return view('keranjang');
})->name('keranjang');

Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

Route::get('/halaman_ulasan', function () {
    return view('halaman_ulasan');
})->name('halaman_ulasan');

Route::view('/halaman-ulasan', 'halaman_ulasan')->name('halaman-ulasan');

Route::get('/halaman_wishlist', function () {
    return view('halaman_wishlist');
})->name('halaman_wishlist');

Route::get('/dashboard_penjual', function () {
    return view('dashboard_penjual');
})->name('dashboard.penjual');

Route::get('/profil_penjual', function () {
    return view('profil_penjual');
})->name('profil.penjual');

Route::get('/invoice', function () {
    return view('invoice');
})->name('invoice');

Route::get('/dashboard_admin', function () {
    return view('dashboard_admin');
})->name('dashboard.admin');
