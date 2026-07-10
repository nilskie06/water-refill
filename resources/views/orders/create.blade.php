<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order - Water Refill Station</title>
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
        .input-field { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.9); transition: all 0.3s ease; }
        .input-field:focus { border-color: var(--accent-cyan); box-shadow: 0 0 0 3px rgba(6,182,212,0.15); outline: none; }
        .input-field::placeholder { color: rgba(255,255,255,0.3); }
        .input-field option { background: #1e1b4b; color: white; }
        .btn-primary { background: linear-gradient(135deg, var(--accent-cyan), #2563eb); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(6,182,212,0.3); }
        .btn-ghost { transition: all 0.2s ease; }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); }
        .mobile-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
        .link-cyan { color: rgba(6,182,212,0.8); transition: color 0.2s; }
        .link-cyan:hover { color: #06b6d4; }
        .total-display { background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(6,182,212,0.08)); border: 1px solid rgba(16,185,129,0.15); border-radius: 1rem; }
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
            <div class="sticky top-0 z-30 px-4 lg:px-8 py-4 flex items-center gap-4" style="background: rgba(15,23,42,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/5"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <a href="/orders" class="link-cyan text-sm font-medium">← Back</a>
                <h1 class="text-lg font-semibold">📦 New Order</h1>
            </div>

            <div class="p-4 lg:p-8">
                <div class="glass-card p-6 lg:p-8 max-w-2xl" style="animation: slideUp 0.5s ease-out;">
                    <form id="orderForm" onsubmit="submitOrder(event)">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Customer *</label>
                                <select id="customerId" required class="input-field w-full px-4 py-3 rounded-xl text-sm">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Product</label>
                                <input type="text" id="product" value="Pure Water Gallon" class="input-field w-full px-4 py-3 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Quantity *</label>
                                <input type="number" id="quantity" value="1" min="1" required class="input-field w-full px-4 py-3 rounded-xl text-sm" oninput="calcTotal()">
                            </div>
                            <div>
                                <label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Unit Price (₱) *</label>
                                <input type="number" id="unitPrice" value="25" step="0.01" min="0" required class="input-field w-full px-4 py-3 rounded-xl text-sm" oninput="calcTotal()">
                            </div>
                            <div>
                                <label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Delivery Type</label>
                                <select id="deliveryType" class="input-field w-full px-4 py-3 rounded-xl text-sm">
                                    <option value="pickup">Pickup</option>
                                    <option value="delivery">Delivery</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Bottle In</label>
                                <input type="number" id="bottleIn" value="0" min="0" class="input-field w-full px-4 py-3 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Bottle Out</label>
                                <input type="number" id="bottleOut" value="1" min="0" class="input-field w-full px-4 py-3 rounded-xl text-sm">
                            </div>
                            <div class="flex items-end">
                                <div class="total-display w-full p-4">
                                    <p class="text-xs text-white/40 font-medium uppercase tracking-wider mb-1">Total Amount</p>
                                    <p class="text-2xl font-bold text-emerald-400" id="totalDisplay">₱25.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-8">
                            <a href="/orders" class="btn-ghost px-5 py-2.5 rounded-xl text-sm text-white/60 border border-white/10">Cancel</a>
                            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-medium text-white">Create Order</button>
                        </div>
                    </form>
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

    function calcTotal() {
        const q = parseInt(document.getElementById('quantity').value) || 0;
        const p = parseFloat(document.getElementById('unitPrice').value) || 0;
        document.getElementById('totalDisplay').textContent = '₱' + (q * p).toFixed(2);
        document.getElementById('bottleOut').value = q;
    }

    async function submitOrder(e) {
        e.preventDefault();
        const data = {
            customer_id: document.getElementById('customerId').value,
            product: document.getElementById('product').value,
            quantity: parseInt(document.getElementById('quantity').value),
            unit_price: parseFloat(document.getElementById('unitPrice').value),
            delivery_type: document.getElementById('deliveryType').value,
            bottle_in: parseInt(document.getElementById('bottleIn').value) || 0,
            bottle_out: parseInt(document.getElementById('bottleOut').value) || 1
        };
        const res = await fetch('/api/orders', { method: 'POST', headers: { 'Content-Type': 'application/json', 'credentials': 'same-origin' }, body: JSON.stringify(data) });
        if (res.ok) window.location.href = '/orders';
        else alert('Error creating order');
    }
    </script>
</body>
</html>
