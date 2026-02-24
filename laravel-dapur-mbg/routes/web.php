<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Supplier\DashboardController as SupplierDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rute umum
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Rute autentikasi
Auth::routes();

// Rute untuk Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', 'ProductController');
    Route::resource('orders', 'OrderController');
    Route::resource('users', 'UserController');
});

// Rute untuk Supplier
Route::middleware(['auth', 'role:supplier'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', 'ProductController');
    Route::get('/orders', [SupplierDashboardController::class, 'orders'])->name('orders');
});

// Rute untuk Customer
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [CustomerDashboardController::class, 'products'])->name('products');
    Route::get('/cart', [CustomerDashboardController::class, 'cart'])->name('cart');
    Route::get('/checkout', [CustomerDashboardController::class, 'checkout'])->name('checkout');
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
});