<?php

use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

Route::get('/products/{productId}/prices', [
    ProductPriceController::class,
    'index',
]);
Route::post('/products/{productId}/prices', [
    ProductPriceController::class,
    'store',
]);

Route::get('/currencies', [CurrencyController::class, 'index']);
Route::post('/currencies', [CurrencyController::class, 'store']);
