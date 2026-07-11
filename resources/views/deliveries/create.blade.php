@extends('layouts.app')
@section('title', 'New Delivery - Water Refill Station')

@section('content')
<div class="mb-6">
    <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">➕ New Delivery</h1>
    <p class="text-white/40 text-xs lg:text-sm mt-1">Schedule a new delivery</p>
</div>

<form id="deliveryForm" onsubmit="saveDelivery(event)" class="max-w-2xl">
    <!-- Customer & Order Selection -->
    <div class="glass-card p-6 mb-4">
        <h2 class="text-lg font-semibold text-white/90 mb-4">👤 Customer & Order</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-white/60 text-sm mb-2">Customer *</label>
                <select id="customerId" required onchange="loadOrders()" class="input-field w-full px-4 py-3 text-sm">
                    <option value="">Select customer</option>
                    @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-white/60 text-sm mb-2">Link to Order</label>
                <select id="orderId" onchange="fillFromOrder()" class="input-field w-full px-4 py-3 text-sm">
                    <option value="">No order (manual entry)</option>
                </select>
                <p id="orderHint" class="text-cyan-400/60 text-[10px] mt-1 hidden">✅ Order selected — fields auto-filled</p>
            </div>
        </div>
    </div>

    <!-- Order Summary (shown when order is selected) -->
    <div id="orderSummary" class="hidden glass-card p-4 mb-4 border-l-4 border-cyan-500/30 bg-cyan-500/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center shrink-0">
                <span class="text-sm">📦</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white/90 text-sm font-semibold" id="summaryOrder">-</p>
                <p class="text-white/50 text-xs" id="summaryItems">-</p>
            </div>
            <div class="text-right">
                <p class="text-emerald-400 font-bold text-sm" id="summaryTotal">-</p>
                <p class="text-white/40 text-[10px]" id="summaryStatus">-</p>
            </div>
        </div>
    </div>

    <!-- Delivery Details -->
    <div class="glass-card p-6 mb-4">
        <h2 class="text-lg font-semibold text-white/90 mb-4">🚚 Delivery Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-white/60 text-sm mb-2">Delivery Type</label>
                <select id="deliveryType" class="input-field w-full px-4 py-3 text-sm">
                    <option value="regular">🚚 Regular</option>
                    <option value="rush">⚡ Rush</option>
                    <option value="scheduled">📅 Scheduled</option>
                    <option value="pickup">📦 Pickup</option>
                </select>
            </div>
            <div>
                <label class="block text-white/60 text-sm mb-2">Quantity *</label>
                <input type="number" id="quantity" required value="1" min="1" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-white/60 text-sm mb-2">Delivery Date *</label>
                <input type="date" id="deliveryDate" required value="{{ date('Y-m-d') }}" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-white/60 text-sm mb-2">Delivery Time</label>
                <input type="time" id="deliveryTime" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-white/60 text-sm mb-2">Address *</label>
                <textarea id="address" required class="input-field w-full px-4 py-3 text-sm" rows="2"></textarea>
            </div>
            <div>
                <label class="block text-white/60 text-sm mb-2">Contact Number</label>
                <input type="text" id="contactNumber" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-white/60 text-sm mb-2">Route</label>
                <select id="route" class="input-field w-full px-4 py-3 text-sm">
                    <option value="">Select route</option>
                    <option value="morning">🌅 Morning</option>
                    <option value="afternoon">☀️ Afternoon</option>
                    <option value="evening">🌙 Evening</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-white/60 text-sm mb-2">Remarks</label>
                <textarea id="remarks" class="input-field w-full px-4 py-3 text-sm" rows="2"></textarea>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="/deliveries" class="btn-secondary px-6 py-2.5 rounded-xl text-sm">Cancel</a>
        <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-white text-sm font-medium">Create Delivery</button>
    </div>
</form>
@endsection

@section('scripts')
<script>
let customerOrders = [];
let selectedOrder = null;

async function loadOrders() {
    const customerId = document.getElementById('customerId').value;
    const orderSelect = document.getElementById('orderId');
    const summary = document.getElementById('orderSummary');
    const hint = document.getElementById('orderHint');

    orderSelect.innerHTML = '<option value="">Loading orders...</option>';
    summary.classList.add('hidden');
    hint.classList.add('hidden');
    selectedOrder = null;

    if (!customerId) {
        orderSelect.innerHTML = '<option value="">Select customer first</option>';
        return;
    }

    // Fetch customer details and their orders
    const [custRes, ordersRes] = await Promise.all([
        fetch(`/api/customers/${customerId}`, { credentials: 'same-origin' }),
        fetch(`/api/orders?per_page=100`, { credentials: 'same-origin' })
    ]);

    // Fill customer info
    const customer = await custRes.json();
    if (customer.address) document.getElementById('address').value = customer.address;
    if (customer.contact) document.getElementById('contactNumber').value = customer.contact;

    // Filter orders for this customer that aren't completed
    const allOrders = await ordersRes.json();
    customerOrders = (allOrders.data || []).filter(o =>
        o.customer_id == customerId && o.status !== 'completed'
    );

    if (customerOrders.length === 0) {
        orderSelect.innerHTML = '<option value="">No pending orders for this customer</option>';
    } else {
        orderSelect.innerHTML = '<option value="">No order (manual entry)</option>' +
            customerOrders.map(o =>
                `<option value="${o.id}">${o.order_number} — ₱${parseFloat(o.total).toFixed(2)} (${o.status})</option>`
            ).join('');
    }
}

function fillFromOrder() {
    const orderId = document.getElementById('orderId').value;
    const summary = document.getElementById('orderSummary');
    const hint = document.getElementById('orderHint');

    if (!orderId) {
        summary.classList.add('hidden');
        hint.classList.add('hidden');
        selectedOrder = null;
        return;
    }

    selectedOrder = customerOrders.find(o => o.id == orderId);
    if (!selectedOrder) return;

    // Auto-fill from order
    document.getElementById('quantity').value = selectedOrder.quantity || 1;
    if (selectedOrder.delivery_address) document.getElementById('address').value = selectedOrder.delivery_address;

    // Show order summary
    summary.classList.remove('hidden');
    hint.classList.remove('hidden');
    document.getElementById('summaryOrder').textContent = selectedOrder.order_number;
    document.getElementById('summaryItems').textContent = `${selectedOrder.quantity}x ${selectedOrder.items || 'water delivery'}`;
    document.getElementById('summaryTotal').textContent = '₱' + parseFloat(selectedOrder.total).toFixed(2);
    document.getElementById('summaryStatus').textContent = selectedOrder.status;
}

async function saveDelivery(e) {
    e.preventDefault();
    const data = {
        customer_id: document.getElementById('customerId').value,
        order_id: document.getElementById('orderId').value || null,
        delivery_date: document.getElementById('deliveryDate').value,
        delivery_time: document.getElementById('deliveryTime').value || null,
        address: document.getElementById('address').value,
        contact_number: document.getElementById('contactNumber').value,
        quantity: document.getElementById('quantity').value,
        delivery_type: document.getElementById('deliveryType').value,
        route: document.getElementById('route').value || null,
        remarks: document.getElementById('remarks').value,
    };
    const res = await fetch('/api/deliveries', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    });
    if (res.ok) {
        window.location.href = '/deliveries';
    } else {
        const err = await res.json();
        alert(err.message || 'Error saving delivery');
    }
}
</script>
@endsection
