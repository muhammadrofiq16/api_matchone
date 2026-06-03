<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\AdminOrderController;
use App\Http\Controllers\Web\AdminCartController;

use App\Http\Controllers\Web\KasirController;
use App\Http\Controllers\Web\KasirCategoryController;
use App\Http\Controllers\Web\KasirProductController;
use App\Http\Controllers\Web\KasirOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
Route::post('/login', [AdminController::class, 'login']);
Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

// Redirect dashboard otomatis sesuai role
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if (Auth::user()->role === 'kasir') {
        return redirect()->route('kasir.dashboard');
    }

    abort(403);
})->middleware('auth')->name('dashboard');


// ==========================
// ROUTE ADMIN
// ==========================
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('users', UserController::class);

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

        Route::get('/carts', [AdminCartController::class, 'index'])->name('carts.index');
        Route::delete('/carts/{id}', [AdminCartController::class, 'destroy'])->name('carts.destroy');
        Route::delete('/carts/user/{userId}', [AdminCartController::class, 'clearUserCart'])->name('carts.clearUserCart');
    });


// ==========================
// ROUTE KASIR
// ==========================
Route::middleware(['auth', 'kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {

        Route::get('/dashboard', [KasirController::class, 'index'])->name('dashboard');

        // POS Kasir
        Route::get('/pos', [KasirController::class, 'pos'])->name('pos');
        Route::post('/pos/checkout', [KasirController::class, 'checkout'])->name('pos.checkout');

        // Route ini boleh tetap ada walaupun tidak ditampilkan di sidebar
        Route::resource('categories', KasirCategoryController::class);
        Route::resource('products', KasirProductController::class);

        // Pesanan Kasir
        Route::get('/orders', [KasirOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [KasirOrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{id}/status', [KasirOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });