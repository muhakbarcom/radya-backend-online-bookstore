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
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Rute dengan middleware 'auth:sanctum'
Route::middleware('auth:sanctum')->group(function () {
    // Rute untuk semua role
    Route::middleware('role:customer')->group(function () {
        Route::get('/api/books', [BookController::class, 'index'])->name('books.index');
        Route::put('/user/profile', [UserController::class, 'updateProfile'])->name('user.profile');
    });

    // Rute untuk admin
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('books', BookController::class)->except(['create', 'edit', 'index']);

        Route::prefix('inventory')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
            Route::post('/{id}/add-stock', [InventoryController::class, 'addStock'])->name('inventory.add-stock');
            Route::post('/{id}/reduce-stock', [InventoryController::class, 'reduceStock'])->name('inventory.reduce-stock');
            Route::delete('/{id}', [InventoryController::class, 'deleteBook'])->name('inventory.delete');
        });

        Route::apiResource('users', UserController::class)->except(['create', 'edit']);
    });

    // Rute untuk customer
    Route::middleware('role:customer')->group(function () {
        Route::prefix('cart')->group(function () {
            Route::post('/', [ShoppingCartController::class, 'addToCart'])->name('cart.add');
            Route::put('/{id}', [ShoppingCartController::class, 'updateCart'])->name('cart.update');
            Route::delete('/{id}', [ShoppingCartController::class, 'removeFromCart'])->name('cart.remove');
            Route::get('/', [ShoppingCartController::class, 'viewCart'])->name('cart.view');
        });

        Route::post('/orders', [OrderController::class, 'placeOrder'])->name('orders.place');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
