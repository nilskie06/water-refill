<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Water Refill Station')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    @auth
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-800 text-white flex-shrink-0">
            <div class="p-4">
                <h1 class="text-xl font-bold">💧 Water Refill</h1>
                <p class="text-blue-200 text-sm">Management System</p>
            </div>
            <nav class="mt-2">
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('customers') }}" class="block px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('customers*') ? 'bg-blue-700' : '' }}">
                    👥 Customers
                </a>
                <a href="{{ route('orders') }}" class="block px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('orders*') ? 'bg-blue-700' : '' }}">
                    📦 Orders
                </a>
                <a href="{{ route('payments') }}" class="block px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('payments*') ? 'bg-blue-700' : '' }}">
                    💰 Payments
                </a>
                <a href="{{ route('bottles') }}" class="block px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('bottles') ? 'bg-blue-700' : '' }}">
                    🍶 Bottles
                </a>
                <a href="{{ route('reports') }}" class="block px-4 py-3 hover:bg-blue-700 {{ request()->routeIs('reports') ? 'bg-blue-700' : '' }}">
                    📈 Reports
                </a>
            </nav>
            <div class="absolute bottom-0 w-64 p-4 border-t border-blue-700">
                <div class="flex items-center justify-between">
                    <span class="text-sm">{{ auth()->user()->name }}</span>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="text-blue-200 hover:text-white text-sm">Logout</button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    @else
        @yield('content')
    @endauth
</body>
</html>
