@extends('layouts.kasir')

@section('header', 'Detail Pesanan')

@section('content')
<div class="space-y-6">
    @if ($message = Session::get('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-green-700">{{ $message }}</p>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Pesanan #{{ $order->invoice_number }}</h1>
        <a href="{{ route('kasir.orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
            ← Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Utama -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Pelanggan -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">👤 Informasi Pelanggan</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nama Pelanggan</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Email</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->user->email }}</p>
                    </div>
                    @if($order->user->phone)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">No. Telepon</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->user->phone }}</p>
                        </div>
                    @endif
                    @if($order->user->address)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Alamat</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->user->address }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Item Pesanan -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📦 Item Pesanan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700">Qty</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($order->orderItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex gap-3 items-start">
                                            @if($item->product->image)
                                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded-lg">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400">No Img</div>
                                            @endif
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                                                @if($item->product->sku)
                                                    <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center text-gray-900">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-green-600">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-400">Tidak ada item</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Informasi Pembayaran -->
            @if($order->payment)
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">💳 Informasi Pembayaran</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Metode Pembayaran</p>
                            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($order->payment->payment_method ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status Pembayaran</p>
                            @switch($order->payment->status ?? 'pending')
                                @case('pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">Pending</span>
                                    @break
                                @case('paid')
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">Lunas</span>
                                    @break
                                @case('failed')
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">Gagal</span>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Ringkasan Pesanan -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">💰 Ringkasan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($order->orderItems->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}</span>
                    </div>
                    @if(($order->discount_amount ?? 0) > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Diskon:</span>
                            <span class="font-semibold">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if(($order->tax_amount ?? 0) > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pajak:</span>
                            <span class="font-semibold">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if(($order->shipping_cost ?? 0) > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkir:</span>
                            <span class="font-semibold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="border-t border-gray-200 pt-3 flex justify-between">
                        <span class="font-bold text-gray-900">Total:</span>
                        <span class="text-2xl font-bold text-green-600">Rp {{ number_format($order->total_amount ?? $order->total_price ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Pesanan -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Status</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Status Saat Ini</p>
                        @switch($order->status)
                            @case('pending')
                                <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg text-sm font-semibold inline-block">Pending</span>
                                @break
                            @case('paid')
                                <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg text-sm font-semibold inline-block">Sudah Dibayar</span>
                                @break
                            @case('processing')
                                <span class="bg-purple-100 text-purple-800 px-4 py-2 rounded-lg text-sm font-semibold inline-block">Diproses</span>
                                @break
                            @case('completed')
                                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-sm font-semibold inline-block">Selesai</span>
                                @break
                            @case('cancelled')
                                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-lg text-sm font-semibold inline-block">Dibatalkan</span>
                                @break
                        @endswitch
                    </div>
                    <div class="text-sm text-gray-500">
                        <p class="mb-2"><strong>Dibuat:</strong> {{ $order->created_at->format('d F Y H:i') }}</p>
                        <p><strong>Diupdate:</strong> {{ $order->updated_at->format('d F Y H:i') }}</p>
                    </div>
                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                        <button onclick="openStatusModal()" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-semibold transition">
                            ✏️ Ubah Status
                        </button>
                    @endif
                </div>
            </div>

            @if($order->notes)
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">📝 Catatan</h3>
                    <p class="text-gray-700">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Ubah Status -->
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Ubah Status Pesanan #{{ $order->invoice_number }}</h3>
        </div>
        <form action="{{ route('kasir.orders.updateStatus', $order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="px-6 py-4">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Status Baru</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Sudah Dibayar</option>
                    <option value="processing">Diproses</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatusModal() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});
</script>

@endsection
