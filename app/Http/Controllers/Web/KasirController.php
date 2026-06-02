<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
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
            
        // Total transaksi hari ini (hanya yang paid/completed)
        $revenueToday = $hasOrderTable
            ? Order::whereDate('created_at', $today)
                   ->whereIn('status', ['paid', 'completed'])
                   ->sum('total_amount')
            : 0;

        // Pesanan yang butuh diproses (pending/processing)
        $activeOrders = $hasOrderTable
            ? Order::whereIn('status', ['pending', 'processing', 'paid'])->count()
            : 0;

        return view('kasir.dashboard', compact('ordersToday', 'revenueToday', 'activeOrders'));
    }

    public function pos()
    {
        // Ambil produk yang tersedia
        $products = Product::with('category')->where('is_available', true)->get();
        return view('kasir.pos', compact('products'));
    }
}
