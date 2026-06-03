@extends('layouts.kasir')

@section('header', 'Pesanan Kasir')

@section('content')

@if (session('success'))
    <div class="mb-4 p-4 rounded bg-green-100 text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Pesanan</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left text-gray-700">
                    <th class="px-4 py-3 border">No.</th>
                    <th class="px-4 py-3 border">Pesanan</th>
                    <th class="px-4 py-3 border">Pelanggan</th>
                    <th class="px-4 py-3 border">Tanggal</th>
                    <th class="px-4 py-3 border">Total</th>
                    <th class="px-4 py-3 border">Status</th>
                    <th class="px-4 py-3 border">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($orders as $order)
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

                        $total = $order->total_amount ?? $order->total_price ?? $order->total ?? 0;
                    @endphp

                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border">
                            {{ $loop->iteration }}
                        </td>

                        <td class="px-4 py-3 border">
                            <div class="font-semibold">
                                {{ $order->invoice_number ?? 'ORD-' . $order->id }}
                            </div>
                        </td>

                        <td class="px-4 py-3 border">
                            <div class="font-medium">
                                {{ $order->user->name ?? 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $order->user->email ?? '-' }}
                            </div>
                        </td>

                        <td class="px-4 py-3 border">
                            {{ $order->created_at?->format('d M Y H:i') ?? '-' }}
                        </td>

                        <td class="px-4 py-3 border">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-3 border">
                            <span class="px-3 py-1 rounded-full text-sm {{ $statusColor[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabel[$order->status] ?? $order->status }}
                            </span>
                        </td>

                        <td class="px-4 py-3 border">
                            <a href="{{ route('kasir.orders.show', $order->id) }}"
                               class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">
                                Lihat
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                            Belum ada pesanan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>

@endsection