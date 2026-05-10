<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // Menampilkan halaman daftar pesanan
    public function index()
    {
        // Ambil semua pesanan beserta data pelanggannya, urutkan dari yang terbaru
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();
        
        return view('admin.orders.index', compact('orders'));
    }

    // Mengubah status pesanan
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Validasi agar status tidak asal-asalan
        $request->validate([
            'status' => 'required|in:pending,paid,processing,completed,cancelled'
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Status pesanan #' . $order->invoice_number . ' berhasil diubah!');
    }
}