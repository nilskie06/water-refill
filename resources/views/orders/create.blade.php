@extends('layouts.app')
@section('title', 'New Order - Water Refill Station')

@section('content')
<div class="mb-6"><a href="/orders" class="text-cyan-400 hover:text-cyan-300 text-sm">← Back to Orders</a></div>

<div class="glass-card p-6 lg:p-8 max-w-2xl">
    <h1 class="text-2xl font-bold text-white mb-6">📦 New Order</h1>
    <form id="orderForm" onsubmit="submitOrder(event)">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Customer *</label>
                <select id="customerId" required class="input-field w-full px-4 py-3 text-sm">
                    <option value="">Select Customer</option>
                    @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Product</label>
                <input type="text" id="product" value="Pure Water Gallon" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Quantity *</label>
                <input type="number" id="quantity" value="1" min="1" required class="input-field w-full px-4 py-3 text-sm" oninput="calcTotal()">
            </div>
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Unit Price (₱) *</label>
                <input type="number" id="unitPrice" value="25" step="0.01" min="0" required class="input-field w-full px-4 py-3 text-sm" oninput="calcTotal()">
            </div>
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Delivery Type</label>
                <select id="deliveryType" class="input-field w-full px-4 py-3 text-sm"><option value="pickup">Pickup</option><option value="delivery">Delivery</option></select>
            </div>
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Bottle In</label>
                <input type="number" id="bottleIn" value="0" min="0" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Bottle Out</label>
                <input type="number" id="bottleOut" value="1" min="0" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div class="flex items-end">
                <div class="w-full p-4 rounded-xl bg-white/[0.03] border border-white/5">
                    <p class="text-white/40 text-xs uppercase tracking-wider">Total Amount</p>
                    <p class="text-2xl font-bold text-emerald-400 mt-1" id="totalDisplay">₱25.00</p>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-8">
            <a href="/orders" class="btn-secondary px-6 py-2.5 rounded-xl text-sm">Cancel</a>
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-white text-sm font-medium">Create Order</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function calcTotal() { const q = parseInt(document.getElementById('quantity').value)||0; const p = parseFloat(document.getElementById('unitPrice').value)||0; document.getElementById('totalDisplay').textContent = '₱' + (q*p).toFixed(2); document.getElementById('bottleOut').value = q; }
async function submitOrder(e) {
    e.preventDefault();
    const data = { customer_id: document.getElementById('customerId').value, product: document.getElementById('product').value, quantity: parseInt(document.getElementById('quantity').value), unit_price: parseFloat(document.getElementById('unitPrice').value), delivery_type: document.getElementById('deliveryType').value, bottle_in: parseInt(document.getElementById('bottleIn').value)||0, bottle_out: parseInt(document.getElementById('bottleOut').value)||1 };
    const res = await fetch('/api/orders', { method: 'POST', headers: {'Content-Type':'application/json'}, credentials: 'same-origin', body: JSON.stringify(data) });
    if (res.ok) window.location.href = '/orders'; else alert('Error creating order');
}
</script>
@endsection
