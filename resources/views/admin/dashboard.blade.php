@extends('layouts.admin')

@section('header', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-gradient-to-r from-emerald-500 to-green-600 rounded-2xl shadow-lg p-6 transform hover:scale-105 transition duration-300">
        <div class="flex justify-between items-center text-white">
            <div>
                <p class="text-green-100 text-sm font-semibold uppercase tracking-wider mb-1">Total Produk</p>
                <h3 class="text-4xl font-bold">{{ $totalProducts }}</h3>
            </div>
            <div class="p-3 bg-white/20 rounded-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 transform hover:scale-105 transition duration-300">
        <div class="flex justify-between items-center text-white">
            <div>
                <p class="text-blue-100 text-sm font-semibold uppercase tracking-wider mb-1">Kategori Menu</p>
                <h3 class="text-4xl font-bold">{{ $totalCategories }}</h3>
            </div>
            <div class="p-3 bg-white/20 rounded-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl shadow-lg p-6 transform hover:scale-105 transition duration-300">
        <div class="flex justify-between items-center text-white">
            <div>
                <p class="text-purple-100 text-sm font-semibold uppercase tracking-wider mb-1">Pengguna</p>
                <h3 class="text-4xl font-bold">{{ $totalUsers }}</h3>
            </div>
            <div class="p-3 bg-white/20 rounded-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.orders.index') }}" class="block bg-gradient-to-r from-orange-400 to-red-500 rounded-2xl shadow-lg p-6 transform hover:scale-105 transition duration-300">
        <div class="flex justify-between items-center text-white">
            <div>
                <p class="text-orange-100 text-sm font-semibold uppercase tracking-wider mb-1">Pesanan Aktif</p>
                <h3 class="text-4xl font-bold">{{ $activeOrders }}</h3>
            </div>
            <div class="p-3 bg-white/20 rounded-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
        </div>
    </a>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-md border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">✨ 5 Menu Matcha Terbaru</h3>
            <a href="{{ route('admin.products.index') }}" class="text-sm text-green-600 font-semibold hover:text-green-800 transition">Lihat Semua &rarr;</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-100 text-gray-500 text-sm">
                        <th class="py-3 px-2">Produk</th>
                        <th class="py-3 px-2">Kategori</th>
                        <th class="py-3 px-2">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestProducts as $product)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="py-3 px-2 flex items-center gap-3">
                            @if($product->image)
                                <img src="{{ asset('storage/products/'.$product->image) }}" class="w-10 h-10 object-cover rounded-lg shadow-sm">
                            @else
                                <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400">No Img</div>
                            @endif
                            <span class="font-semibold text-gray-700">{{ $product->name }}</span>
                        </td>
                        <td class="py-3 px-2 text-sm text-gray-600">
                            <span class="bg-gray-100 px-2 py-1 rounded text-xs font-medium">{{ $product->category->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-2 font-medium text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-6 text-center text-gray-400 italic">Belum ada produk yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 flex flex-col justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4">🚀 Status Sistem</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <p class="text-sm text-gray-600">API Database: <span class="font-semibold text-green-600">Connected</span></p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <p class="text-sm text-gray-600">Sanctum Auth: <span class="font-semibold text-green-600">Active</span></p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <p class="text-sm text-gray-600">Order System: <span class="font-semibold text-green-600">Active</span></p>
                </div>
            </div>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-100">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Tindakan Cepat</h4>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <a href="{{ route('admin.products.create') }}" class="text-center py-2 bg-green-50 text-green-700 rounded-lg text-sm font-semibold hover:bg-green-100 transition">+ Produk</a>
                <a href="{{ route('admin.categories.index') }}" class="text-center py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-semibold hover:bg-blue-100 transition">+ Kategori</a>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="block text-center py-2 bg-orange-50 text-orange-700 rounded-lg text-sm font-semibold hover:bg-orange-100 transition">📋 Kelola Pesanan</a>
        </div>
    </div>

</div>
@endsection