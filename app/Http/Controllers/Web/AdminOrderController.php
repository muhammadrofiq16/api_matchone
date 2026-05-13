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
        // Menggunakan paginate(10) agar fungsi links() di Blade berjalan lancar
        $orders = Order::with('user')->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.orders.index', compact('orders'));
    }

    // Menampilkan detail pesanan (Tambahan Baru)
    public function show($id)
    {
        // Mengambil detail pesanan beserta relasi item, produk, dan usernya
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }

    // Mengubah status pesanan
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Validasi agar status tidak asal-asalan (sesuai yang kamu buat)
        $request->validate([
            'status' => 'required|in:pending,paid,processing,completed,cancelled'
        ]);

        $order->status = $request->status;
        $order->save();

        // Menyimpan nomor pesanan ke dalam flash message
        return redirect()->back()->with('success', 'Status pesanan #' . $order->invoice_number . ' berhasil diubah!');
    }
}