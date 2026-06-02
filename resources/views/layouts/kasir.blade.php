<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchone Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased flex h-screen">

    <aside class="w-64 bg-gray-800 text-white flex flex-col hidden md:flex">
        <div class="h-16 flex items-center justify-center border-b border-gray-700">
            <h1 class="text-2xl font-bold text-green-400">Matchone POS</h1>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/kasir/dashboard" class="block px-4 py-2 rounded hover:bg-gray-700 transition">📊 Dashboard</a>
            <a href="/kasir/pos" class="block px-4 py-2 rounded hover:bg-gray-700 transition">🛒 Menu Kasir</a>
            <a href="/kasir/orders" class="block px-4 py-2 rounded hover:bg-gray-700 transition">📋 Pesanan Masuk</a>
            <a href="/kasir/categories" class="block px-4 py-2 rounded hover:bg-gray-700 transition">🏷️ Tambah Kategori</a>
            <a href="/kasir/products" class="block px-4 py-2 rounded hover:bg-gray-700 transition">🍵 Tambah Produk</a>
        </nav>
        <div class="p-4 border-t border-gray-700 text-sm text-gray-400">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 rounded hover:bg-red-600 hover:text-white transition">🚪 Logout</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white shadow flex items-center justify-between px-6">
            <h2 class="text-xl font-semibold text-gray-800">@yield('header')</h2>
            <div class="text-gray-600">
                <span class="font-medium">{{ Auth::user()->name ?? 'Kasir' }}</span>
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded ml-2">Kasir</span>
            </div>
        </header>

        <div class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </div>
    </main>

</body>
</html>
