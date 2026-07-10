@extends('layouts.app')
@section('title', 'Orders - Water Refill Station')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">📦 Orders</h1>
        <p class="text-white/40 text-sm mt-1">Manage refill orders</p>
    </div>
    <a href="/orders/create" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ New Order</a>
</div>

<div class="flex gap-3 mb-4">
    <select id="statusFilter" onchange="loadOrders()" class="input-field px-4 py-2.5 text-sm">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="delivered">Delivered</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>
</div>

<div class="table-container">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Order #</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Date</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Qty</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Total</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody id="orderList"></tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
const sc = { pending: 'badge-pending', delivered: 'badge-delivered', completed: 'badge-completed', cancelled: 'badge-cancelled' };
async function loadOrders() {
    const status = document.getElementById('statusFilter').value;
    const res = await fetch(`/api/orders?status=${status}`, { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    document.getElementById('orderList').innerHTML = data.data.map(o => `
        <tr class="table-row">
            <td class="px-6 py-4 text-sm font-medium text-white/90">${o.order_number}</td>
            <td class="px-6 py-4 text-sm text-white/70">${o.customer?.name || '-'}</td>
            <td class="px-6 py-4 text-sm text-white/60">${o.order_date?.split('T')[0] || ''}</td>
            <td class="px-6 py-4 text-sm text-white/70">${o.quantity}</td>
            <td class="px-6 py-4 text-sm font-medium text-emerald-400">₱${parseFloat(o.total).toFixed(2)}</td>
            <td class="px-6 py-4"><span class="badge ${sc[o.status] || ''}">${o.status}</span></td>
            <td class="px-6 py-4 text-sm space-x-2">
                ${o.status==='pending'?`<button onclick="updateStatus(${o.id},'delivered')" class="text-cyan-400 hover:text-cyan-300">Deliver</button>`:''}
                ${o.status==='delivered'?`<button onclick="updateStatus(${o.id},'completed')" class="text-emerald-400 hover:text-emerald-300">Complete</button>`:''}
                ${o.status!=='cancelled'&&o.status!=='completed'?`<button onclick="updateStatus(${o.id},'cancelled')" class="text-rose-400 hover:text-rose-300">Cancel</button>`:''}
            </td>
        </tr>`).join('');
}
async function updateStatus(id, status) {
    await fetch(`/api/orders/${id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ status }) });
    loadOrders();
}
loadOrders();
</script>
@endsection
