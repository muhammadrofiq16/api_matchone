<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan. Pastikan token login dikirim.'
                ], 401);
            }

            $cartItems = Cart::where('user_id', $user->id)
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'message' => 'Keranjang kosong'
                ], 422);
            }

            $total = 0;

            foreach ($cartItems as $item) {
                if (!$item->product) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Ada produk di cart yang tidak ditemukan',
                        'cart_id' => $item->id,
                        'product_id' => $item->product_id
                    ], 422);
                }

                if (!$item->qty || $item->qty < 1) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Qty produk tidak valid',
                        'cart_id' => $item->id,
                        'qty' => $item->qty
                    ], 422);
                }

                $total += $item->qty * $item->product->price;
            }

            $order = Order::create([
                'user_id'        => $user->id,
                'invoice_number' => 'INV-' . time(),
                'total_price'    => $total,
                'status'         => 'pending',
                'payment_method' => 'belum_dipilih',
            ]);

            foreach ($cartItems as $item) {
                DB::table('order_items')->insert([
                    'order_id'          => $order->id,
                    'product_id'        => $item->product_id,
                    'qty'               => $item->qty,
                    'price_at_purchase' => $item->product->price,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Checkout berhasil',
                'order'   => $order->load('orderItems.product'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Checkout gagal',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }
}