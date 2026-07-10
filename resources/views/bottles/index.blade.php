<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bottle Balances - Water Refill Station</title>
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
        .mobile-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="min-h-screen text-white/90">
    <!-- Mobile Menu Button -->
    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full'); document.getElementById('overlay').classList.toggle('hidden')" class="lg:hidden fixed top-4 left-4 z-50 p-2 glass-card text-white/70 hover:text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <div id="overlay" onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); this.classList.add('hidden')" class="lg:hidden fixed inset-0 z-40 mobile-overlay hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed lg:sticky top-0 left-0 w-64 h-screen z-40 -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <div class="p-6">
            <h1 class="text-xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">💧 Water Refill</h1>
            <p class="text-white/40 text-xs mt-1">Management System</p>
        </div>
        <nav class="mt-2 px-2">
            <a href="/dashboard" class="nav-link flex items-center gap-3 px-4 py-3 text-white/60 hover:text-white">📊 Dashboard</a>
            <a href="/customers" class="nav-link flex items-center gap-3 px-4 py-3 text-white/60 hover:text-white">👥 Customers</a>
            <a href="/orders" class="nav-link flex items-center gap-3 px-4 py-3 text-white/60 hover:text-white">📦 Orders</a>
            <a href="/payments" class="nav-link flex items-center gap-3 px-4 py-3 text-white/60 hover:text-white">💰 Payments</a>
            <a href="/bottles" class="nav-link active flex items-center gap-3 px-4 py-3 text-white/90">🍶 Bottles</a>
            <a href="/reports" class="nav-link flex items-center gap-3 px-4 py-3 text-white/60 hover:text-white">📈 Reports</a>
        </nav>
        <div class="absolute bottom-0 w-full p-4 border-t border-white/5">
            <form action="/logout" method="POST">@csrf<button type="submit" class="text-white/40 hover:text-white/70 text-sm transition">← Logout</button></form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 p-4 lg:p-8" style="animation: fadeIn 0.6s ease">
        <div class="mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🍶 Bottle Balances</h1>
            <p class="text-white/40 text-sm mt-1">Track bottles issued and returned</p>
        </div>

        <div class="table-container">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Out</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Returned</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Balance</th>
                    </tr>
                </thead>
                <tbody id="bottleList"></tbody>
            </table>
        </div>
    </main>

    <script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = '/login';

    async function loadBottles() {
        const res = await fetch('/api/customers?per_page=100', { headers: { 'Authorization': 'Bearer ' + token } });
        if (!res.ok) { window.location.href = '/login'; return; }
        const data = await res.json();
        let html = '';
        for (const c of data.data) {
            const r = await fetch(`/api/customers/${c.id}`, { headers: { 'Authorization': 'Bearer ' + token } });
            if (r.ok) {
                const full = await r.json();
                if (full.bottle_balance) {
                    const b = full.bottle_balance;
                    const balColor = b.balance > 0 ? 'text-amber-400' : 'text-emerald-400';
                    html += `<tr class="table-row">
                        <td class="px-6 py-4 font-medium text-white/90">${c.name}</td>
                        <td class="px-6 py-4 text-white/60">${b.bottles_out}</td>
                        <td class="px-6 py-4 text-white/60">${b.bottles_returned}</td>
                        <td class="px-6 py-4 font-bold ${balColor}">${b.balance}</td>
                    </tr>`;
                }
            }
        }
        document.getElementById('bottleList').innerHTML = html || '<tr><td colspan="4" class="px-6 py-12 text-center text-white/30">No bottle records found</td></tr>';
    }
    loadBottles();
    </script>
</body>
</html>
