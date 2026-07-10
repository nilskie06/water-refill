<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order - Water Refill Station</title>
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
            <div class="mb-6"><a href="/orders" class="text-blue-600 hover:underline">← Back to Orders</a></div>

            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <h1 class="text-2xl font-bold mb-6">📦 New Order</h1>
                <form id="orderForm" onsubmit="submitOrder(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Customer *</label>
                            <select id="customerId" required class="w-full px-3 py-2 border rounded-lg">
                                <option value="">Select Customer</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Product</label>
                            <input type="text" id="product" value="Pure Water Gallon" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Quantity *</label>
                            <input type="number" id="quantity" value="1" min="1" required class="w-full px-3 py-2 border rounded-lg" oninput="calcTotal()">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Unit Price (₱) *</label>
                            <input type="number" id="unitPrice" value="25" step="0.01" min="0" required class="w-full px-3 py-2 border rounded-lg" oninput="calcTotal()">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Delivery Type</label>
                            <select id="deliveryType" class="w-full px-3 py-2 border rounded-lg">
                                <option value="pickup">Pickup</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Bottle In</label>
                            <input type="number" id="bottleIn" value="0" min="0" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Bottle Out</label>
                            <input type="number" id="bottleOut" value="1" min="0" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="mb-4 flex items-end">
                            <div class="w-full p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500">Total Amount</p>
                                <p class="text-2xl font-bold text-green-600" id="totalDisplay">₱25.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 mt-6">
                        <a href="/orders" class="px-4 py-2 border rounded-lg">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Order</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
    const token = localStorage.getItem('token');
    if (!token) window.location.href = '/login';

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
        const res = await fetch('/api/orders', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token }, body: JSON.stringify(data) });
        if (res.ok) window.location.href = '/orders';
        else alert('Error creating order');
    }
    </script>
</body>
</html>
