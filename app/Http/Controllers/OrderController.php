<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = Order::where('user_id', $user->id)
            ->with('items.product')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $totalPrice = 0;
        $itemsData = [];

        // Validate and prepare items
        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            if (!$product->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => "Produk {$product->name} tidak tersedia",
                ], 400);
            }

            $subtotal = $product->price * $item['qty'];
            $totalPrice += $subtotal;

            $itemsData[] = [
                'product_id' => $product->id,
                'qty' => $item['qty'],
                'price_at_purchase' => $product->price,
                'subtotal' => $subtotal,
            ];
        }

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create order items
        foreach ($itemsData as $itemData) {
            OrderItem::create([
                'order_id' => $order->id,
                ...$itemData,
            ]);
        }

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat',
            'data' => $order,
        ], 201);
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        // Check if order belongs to user or user is admin
        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $order->load('items.product', 'user');

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        // Only admin can update order status
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat mengupdate order',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|string|in:pending,paid,processing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil diupdate',
            'data' => $order,
        ]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        // Only owner or admin can delete
        if ($order->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Only pending orders can be deleted
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya order dengan status pending yang dapat dihapus',
            ], 400);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dihapus',
        ]);
    }
}
