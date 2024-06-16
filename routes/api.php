<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ShoppingCartController;

// Rute tanpa middleware
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute dengan middleware 'auth:sanctum'
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rute untuk admin
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('books', BookController::class)->except(['create', 'edit']);

        Route::prefix('inventory')->group(function () {
            Route::get('/', [InventoryController::class, 'index']);
            Route::post('/{id}/add-stock', [InventoryController::class, 'addStock']);
            Route::post('/{id}/reduce-stock', [InventoryController::class, 'reduceStock']);
            Route::delete('/{id}', [InventoryController::class, 'deleteBook']);
        });

        Route::apiResource('users', UserController::class)->except(['create', 'edit']);
    });

    // Rute untuk customer
    Route::middleware('role:customer')->group(function () {
        Route::prefix('cart')->group(function () {
            Route::post('/', [ShoppingCartController::class, 'addToCart']);
            Route::put('/{id}', [ShoppingCartController::class, 'updateCart']);
            Route::delete('/{id}', [ShoppingCartController::class, 'removeFromCart']);
            Route::get('/', [ShoppingCartController::class, 'viewCart']);
        });

        Route::post('/orders', [OrderController::class, 'placeOrder']);
    });

    Route::put('/user/profile', [UserController::class, 'updateProfile']);
});
