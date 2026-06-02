<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/shop', [ProductController::class, 'index'])->name('catalog');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/midtrans/webhook', [OrderController::class, 'handleWebhook'])->name('midtrans.webhook');

// Auth Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customer Routes (Protected by auth)
Route::middleware('auth')->group(function () {
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'destroy'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    // Order Routes
    Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/order/{order_code}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/order/{id}/pay', [OrderController::class, 'pay'])->name('orders.pay');

    // Profile & Address Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/address', [ProfileController::class, 'storeAddress'])->name('profile.address.store');
    Route::put('/profile/address/{address}', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
    Route::delete('/profile/address/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.address.destroy');
    Route::post('/profile/address/{address}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.address.default');
    Route::get('/api/provinces', [ProfileController::class, 'getProvinces'])->name('api.provinces');
    Route::get('/api/cities', [ProfileController::class, 'getCities'])->name('api.cities');
});

// Admin Routes (Protected by auth & admin role - Supports both Subdomain admin.* and Path /admin)
$adminRoutes = function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Products Management
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct'])->name('products.destroy');

    // Orders Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
};

// 1. Subdomain routing (admin.domain.com)
$appHost = parse_url(config('app.url'), PHP_URL_HOST);
if ($appHost && !app()->runningInConsole()) {
    Route::domain('admin.' . $appHost)->middleware(['auth', 'admin'])->name('admin.')->group($adminRoutes);
}

// 2. Path prefix routing (domain.com/admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group($adminRoutes);
