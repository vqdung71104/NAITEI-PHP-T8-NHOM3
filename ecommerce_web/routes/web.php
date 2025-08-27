<?php

use App\Http\Controllers\AdminController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Controller;

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'viewall'])->name('products.viewall');
Route::get('/products/{id}', [ProductController::class, 'detail'])->name('products.detail');

Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

// Trang checkout (hiển thị form)
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');

// Xử lý đặt hàng (submit form)
Route::post('/checkout/process', [CartController::class, 'processCheckout'])->name('checkout.process');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/orders/track', [OrderController::class, 'trackOrders'])->name('orders.track');
});

// Trang chi tiết đơn hàng sau khi đặt thành công
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');



Route::get('/', function () {
    if (Auth::check()) {
        // If already logged in, redirect to homepage
        return redirect()->route('home');
    } else {
        // If not logged in, redirect to login page
        return redirect()->route('login');
    }
});

// Routes for Admin - Only accessible by admin
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    // Category CRUD routes
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
    
    // Product CRUD routes
    Route::get('/products', [AdminController::class, 'products'])->name('products.index');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    
    // Order management routes for admin
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/statistics/overview', [OrderController::class, 'statistics'])->name('statistics');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        
    });
    
    // User management routes for admin
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::post('/', [AdminController::class, 'storeUser'])->name('store');
        Route::get('/{user}', [AdminController::class, 'showUser'])->name('show');
        Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('destroy');
    });

    // Xác nhận đơn hàng
    Route::post('/orders/{orderId}/confirm', [OrderController::class, 'confirmOrder'])->name('admin.orders.confirm');
});


Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if ($user && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/lang',  [LanguageController::class, 'changeLanguage'])->name('lang.set');
Route::get('/language/{language}', [LanguageController::class, 'changeLanguageBlade'])->name('language.change'); 



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';