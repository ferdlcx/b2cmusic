<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/shop', [ProductController::class, 'index'])->name('catalog');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/midtrans/webhook', [OrderController::class, 'handleWebhook'])->name('midtrans.webhook');

// Test SMTP Route
Route::get('/dmail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Halo! Ini adalah email percobaan untuk menguji konfigurasi SMTP Google Anda. Jika Anda menerima email ini, berarti SMTP Google di DjudasMS sudah berjalan dengan sempurna!', function ($message) {
            $message->to('gakdi940@gmail.com')
                    ->subject('Test SMTP Berhasil - DjudasMS');
        });
        return "Email percobaan berhasil dikirim ke gakdi940@gmail.com! Silakan cek kotak masuk Anda.";
    } catch (\Exception $e) {
        return "Gagal mengirim email. Error: " . $e->getMessage();
    }
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Forgot & Reset Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/email/verify/otp', [AuthController::class, 'verifyOtp'])->name('verification.verify.otp');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->middleware('throttle:6,1')->name('verification.send');
});

// Customer Routes (Protected by auth and email verification)
Route::middleware(['auth', 'verified'])->group(function () {
    // Customer Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'destroy'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Wishlist Routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'store'])->name('wishlist.add');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/{id}/to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.toCart');

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/shipping-cost', [CheckoutController::class, 'calculateShipping'])->name('checkout.shippingCost');

    // Order Routes
    Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/order/{order_code}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/order/{id}/check-status', [OrderController::class, 'checkStatus'])->name('orders.checkStatus');
    Route::get('/order/{order_code}/invoice', [InvoiceController::class, 'download'])->name('orders.invoice');
    Route::get('/order/{id}/track', [TrackingController::class, 'track'])->name('orders.track');
    Route::post('/order/{id}/delivered', [TrackingController::class, 'simulateDelivery'])->name('orders.delivered');

    // Review Routes
    Route::post('/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Return Request Routes
    Route::get('/return/{order_id}/create', [ReturnController::class, 'create'])->name('returns.create');
    Route::post('/return', [ReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');

    // Profile & Address Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/address', [ProfileController::class, 'storeAddress'])->name('profile.address.store');
    Route::put('/profile/address/{address}', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
    Route::delete('/profile/address/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.address.destroy');
    Route::post('/profile/address/{address}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.address.default');
    Route::get('/api/provinces', [ProfileController::class, 'getProvinces'])->name('api.provinces');
    Route::get('/api/cities', [ProfileController::class, 'getCities'])->name('api.cities');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
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
    Route::post('/products/{id}/restore', [AdminController::class, 'restoreProduct'])->name('products.restore');
    Route::get('/products/trashed', [AdminController::class, 'trashedProducts'])->name('products.trashed');

    // Categories Management
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    // Brands Management
    Route::get('/brands', [AdminController::class, 'brands'])->name('brands');
    Route::get('/brands/create', [AdminController::class, 'createBrand'])->name('brands.create');
    Route::post('/brands', [AdminController::class, 'storeBrand'])->name('brands.store');
    Route::get('/brands/{id}/edit', [AdminController::class, 'editBrand'])->name('brands.edit');
    Route::put('/brands/{id}', [AdminController::class, 'updateBrand'])->name('brands.update');
    Route::delete('/brands/{id}', [AdminController::class, 'destroyBrand'])->name('brands.destroy');

    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggleStatus');

    // Coupons Management
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons');
    Route::get('/coupons/create', [AdminController::class, 'createCoupon'])->name('coupons.create');
    Route::post('/coupons', [AdminController::class, 'storeCoupon'])->name('coupons.store');
    Route::get('/coupons/{id}/edit', [AdminController::class, 'editCoupon'])->name('coupons.edit');
    Route::put('/coupons/{id}', [AdminController::class, 'updateCoupon'])->name('coupons.update');
    Route::delete('/coupons/{id}', [AdminController::class, 'destroyCoupon'])->name('coupons.destroy');

    // Reviews Moderation
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{id}/approve', [AdminController::class, 'approveReview'])->name('reviews.approve');
    Route::post('/reviews/{id}/reject', [AdminController::class, 'rejectReview'])->name('reviews.reject');

    // Return Requests Management
    Route::get('/returns', [AdminController::class, 'returnRequests'])->name('returns');
    Route::post('/returns/{id}/approve', [AdminController::class, 'approveReturn'])->name('returns.approve');
    Route::post('/returns/{id}/reject', [AdminController::class, 'rejectReturn'])->name('returns.reject');

    // Flash Sales Management
    Route::get('/flash-sales', [AdminController::class, 'flashSales'])->name('flashSales');
    Route::get('/flash-sales/create', [AdminController::class, 'createFlashSale'])->name('flashSales.create');
    Route::post('/flash-sales', [AdminController::class, 'storeFlashSale'])->name('flashSales.store');
    Route::get('/flash-sales/{id}/edit', [AdminController::class, 'editFlashSale'])->name('flashSales.edit');
    Route::put('/flash-sales/{id}', [AdminController::class, 'updateFlashSale'])->name('flashSales.update');
    Route::delete('/flash-sales/{id}', [AdminController::class, 'destroyFlashSale'])->name('flashSales.destroy');

    // Orders Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // Reports & Analytics
    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/products', [ReportController::class, 'productReport'])->name('reports.products');
    Route::get('/reports/customers', [ReportController::class, 'customerReport'])->name('reports.customers');
    Route::get('/reports/export/sales/pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.export.sales.pdf');
    Route::get('/reports/export/sales/excel', [ReportController::class, 'exportSalesExcel'])->name('reports.export.sales.excel');

    // Activity Log
    Route::get('/activity-log', [AdminController::class, 'activityLog'])->name('activityLog');
};

// 2. Path prefix routing (domain.com/admin)
Route::middleware(['auth', 'admin', 'ip_whitelist'])->prefix('admin')->name('admin.')->group($adminRoutes);

