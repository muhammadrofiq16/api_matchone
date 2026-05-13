<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController; // ← tambahan

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']); // ← tambahan: login via Google

// Category routes (public - read only)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Product routes (public - read only)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::delete('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::put('/auth/profile', [AuthController::class, 'update']);

    // Cart routes
    Route::get('/cart/summary', [CartController::class, 'summary']); // ← dipindah ke atas agar tidak bentrok dengan /cart/{id}
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'store']);

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    // Order Items routes
    Route::get('/orders/{orderId}/items', [OrderItemController::class, 'index']);
    Route::post('/orders/{orderId}/items', [OrderItemController::class, 'store']);
    Route::get('/orders/{orderId}/items/{itemId}', [OrderItemController::class, 'show']);
    Route::put('/orders/{orderId}/items/{itemId}', [OrderItemController::class, 'update']);
    Route::delete('/orders/{orderId}/items/{itemId}', [OrderItemController::class, 'destroy']);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        // Category management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Product management
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::patch('/products/{id}/toggle-availability', [ProductController::class, 'toggleAvailability']);

        // Order management (admin only)
        Route::put('/orders/{id}', [OrderController::class, 'update']);

        // Order Items management (admin only)
        Route::get('/order-items', [OrderItemController::class, 'allItems']);
    });
});