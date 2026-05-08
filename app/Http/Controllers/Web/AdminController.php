<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Wajib ditambahkan untuk fitur Login
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();
        
        // Mengambil 5 produk terbaru beserta data kategorinya untuk tabel di dashboard
        $latestProducts = Product::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalProducts', 'totalCategories', 'totalUsers', 'latestProducts'));
    }

    public function showLogin()
    {
        return view('admin.login');
    }

    // Fungsi untuk memproses data dari form login
    public function login(Request $request)
    {
        // 1. Validasi input form
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba melakukan login
        if (Auth::attempt($credentials)) {
            // Mencegah session fixation attack
            $request->session()->regenerate();

            // Cek apakah user yang login memiliki role 'admin'
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                // Jika user biasa (pengguna aplikasi Android) mencoba login ke web
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akses ditolak. Anda bukan admin Matchone.',
                ]);
            }
        }

        // 3. Jika email/password salah
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Fungsi untuk logout
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}