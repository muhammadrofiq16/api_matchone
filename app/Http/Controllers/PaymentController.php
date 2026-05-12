<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $query = Payment::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment type
        if ($request->has('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        $payments = $query->with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($payments);
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_type' => 'required|in:bank_transfer,e_wallet,cash_on_delivery',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Check if payment already exists
        $existingPayment = Payment::where('order_id', $order->id)->first();
        if ($existingPayment) {
            return response()->json([
                'message' => 'Payment already exists for this order',
                'payment' => $existingPayment
            ], 400);
        }

        // Validate amount paid doesn't exceed order total
        if ($request->amount_paid > $order->total_price) {
            return response()->json([
                'message' => 'Amount paid cannot exceed order total',
                'order_total' => $order->total_price,
            ], 422);
        }

        $paymentData = $request->only('order_id', 'payment_type', 'amount_paid', 'notes');

        // Handle file upload for payment proof
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payments', 'public');
            $paymentData['payment_proof'] = $path;
            $paymentData['status'] = 'pending'; // Set to pending for verification
        } else {
            $paymentData['status'] = 'pending';
        }

        $payment = Payment::create($paymentData);

        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment->load('order')
        ], 201);
    }

    /**
     * Display a specific payment.
     */
    public function show(Payment $payment)
    {
        return response()->json([
            'payment' => $payment->load('order')
        ]);
    }

    /**
     * Update payment status (admin only).
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,verified,rejected,refunded',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $payment->notes,
        ]);

        // Update order status if payment verified
        if ($request->status === 'verified') {
            $payment->order->update(['status' => 'paid']);
        } elseif ($request->status === 'rejected') {
            $payment->order->update(['status' => 'pending']);
        } elseif ($request->status === 'refunded') {
            $payment->order->update(['status' => 'cancelled']);
        }

        return response()->json([
            'message' => 'Payment status updated successfully',
            'payment' => $payment->load('order')
        ]);
    }

    /**
     * Get payment by order.
     */
    public function getByOrder(Order $order)
    {
        $payment = Payment::where('order_id', $order->id)->first();

        if (!$payment) {
            return response()->json([
                'message' => 'No payment found for this order'
            ], 404);
        }

        return response()->json([
            'payment' => $payment
        ]);
    }

    /**
     * Delete a payment (admin only, only if pending).
     */
    public function destroy(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Can only delete pending payments'
            ], 403);
        }

        // Delete uploaded file if exists
        if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }
}
