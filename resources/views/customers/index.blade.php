<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Water Refill Station</title>
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
        .input-field { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.9); transition: all 0.3s ease; }
        .input-field:focus { border-color: var(--accent-cyan); box-shadow: 0 0 0 3px rgba(6,182,212,0.15); outline: none; }
        .input-field::placeholder { color: rgba(255,255,255,0.3); }
        .btn-primary { background: linear-gradient(135deg, var(--accent-cyan), #2563eb); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(6,182,212,0.3); }
        .btn-ghost { transition: all 0.2s ease; }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); }
        .modal-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); }
        .modal-card { background: rgba(15,23,42,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); }
        .mobile-overlay { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
        .link-cyan { color: rgba(6,182,212,0.8); transition: color 0.2s; }
        .link-cyan:hover { color: #06b6d4; }
        .link-amber { color: rgba(245,158,11,0.8); transition: color 0.2s; }
        .link-amber:hover { color: #f59e0b; }
        .link-red { color: rgba(239,68,68,0.8); transition: color 0.2s; }
        .link-red:hover { color: #ef4444; }
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
                <a href="/customers" class="nav-link active flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/90"><span class="text-lg">👥</span> Customers</a>
                <a href="/orders" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">📦</span> Orders</a>
                <a href="/payments" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">💰</span> Payments</a>
                <a href="/bottles" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">🍶</span> Bottles</a>
                <a href="/reports" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-medium text-white/50 hover:text-white/90"><span class="text-lg">📈</span> Reports</a>
            </nav>
            <div class="p-4 border-t border-white/5"><form action="/logout" method="POST">@csrf<button type="submit" class="text-xs px-3 py-1.5 rounded-lg" style="background: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.15);">Sign Out</button></form></div>
        </aside>

        <main class="flex-1 min-h-screen">
            <div class="sticky top-0 z-30 px-4 lg:px-8 py-4 flex items-center justify-between" style="background: rgba(15,23,42,0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.05);">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-white/60 hover:text-white hover:bg-white/5"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <h1 class="text-lg font-semibold">👥 Customers</h1>
                <button onclick="openModal()" class="btn-primary text-white px-4 py-2 rounded-xl text-sm font-medium">+ Add Customer</button>
            </div>

            <div class="p-4 lg:p-8">
                <div class="mb-6">
                    <input type="text" id="searchInput" placeholder="Search customers..." onkeyup="loadCustomers()"
                        class="input-field w-full md:w-96 px-4 py-3 rounded-xl text-sm">
                </div>

                <div class="table-container" style="animation: fadeIn 0.5s ease-out;">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Name</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Contact</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Address</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Orders</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-white/30 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="customerList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 modal-overlay flex items-center justify-center z-50 hidden p-4">
        <div class="modal-card rounded-2xl p-6 w-full max-w-md" style="animation: slideUp 0.3s ease-out;">
            <h2 class="text-xl font-bold mb-5 text-white/90" id="modalTitle">Add Customer</h2>
            <form id="customerForm" onsubmit="saveCustomer(event)">
                <input type="hidden" id="customerId">
                <div class="mb-4"><label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Name *</label><input type="text" id="customerName" required class="input-field w-full px-4 py-3 rounded-xl text-sm"></div>
                <div class="mb-4"><label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Contact</label><input type="text" id="customerContact" class="input-field w-full px-4 py-3 rounded-xl text-sm"></div>
                <div class="mb-4"><label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Address</label><textarea id="customerAddress" class="input-field w-full px-4 py-3 rounded-xl text-sm" rows="2"></textarea></div>
                <div class="mb-5"><label class="block text-white/50 text-xs font-medium mb-2 uppercase tracking-wider">Notes</label><textarea id="customerNotes" class="input-field w-full px-4 py-3 rounded-xl text-sm" rows="2"></textarea></div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="btn-ghost px-4 py-2.5 rounded-xl text-sm text-white/60 border border-white/10">Cancel</button>
                    <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-medium text-white">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    async function api(url, opts = {}) {
        const res = await fetch(url, { credentials: 'same-origin', ...opts });
        if (res.status === 401) { window.location.href = '/login'; return null; }
        return res.json();
    }

    async function loadCustomers() {
        const search = document.getElementById('searchInput').value;
        const res = await fetch(`/api/customers?search=${search}`, { headers: { 'credentials': 'same-origin' } });
        if (!res.ok) { window.location.href = '/login'; return; }
        const data = await res.json();
        document.getElementById('customerList').innerHTML = data.data.map(c => `
            <tr class="table-row">
                <td class="px-5 py-3.5 text-sm font-medium">${c.name}</td>
                <td class="px-5 py-3.5 text-sm text-white/50">${c.contact || '-'}</td>
                <td class="px-5 py-3.5 text-sm text-white/50">${c.address || '-'}</td>
                <td class="px-5 py-3.5 text-sm text-white/50">${c.orders_count}</td>
                <td class="px-5 py-3.5 text-sm space-x-3">
                    <a href="/customers/${c.id}" class="link-cyan text-xs font-medium">View</a>
                    <button onclick="editCustomer(${c.id}, '${c.name.replace(/'/g, "\\'")}', '${(c.contact || '').replace(/'/g, "\\'")}', '${(c.address || '').replace(/'/g, "\\'")}', '${(c.notes || '').replace(/'/g, "\\'")}')" class="link-amber text-xs font-medium">Edit</button>
                    <button onclick="deleteCustomer(${c.id})" class="link-red text-xs font-medium">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    function openModal() {
        document.getElementById('customerId').value = '';
        document.getElementById('customerName').value = '';
        document.getElementById('customerContact').value = '';
        document.getElementById('customerAddress').value = '';
        document.getElementById('customerNotes').value = '';
        document.getElementById('modalTitle').textContent = 'Add Customer';
        document.getElementById('modal').classList.remove('hidden');
    }
    function editCustomer(id, name, contact, address, notes) {
        document.getElementById('customerId').value = id;
        document.getElementById('customerName').value = name;
        document.getElementById('customerContact').value = contact;
        document.getElementById('customerAddress').value = address;
        document.getElementById('customerNotes').value = notes;
        document.getElementById('modalTitle').textContent = 'Edit Customer';
        document.getElementById('modal').classList.remove('hidden');
    }
    function closeModal() { document.getElementById('modal').classList.add('hidden'); }

    async function saveCustomer(e) {
        e.preventDefault();
        const id = document.getElementById('customerId').value;
        const data = { name: document.getElementById('customerName').value, contact: document.getElementById('customerContact').value, address: document.getElementById('customerAddress').value, notes: document.getElementById('customerNotes').value };
        const url = id ? `/api/customers/${id}` : '/api/customers';
        const method = id ? 'PUT' : 'POST';
        await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'credentials': 'same-origin' }, body: JSON.stringify(data) });
        closeModal();
        loadCustomers();
    }
    async function deleteCustomer(id) {
        if (!confirm('Delete this customer?')) return;
        await fetch(`/api/customers/${id}`, { method: 'DELETE', headers: { 'credentials': 'same-origin' } });
        loadCustomers();
    }
    loadCustomers();
    </script>
</body>
</html>
