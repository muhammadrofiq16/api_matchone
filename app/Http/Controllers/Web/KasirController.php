<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasirController extends Controller
{
    public function index()
    {
        $hasOrderTable = Schema::hasTable('orders');

        $today = Carbon::today();

        // Pesanan hari ini
        $ordersToday = $hasOrderTable
            ? Order::whereDate('created_at', $today)->count()
            : 0;

        // Total transaksi hari ini
        $revenueToday = $hasOrderTable
            ? Order::whereDate('created_at', $today)
                ->whereIn('status', ['paid', 'completed'])
                ->sum('total_price')
            : 0;

        // Pesanan yang butuh diproses
        $activeOrders = $hasOrderTable
            ? Order::whereIn('status', ['pending', 'processing', 'paid'])->count()
            : 0;

        return view('kasir.dashboard', compact(
            'ordersToday',
            'revenueToday',
            'activeOrders'
        ));
    }

    public function pos()
    {
        // Ambil produk yang tersedia
        $products = Product::with('category')
            ->where('is_available', true)
            ->get();

        return view('kasir.pos', compact('products'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|string',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $cart = json_decode($request->cart, true);

        if (!$cart || count($cart) === 0) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        DB::beginTransaction();

        try {
            $total = 0;

            // Hitung total transaksi
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['id']);
                $qty = (int) $item['qty'];

                if ($qty <= 0) {
                    return back()->with('error', 'Jumlah produk tidak valid.');
                }

                $subtotal = $product->price * $qty;
                $total += $subtotal;
            }

            // Buat order
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_number' => 'POS-' . date('YmdHis'),
                'total_price' => $total,
                'status' => 'paid',
                'payment_method' => $request->payment_method ?? 'cash',
                'notes' => $request->notes,
            ]);

            // Buat order item
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['id']);
                $qty = (int) $item['qty'];
                $subtotal = $product->price * $qty;

               OrderItem::create([
    'order_id' => $order->id,
    'product_id' => $product->id,
    'qty' => $qty,
    'price' => $product->price,
    'price_at_purchase' => $product->price,
    'subtotal' => $subtotal,
]);
            }

            DB::commit();

            return redirect()
                ->route('kasir.orders.show', $order->id)
                ->with('success', 'Transaksi berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }
}