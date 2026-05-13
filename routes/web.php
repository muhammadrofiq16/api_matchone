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
// Catatan: Middleware 'admin' perlu kita buat nanti agar user biasa tidak bisa masuk
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    // Manajemen Data (CRUD) via Web
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
    
    // Manajemen Pesanan via Web Admin
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show'); // Tambahan route detail
    Route::put('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus'); // Ubah post ke put

    // Manajemen Keranjang Belanja via Web Admin
    Route::get('/carts', [AdminCartController::class, 'index'])->name('carts.index');
    Route::delete('/carts/{id}', [AdminCartController::class, 'destroy'])->name('carts.destroy');
    Route::delete('/carts/user/{userId}', [AdminCartController::class, 'clearUserCart'])->name('carts.clearUserCart');

});