@extends('layouts.admin')

@section('header', 'Detail Pesanan')

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-blue-500 hover:text-blue-600 font-medium">
            ← Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold mb-4">Informasi Pelanggan</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 mb-1">Nama</p>
                        <p class="font-semibold">{{ $order->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Email</p>
                        <p class="font-semibold">{{ $order->user->email ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold mb-4">Item Pesanan</h2>
                <table class="w-full text-sm">
                    <thead class="border-b">
                        <tr>
                            <th class="text-left py-2">Produk</th>
                            <th class="text-right py-2">Harga</th>
                            <th class="text-center py-2">Qty</th>
                            <th class="text-right py-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->orderItems ?? [] as $item)
                            <tr class="border-b">
                                <td class="py-3">
                                    <p class="font-medium">{{ $item->product->name ?? 'Produk' }}</p>
                                </td>
                                <td class="py-3 text-right">Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</td>
                                <td class="py-3 text-center">{{ $item->quantity ?? 0 }}</td>
                                <td class="py-3 text-right font-semibold">Rp {{ number_format(($item->price ?? 0) * ($item->quantity ?? 0), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-400">Tidak ada item</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold mb-4">Ringkasan</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Total:</span>
                        <span class="font-bold text-lg text-green-600">
                            Rp {{ number_format($order->total_amount ?? $order->total_price ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold mb-4">Status</h2>
                @php
                    $statusColor = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'paid' => 'bg-blue-100 text-blue-800',
                        'processing' => 'bg-purple-100 text-purple-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    $statusLabel = [
                        'pending' => 'Pending',
                        'paid' => 'Dibayar',
                        'processing' => 'Diproses',
                        'completed' => 'Selesai',
                        'cancelled' => 'Batal',
                    ];
                @endphp
                <span class="px-4 py-2 rounded-lg text-sm font-semibold inline-block {{ $statusColor[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabel[$order->status] ?? $order->status }}
                </span>
                
                @if($order->status !== 'completed' && $order->status !== 'cancelled')
                    <button onclick="showStatusModal()" class="w-full mt-4 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg font-semibold">
                        Ubah Status
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Status -->
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-96">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold">Ubah Status Pesanan #{{ $order->invoice_number }}</h3>
        </div>
        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6">
                <label class="block text-sm font-semibold mb-2">Status Baru</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    <option value="">Pilih Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Dibayar</option>
                    <option value="processing">Diproses</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Batal</option>
                </select>
            </div>
            <div class="p-6 border-t flex gap-3 justify-end">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 border border-gray-300 rounded-lg">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showStatusModal() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

document.getElementById('statusModal').addEventListener('click', (e) => {
    if (e.target.id === 'statusModal') closeStatusModal();
});
</script>

@endsection
