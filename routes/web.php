<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [GuestController::class, 'index']);

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/profile', function () {
    return view('profile');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [CompanyController::class, 'index']);
});