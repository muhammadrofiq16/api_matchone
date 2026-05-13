<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index()
    {
        $userId = Auth::id();
        $cartItems = Cart::where('user_id', $userId)
            ->with(['product.category'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product' => $item->product,
                    'quantity' => $item->qty,
                    'subtotal' => $item->qty * $item->product->price,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

        $total = $cartItems->sum('subtotal');

        return response()->json([
            'message' => 'Cart retrieved successfully',
            'data' => $cartItems,
            'total' => $total,
            'count' => count($cartItems)
        ], 200);
    }

    /**
     * Add product to cart
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'qty' => 'required|integer|min:1',
            ]);

            $userId = Auth::id();
            $productId = $validated['product_id'];

            // Check if product exists and is available
            $product = Product::find($productId);
            if (!$product || !$product->is_available) {
                return response()->json([
                    'message' => 'Product not available'
                ], 400);
            }

            // Check if product already in cart
            $existingCart = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($existingCart) {
                // Update quantity
                $existingCart->update(['qty' => $existingCart->qty + $validated['qty']]);
                $cartItem = $existingCart->load('product.category');
            } else {
                // Create new cart item
                $cartItem = Cart::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'qty' => $validated['qty'],
                ]);
                $cartItem->load('product.category');
            }

            return response()->json([
                'message' => 'Product added to cart successfully',
                'data' => [
                    'id' => $cartItem->id,
                    'product' => $cartItem->product,
                    'quantity' => $cartItem->qty,
                    'subtotal' => $cartItem->qty * $cartItem->product->price,
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'qty' => 'required|integer|min:1',
            ]);

            $userId = Auth::id();
            $cartItem = Cart::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'message' => 'Cart item not found'
                ], 404);
            }

            $cartItem->update(['qty' => $validated['qty']]);
            $cartItem->load('product.category');

            return response()->json([
                'message' => 'Cart item updated successfully',
                'data' => [
                    'id' => $cartItem->id,
                    'product' => $cartItem->product,
                    'quantity' => $cartItem->qty,
                    'subtotal' => $cartItem->qty * $cartItem->product->price,
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy($id)
    {
        $userId = Auth::id();
        $cartItem = Cart::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'message' => 'Cart item not found'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart successfully'
        ], 200);
    }

    /**
     * Clear all cart items
     */
    public function clear()
    {
        $userId = Auth::id();
        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'Cart cleared successfully'
        ], 200);
    }

    /**
     * Get cart summary
     */
    public function summary()
    {
        $userId = Auth::id();
        $cartItems = Cart::where('user_id', $userId)
            ->with('product')
            ->get();

        $total = 0;
        $itemCount = 0;

        foreach ($cartItems as $item) {
            $total += $item->qty * $item->product->price;
            $itemCount += $item->qty;
        }

        return response()->json([
            'message' => 'Cart summary retrieved successfully',
            'data' => [
                'total_items' => count($cartItems),
                'total_quantity' => $itemCount,
                'total_price' => $total,
            ]
        ], 200);
    }
}
