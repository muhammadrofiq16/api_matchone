<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class OrderItemController extends Controller
{
    /**
     * Display all order items for a specific order.
     */
    public function index($orderId)
    {
        $order = Order::findOrFail($orderId);
        $user = Auth::user();
        
        // Check authorization - user can only see their own order items
        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $items = $order->items()->with('product')->get();
        
        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * Store a new order item.
     */
    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $user = Auth::user();
        
        // Check authorization - user can only add items to their own order
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get product to get current price
        $product = Product::findOrFail($validated['product_id']);

        // Create order item
        $orderItem = $order->items()->create([
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'price' => $product->price,
            'subtotal' => $product->price * $validated['quantity'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan ke pesanan',
            'data' => $orderItem->load('product'),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display a specific order item.
     */
    public function show($orderId, $itemId)
    {
        $order = Order::findOrFail($orderId);
        $user = Auth::user();
        
        // Check authorization
        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $item = $order->items()->findOrFail($itemId);

        return response()->json([
            'success' => true,
            'data' => $item->load('product'),
        ]);
    }

    /**
     * Update a specific order item.
     */
    public function update(Request $request, $orderId, $itemId)
    {
        $order = Order::findOrFail($orderId);
        $user = Auth::user();
        
        // Check authorization
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $item = $order->items()->findOrFail($itemId);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Update quantity and recalculate subtotal
        $item->update([
            'quantity' => $validated['quantity'],
            'subtotal' => $item->price * $validated['quantity'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil diperbarui',
            'data' => $item->load('product'),
        ]);
    }

    /**
     * Delete a specific order item.
     */
    public function destroy($orderId, $itemId)
    {
        $order = Order::findOrFail($orderId);
        $user = Auth::user();
        
        // Check authorization
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $item = $order->items()->findOrFail($itemId);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari pesanan',
        ]);
    }

    /**
     * Get order items for admin (all items).
     */
    public function allItems()
    {
        $items = OrderItem::with(['order', 'product'])->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}
