@extends('layouts.kasir')

@section('header', 'Menu Kasir (POS)')

@section('content')

@if(session('success'))
    <div class="mb-4 p-4 rounded bg-green-100 text-green-700">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 rounded bg-red-100 text-red-700">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LIST PRODUK --}}
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Pilih Produk</h3>

       <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    @forelse($products as $product)
        <div class="border rounded-xl p-4 hover:shadow-lg transition bg-white min-h-[360px] flex flex-col">

            {{-- FOTO PRODUK CLOUDINARY --}}
            @if(!empty($product->image))
                <img src="{{ $product->image }}"
                     alt="{{ $product->name }}"
                     class="w-full h-56 object-cover rounded-xl mb-4 bg-gray-100">
            @else
                <div class="w-full h-56 bg-gray-200 rounded-xl mb-4 flex items-center justify-center text-gray-500 text-sm">
                    Tidak ada foto
                </div>
            @endif

            <h4 class="font-semibold text-gray-800 text-base">
                {{ $product->name }}
            </h4>

            <p class="text-sm text-gray-500 mb-2">
                {{ $product->category->name ?? 'Tanpa kategori' }}
            </p>

            <p class="font-bold text-green-600 text-lg mb-4">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </p>

            <button type="button"
                class="btn-add-cart mt-auto w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg"
                data-id="{{ $product->id }}"
                data-name="{{ $product->name }}"
                data-price="{{ $product->price }}">
                Tambah
            </button>
        </div>
    @empty
        <p class="text-gray-500">
            Belum ada produk tersedia.
        </p>
    @endforelse
        </div>
    </div>


    {{-- KERANJANG --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Keranjang Pesanan</h3>

        <div id="cart-items" class="space-y-3">
            <p class="text-gray-500">Keranjang kosong.</p>
        </div>

        <div class="border-t mt-4 pt-4 space-y-2">
            <div class="flex justify-between">
                <span>Total Item</span>
                <span id="total-item">0</span>
            </div>

            <div class="flex justify-between font-bold text-lg">
                <span>Total Bayar</span>
                <span id="total-price">Rp 0</span>
            </div>
        </div>

        <form action="{{ route('kasir.pos.checkout') }}" method="POST" class="mt-6">
            @csrf

            <input type="hidden" name="cart" id="cart-input">

            <label class="block text-sm font-medium mb-1">
                Metode Pembayaran
            </label>

            <select name="payment_method" class="w-full border rounded px-3 py-2 mb-3">
                <option value="cash">Cash</option>
                <option value="qris">QRIS</option>
                <option value="transfer">Transfer</option>
            </select>

            <label class="block text-sm font-medium mb-1">
                Catatan
            </label>

            <textarea name="notes" class="w-full border rounded px-3 py-2 mb-3" rows="2"></textarea>

            <button type="submit"
                onclick="prepareCheckout(event)"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Proses Pembayaran
            </button>
        </form>
    </div>

</div>


<script>
    let cart = [];

    function rupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    function addToCart(id, name, price) {
        const existing = cart.find(item => item.id === id);

        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({
                id: id,
                name: name,
                price: Number(price),
                qty: 1
            });
        }

        renderCart();
    }

    function increaseQty(id) {
        const item = cart.find(item => item.id === id);

        if (item) {
            item.qty += 1;
        }

        renderCart();
    }

    function decreaseQty(id) {
        const item = cart.find(item => item.id === id);

        if (item) {
            item.qty -= 1;

            if (item.qty <= 0) {
                cart = cart.filter(item => item.id !== id);
            }
        }

        renderCart();
    }

    function removeItem(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    function renderCart() {
        const cartItems = document.getElementById('cart-items');
        const totalItem = document.getElementById('total-item');
        const totalPrice = document.getElementById('total-price');

        cartItems.innerHTML = '';

        if (cart.length === 0) {
            cartItems.innerHTML = '<p class="text-gray-500">Keranjang kosong.</p>';
        }

        let itemCount = 0;
        let total = 0;

        cart.forEach(item => {
            itemCount += item.qty;
            total += item.price * item.qty;

            cartItems.innerHTML += `
                <div class="border rounded p-3">
                    <div class="font-semibold text-gray-800">
                        ${item.name}
                    </div>

                    <div class="text-sm text-gray-500">
                        ${rupiah(item.price)}
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center gap-2">
                            <button type="button"
                                onclick="decreaseQty(${item.id})"
                                class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded">
                                -
                            </button>

                            <span class="font-semibold">
                                ${item.qty}
                            </span>

                            <button type="button"
                                onclick="increaseQty(${item.id})"
                                class="px-2 py-1 bg-gray-200 hover:bg-gray-300 rounded">
                                +
                            </button>
                        </div>

                        <button type="button"
                            onclick="removeItem(${item.id})"
                            class="text-red-600 hover:text-red-800 text-sm">
                            Hapus
                        </button>
                    </div>

                    <div class="text-sm font-semibold text-right mt-2">
                        Subtotal: ${rupiah(item.price * item.qty)}
                    </div>
                </div>
            `;
        });

        totalItem.innerText = itemCount;
        totalPrice.innerText = rupiah(total);
    }

    function prepareCheckout(event) {
        if (cart.length === 0) {
            event.preventDefault();
            alert('Keranjang masih kosong.');
            return;
        }

        document.getElementById('cart-input').value = JSON.stringify(cart);
    }

    document.querySelectorAll('.btn-add-cart').forEach(button => {
        button.addEventListener('click', function () {
            const id = Number(this.dataset.id);
            const name = this.dataset.name;
            const price = Number(this.dataset.price);

            addToCart(id, name, price);
        });
    });
</script>

@endsection