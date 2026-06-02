@extends('layouts.kasir')

@section('header', 'Manajemen Pesanan')

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Daftar Pesanan</h2>

        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold">No. Pesanan</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Pelanggan</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Total</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $order)
                    @php
                        $statusColor = [
                            'pending'    => 'bg-yellow-100 text-yellow-800',
                            'paid'       => 'bg-blue-100 text-blue-800',
                            'processing' => 'bg-purple-100 text-purple-800',
                            'completed'  => 'bg-green-100 text-green-800',
                            'cancelled'  => 'bg-red-100 text-red-800',
                        ];
                        $statusLabel = [
                            'pending'    => 'Pending',
                            'paid'       => 'Dibayar',
                            'processing' => 'Diproses',
                            'completed'  => 'Selesai',
                            'cancelled'  => 'Batal',
                        ];
                        $orderId      = $order->id ?? 0;
                        $invoiceNum   = $order->invoice_number ?? 'N/A';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-xs font-semibold">
                                {{ $invoiceNum }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm">
                                <p class="font-medium">{{ $order->user->name ?? 'N/A' }}</p>
                                <p class="text-gray-500 text-xs">{{ $order->user->email ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $order->created_at?->format('d M Y H:i') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-green-600">
                            Rp {{ number_format($order->total_amount ?? $order->total_price ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded text-xs font-semibold {{ $statusColor[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabel[$order->status] ?? $order->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('kasir.orders.show', $orderId) }}"
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium transition">
                                    Lihat
                                </a>
                                @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                    <button
                                        data-order-id="{{ $orderId }}"
                                        data-invoice="{{ $invoiceNum }}"
                                        onclick="showStatusModal(this)"
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-xs font-medium transition">
                                        Ubah
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">
                            Belum ada pesanan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($orders->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ===== MODAL UBAH STATUS (hanya satu, tidak duplikat) ===== --}}
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Ubah Status Pesanan</h3>
            <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')
            <div class="px-6 py-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Status Baru</label>
                <select name="status" id="statusSelect"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <option value="">-- Pilih Status --</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Sudah Dibayar</option>
                    <option value="processing">Diproses</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
                <button type="button" onclick="closeStatusModal()"
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Baca data-order-id dan data-invoice dari atribut tombol yang diklik.
    function showStatusModal(btn) {
        var orderId     = btn.getAttribute('data-order-id');
        var invoiceNum  = btn.getAttribute('data-invoice');

        document.getElementById('modalTitle').textContent = 'Ubah Status Pesanan #' + invoiceNum;
        document.getElementById('statusForm').action      = '/admin/orders/' + orderId + '/status';
        document.getElementById('statusSelect').value     = '';
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }

    // Tutup modal jika klik di luar area modal
    document.getElementById('statusModal').addEventListener('click', function (e) {
        if (e.target === this) closeStatusModal();
    });
</script>
@endsection
