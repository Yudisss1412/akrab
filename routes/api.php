<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Product API routes
Route::get('/products/popular', [ProductController::class, 'popular']);
Route::get('/products/{id}', [ProductController::class, 'apiShow']);
Route::get('/products/filter', [ProductController::class, 'filter'])->name('api.products.filter');