@php
    $activePage = '';
    if (request()->routeIs('dashboard')) $activePage = 'dashboard';
    elseif (request()->routeIs('customers*')) $activePage = 'customers';
    elseif (request()->routeIs('orders*')) $activePage = 'orders';
    elseif (request()->routeIs('payments*')) $activePage = 'payments';
    elseif (request()->routeIs('bottles')) $activePage = 'bottles';
    elseif (request()->routeIs('deliveries.calendar')) $activePage = 'calendar';
    elseif (request()->routeIs('deliveries.routes')) $activePage = 'routes';
    elseif (request()->routeIs('deliveries.history')) $activePage = 'history';
    elseif (request()->routeIs('deliveries*')) $activePage = 'deliveries';
    elseif (request()->routeIs('drivers')) $activePage = 'drivers';
    elseif (request()->routeIs('vehicles')) $activePage = 'vehicles';
    elseif (request()->routeIs('reports')) $activePage = 'reports';
@endphp
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
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); }
        @media (max-width: 1023px) { body { padding-bottom: 80px; } }
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
        .badge { padding: 4px 12px; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; }
        .badge-pending { background: rgba(251,191,36,0.12); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2); }
        .badge-delivered { background: rgba(6,182,212,0.12); color: #22d3ee; border: 1px solid rgba(6,182,212,0.2); }
        .badge-completed { background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.2); }
        .badge-cancelled { background: rgba(244,63,94,0.12); color: #fb7185; border: 1px solid rgba(244,63,94,0.2); }
        select.input-field { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E"); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; }
        select.input-field option { background: #1e1b4b; color: white; }
        .mobile-cards { display: none; }
        @media (max-width: 768px) { .desktop-table { display: none !important; } .mobile-cards { display: block !important; } }
        .hamburger { display: flex; flex-direction: column; justify-content: space-between; width: 22px; height: 16px; cursor: pointer; }
        .hamburger span { display: block; height: 2px; width: 100%; background: white; border-radius: 2px; transition: all 0.3s ease; }
        .hamburger.open span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; z-index: 50; background: rgba(15,23,42,0.95); backdrop-filter: blur(20px); border-top: 1px solid rgba(255,255,255,0.08); padding: 8px 0 env(safe-area-inset-bottom, 8px); }
        .bottom-nav-item { display: flex; flex-direction: column; align-items: center; gap: 2px; padding: 6px 0; color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.625rem; transition: all 0.2s; min-width: 48px; }
        .bottom-nav-item.active { color: var(--accent-cyan); }
        .bottom-nav-item svg { width: 22px; height: 22px; }
        @media (min-width: 1024px) { .bottom-nav { display: none; } body { padding-bottom: 0; } }
        #loading-screen { position: fixed; inset: 0; z-index: 9999; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); display: flex; align-items: center; justify-content: center; transition: opacity 0.5s ease, visibility 0.5s ease; }
        #loading-screen.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        .loader-container { text-align: center; position: relative; }
        .loader-ring { width: 80px; height: 80px; margin: 0 auto 24px; position: relative; }
        .loader-ring::before, .loader-ring::after { content: ''; position: absolute; inset: 0; border-radius: 50%; border: 3px solid transparent; }
        .loader-ring::before { border-top-color: #06b6d4; border-right-color: #06b6d4; animation: spin 1s linear infinite; }
        .loader-ring::after { inset: 8px; border-bottom-color: #8b5cf6; border-left-color: #8b5cf6; animation: spin 1.5s linear infinite reverse; }
        .loader-dot { width: 8px; height: 8px; border-radius: 50%; background: #06b6d4; display: inline-block; margin: 0 4px; animation: bounce 1.4s infinite ease-in-out; }
        .loader-dot:nth-child(1) { animation-delay: -0.32s; }
        .loader-dot:nth-child(2) { animation-delay: -0.16s; background: #8b5cf6; }
        .loader-dot:nth-child(3) { background: #10b981; }
        .loader-text { margin-top: 20px; font-size: 0.875rem; color: rgba(255,255,255,0.5); letter-spacing: 0.2em; text-transform: uppercase; animation: pulse-text 2s ease-in-out infinite; }
        .loader-icon { font-size: 2.5rem; margin-bottom: 16px; animation: float 3s ease-in-out infinite; }
        .loader-glow { position: absolute; width: 200px; height: 200px; background: radial-gradient(circle, rgba(6,182,212,0.15) 0%, transparent 70%); border-radius: 50%; animation: glow-pulse 2s ease-in-out infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
        @keyframes glow-pulse { 0%, 100% { transform: scale(1); opacity: 0.5; } 50% { transform: scale(1.3); opacity: 1; } }
        @keyframes pulse-text { 0%, 100% { opacity: 0.5; } 50% { opacity: 1; } }
        @keyframes slide-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .content-reveal { animation: slide-up 0.6s ease forwards; }
    </style>
</head>
<body class="text-white/90">
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loader-container">
            <div class="loader-glow"></div>
            <div class="loader-icon">💧</div>
            <div class="loader-ring"></div>
            <div style="margin-top: 20px;"><span class="loader-dot"></span><span class="loader-dot"></span><span class="loader-dot"></span></div>
            <div class="loader-text">Loading Water Refill</div>
        </div>
    </div>

    <div x-data="{ sidebarOpen: false }" class="lg:flex lg:h-screen">
        <!-- Hamburger Button -->
        <button @click="sidebarOpen = !sidebarOpen" class="fixed top-4 left-4 z-50 w-12 h-12 glass-card lg:hidden flex items-center justify-center" :class="sidebarOpen ? 'left-64' : 'left-4'" style="transition: left 0.3s ease;">
            <div class="hamburger" :class="sidebarOpen ? 'open' : ''">
                <span></span><span></span><span></span>
            </div>
        </button>

        <!-- Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity duration-200" x-transition:leave="transition-opacity duration-200" class="lg:hidden fixed inset-0 z-30 bg-black/60 backdrop-blur-sm"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="sidebar fixed lg:sticky top-0 left-0 w-64 h-screen shrink-0 z-40 transition-transform duration-300 flex flex-col">
            <div class="p-6">
                <h1 class="text-xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">💧 Water Refill</h1>
                <p class="text-white/40 text-xs mt-1">Management System</p>
            </div>
            <nav class="mt-2 px-2 flex-1">
                <a href="/dashboard" class="nav-link {{ $activePage === 'dashboard' ? 'active' : '' }}" @click="sidebarOpen = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="/customers" class="nav-link {{ $activePage === 'customers' ? 'active' : '' }}" @click="sidebarOpen = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Customers
                </a>
                <a href="/orders" class="nav-link {{ $activePage === 'orders' ? 'active' : '' }}" @click="sidebarOpen = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Orders
                </a>
                <a href="/payments" class="nav-link {{ $activePage === 'payments' ? 'active' : '' }}" @click="sidebarOpen = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Payments
                </a>
                <a href="/bottles" class="nav-link {{ $activePage === 'bottles' ? 'active' : '' }}" @click="sidebarOpen = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    Bottles
                </a>
                <a href="/deliveries" class="nav-link {{ $activePage === 'deliveries' ? 'active' : '' }}" @click="sidebarOpen = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM5.5 17H3V7l2-3h12l3 5v8h-2.5M8 17H17m-9-5h7"/></svg>
                    Deliveries
                </a>
                <a href="/deliveries/calendar" class="nav-link {{ $activePage === 'calendar' ? 'active' : '' }}" @click="sidebarOpen = false" style="padding-left: 2.5rem;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Calendar
                </a>
                <a href="/deliveries/routes" class="nav-link {{ $activePage === 'routes' ? 'active' : '' }}" @click="sidebarOpen = false" style="padding-left: 2.5rem;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Routes
                </a>
                <a href="/drivers" class="nav-link {{ $activePage === 'drivers' ? 'active' : '' }}" @click="sidebarOpen = false" style="padding-left: 2.5rem;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Drivers
                </a>
                <a href="/vehicles" class="nav-link {{ $activePage === 'vehicles' ? 'active' : '' }}" @click="sidebarOpen = false" style="padding-left: 2.5rem;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM5 17H3V8l2-3h12l3 5v7h-2.5"/></svg>
                    Vehicles
                </a>
                <a href="/deliveries/history" class="nav-link {{ $activePage === 'history' ? 'active' : '' }}" @click="sidebarOpen = false" style="padding-left: 2.5rem;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    History
                </a>
                <a href="/reports" class="nav-link {{ $activePage === 'reports' ? 'active' : '' }}" @click="sidebarOpen = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Reports
                </a>
            </nav>
            <div class="p-4 border-t border-white/5">
                <form action="/logout" method="POST">@csrf<button type="submit" class="text-white/40 hover:text-white/70 text-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button></form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="lg:flex-1 pt-20 pb-4 px-4 lg:pt-4 lg:pb-6 lg:px-6 overflow-y-auto" style="padding-bottom: 100px;">
            @yield('content')
        </main>

        <!-- Bottom Navigation Bar (Mobile) -->
        <nav class="bottom-nav lg:hidden">
            <div class="flex justify-around items-center max-w-lg mx-auto">
                <a href="/dashboard" class="bottom-nav-item {{ $activePage === 'dashboard' ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Home</span>
                </a>
                <a href="/customers" class="bottom-nav-item {{ $activePage === 'customers' ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Customers</span>
                </a>
                <a href="/orders" class="bottom-nav-item {{ $activePage === 'orders' ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Orders</span>
                </a>
                <a href="/payments" class="bottom-nav-item {{ $activePage === 'payments' ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Payments</span>
                </a>
                <a href="/deliveries" class="bottom-nav-item {{ $activePage === 'deliveries' ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM5.5 17H3V7l2-3h12l3 5v8h-2.5M8 17H17m-9-5h7"/></svg>
                    <span>Deliver</span>
                </a>
                <a href="/drivers" class="bottom-nav-item {{ $activePage === 'drivers' ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Drivers</span>
                </a>
                <a href="/vehicles" class="bottom-nav-item {{ $activePage === 'vehicles' ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM5 17H3V8l2-3h12l3 5v7h-2.5"/></svg>
                    <span>Vehicles</span>
                </a>
                <a href="/reports" class="bottom-nav-item {{ $activePage === 'reports' ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Reports</span>
                </a>
            </div>
        </nav>
    </div>

    <script>
        window.addEventListener('load', function() { setTimeout(function() { document.getElementById('loading-screen').classList.add('hidden'); }, 1200); });
        setTimeout(function() { var ls = document.getElementById('loading-screen'); if (ls && !ls.classList.contains('hidden')) ls.classList.add('hidden'); }, 3000);
    </script>

    @yield('scripts')
</body>
</html>
