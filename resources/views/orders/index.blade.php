<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Water Refill Station</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-blue-800 text-white flex-shrink-0">
            <div class="p-4"><h1 class="text-xl font-bold">💧 Water Refill</h1></div>
            <nav class="mt-2">
                <a href="/dashboard" class="block px-4 py-3 hover:bg-blue-700">📊 Dashboard</a>
                <a href="/customers" class="block px-4 py-3 hover:bg-blue-700">👥 Customers</a>
                <a href="/orders" class="block px-4 py-3 hover:bg-blue-700 bg-blue-700">📦 Orders</a>
                <a href="/payments" class="block px-4 py-3 hover:bg-blue-700">💰 Payments</a>
                <a href="/bottles" class="block px-4 py-3 hover:bg-blue-700">🍶 Bottles</a>
                <a href="/reports" class="block px-4 py-3 hover:bg-blue-700">📈 Reports</a>
            </nav>
            <div class="absolute bottom-0 w-64 p-4 border-t border-blue-700">
                <form action="/logout" method="POST">@csrf<button type="submit" class="text-blue-200 hover:text-white text-sm">Logout</button></form>
            </div>
        </aside>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">📦 Orders</h1>
                <a href="/orders/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ New Order</a>
            </div>

            <div class="flex gap-4 mb-4">
                <select id="statusFilter" onchange="loadOrders()" class="px-4 py-2 border rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="delivered">Delivered</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="orderList" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = '/login';
    const sc = { pending: 'bg-yellow-100 text-yellow-800', delivered: 'bg-blue-100 text-blue-800', completed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-800' };

    async function loadOrders() {
        const status = document.getElementById('statusFilter').value;
        const res = await fetch(`/api/orders?status=${status}`, { headers: { 'Authorization': 'Bearer ' + token } });
        if (!res.ok) { window.location.href = '/login'; return; }
        const data = await res.json();
        document.getElementById('orderList').innerHTML = data.data.map(o => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-medium">${o.order_number}</td>
                <td class="px-4 py-3 text-sm">${o.customer?.name || '-'}</td>
                <td class="px-4 py-3 text-sm">${o.order_date?.split('T')[0] || ''}</td>
                <td class="px-4 py-3 text-sm">${o.quantity}</td>
                <td class="px-4 py-3 text-sm font-medium">₱${parseFloat(o.total).toFixed(2)}</td>
                <td class="px-4 py-3 text-sm"><span class="px-2 py-1 text-xs rounded-full ${sc[o.status] || ''}">${o.status}</span></td>
                <td class="px-4 py-3 text-sm space-x-2">
                    ${o.status === 'pending' ? `<button onclick="updateStatus(${o.id}, 'delivered')" class="text-blue-600 hover:underline text-xs">Deliver</button>` : ''}
                    ${o.status === 'delivered' ? `<button onclick="updateStatus(${o.id}, 'completed')" class="text-green-600 hover:underline text-xs">Complete</button>` : ''}
                    ${o.status !== 'cancelled' && o.status !== 'completed' ? `<button onclick="updateStatus(${o.id}, 'cancelled')" class="text-red-600 hover:underline text-xs">Cancel</button>` : ''}
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
