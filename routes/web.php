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
use App\Http\Controllers\RajaOngkirController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');

// Static Pages
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/doctest', 'pages.doctest')->name('doctest');
Route::post('/midtrans/webhook', [OrderController::class, 'handleWebhook'])->name('midtrans.webhook');
Route::any('/api/biteship/webhook', [App\Http\Controllers\TrackingController::class, 'biteshipWebhook'])->name('biteship.webhook');

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

Route::get('/sendmail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Halo! Ini adalah email uji coba dari DjudasMS (gakdi940@gmail.com) menuju jetthan09@gmail.com menggunakan SMTP Gmail yang sudah aktif.', function ($message) {
            $message->to('jetthan09@gmail.com')
                    ->subject('Test SMTP DjudasMS - Sukses');
        });
        return "SUKSES: Email uji coba berhasil dikirim ke jetthan09@gmail.com!";
    } catch (\Exception $e) {
        return "GAGAL: " . $e->getMessage();
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
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->middleware('throttle:5,1')->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
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
    Route::get('/cart/remove/{id}', function() { return redirect()->route('cart.index'); }); // Fallback for GET request
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Wishlist Routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'store'])->name('wishlist.add');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/{id}/to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.toCart');

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/cancel-buy-now', [CheckoutController::class, 'cancelBuyNow'])->name('checkout.cancelBuyNow');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/shipping-cost', [CheckoutController::class, 'calculateShipping'])->name('checkout.shippingCost');

    // Order Routes
    Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/order/{order_code}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/order/{id}/check-status', [OrderController::class, 'checkStatus'])->name('orders.checkStatus');
    Route::get('/order/{order_code}/invoice', [InvoiceController::class, 'download'])->name('orders.invoice');
    Route::get('/order/{id}/track', [TrackingController::class, 'track'])->name('orders.track');
    Route::get('/order/{id}/biteship-track', [OrderController::class, 'getBiteshipTracking'])->name('orders.biteshipTrack');
    Route::post('/order/{id}/delivered', [TrackingController::class, 'simulateDelivery'])->name('orders.delivered');
    Route::post('/order/{id}/sandbox-arrive', [TrackingController::class, 'sandboxArrive'])->name('orders.sandboxArrive');
    Route::post('/order/{id}/simulate-payment', [TrackingController::class, 'simulatePayment'])->name('orders.simulatePayment');
    Route::post('/order/{id}/simulate-shipment', [TrackingController::class, 'simulateShipment'])->name('orders.simulateShipment');
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

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
    
    // RajaOngkir API (V2 Komerce)
    Route::get('/api/rajaongkir/search-area', [App\Http\Controllers\RajaOngkirController::class, 'searchArea'])->name('api.rajaongkir.search');
    Route::post('/api/rajaongkir/rates', [App\Http\Controllers\RajaOngkirController::class, 'getRates'])->name('api.rajaongkir.rates');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

});

// Simulator Sandbox (Untuk testing di luar admin tanpa login)
Route::get('/simulasi', [App\Http\Controllers\TrackingController::class, 'simulatorPage'])->name('simulasi.index');
Route::post('/simulasi/{id}/ship', [App\Http\Controllers\TrackingController::class, 'simulateShipment'])->name('simulasi.ship');
Route::post('/simulasi/{id}/arrive', [App\Http\Controllers\TrackingController::class, 'sandboxArrive'])->name('simulasi.arrive');
Route::post('/simulasi/webhook/status', [App\Http\Controllers\TrackingController::class, 'triggerWebhookStatus'])->name('simulasi.webhook.status');
Route::post('/simulasi/webhook/price', [App\Http\Controllers\TrackingController::class, 'triggerWebhookPrice'])->name('simulasi.webhook.price');
Route::post('/simulasi/clear', [App\Http\Controllers\TrackingController::class, 'clearSimulator'])->name('simulasi.clear');

// Admin Routes (Protected by auth & admin role - Supports both Subdomain admin.* and Path /admin)
$adminRoutes = function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Products Management
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/products/{id}/restore', [AdminController::class, 'restoreProduct'])->name('products.restore');
    Route::delete('/products/{id}/force', [AdminController::class, 'forceDeleteProduct'])->name('products.forceDelete');
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

    // Reviews Moderation
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{id}/approve', [AdminController::class, 'approveReview'])->name('reviews.approve');
    Route::post('/reviews/{id}/reject', [AdminController::class, 'rejectReview'])->name('reviews.reject');

    // Return Requests Management
    Route::get('/returns', [AdminController::class, 'returnRequests'])->name('returns');
    Route::post('/returns/{id}/approve', [AdminController::class, 'approveReturn'])->name('returns.approve');
    Route::post('/returns/{id}/reject', [AdminController::class, 'rejectReturn'])->name('returns.reject');

    // Orders Management
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::post('/orders/{id}/arrive', [AdminController::class, 'simulateCourierArrived'])->name('orders.arrive');
    Route::get('/orders/{id}/shipping', [App\Http\Controllers\AdminController::class, 'manageShipping'])->name('orders.shipping');
    Route::post('/orders/{id}/ship', [App\Http\Controllers\AdminController::class, 'shipOrder'])->name('orders.ship');
    Route::get('/orders/{id}/print-label', [App\Http\Controllers\AdminController::class, 'printLabel'])->name('orders.print_label');
    Route::post('/orders/{id}/force-delivered', [App\Http\Controllers\TrackingController::class, 'sandboxArrive'])->name('orders.force_delivered');

    // --- SUPER ADMIN ONLY FEATURES ---
    Route::middleware('super_admin')->group(function () {
        // Users Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
        Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggleStatus');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Coupons Management
        Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons');
        Route::get('/coupons/create', [AdminController::class, 'createCoupon'])->name('coupons.create');
        Route::post('/coupons', [AdminController::class, 'storeCoupon'])->name('coupons.store');
        Route::get('/coupons/{id}/edit', [AdminController::class, 'editCoupon'])->name('coupons.edit');
        Route::put('/coupons/{id}', [AdminController::class, 'updateCoupon'])->name('coupons.update');
        Route::delete('/coupons/{id}', [AdminController::class, 'destroyCoupon'])->name('coupons.destroy');

        // Flash Sales Management
        Route::get('/flash-sales', [AdminController::class, 'flashSales'])->name('flashSales');
        Route::get('/flash-sales/create', [AdminController::class, 'createFlashSale'])->name('flashSales.create');
        Route::post('/flash-sales', [AdminController::class, 'storeFlashSale'])->name('flashSales.store');
        Route::get('/flash-sales/{id}/edit', [AdminController::class, 'editFlashSale'])->name('flashSales.edit');
        Route::put('/flash-sales/{id}', [AdminController::class, 'updateFlashSale'])->name('flashSales.update');
        Route::delete('/flash-sales/{id}', [AdminController::class, 'destroyFlashSale'])->name('flashSales.destroy');

        // Reports & Analytics
        Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
        Route::get('/reports/products', [ReportController::class, 'productReport'])->name('reports.products');
        Route::get('/reports/customers', [ReportController::class, 'customerReport'])->name('reports.customers');
        Route::get('/reports/export/sales/pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.export.sales.pdf');
        Route::get('/reports/export/sales/excel', [ReportController::class, 'exportSalesExcel'])->name('reports.export.sales.excel');

        // Activity Log
        Route::get('/activity-log', [AdminController::class, 'activityLog'])->name('activityLog');
    });
};

