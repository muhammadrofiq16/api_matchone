@extends('layouts.kasir')

@section('header', 'Menu Kasir (POS)')

@section('content')
<div class="flex flex-col md:flex-row gap-6 h-full" x-data="posApp()">
    <!-- Bagian Produk -->
    <div class="w-full md:w-2/3 bg-white rounded-lg shadow flex flex-col h-[calc(100vh-120px)]">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-gray-700">Pilih Produk</h3>
            <input type="text" placeholder="Cari..." class="border rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-green-300">
        </div>
        <div class="p-4 overflow-y-auto grid grid-cols-2 lg:grid-cols-3 gap-4 flex-1">
            @forelse($products as $product)
            <div class="border rounded-lg p-3 cursor-pointer hover:shadow-md transition bg-gray-50 flex flex-col items-center text-center" @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                <img src="{{ $product->image ?? 'https://via.placeholder.com/100' }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded mb-2">
                <h4 class="font-medium text-sm text-gray-800">{{ $product->name }}</h4>
                <p class="text-green-600 font-bold text-sm mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 py-8">Belum ada produk tersedia.</div>
            @endforelse
        </div>
    </div>

    <!-- Bagian Keranjang / Order -->
    <div class="w-full md:w-1/3 bg-white rounded-lg shadow flex flex-col h-[calc(100vh-120px)]">
        <div class="p-4 border-b bg-gray-50 rounded-t-lg">
            <h3 class="font-semibold text-gray-700">Keranjang Pesanan</h3>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
            <template x-if="cart.length === 0">
                <div class="text-center text-gray-400 py-10">Keranjang kosong.</div>
            </template>
            
            <template x-for="(item, index) in cart" :key="index">
                <div class="flex justify-between items-center mb-3 bg-white p-3 rounded shadow-sm">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium" x-text="item.name"></h4>
                        <div class="flex items-center mt-1">
                            <button @click="decreaseQty(index)" class="w-6 h-6 rounded bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-xs">-</button>
                            <span class="mx-3 text-sm" x-text="item.qty"></span>
                            <button @click="increaseQty(index)" class="w-6 h-6 rounded bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-xs">+</button>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-gray-800" x-text="formatRupiah(item.price * item.qty)"></div>
                        <button @click="removeItem(index)" class="text-red-500 text-xs mt-1 hover:underline">Hapus</button>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-4 border-t bg-white rounded-b-lg">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Total Item</span>
                <span class="font-medium" x-text="totalItems()"></span>
            </div>
            <div class="flex justify-between mb-4 text-lg">
                <span class="font-bold text-gray-800">Total Bayar</span>
                <span class="font-bold text-green-600" x-text="formatRupiah(totalPrice())"></span>
            </div>
            
            <button @click="processOrder()" :disabled="cart.length === 0" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                Proses Pembayaran
            </button>
        </div>
    </div>
</div>

<script>
    function posApp() {
        return {
            cart: [],
            
            addToCart(id, name, price) {
                const existing = this.cart.find(item => item.id === id);
                if (existing) {
                    existing.qty++;
                } else {
                    this.cart.push({ id, name, price, qty: 1 });
                }
            },
            
            increaseQty(index) {
                this.cart[index].qty++;
            },
            
            decreaseQty(index) {
                if (this.cart[index].qty > 1) {
                    this.cart[index].qty--;
                } else {
                    this.removeItem(index);
                }
            },
            
            removeItem(index) {
                this.cart.splice(index, 1);
            },
            
            totalItems() {
                return this.cart.reduce((sum, item) => sum + item.qty, 0);
            },
            
            totalPrice() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            },
            
            formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
            },
            
            processOrder() {
                if(this.cart.length === 0) return;
                alert('Fitur proses pesanan Kasir belum dihubungkan ke backend (Checkout Controller).\nTotal Bayar: ' + this.formatRupiah(this.totalPrice()));
                // Idealnya: Kirim data this.cart via fetch/axios ke backend untuk membuat Order
                this.cart = [];
            }
        }
    }
</script>
@endsection
