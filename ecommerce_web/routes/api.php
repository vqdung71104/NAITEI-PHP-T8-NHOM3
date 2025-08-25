<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::prefix('categories')->group(function () {
    
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/{id}', [CategoryController::class, 'show'])->name('categories.show');
    
    Route::middleware('admin')->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
});

Route::prefix('products')->group(function () {
   
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
   
    Route::middleware('admin')->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    });
});


// Order API routes - Support both session and token authentication
Route::middleware(['auth'])->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('api.orders.index');
    Route::post('/', [OrderController::class, 'store'])->name('api.orders.store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('api.orders.show');
    Route::put('/{order}', [OrderController::class, 'update'])->name('api.orders.update');
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('api.orders.destroy');
    Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('api.orders.cancel');
    
    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::get('/statistics/overview', [OrderController::class, 'statistics'])->name('api.orders.statistics');
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('api.orders.update-status');
    });
});

// User Management API routes - Admin only
Route::middleware(['auth', 'admin'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('api.users.index');
    Route::post('/', [UserController::class, 'store'])->name('api.users.store');
    Route::get('/statistics', [UserController::class, 'statistics'])->name('api.users.statistics');
    Route::get('/{user}', [UserController::class, 'show'])->name('api.users.show');
    Route::put('/{user}', [UserController::class, 'update'])->name('api.users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('api.users.destroy');
    Route::post('/{user}/status', [UserController::class, 'updateStatus'])->name('api.users.update-status');
});
