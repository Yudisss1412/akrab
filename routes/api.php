<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\SubcategoryController;

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
Route::get('/products/search', [ProductController::class, 'apiSearch']);
Route::get('/products/filter', [ProductController::class, 'apiFilter'])->name('api.products.filter');
Route::get('/products/{id}', [ProductController::class, 'apiShow']);

// Subcategory API routes
Route::get('/subcategories/category/{categoryName}', [SubcategoryController::class, 'getSubcategoriesByCategoryName']);

// Midtrans webhook
Route::post('/midtrans/notification', [App\Http\Controllers\PaymentController::class, 'midtransNotification']);