<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Payment - Water Refill Station</title>
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
            <div class="mb-6"><a href="/payments" class="text-blue-600 hover:underline">← Back to Payments</a></div>

            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <h1 class="text-2xl font-bold mb-6">💰 Record Payment</h1>
                <form id="paymentForm" onsubmit="submitPayment(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Order *</label>
                            <select id="orderId" required class="w-full px-3 py-2 border rounded-lg" onchange="showOrderInfo()">
                                <option value="">Select Order</option>
                                @foreach($orders as $o)
                                    <option value="{{ $o->id }}" data-total="{{ $o->total }}">{{ $o->order_number }} - {{ $o->customer->name }} (₱{{ $o->total }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Amount *</label>
                            <input type="number" id="amount" step="0.01" min="0.01" required class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Payment Method *</label>
                            <select id="paymentMethod" required class="w-full px-3 py-2 border rounded-lg">
                                <option value="cash">Cash</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Payment Date *</label>
                            <input type="date" id="paymentDate" required class="w-full px-3 py-2 border rounded-lg">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <a href="/payments" class="px-4 py-2 border rounded-lg">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Record Payment</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = '/login';
    document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];

    async function submitPayment(e) {
        e.preventDefault();
        const data = {
            order_id: document.getElementById('orderId').value,
            amount: parseFloat(document.getElementById('amount').value),
            payment_method: document.getElementById('paymentMethod').value,
            payment_date: document.getElementById('paymentDate').value
        };
        const res = await fetch('/api/payments', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token }, body: JSON.stringify(data) });
        if (res.ok) window.location.href = '/payments';
        else { const err = await res.json(); alert(err.message || 'Error'); }
    }
    </script>
</body>
</html>
