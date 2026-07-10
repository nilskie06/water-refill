<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile - Water Refill Station</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --bg-primary: #0f172a; --bg-secondary: #1e1b4b; --accent-cyan: #06b6d4; --accent-violet: #8b5cf6; --accent-emerald: #10b981; }
        * { font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
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
        .badge-pending { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.2); }
        .badge-delivered { background: rgba(6,182,212,0.15); color: #22d3ee; border: 1px solid rgba(6,182,212,0.2); }
        .badge-completed { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.2); }
        .badge-cancelled { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
        .mobile-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
        .link-cyan { color: rgba(6,182,212,0.8); transition: color 0.2s; }
        .link-cyan:hover { color: #06b6d4; }
        .info-label { color: rgba(255,255,255,0.35); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
    </style>
</head>
<body class="min-h-screen text-white/90">
    <div x-data="profile()" x-init="load()" x-cloak x-data="{ sidebarOpen: false }" class="flex min-h-screen" style="display:none;">
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
                <a href="/customers" class="nav-link active flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/90"><span class="text-lg">👥</span> Customers</a>
                <a href="/orders" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">📦</span> Orders</a>
                <a href="/payments" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">💰</span> Payments</a>
                <a href="/bottles" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">🍶</span> Bottles</a>
                <a href="/reports" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">📈</span> Reports</a>
            </nav>
            <div class="p-4 border-t border-white/5"><form action="/logout" method="POST">@csrf<button type="submit" class="text-xs px-3 py-1.5 rounded-lg" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.15);">Sign Out</button></form></div>
        </aside>

        <main class="flex-1 min-h-screen">
            <div class="sticky top-0 z-30 px-4 lg:px-8 py-4 flex items-center gap-4" style="background: rgba(15,23,42,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/5"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <a href="/customers" class="link-cyan text-sm font-medium">← Back</a>
                <h1 class="text-lg font-semibold" x-text="customer.name || 'Customer Profile'"></h1>
            </div>

            <div class="p-4 lg:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6" style="animation: fadeIn 0.5s ease-out;">
                    <!-- Customer Info -->
                    <div class="glass-card p-6">
                        <h2 class="text-lg font-bold mb-5 text-white/90">Customer Details</h2>
                        <div class="space-y-4">
                            <div><p class="info-label mb-1">Contact</p><p class="text-sm" x-text="customer.contact || '-'"></p></div>
                            <div><p class="info-label mb-1">Address</p><p class="text-sm" x-text="customer.address || '-'"></p></div>
                            <div><p class="info-label mb-1">Notes</p><p class="text-sm text-white/50" x-text="customer.notes || '-'"></p></div>
                            <div class="pt-3 border-t border-white/5"><p class="info-label mb-1">Total Orders</p><p class="text-lg font-bold text-cyan-400" x-text="customer.total_orders || 0"></p></div>
                            <div><p class="info-label mb-1">Total Spent</p><p class="text-lg font-bold text-emerald-400" x-text="'₱' + formatNumber(customer.total_spent)"></p></div>
                        </div>
                    </div>

                    <!-- Bottle Balance -->
                    <div class="glass-card p-6">
                        <h2 class="text-lg font-bold mb-5 text-white/90">🍶 Bottle Balance</h2>
                        <template x-if="customer.bottle_balance">
                            <div class="space-y-4">
                                <div><p class="info-label mb-1">Bottles Out</p><p class="text-2xl font-bold text-amber-400" x-text="customer.bottle_balance?.bottles_out || 0"></p></div>
                                <div><p class="info-label mb-1">Returned</p><p class="text-2xl font-bold text-emerald-400" x-text="customer.bottle_balance?.bottles_returned || 0"></p></div>
                                <div><p class="info-label mb-1">Balance</p><p class="text-2xl font-bold text-orange-400" x-text="customer.bottle_balance?.balance || 0"></p></div>
                            </div>
                        </template>
                        <template x-if="!customer.bottle_balance">
                            <p class="text-white/30 text-sm">No bottle records</p>
                        </template>
                    </div>

                    <!-- Quick Stats -->
                    <div class="glass-card p-6">
                        <h2 class="text-lg font-bold mb-5 text-white/90">📊 Summary</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center"><span class="text-sm text-white/40">Pending Orders</span><span class="font-bold text-amber-400" x-text="pendingCount"></span></div>
                            <div class="flex justify-between items-center"><span class="text-sm text-white/40">Completed Orders</span><span class="font-bold text-emerald-400" x-text="completedCount"></span></div>
                            <div class="flex justify-between items-center"><span class="text-sm text-white/40">Outstanding Balance</span><span class="font-bold text-red-400" x-text="'₱' + formatNumber(outstanding)"></span></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="table-container" style="animation: slideUp 0.5s ease-out 0.2s both;">
                    <div class="p-5 border-b border-white/5">
                        <h2 class="text-lg font-semibold text-white/90">Recent Orders</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Order #</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Date</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Qty</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Total</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Paid</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="o in customer.orders || []" :key="o.id">
                                    <tr class="table-row">
                                        <td class="px-5 py-3.5 text-sm font-medium" x-text="o.order_number"></td>
                                        <td class="px-5 py-3.5 text-sm text-white/50" x-text="o.order_date"></td>
                                        <td class="px-5 py-3.5 text-sm text-white/50" x-text="o.quantity"></td>
                                        <td class="px-5 py-3.5 text-sm font-semibold text-emerald-400" x-text="'₱' + o.total"></td>
                                        <td class="px-5 py-3.5 text-sm text-white/50" x-text="'₱' + formatNumber(o.amount_paid)"></td>
                                        <td class="px-5 py-3.5">
                                            <span class="badge"
                                                :class="{
                                                    'badge-pending': o.status === 'pending',
                                                    'badge-delivered': o.status === 'delivered',
                                                    'badge-completed': o.status === 'completed',
                                                    'badge-cancelled': o.status === 'cancelled'
                                                }" x-text="o.status"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    function profile() {
        return {
            customer: {},
            token: localStorage.getItem('token'),
            get pendingCount() { return (this.customer.orders || []).filter(o => o.status === 'pending').length; },
            get completedCount() { return (this.customer.orders || []).filter(o => o.status === 'completed').length; },
            get outstanding() { return (this.customer.orders || []).reduce((sum, o) => sum + parseFloat(o.balance || 0), 0); },
            async load() {
                const pathParts = window.location.pathname.split('/');
                const id = pathParts[pathParts.length - 1];
                const res = await fetch(`/api/customers/${id}`, {
                    headers: { 'Authorization': 'Bearer ' + this.token }
                });
                if (res.ok) this.customer = await res.json();
            },
            formatNumber(n) { return parseFloat(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }); }
        }
    }
    </script>
</body>
</html>
