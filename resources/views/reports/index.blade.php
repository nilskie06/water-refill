<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Water Refill Station</title>
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
                <a href="/bottles" class="block px-4 py-3 hover:bg-blue-700">🍶 Bottles</a>
                <a href="/reports" class="block px-4 py-3 hover:bg-blue-700 bg-blue-700">📈 Reports</a>
            </nav>
            <div class="absolute bottom-0 w-64 p-4 border-t border-blue-700">
                <form action="/logout" method="POST">@csrf<button type="submit" class="text-blue-200 hover:text-white text-sm">Logout</button></form>
            </div>
        </aside>

        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">📈 Daily Sales Report</h1>

            <div class="flex gap-4 mb-6 flex-wrap">
                <input type="date" id="dateFrom" class="px-4 py-2 border rounded-lg">
                <span class="self-center">to</span>
                <input type="date" id="dateTo" class="px-4 py-2 border rounded-lg">
                <button onclick="loadReport()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                <button onclick="setToday()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Today</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-sm text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold" id="totalOrders">0</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-sm text-gray-500">Bottles Sold</p>
                    <p class="text-2xl font-bold" id="bottlesSold">0</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-sm text-gray-500">Gross Sales</p>
                    <p class="text-2xl font-bold text-green-600" id="grossSales">₱0</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-sm text-gray-500">Payments Received</p>
                    <p class="text-2xl font-bold text-blue-600" id="paymentsReceived">₱0</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-sm text-gray-500">Outstanding</p>
                    <p class="text-2xl font-bold text-red-600" id="outstanding">₱0</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b"><h2 class="text-lg font-semibold">🏆 Top Customers</h2></div>
                    <div class="p-4" id="topCustomers"><p class="text-gray-400 text-center py-4">No data</p></div>
                </div>
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b"><h2 class="text-lg font-semibold">💳 Payments by Method</h2></div>
                    <div class="p-4" id="paymentsByMethod"><p class="text-gray-400 text-center py-4">No data</p></div>
                </div>
            </div>
        </main>
    </div>

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

        document.getElementById('topCustomers').innerHTML = (data.top_customers || []).map((c, i) =>
            `<div class="flex justify-between items-center py-2 border-b last:border-0"><div><span class="font-medium">${c.customer?.name}</span><span class="text-sm text-gray-500 ml-2">(${c.order_count} orders)</span></div><span class="font-bold text-green-600">₱${fmt(c.total_spent)}</span></div>`
        ).join('') || '<p class="text-gray-400 text-center py-4">No data</p>';

        document.getElementById('paymentsByMethod').innerHTML = (data.payments_by_method || []).map(m =>
            `<div class="flex justify-between items-center py-2 border-b last:border-0"><span class="capitalize">${m.payment_method.replace('_', ' ')}</span><span class="font-bold">₱${fmt(m.total)}</span></div>`
        ).join('') || '<p class="text-gray-400 text-center py-4">No data</p>';
    }

    setToday();
    </script>
</body>
</html>
