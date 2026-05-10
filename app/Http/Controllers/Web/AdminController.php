<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index()
    {
        // Pengecekan tabel untuk mencegah error "Table not found" setelah migrate:fresh
        $hasOrderTable = Schema::hasTable('orders');

        $totalProducts = Product::count();
        $totalCategories = Category::count();
        
        // Perbaikan 1: Hanya hitung user yang BUKAN admin agar angka sinkron (5 user)
        $totalUsers = User::where('role', '!=', 'admin')->count(); 
        
        // Perbaikan 2: Cek keberadaan tabel orders sebelum menghitung
        $totalOrders = $hasOrderTable ? Order::count() : 0;
        
        $activeOrders = $hasOrderTable 
            ? Order::whereIn('status', ['pending', 'processing', 'paid'])->count() 
            : 0;
        
        // Mengambil 5 produk terbaru beserta data kategorinya
        $latestProducts = Product::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProducts', 
            'totalCategories', 
            'totalUsers', 
            'totalOrders', 
            'activeOrders', 
            'latestProducts'
        ));
    }

    public function showLogin()
    {
        // Pastikan jika sudah login admin tidak perlu ke halaman login lagi
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // 1. Validasi input form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba melakukan login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 3. Proteksi: Hanya role 'admin' yang boleh masuk ke Web Dashboard
            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                // Jika user biasa (Android) mencoba masuk, paksa logout
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akses ditolak. Area ini khusus untuk Administrator Matchone.',
                ]);
            }
        }

        // 3. Jika email/password salah
        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }
}