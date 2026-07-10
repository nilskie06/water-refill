<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bottle Balances - Water Refill Station</title>
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
                <a href="/payments" class="block px-4 py-3 hover:bg-blue-700">💰 Payments</a>
                <a href="/bottles" class="block px-4 py-3 hover:bg-blue-700 bg-blue-700">🍶 Bottles</a>
                <a href="/reports" class="block px-4 py-3 hover:bg-blue-700">📈 Reports</a>
            </nav>
            <div class="absolute bottom-0 w-64 p-4 border-t border-blue-700">
                <form action="/logout" method="POST">@csrf<button type="submit" class="text-blue-200 hover:text-white text-sm">Logout</button></form>
            </div>
        </aside>

        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">🍶 Bottle Balances</h1>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bottles Out</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Returned</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                        </tr>
                    </thead>
                    <tbody id="bottleList" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </main>
    </div>

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
                    html += `<tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium">${c.name}</td>
                        <td class="px-4 py-3 text-sm">${b.bottles_out}</td>
                        <td class="px-4 py-3 text-sm">${b.bottles_returned}</td>
                        <td class="px-4 py-3 text-sm"><span class="font-bold ${b.balance > 0 ? 'text-orange-600' : 'text-green-600'}">${b.balance}</span></td>
                    </tr>`;
                }
            }
        }
        document.getElementById('bottleList').innerHTML = html || '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-400">No bottle records</td></tr>';
    }
    loadBottles();
    </script>
</body>
</html>
