<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Water Refill Station</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --bg-primary: #0f172a; --bg-secondary: #1e1b4b; --accent-cyan: #06b6d4; --accent-violet: #8b5cf6; --accent-emerald: #10b981; }
        * { font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .glass-card { background: rgba(255,255,255,0.04); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; }
        .sidebar { background: rgba(0,0,0,0.3); backdrop-filter: blur(20px); border-right: 1px solid rgba(255,255,255,0.06); }
        .nav-link { transition: all 0.2s ease; border-radius: 0.75rem; margin: 2px 8px; }
        .nav-link:hover { background: rgba(255,255,255,0.08); }
        .nav-link.active { background: linear-gradient(135deg, rgba(6,182,212,0.15), rgba(139,92,246,0.15)); border: 1px solid rgba(6,182,212,0.2); }
        .table-container { background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; overflow: hidden; }
        .table-row { border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.2s; }
        .table-row:hover { background: rgba(255,255,255,0.04); }
        .table-row:nth-child(even) { background: rgba(255,255,255,0.02); }
        .badge { padding: 4px 12px; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-method { background: rgba(139,92,246,0.12); color: #a78bfa; border: 1px solid rgba(139,92,246,0.2); }
        .btn-primary { background: linear-gradient(135deg, var(--accent-cyan), #2563eb); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(6,182,212,0.3); }
        .mobile-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="min-h-screen text-white/90">
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
                <a href="/dashboard" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">📊</span> Dashboard</a>
                <a href="/customers" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">👥</span> Customers</a>
                <a href="/orders" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">📦</span> Orders</a>
                <a href="/payments" class="nav-link active flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/90"><span class="text-lg">💰</span> Payments</a>
                <a href="/bottles" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">🍶</span> Bottles</a>
                <a href="/reports" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">📈</span> Reports</a>
            </nav>
            <div class="p-4 border-t border-white/5"><form action="/logout" method="POST">@csrf<button type="submit" class="text-xs px-3 py-1.5 rounded-lg" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.15);">Sign Out</button></form></div>
        </aside>

        <main class="flex-1 min-h-screen">
            <div class="sticky top-0 z-30 px-4 lg:px-8 py-4 flex items-center justify-between" style="background: rgba(15,23,42,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/5"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <h1 class="text-lg font-semibold">💰 Payments</h1>
                <a href="/payments/create" class="btn-primary text-white px-4 py-2 rounded-xl text-sm font-medium">+ Record Payment</a>
            </div>

            <div class="p-4 lg:p-8">
                <div class="table-container" style="animation: fadeIn 0.5s ease-out;">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">ID</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Order #</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Customer</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Amount</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Method</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody id="paymentList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    async function api(url, opts = {}) {
        const res = await fetch(url, { credentials: 'same-origin', ...opts });
        if (res.status === 401) { window.location.href = '/login'; return null; }
        return res.json();
    }

    async function loadPayments() {
        const res = await fetch('/api/payments', { headers: { 'credentials': 'same-origin' } });
        if (!res.ok) { window.location.href = '/login'; return; }
        const data = await res.json();
        document.getElementById('paymentList').innerHTML = data.data.map(p => `
            <tr class="table-row">
                <td class="px-5 py-3.5 text-sm text-white/40">#${p.id}</td>
                <td class="px-5 py-3.5 text-sm font-medium">${p.order?.order_number || '-'}</td>
                <td class="px-5 py-3.5 text-sm text-white/50">${p.order?.customer?.name || '-'}</td>
                <td class="px-5 py-3.5 text-sm font-semibold text-emerald-400">₱${parseFloat(p.amount).toFixed(2)}</td>
                <td class="px-5 py-3.5"><span class="badge badge-method">${p.payment_method?.replace('_', ' ') || '-'}</span></td>
                <td class="px-5 py-3.5 text-sm text-white/50">${p.payment_date?.split('T')[0] || ''}</td>
            </tr>
        `).join('');
    }
    loadPayments();
    </script>
</body>
</html>
