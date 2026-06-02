<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\AdminOrderController;
use App\Http\Controllers\Web\AdminCartController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Halaman utama (bisa dibiarkan untuk landing page aplikasi Matchone nanti)
Route::get('/', function () {
    return view('welcome');
});

// Route untuk autentikasi Admin (Login & Logout)
Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminController::class, 'login']);
Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

// Group khusus Admin (Hanya bisa diakses jika sudah login dan punya role 'admin')
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // Manajemen Data (CRUD) via Web
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
    
    // Manajemen Pesanan via Web Admin
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Manajemen Keranjang Belanja via Web Admin
    Route::get('/carts', [AdminCartController::class, 'index'])->name('carts.index');
    Route::delete('/carts/{id}', [AdminCartController::class, 'destroy'])->name('carts.destroy');
    Route::delete('/carts/user/{userId}', [AdminCartController::class, 'clearUserCart'])->name('carts.clearUserCart');

});

// Group khusus Kasir (Hanya bisa diakses jika sudah login dan punya role 'kasir')
Route::middleware(['auth', 'kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    
    // Dashboard Kasir & POS
    Route::get('/dashboard', [\App\Http\Controllers\Web\KasirController::class, 'index'])->name('dashboard');
    Route::get('/pos', [\App\Http\Controllers\Web\KasirController::class, 'pos'])->name('pos');
    
    // Kasir CRUD Produk dan Kategori (Duplicate view untuk kasir)
    Route::resource('categories', \App\Http\Controllers\Web\KasirCategoryController::class);
    Route::resource('products', \App\Http\Controllers\Web\KasirProductController::class);
    
    // Kasir Pesanan
    Route::get('/orders', [\App\Http\Controllers\Web\KasirOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [\App\Http\Controllers\Web\KasirOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}/status', [\App\Http\Controllers\Web\KasirOrderController::class, 'updateStatus'])->name('orders.updateStatus');

});