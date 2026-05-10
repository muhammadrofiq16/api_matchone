<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchone Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased flex h-screen">

    <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">
        <div class="h-16 flex items-center justify-center border-b border-gray-800">
            <h1 class="text-2xl font-bold text-green-500">Matchone</h1>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/admin/dashboard" class="block px-4 py-2 rounded hover:bg-gray-800 transition">📊 Dashboard</a>
            <a href="/admin/categories" class="block px-4 py-2 rounded hover:bg-gray-800 transition">🏷️ Kategori</a>
            <a href="/admin/products" class="block px-4 py-2 rounded hover:bg-gray-800 transition">🍵 Produk</a>
            <a href="/admin/users" class="block px-4 py-2 rounded hover:bg-gray-800 transition">👥 Users</a>
        </nav>
        <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
            © 2026 Muhammad Rofiq
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white shadow flex items-center justify-between px-6">
            <h2 class="text-xl font-semibold text-gray-800">@yield('header')</h2>
            <div class="text-gray-600">Admin</div>
        </header>

        <div class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </div>
    </main>

</body>
</html>