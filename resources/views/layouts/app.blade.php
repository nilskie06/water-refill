<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Water Refill Station')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --bg-primary: #0f172a; --bg-secondary: #1e1b4b; --accent-cyan: #06b6d4; --accent-violet: #8b5cf6; --accent-emerald: #10b981; }
        * { font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); }
        [x-cloak] { display: none !important; }
        .sidebar { background: rgba(0,0,0,0.3); backdrop-filter: blur(20px); border-right: 1px solid rgba(255,255,255,0.06); }
        .nav-link { transition: all 0.2s ease; border-radius: 0.75rem; margin: 2px 8px; }
        .nav-link:hover { background: rgba(255,255,255,0.08); }
        .nav-link.active { background: linear-gradient(135deg, rgba(6,182,212,0.15), rgba(139,92,246,0.15)); border: 1px solid rgba(6,182,212,0.2); }
        .mobile-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="min-h-screen text-white/90">
    @auth
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
        <div x-show="sidebarOpen" x-transition class="mobile-overlay fixed inset-0 z-40 lg:hidden" @click="sidebarOpen = false"></div>
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="sidebar fixed lg:sticky top-0 left-0 z-50 w-72 h-screen flex flex-col transition-transform duration-300 ease-in-out">
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(139,92,246,0.2));"><span class="text-xl">💧</span></div>
                    <div><h1 class="text-lg font-bold" style="background: linear-gradient(135deg, #06b6d4, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Water Refill</h1></div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white/50 hover:text-white p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <nav class="flex-1 mt-2 px-2">
                <a href="{{ route('dashboard') }}" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('dashboard') ? 'active text-white/90' : 'text-white/50 hover:text-white/90' }}"><span class="text-lg">📊</span> Dashboard</a>
                <a href="{{ route('customers') }}" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('customers*') ? 'active text-white/90' : 'text-white/50 hover:text-white/90' }}"><span class="text-lg">👥</span> Customers</a>
                <a href="{{ route('orders') }}" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('orders*') ? 'active text-white/90' : 'text-white/50 hover:text-white/90' }}"><span class="text-lg">📦</span> Orders</a>
                <a href="{{ route('payments') }}" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('payments*') ? 'active text-white/90' : 'text-white/50 hover:text-white/90' }}"><span class="text-lg">💰</span> Payments</a>
                <a href="{{ route('bottles') }}" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('bottles') ? 'active text-white/90' : 'text-white/50 hover:text-white/90' }}"><span class="text-lg">🍶</span> Bottles</a>
                <a href="{{ route('reports') }}" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium {{ request()->routeIs('reports') ? 'active text-white/90' : 'text-white/50 hover:text-white/90' }}"><span class="text-lg">📈</span> Reports</a>
            </nav>
            <div class="p-4 border-t border-white/5">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-white/40">{{ auth()->user()->name }}</span>
                    <form action="/logout" method="POST" class="inline">@csrf<button type="submit" class="text-xs px-3 py-1.5 rounded-lg" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.15);">Sign Out</button></form>
                </div>
            </div>
        </aside>

        <main class="flex-1 min-h-screen">
            <div class="sticky top-0 z-30 px-4 lg:px-8 py-4 flex items-center" style="background: rgba(15,23,42,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/5"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
            </div>
            <div class="p-4 lg:p-8">
                @if(session('success'))
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #6ee7b7;">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5;">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    @else
        @yield('content')
    @endauth
</body>
</html>
