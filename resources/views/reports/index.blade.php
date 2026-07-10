<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Water Refill Station</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --bg-primary: #0f172a; --bg-secondary: #1e1b4b; --accent-cyan: #06b6d4; --accent-violet: #8b5cf6; --accent-emerald: #10b981; }
        * { font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse-glow { 0%, 100% { box-shadow: 0 0 5px rgba(6,182,212,0.2); } 50% { box-shadow: 0 0 20px rgba(6,182,212,0.4); } }
        .glass-card { background: rgba(255,255,255,0.04); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); border-radius: 1rem; }
        .sidebar { background: rgba(0,0,0,0.3); backdrop-filter: blur(20px); border-right: 1px solid rgba(255,255,255,0.06); }
        .nav-link { transition: all 0.2s ease; border-radius: 0.75rem; margin: 2px 8px; }
        .nav-link:hover { background: rgba(255,255,255,0.08); }
        .nav-link.active { background: linear-gradient(135deg, rgba(6,182,212,0.15), rgba(139,92,246,0.15)); border: 1px solid rgba(6,182,212,0.2); }
        .stat-card { animation: fadeIn 0.5s ease forwards; }
        .stat-card:hover { animation: pulse-glow 2s ease infinite; border-color: rgba(6,182,212,0.3); }
        .btn-primary { background: linear-gradient(135deg, var(--accent-cyan), #2563eb); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(6,182,212,0.3); }
        .btn-secondary { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); transition: all 0.2s ease; }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); }
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
            <a href="/bottles" class="nav-link flex items-center gap-3 px-4 py-3 text-white/60 hover:text-white">🍶 Bottles</a>
            <a href="/reports" class="nav-link active flex items-center gap-3 px-4 py-3 text-white/90">📈 Reports</a>
        </nav>
        <div class="absolute bottom-0 w-full p-4 border-t border-white/5">
            <form action="/logout" method="POST">@csrf<button type="submit" class="text-white/40 hover:text-white/70 text-sm transition">← Logout</button></form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 p-4 lg:p-8" style="animation: fadeIn 0.6s ease">
        <div class="mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">📈 Daily Sales Report</h1>
            <p class="text-white/40 text-sm mt-1">Track your business performance</p>
        </div>

        <!-- Date Filter -->
        <div class="glass-card p-4 mb-6 flex flex-wrap gap-3 items-center">
            <input type="date" id="dateFrom" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white/80 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none transition text-sm">
            <span class="text-white/30">to</span>
            <input type="date" id="dateTo" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white/80 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none transition text-sm">
            <button onclick="loadReport()" class="btn-primary px-5 py-2 rounded-lg text-white text-sm font-medium">Filter</button>
            <button onclick="setToday()" class="btn-secondary px-5 py-2 rounded-lg text-white/70 text-sm">Today</button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="stat-card glass-card p-4 text-center">
                <p class="text-white/40 text-xs uppercase tracking-wider">Orders</p>
                <p class="text-2xl font-bold text-cyan-400 mt-1" id="totalOrders">0</p>
            </div>
            <div class="stat-card glass-card p-4 text-center" style="animation-delay: 0.1s">
                <p class="text-white/40 text-xs uppercase tracking-wider">Bottles</p>
                <p class="text-2xl font-bold text-violet-400 mt-1" id="bottlesSold">0</p>
            </div>
            <div class="stat-card glass-card p-4 text-center" style="animation-delay: 0.2s">
                <p class="text-white/40 text-xs uppercase tracking-wider">Gross Sales</p>
                <p class="text-2xl font-bold text-emerald-400 mt-1" id="grossSales">₱0</p>
            </div>
            <div class="stat-card glass-card p-4 text-center" style="animation-delay: 0.3s">
                <p class="text-white/40 text-xs uppercase tracking-wider">Payments</p>
                <p class="text-2xl font-bold text-cyan-400 mt-1" id="paymentsReceived">₱0</p>
            </div>
            <div class="stat-card glass-card p-4 text-center" style="animation-delay: 0.4s">
                <p class="text-white/40 text-xs uppercase tracking-wider">Outstanding</p>
                <p class="text-2xl font-bold text-rose-400 mt-1" id="outstanding">₱0</p>
            </div>
        </div>

        <!-- Top Customers & Payment Methods -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="glass-card p-6">
                <h2 class="text-lg font-semibold text-white/90 mb-4 flex items-center gap-2">🏆 Top Customers</h2>
                <div id="topCustomers" class="space-y-3"><p class="text-white/30 text-center py-6">No data</p></div>
            </div>
            <div class="glass-card p-6">
                <h2 class="text-lg font-semibold text-white/90 mb-4 flex items-center gap-2">💳 Payment Methods</h2>
                <div id="paymentsByMethod" class="space-y-3"><p class="text-white/30 text-center py-6">No data</p></div>
            </div>
        </div>
    </main>

    <script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = '/login';
    const fmt = n => parseFloat(n || 0).toLocaleString('en-US', {minimumFractionDigits: 2});

    function setToday() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dateFrom').value = today;
        document.getElementById('dateTo').value = today;
        loadReport();
    }

    async function loadReport() {
        const from = document.getElementById('dateFrom').value;
        const to = document.getElementById('dateTo').value;
        const res = await fetch(`/api/reports/daily-sales?date_from=${from}&date_to=${to}`, { headers: { 'Authorization': 'Bearer ' + token } });
        if (!res.ok) return;
        const data = await res.json();
        document.getElementById('totalOrders').textContent = data.summary?.total_orders || 0;
        document.getElementById('bottlesSold').textContent = data.summary?.total_bottles_sold || 0;
        document.getElementById('grossSales').textContent = '₱' + fmt(data.summary?.gross_sales);
        document.getElementById('paymentsReceived').textContent = '₱' + fmt(data.summary?.payments_received);
        document.getElementById('outstanding').textContent = '₱' + fmt(data.summary?.outstanding_balance);

        document.getElementById('topCustomers').innerHTML = (data.top_customers || []).map(c =>
            `<div class="flex justify-between items-center py-3 px-4 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition">
                <div><span class="font-medium text-white/90">${c.customer?.name}</span><span class="text-white/30 text-xs ml-2">${c.order_count} orders</span></div>
                <span class="font-bold text-emerald-400">₱${fmt(c.total_spent)}</span>
            </div>`
        ).join('') || '<p class="text-white/30 text-center py-6">No data</p>';

        document.getElementById('paymentsByMethod').innerHTML = (data.payments_by_method || []).map(m =>
            `<div class="flex justify-between items-center py-3 px-4 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition">
                <span class="capitalize text-white/70">${m.payment_method.replace('_', ' ')}</span>
                <span class="font-bold text-cyan-400">₱${fmt(m.total)}</span>
            </div>`
        ).join('') || '<p class="text-white/30 text-center py-6">No data</p>';
    }
    setToday();
    </script>
</body>
</html>
