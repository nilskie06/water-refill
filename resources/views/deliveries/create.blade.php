@extends('layouts.app')
@section('title', 'New Delivery - Water Refill Station')

@section('content')
<div class="mb-6">
    <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">➕ New Delivery</h1>
    <p class="text-white/40 text-xs lg:text-sm mt-1">Schedule a new delivery</p>
</div>

<form id="deliveryForm" onsubmit="saveDelivery(event)" class="max-w-2xl">
    <div class="glass-card p-6 mb-4">
        <h2 class="text-lg font-semibold text-white/90 mb-4">📦 Delivery Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-white/60 text-sm mb-2">Customer *</label>
                <select id="customerId" required class="input-field w-full px-4 py-3 text-sm">
                    <option value="">Select customer</option>
                    @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select></div>
            <div><label class="block text-white/60 text-sm mb-2">Delivery Type</label>
                <select id="deliveryType" class="input-field w-full px-4 py-3 text-sm">
                    <option value="regular">🚚 Regular</option><option value="rush">⚡ Rush</option>
                    <option value="scheduled">📅 Scheduled</option><option value="pickup">📦 Pickup</option>
                </select></div>
            <div><label class="block text-white/60 text-sm mb-2">Delivery Date *</label><input type="date" id="deliveryDate" required value="{{ date('Y-m-d') }}" class="input-field w-full px-4 py-3 text-sm"></div>
            <div><label class="block text-white/60 text-sm mb-2">Delivery Time</label><input type="time" id="deliveryTime" class="input-field w-full px-4 py-3 text-sm"></div>
            <div class="md:col-span-2"><label class="block text-white/60 text-sm mb-2">Address *</label><textarea id="address" required class="input-field w-full px-4 py-3 text-sm" rows="2"></textarea></div>
            <div><label class="block text-white/60 text-sm mb-2">Contact Number</label><input type="text" id="contactNumber" class="input-field w-full px-4 py-3 text-sm"></div>
            <div><label class="block text-white/60 text-sm mb-2">Quantity *</label><input type="number" id="quantity" required value="1" min="1" class="input-field w-full px-4 py-3 text-sm"></div>
            <div><label class="block text-white/60 text-sm mb-2">Route</label>
                <select id="route" class="input-field w-full px-4 py-3 text-sm">
                    <option value="">Select route</option><option value="morning">🌅 Morning</option>
                    <option value="afternoon">☀️ Afternoon</option><option value="evening">🌙 Evening</option>
                </select></div>
            <div class="md:col-span-2"><label class="block text-white/60 text-sm mb-2">Remarks</label><textarea id="remarks" class="input-field w-full px-4 py-3 text-sm" rows="2"></textarea></div>
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
async function saveDelivery(e) {
    e.preventDefault();
    const data = {
        customer_id: document.getElementById('customerId').value,
        delivery_date: document.getElementById('deliveryDate').value,
        delivery_time: document.getElementById('deliveryTime').value || null,
        address: document.getElementById('address').value,
        contact_number: document.getElementById('contactNumber').value,
        quantity: document.getElementById('quantity').value,
        delivery_type: document.getElementById('deliveryType').value,
        route: document.getElementById('route').value || null,
        remarks: document.getElementById('remarks').value,
    };
    const res = await fetch('/api/deliveries', { method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin', body:JSON.stringify(data) });
    if (res.ok) window.location.href = '/deliveries';
    else { const err = await res.json(); alert(err.message || 'Error saving delivery'); }
}
// Auto-fill address from customer
document.getElementById('customerId').addEventListener('change', async function() {
    const id = this.value;
    if (!id) return;
    const res = await fetch(`/api/customers/${id}`, { credentials:'same-origin' });
    const c = await res.json();
    if (c.address) document.getElementById('address').value = c.address;
    if (c.contact) document.getElementById('contactNumber').value = c.contact;
});
</script>
@endsection