<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Water Refill Station</title>
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
        .badge-pending { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.2); text-shadow: 0 0 10px rgba(245,158,11,0.3); }
        .badge-delivered { background: rgba(6,182,212,0.15); color: #22d3ee; border: 1px solid rgba(6,182,212,0.2); text-shadow: 0 0 10px rgba(6,182,212,0.3); }
        .badge-completed { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.2); text-shadow: 0 0 10px rgba(16,185,129,0.3); }
        .badge-cancelled { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.2); text-shadow: 0 0 10px rgba(239,68,68,0.3); }
        .input-field { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.9); transition: all 0.3s ease; }
        .input-field:focus { border-color: var(--accent-cyan); box-shadow: 0 0 0 3px rgba(6,182,212,0.15); outline: none; }
        .input-field option { background: #1e1b4b; color: white; }
        .btn-primary { background: linear-gradient(135deg, var(--accent-cyan), #2563eb); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(6,182,212,0.3); }
        .btn-sm { padding: 4px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 600; transition: all 0.2s; }
        .btn-deliver { background: rgba(6,182,212,0.1); color: #22d3ee; border: 1px solid rgba(6,182,212,0.2); }
        .btn-deliver:hover { background: rgba(6,182,212,0.2); }
        .btn-complete { background: rgba(16,185,129,0.1); color: #34d399; border: 1px solid rgba(16,185,129,0.2); }
        .btn-complete:hover { background: rgba(16,185,129,0.2); }
        .btn-cancel { background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
        .btn-cancel:hover { background: rgba(239,68,68,0.2); }
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
                <a href="/orders" class="nav-link active flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/90"><span class="text-lg">📦</span> Orders</a>
                <a href="/payments" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">💰</span> Payments</a>
                <a href="/bottles" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">🍶</span> Bottles</a>
                <a href="/reports" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">📈</span> Reports</a>
            </nav>
            <div class="p-4 border-t border-white/5"><form action="/logout" method="POST">@csrf<button type="submit" class="text-xs px-3 py-1.5 rounded-lg" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.15);">Sign Out</button></form></div>
        </aside>

        <main class="flex-1 min-h-screen">
            <div class="sticky top-0 z-30 px-4 lg:px-8 py-4 flex items-center justify-between" style="background: rgba(15,23,42,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/5"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <h1 class="text-lg font-semibold">📦 Orders</h1>
                <a href="/orders/create" class="btn-primary text-white px-4 py-2 rounded-xl text-sm font-medium">+ New Order</a>
            </div>

            <div class="p-4 lg:p-8">
                <div class="mb-6 flex gap-3">
                    <select id="statusFilter" onchange="loadOrders()" class="input-field px-4 py-2.5 rounded-xl text-sm">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="delivered">Delivered</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="table-container" style="animation: fadeIn 0.5s ease-out;">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Order #</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Customer</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Date</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Qty</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Total</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orderList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = '/login';
    const sc = { pending: 'badge-pending', delivered: 'badge-delivered', completed: 'badge-completed', cancelled: 'badge-cancelled' };

    async function loadOrders() {
        const status = document.getElementById('statusFilter').value;
        const res = await fetch(`/api/orders?status=${status}`, { headers: { 'Authorization': 'Bearer ' + token } });
        if (!res.ok) { window.location.href = '/login'; return; }
        const data = await res.json();
        document.getElementById('orderList').innerHTML = data.data.map(o => `
            <tr class="table-row">
                <td class="px-5 py-3.5 text-sm font-medium">${o.order_number}</td>
                <td class="px-5 py-3.5 text-sm text-white/50">${o.customer?.name || '-'}</td>
                <td class="px-5 py-3.5 text-sm text-white/50">${o.order_date?.split('T')[0] || ''}</td>
                <td class="px-5 py-3.5 text-sm text-white/50">${o.quantity}</td>
                <td class="px-5 py-3.5 text-sm font-semibold text-emerald-400">₱${parseFloat(o.total).toFixed(2)}</td>
                <td class="px-5 py-3.5"><span class="badge ${sc[o.status] || ''}">${o.status}</span></td>
                <td class="px-5 py-3.5 text-sm space-x-2">
                    ${o.status === 'pending' ? `<button onclick="updateStatus(${o.id}, 'delivered')" class="btn-sm btn-deliver">Deliver</button>` : ''}
                    ${o.status === 'delivered' ? `<button onclick="updateStatus(${o.id}, 'completed')" class="btn-sm btn-complete">Complete</button>` : ''}
                    ${o.status !== 'cancelled' && o.status !== 'completed' ? `<button onclick="updateStatus(${o.id}, 'cancelled')" class="btn-sm btn-cancel">Cancel</button>` : ''}
                </td>
            </tr>
        `).join('');
    }

    async function updateStatus(id, status) {
        await fetch(`/api/orders/${id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token }, body: JSON.stringify({ status }) });
        loadOrders();
    }
    loadOrders();
    </script>
</body>
</html>
