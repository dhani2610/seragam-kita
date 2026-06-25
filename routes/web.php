<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProductController as AdminProduct;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\CustomerController as AdminCustomer;
use App\Http\Controllers\Admin\SettingController as AdminSetting;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Frontend routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{slug}', [HomeController::class, 'showProduct'])->name('product.detail');
Route::get('/about', [HomeController::class, 'aboutUs'])->name('about');
Route::get('/flow', [HomeController::class, 'flow'])->name('flow');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/cities/{province_id}', [AuthController::class, 'getCities']);

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Customer Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/cost', [CheckoutController::class, 'checkCost']);
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'showPayment'])->name('checkout.payment');
    Route::post('/checkout/payment/{order}/simulate', [CheckoutController::class, 'simulatePaymentSuccess']);

    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::post('/review/submit', [HomeController::class, 'submitReview'])->name('review.submit');
});

// Admin Protected routes (using role check middleware or inline closure)
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function () {
        
        Route::middleware(['admin'])->group(function () {
            
            // Dashboard
            Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');

            // Categories
            Route::get('/categories', [AdminProduct::class, 'categories'])->name('admin.categories');
            Route::get('/categories/data', [AdminProduct::class, 'categoryData']);
            Route::post('/categories/store', [AdminProduct::class, 'storeCategory']);
            Route::get('/categories/show/{id}', [AdminProduct::class, 'showCategory']);
            Route::post('/categories/update/{id}', [AdminProduct::class, 'updateCategory']);
            Route::delete('/categories/delete/{id}', [AdminProduct::class, 'destroyCategory']);

            // Products
            Route::get('/products', [AdminProduct::class, 'index'])->name('admin.products');
            Route::get('/products/data', [AdminProduct::class, 'productData']);
            Route::post('/products/store', [AdminProduct::class, 'storeProduct']);
            Route::get('/products/show/{id}', [AdminProduct::class, 'showProduct']);
            Route::post('/products/update/{id}', [AdminProduct::class, 'updateProduct']);
            Route::delete('/products/delete/{id}', [AdminProduct::class, 'destroyProduct']);
            Route::delete('/products/image/delete/{id}', [AdminProduct::class, 'deleteProductImage']);

            // Orders
            Route::get('/orders', [AdminOrder::class, 'index'])->name('admin.orders');
            Route::get('/orders/data', [AdminOrder::class, 'orderData']);
            Route::get('/orders/show/{id}', [AdminOrder::class, 'show']);
            Route::post('/orders/status/{id}', [AdminOrder::class, 'updateStatus']);
            
            // Reports & Sales
            Route::get('/reports', [AdminOrder::class, 'reports'])->name('admin.reports');
            Route::get('/reports/data', [AdminOrder::class, 'reportData']);
            Route::get('/reports/export/pdf', [AdminOrder::class, 'exportPdf']);
            Route::get('/reports/export/excel', [AdminOrder::class, 'exportExcel']);

            // Customers
            Route::get('/customers', [AdminCustomer::class, 'index'])->name('admin.customers');
            Route::get('/customers/data', [AdminCustomer::class, 'data']);
            Route::get('/customers/show/{id}', [AdminCustomer::class, 'show']);
            Route::post('/customers/update/{id}', [AdminCustomer::class, 'update']);
            Route::post('/customers/toggle-status/{id}', [AdminCustomer::class, 'toggleStatus']);
            Route::delete('/customers/delete/{id}', [AdminCustomer::class, 'destroy']);

            // Settings
            Route::get('/settings/sliders', [AdminSetting::class, 'sliders'])->name('admin.sliders');
            Route::get('/settings/sliders/data', [AdminSetting::class, 'sliderData']);
            Route::post('/settings/sliders/store', [AdminSetting::class, 'storeSlider']);
            Route::get('/settings/sliders/show/{id}', [AdminSetting::class, 'showSlider']);
            Route::post('/settings/sliders/update/{id}', [AdminSetting::class, 'updateSlider']);
            Route::delete('/settings/sliders/delete/{id}', [AdminSetting::class, 'destroySlider']);

            Route::get('/settings/website', [AdminSetting::class, 'website'])->name('admin.website');
            Route::post('/settings/website/update', [AdminSetting::class, 'updateWebsite']);
            Route::post('/settings/website/rajaongkir', [AdminSetting::class, 'updateRajaongkir']);
        });

    });
});
