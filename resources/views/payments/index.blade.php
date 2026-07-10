<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Water Refill Station</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-blue-800 text-white flex-shrink-0">
            <div class="p-4"><h1 class="text-xl font-bold">💧 Water Refill</h1></div>
            <nav class="mt-2">
                <a href="/dashboard" class="block px-4 py-3 hover:bg-blue-700">📊 Dashboard</a>
                <a href="/customers" class="block px-4 py-3 hover:bg-blue-700">👥 Customers</a>
                <a href="/orders" class="block px-4 py-3 hover:bg-blue-700">📦 Orders</a>
                <a href="/payments" class="block px-4 py-3 hover:bg-blue-700 bg-blue-700">💰 Payments</a>
                <a href="/bottles" class="block px-4 py-3 hover:bg-blue-700">🍶 Bottles</a>
                <a href="/reports" class="block px-4 py-3 hover:bg-blue-700">📈 Reports</a>
            </nav>
            <div class="absolute bottom-0 w-64 p-4 border-t border-blue-700">
                <form action="/logout" method="POST">@csrf<button type="submit" class="text-blue-200 hover:text-white text-sm">Logout</button></form>
            </div>
        </aside>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">💰 Payments</h1>
                <a href="/payments/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Record Payment</a>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody id="paymentList" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = '/login';

    async function loadPayments() {
        const res = await fetch('/api/payments', { headers: { 'Authorization': 'Bearer ' + token } });
        if (!res.ok) { window.location.href = '/login'; return; }
        const data = await res.json();
        document.getElementById('paymentList').innerHTML = data.data.map(p => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm">#${p.id}</td>
                <td class="px-4 py-3 text-sm">${p.order?.order_number || '-'}</td>
                <td class="px-4 py-3 text-sm">${p.order?.customer?.name || '-'}</td>
                <td class="px-4 py-3 text-sm font-medium text-green-600">₱${parseFloat(p.amount).toFixed(2)}</td>
                <td class="px-4 py-3 text-sm"><span class="px-2 py-1 text-xs rounded-full bg-gray-100">${p.payment_method}</span></td>
                <td class="px-4 py-3 text-sm">${p.payment_date?.split('T')[0] || ''}</td>
            </tr>
        `).join('');
    }
    loadPayments();
    </script>
</body>
</html>
