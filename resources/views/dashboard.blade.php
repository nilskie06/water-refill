<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Water Refill Station</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --bg-primary: #0f172a; --bg-secondary: #1e1b4b; --accent-cyan: #06b6d4; --accent-violet: #8b5cf6; --accent-emerald: #10b981; }
        * { font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulseGlow { 0%, 100% { box-shadow: 0 0 15px rgba(6,182,212,0.08); } 50% { box-shadow: 0 0 30px rgba(6,182,212,0.15); } }
        @keyframes gradientBorder { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .glass-card { background: rgba(255,255,255,0.04); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; }
        .stat-card { background: rgba(255,255,255,0.04); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; transition: all 0.3s ease; animation: fadeIn 0.6s ease-out both; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; inset: 0; border-radius: 1rem; padding: 1px; background: linear-gradient(135deg, transparent, rgba(255,255,255,0.1), transparent); -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); -webkit-mask-composite: xor; mask-composite: exclude; opacity: 0; transition: opacity 0.3s; }
        .stat-card:hover::before { opacity: 1; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.3); }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .sidebar { background: rgba(0,0,0,0.3); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-right: 1px solid rgba(255,255,255,0.06); }
        .nav-link { transition: all 0.2s ease; border-radius: 0.75rem; margin: 2px 8px; }
        .nav-link:hover { background: rgba(255,255,255,0.08); }
        .nav-link.active { background: linear-gradient(135deg, rgba(6,182,212,0.15), rgba(139,92,246,0.15)); border: 1px solid rgba(6,182,212,0.2); }
        .table-container { background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; overflow: hidden; }
        .table-row { border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.2s; }
        .table-row:hover { background: rgba(255,255,255,0.04); }
        .table-row:nth-child(even) { background: rgba(255,255,255,0.02); }
        .badge { padding: 4px 12px; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-pending { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.2); text-shadow: 0 0 10px rgba(245,158,11,0.3); }
        .badge-delivered { background: rgba(6,182,212,0.15); color: #22d3ee; border: 1px solid rgba(6,182,212,0.2); text-shadow: 0 0 10px rgba(6,182,212,0.3); }
        .badge-completed { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.2); text-shadow: 0 0 10px rgba(16,185,129,0.3); }
        .badge-cancelled { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.2); text-shadow: 0 0 10px rgba(239,68,68,0.3); }
        .mobile-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
    </style>
</head>
<body class="min-h-screen text-white/90">
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-300" x-transition:leave="transition-opacity duration-300"
            @click="sidebarOpen = false" class="mobile-overlay fixed inset-0 z-40 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="sidebar fixed lg:sticky top-0 left-0 z-50 w-72 h-screen flex flex-col transition-transform duration-300 ease-in-out">
            <div class="p-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(139,92,246,0.2));">
                        <span class="text-xl">💧</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold" style="background: linear-gradient(135deg, #06b6d4, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Water Refill</h1>
                        <p class="text-white/30 text-xs">Management System</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white/50 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex-1 mt-2 px-2">
                <a href="/dashboard" class="nav-link active flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/90">
                    <span class="text-lg">📊</span> Dashboard
                </a>
                <a href="/customers" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90">
                    <span class="text-lg">👥</span> Customers
                </a>
                <a href="/orders" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90">
                    <span class="text-lg">📦</span> Orders
                </a>
                <a href="/payments" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90">
                    <span class="text-lg">💰</span> Payments
                </a>
                <a href="/bottles" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90">
                    <span class="text-lg">🍶</span> Bottles
                </a>
                <a href="/reports" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90">
                    <span class="text-lg">📈</span> Reports
                </a>
            </nav>
            <div class="p-4 border-t border-white/5">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-white/40">Logout</span>
                    <form action="/logout" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1.5 rounded-lg transition-all" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.15);">Sign Out</button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-h-screen">
            <!-- Top Bar -->
            <div class="sticky top-0 z-30 px-4 lg:px-8 py-4 flex items-center justify-between" style="background: rgba(15,23,42,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold">📊 Dashboard</h1>
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm" style="background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(139,92,246,0.2)); border: 1px solid rgba(6,182,212,0.3);">A</div>
            </div>

            <div class="p-4 lg:p-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="stat-card p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05)); border: 1px solid rgba(16,185,129,0.2);">💰</div>
                            <div>
                                <p class="text-xs text-white/40 font-medium uppercase tracking-wider">Today's Sales</p>
                                <p class="text-2xl font-bold text-white/90" id="todaySales">₱0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, rgba(6,182,212,0.15), rgba(6,182,212,0.05)); border: 1px solid rgba(6,182,212,0.2);">📦</div>
                            <div>
                                <p class="text-xs text-white/40 font-medium uppercase tracking-wider">Today's Orders</p>
                                <p class="text-2xl font-bold text-white/90" id="todayOrders">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05)); border: 1px solid rgba(245,158,11,0.2);">⏳</div>
                            <div>
                                <p class="text-xs text-white/40 font-medium uppercase tracking-wider">Outstanding</p>
                                <p class="text-2xl font-bold text-white/90" id="outstanding">₱0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl" style="background: linear-gradient(135deg, rgba(139,92,246,0.15), rgba(139,92,246,0.05)); border: 1px solid rgba(139,92,246,0.2);">🍶</div>
                            <div>
                                <p class="text-xs text-white/40 font-medium uppercase tracking-wider">Bottles Out</p>
                                <p class="text-2xl font-bold text-white/90" id="bottlesOut">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="table-container">
                    <div class="p-5 border-b border-white/5">
                        <h2 class="text-lg font-semibold text-white/90">Recent Orders</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Order #</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Customer</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Qty</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Total</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentOrders"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = '/login';

    const statusBadge = { pending: 'badge-pending', delivered: 'badge-delivered', completed: 'badge-completed', cancelled: 'badge-cancelled' };

    async function loadDashboard() {
        const res = await fetch('/api/dashboard', { headers: { 'Authorization': 'Bearer ' + token } });
        if (!res.ok) { window.location.href = '/login'; return; }
        const data = await res.json();
        document.getElementById('todaySales').textContent = '₱' + parseFloat(data.today_sales || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('todayOrders').textContent = data.today_orders || 0;
        document.getElementById('outstanding').textContent = '₱' + parseFloat(data.outstanding_payments || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('bottlesOut').textContent = data.bottles_out || 0;
        document.getElementById('recentOrders').innerHTML = (data.recent_orders || []).map(o => `
            <tr class="table-row">
                <td class="px-5 py-3.5 text-sm font-medium">${o.order_number}</td>
                <td class="px-5 py-3.5 text-sm text-white/60">${o.customer?.name || '-'}</td>
                <td class="px-5 py-3.5 text-sm text-white/60">${o.quantity}</td>
                <td class="px-5 py-3.5 text-sm font-semibold text-emerald-400">₱${o.total}</td>
                <td class="px-5 py-3.5"><span class="badge ${statusBadge[o.status] || ''}">${o.status}</span></td>
            </tr>
        `).join('');
    }
    loadDashboard();
    </script>
</body>
</html>
