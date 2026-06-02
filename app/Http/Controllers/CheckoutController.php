<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        // Ambil cart user beserta data produk
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong'], 422);
        }

        // Hitung total harga (gunakan $item->qty bukan quantity)
        $total = $cartItems->sum(fn($item) => $item->qty * $item->product->price);

        // Buat order baru
        $order = Order::create([
            'user_id'        => $user->id,
            'invoice_number' => 'INV-' . time(),
            'total_price'    => $total,
            'status'         => 'pending',
            'payment_method' => 'belum_dipilih', // Menambahkan default agar tidak error 1364
        ]);

        // Pindahkan cart items ke order items
        foreach ($cartItems as $item) {
            \Illuminate\Support\Facades\DB::table('order_items')->insert([
                'order_id'          => $order->id,
                'product_id'        => $item->product_id,
                'qty'               => $item->qty,
                'price_at_purchase' => $item->product->price,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        // Kosongkan cart setelah checkout
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Checkout berhasil',
            'order'   => $order->load('orderItems'),
        ], 201);
    }
}