// 2. Path prefix routing (domain.com/admin)
Route::middleware(['auth', 'admin', 'ip_whitelist'])->prefix('admin')->name('admin.')->group($adminRoutes);

// Route untuk cek limit API (GUI Table)
Route::get('/cekapi', function () {
    $results = [];

    // 1. Check RajaOngkir API (Komerce)
    $roKey = env('RAJAONGKIR_API_KEY', config('services.rajaongkir.api_key'));
    try {
        $roStart = microtime(true);
        $roRes = Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
            'key' => $roKey
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
            'search' => 'jakarta',
            'limit' => 1
        ]);
        $roTime = round((microtime(true) - $roStart) * 1000) . 'ms';
        if ($roRes->successful()) {
            $results['RajaOngkir API (Komerce)'] = ['status' => 'OK (Belum Limit)', 'code' => $roRes->status(), 'time' => $roTime, 'detail' => 'Berhasil fetch area'];
        } else {
            $results['RajaOngkir API (Komerce)'] = ['status' => 'LIMIT / ERROR', 'code' => $roRes->status(), 'time' => $roTime, 'detail' => $roRes->body()];
        }
    } catch (\Exception $e) {
        $results['RajaOngkir API (Komerce)'] = ['status' => 'EXCEPTION', 'code' => 500, 'time' => '-', 'detail' => $e->getMessage()];
    }

    // 2. Check Biteship API (Hanya untuk Tracker)
    $biteshipKey = env('BITESHIP_API_KEY');
    try {
        $bsStart = microtime(true);
        $bsRes = Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
            'Authorization' => $biteshipKey
        ])->get('https://api.biteship.com/v1/maps/areas', ['countries' => 'ID', 'input' => 'jkt', 'type' => 'single']);
        $bsTime = round((microtime(true) - $bsStart) * 1000) . 'ms';
        
        if ($bsRes->successful()) {
            $results['Biteship API (Tracking)'] = ['status' => 'OK (Belum Limit)', 'code' => $bsRes->status(), 'time' => $bsTime, 'detail' => 'Token Valid'];
        } else {
            $err = $bsRes->json();
            $results['Biteship API (Tracking)'] = ['status' => 'LIMIT / ERROR', 'code' => $bsRes->status(), 'time' => $bsTime, 'detail' => $err['error'] ?? $bsRes->body()];
        }
    } catch (\Exception $e) {
        $results['Biteship API (Tracking)'] = ['status' => 'EXCEPTION', 'code' => 500, 'time' => '-', 'detail' => $e->getMessage()];
    }

    // 3. Check MailerSend API
    $mailerKey = env('MAILERSEND_API_KEY');
    if (!$mailerKey) {
        $results['MailerSend API'] = ['status' => 'SKIPPED', 'code' => '-', 'time' => '-', 'detail' => 'Pakai SMTP (MAIL_USERNAME)'];
    } else {
        try {
            $msStart = microtime(true);
            $msRes = Illuminate\Support\Facades\Http::timeout(5)->withHeaders([
                'Authorization' => 'Bearer ' . $mailerKey,
                'Content-Type' => 'application/json'
            ])->get('https://api.mailersend.com/v1/identities');
            $msTime = round((microtime(true) - $msStart) * 1000) . 'ms';
            if ($msRes->successful()) {
                $results['MailerSend API'] = ['status' => 'OK', 'code' => $msRes->status(), 'time' => $msTime, 'detail' => 'Token valid'];
            } else {
                $err = $msRes->json();
                $results['MailerSend API'] = ['status' => 'ERROR', 'code' => $msRes->status(), 'time' => $msTime, 'detail' => $err['message'] ?? $msRes->body()];
            }
        } catch (\Exception $e) {
            $results['MailerSend API'] = ['status' => 'EXCEPTION', 'code' => 500, 'time' => '-', 'detail' => $e->getMessage()];
        }
    }

    return view('pages.cekapi', compact('results'));
});


Route::get('/symlink-fix', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Symlink berhasil dibuat! Silakan cek kembali gambar produk Anda.';
});
