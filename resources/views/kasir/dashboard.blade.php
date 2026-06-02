@extends('layouts.kasir')

@section('header', 'Dashboard Kasir')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Stat 1 -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Pesanan Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800">{{ $ordersToday }}</p>
            </div>
        </div>
    </div>
    
    <!-- Stat 2 -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Pendapatan Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($revenueToday, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600">Pesanan Aktif</p>
                <p class="text-3xl font-bold text-gray-800">{{ $activeOrders }}</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Akses Cepat</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('kasir.pos') }}" class="flex items-center p-4 bg-green-50 rounded-lg border border-green-100 hover:bg-green-100 transition">
            <div class="text-green-600 mr-3 text-2xl">🛒</div>
            <div>
                <h4 class="font-semibold text-green-800">Buka Menu POS</h4>
                <p class="text-sm text-green-600">Buat pesanan langsung untuk pelanggan.</p>
            </div>
        </a>
        <a href="{{ route('kasir.orders.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 transition">
            <div class="text-blue-600 mr-3 text-2xl">📋</div>
            <div>
                <h4 class="font-semibold text-blue-800">Cek Pesanan Masuk</h4>
                <p class="text-sm text-blue-600">Lihat dan proses pesanan yang masuk.</p>
            </div>
        </a>
    </div>
</div>
@endsection
