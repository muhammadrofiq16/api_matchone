@extends('layouts.admin')

@section('header', 'Kelola Keranjang Belanja')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">📊 Keranjang Belanja</h2>
            <p class="text-gray-600 text-sm">Monitor dan kelola keranjang belanja pengguna</p>
        </div>
        <div class="text-right">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
                <p class="text-sm font-semibold opacity-90">Total Keranjang</p>
                <h3 class="text-3xl font-bold">{{ $totalCarts ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <form method="GET" class="flex gap-4 flex-wrap">
            @csrf
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pengguna</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email pengguna..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    🔍 Cari
                </button>
                <a href="{{ route('admin.carts.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Carts Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b-2 border-gray-200 bg-gray-50">
                    <th class="py-4 px-6 font-semibold text-gray-700">Pengguna</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Produk</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Jumlah</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Subtotal</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Ditambahkan</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($carts as $cart)
                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ $cart->user ? strtoupper(substr($cart->user->name, 0, 1)) : 'U' }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $cart->user->name ?? 'User Dihapus' }}</p>
                                <p class="text-xs text-gray-500">{{ $cart->user->email ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            @if($cart->product && $cart->product->image)
                                <img src="{{ asset('storage/products/'.$cart->product->image) }}" class="w-10 h-10 object-cover rounded-lg" alt="{{ $cart->product->name ?? 'Product' }}">
                            @else
                                <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400">-</div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-800">{{ $cart->product->name ?? 'Produk Dihapus' }}</p>
                                <p class="text-xs text-gray-500">Rp {{ $cart->product ? number_format($cart->product->price, 0, ',', '.') : '0' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $cart->qty }}</span>
                    </td>
                    <td class="py-4 px-6 font-bold text-gray-800">
                        @if($cart->product)
                            Rp {{ number_format($cart->qty * $cart->product->price, 0, ',', '.') }}
                        @else
                            Rp 0
                        @endif
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-600">
                        {{ $cart->created_at ? $cart->created_at->format('d M Y H:i') : '-' }}
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex gap-2">
                            <button class="delete-cart-btn px-3 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-200 transition" data-cart-id="{{ $cart->id }}">
                                🗑️ Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">
                        <p class="text-lg">📭 Tidak ada data keranjang</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($carts) && $carts->hasPages())
    <div class="mt-6">
        {{ $carts->links() }}
    </div>
    @endif
</div>

<script>
document.querySelectorAll('.delete-cart-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const cartId = this.getAttribute('data-cart-id');

        if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
            fetch(`/admin/carts/${cartId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                alert(data.message || 'Item berhasil dihapus');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            });
        }
    });
});
</script>
@endsection
