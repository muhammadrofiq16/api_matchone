<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Get all orders (Admin) or user's orders (Customer)
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $query = Order::with('user');

        // If user is not admin, only show their orders
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Sort by latest first
        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => $orders
        ], 200);
    }

    /**
     * Create a new order (Customer)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_number' => 'required|string|unique:orders',
                'total_price' => 'required|numeric|min:0',
                'status' => 'required|in:pending,paid,processing,completed,cancelled',
                'payment_method' => 'required|string|max:255',
            ]);

            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => $validated['invoice_number'],
                'total_price' => $validated['total_price'],
                'status' => $validated['status'],
                'payment_method' => $validated['payment_method'],
            ]);

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order->load('user')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Get a single order
     */
    public function show($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $order = Order::with('user')->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        // Check authorization: users can only see their own orders, admins can see all
        if (!$user->isAdmin() && $order->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to access this order'
            ], 403);
        }

        return response()->json([
            'message' => 'Order retrieved successfully',
            'data' => $order
        ], 200);
    }

    /**
     * Update an order status (Admin only)
     */
    public function update(Request $request, $id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found'
                ], 404);
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,paid,processing,completed,cancelled',
                'payment_method' => 'sometimes|required|string|max:255',
                'total_price' => 'sometimes|required|numeric|min:0',
            ]);

            $order->update($validated);

            return response()->json([
                'message' => 'Order updated successfully',
                'data' => $order->load('user')
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Cancel an order (Admin or Order Owner)
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        // Check authorization
        if (!$user->isAdmin() && $order->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized to delete this order'
            ], 403);
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ], 200);
    }
}

