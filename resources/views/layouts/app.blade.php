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
        body { background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); min-height: 100vh; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse-glow { 0%, 100% { box-shadow: 0 0 5px rgba(6,182,212,0.2); } 50% { box-shadow: 0 0 20px rgba(6,182,212,0.4); } }
        .glass-card { background: rgba(255,255,255,0.04); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; }
        .sidebar { background: rgba(0,0,0,0.3); backdrop-filter: blur(20px); border-right: 1px solid rgba(255,255,255,0.06); }
        .nav-link { transition: all 0.2s ease; border-radius: 0.75rem; margin: 2px 8px; display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; }
        .nav-link:hover { background: rgba(255,255,255,0.08); color: white; }
        .nav-link.active { background: linear-gradient(135deg, rgba(6,182,212,0.15), rgba(139,92,246,0.15)); border: 1px solid rgba(6,182,212,0.2); color: white; }
        .stat-card { animation: fadeIn 0.5s ease forwards; }
        .stat-card:hover { animation: pulse-glow 2s ease infinite; border-color: rgba(6,182,212,0.3); }
        .btn-primary { background: linear-gradient(135deg, var(--accent-cyan), #2563eb); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(6,182,212,0.3); }
        .btn-secondary { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); transition: all 0.2s ease; color: rgba(255,255,255,0.7); }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); color: white; }
        .input-field { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; color: rgba(255,255,255,0.9); transition: all 0.2s ease; }
        .input-field:focus { border-color: var(--accent-cyan); box-shadow: 0 0 0 3px rgba(6,182,212,0.15); outline: none; }
        .table-container { background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; overflow: hidden; }
        .table-row { border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.2s; }
        .table-row:hover { background: rgba(255,255,255,0.04); }
        .table-row:nth-child(even) { background: rgba(255,255,255,0.02); }
        .badge { padding: 4px 12px; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; }
        .badge-pending { background: rgba(251,191,36,0.12); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2); }
        .badge-delivered { background: rgba(6,182,212,0.12); color: #22d3ee; border: 1px solid rgba(6,182,212,0.2); }
        .badge-completed { background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.2); }
        .badge-cancelled { background: rgba(244,63,94,0.12); color: #fb7185; border: 1px solid rgba(244,63,94,0.2); }
        .mobile-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
        select.input-field { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E"); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; }
        select.input-field option { background: #1e1b4b; color: white; }
    </style>
</head>
<body class="text-white/90">
    <div x-data="{ open: false }" class="flex min-h-screen">
        <!-- Mobile Menu Button -->
        <button @click="open = !open" class="lg:hidden fixed top-4 left-4 z-50 p-2 glass-card text-white/70 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <!-- Mobile Overlay -->
        <div x-show="open" @click="open = false" x-transition:enter="transition-opacity duration-200" x-transition:leave="transition-opacity duration-200" class="lg:hidden fixed inset-0 z-40 mobile-overlay"></div>

        <!-- Sidebar -->
        <aside :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="sidebar fixed lg:sticky top-0 left-0 w-64 h-screen z-40 transition-transform duration-300 flex flex-col">
            <div class="p-6">
                <h1 class="text-xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">💧 Water Refill</h1>
                <p class="text-white/40 text-xs mt-1">Management System</p>
            </div>
            <nav class="mt-2 px-2 flex-1">
                <a href="/dashboard" class="nav-link @if(request()->routeIs('dashboard')) active @endif">📊 Dashboard</a>
                <a href="/customers" class="nav-link @if(request()->routeIs('customers*')) active @endif">👥 Customers</a>
                <a href="/orders" class="nav-link @if(request()->routeIs('orders*')) active @endif">📦 Orders</a>
                <a href="/payments" class="nav-link @if(request()->routeIs('payments*')) active @endif">💰 Payments</a>
                <a href="/bottles" class="nav-link @if(request()->routeIs('bottles')) active @endif">🍶 Bottles</a>
                <a href="/reports" class="nav-link @if(request()->routeIs('reports')) active @endif">📈 Reports</a>
            </nav>
            <div class="p-4 border-t border-white/5">
                <form action="/logout" method="POST">@csrf<button type="submit" class="text-white/40 hover:text-white/70 text-sm transition">← Logout</button></form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 lg:p-8" style="animation: fadeIn 0.6s ease">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
