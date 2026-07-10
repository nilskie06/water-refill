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

<div class="mb-4">
    <select id="statusFilter" onchange="loadOrders()" class="input-field px-4 py-2.5 text-sm">
        <option value="">All Status</option>
        <option value="pending">⏳ Pending</option>
        <option value="delivered">🚚 Delivered</option>
        <option value="completed">✅ Completed</option>
        <option value="cancelled">❌ Cancelled</option>
    </select>
</div>

<!-- Desktop Table -->
<div class="glass-card desktop-table overflow-x-auto">
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
        <tbody id="orderTable"></tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="mobile-cards space-y-3" id="orderCards"></div>

<div id="emptyState" class="hidden text-center py-12">
    <div class="text-4xl mb-3">📦</div>
    <p class="text-white/40">No orders found</p>
</div>
@endsection

@section('scripts')
<script>
const sc = { pending: 'badge-pending', delivered: 'badge-delivered', completed: 'badge-completed', cancelled: 'badge-cancelled' };

function actionButtons(o) {
    let html = '';
    if (o.status === 'pending') html += `<button onclick="updateStatus(${o.id},'delivered')" class="flex-1 py-2 rounded-lg bg-cyan-500/10 text-cyan-400 text-xs font-medium hover:bg-cyan-500/20 transition">🚚 Deliver</button>`;
    if (o.status === 'delivered') html += `<button onclick="updateStatus(${o.id},'completed')" class="flex-1 py-2 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs font-medium hover:bg-emerald-500/20 transition">✅ Complete</button>`;
    if (o.status !== 'cancelled' && o.status !== 'completed') html += `<button onclick="updateStatus(${o.id},'cancelled')" class="flex-1 py-2 rounded-lg bg-rose-500/10 text-rose-400 text-xs font-medium hover:bg-rose-500/20 transition">❌ Cancel</button>`;
    return html;
}

async function loadOrders() {
    const status = document.getElementById('statusFilter').value;
    const res = await fetch(`/api/orders?status=${status}`, { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    const orders = data.data || [];

    if (orders.length === 0) {
        document.getElementById('emptyState').classList.remove('hidden');
        document.getElementById('orderTable').innerHTML = '';
        document.getElementById('orderCards').innerHTML = '';
        return;
    }
    document.getElementById('emptyState').classList.add('hidden');

    // Desktop
    document.getElementById('orderTable').innerHTML = orders.map(o => `
        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
            <td class="px-6 py-4 text-sm font-medium text-white/90">${o.order_number}</td>
            <td class="px-6 py-4 text-sm text-white/70">${o.customer?.name || '-'}</td>
            <td class="px-6 py-4 text-sm text-white/60">${o.order_date?.split('T')[0] || ''}</td>
            <td class="px-6 py-4 text-sm text-white/70">${o.quantity}</td>
            <td class="px-6 py-4 text-sm font-medium text-emerald-400">₱${parseFloat(o.total).toFixed(2)}</td>
            <td class="px-6 py-4"><span class="badge ${sc[o.status] || ''}">${o.status}</span></td>
            <td class="px-6 py-4 text-sm space-x-2">${actionButtons(o)}</td>
        </tr>`).join('');

    // Mobile
    document.getElementById('orderCards').innerHTML = orders.map(o => `
        <div class="glass-card p-4">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-white font-semibold text-sm">${o.order_number}</h3>
                    <p class="text-white/50 text-xs mt-1">👤 ${o.customer?.name || 'Walk-in'}</p>
                </div>
                <span class="badge ${sc[o.status] || ''}">${o.status}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40">Date</span><p class="text-white/80 font-medium">${o.order_date?.split('T')[0] || '-'}</p></div>
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40">Total</span><p class="text-emerald-400 font-bold">₱${parseFloat(o.total).toFixed(2)}</p></div>
            </div>
            <div class="flex gap-2 pt-2 border-t border-white/5">${actionButtons(o)}</div>
        </div>`).join('');
}
async function updateStatus(id, status) {
    await fetch(`/api/orders/${id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ status }) });
    loadOrders();
}
loadOrders();
</script>
@endsection
