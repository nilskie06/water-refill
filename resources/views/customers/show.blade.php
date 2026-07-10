@extends('layouts.app')
@section('title', 'Customer Profile - Water Refill Station')

@section('content')
<div class="mb-6"><a href="/customers" class="text-cyan-400 hover:text-cyan-300 text-sm">← Back to Customers</a></div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="glass-card p-6">
        <h2 class="text-lg font-bold text-white mb-4" id="custName">Loading...</h2>
        <div class="space-y-3 text-sm">
            <div><span class="text-white/40">Contact:</span> <span class="text-white/80" id="custContact">-</span></div>
            <div><span class="text-white/40">Address:</span> <span class="text-white/80" id="custAddress">-</span></div>
            <div><span class="text-white/40">Notes:</span> <span class="text-white/80" id="custNotes">-</span></div>
            <div><span class="text-white/40">Total Orders:</span> <span class="text-cyan-400 font-bold" id="custOrders">0</span></div>
            <div><span class="text-white/40">Total Spent:</span> <span class="text-emerald-400 font-bold" id="custSpent">₱0</span></div>
        </div>
    </div>
    <div class="glass-card p-6">
        <h2 class="text-lg font-bold text-white mb-4">🍶 Bottle Balance</h2>
        <div class="space-y-3 text-sm" id="bottleInfo">
            <div><span class="text-white/40">Bottles Out:</span> <span class="text-white/80 font-bold" id="bOut">0</span></div>
            <div><span class="text-white/40">Returned:</span> <span class="text-white/80 font-bold" id="bRet">0</span></div>
            <div><span class="text-white/40">Balance:</span> <span class="font-bold" id="bBal">0</span></div>
        </div>
    </div>
    <div class="glass-card p-6">
        <h2 class="text-lg font-bold text-white mb-4">📊 Summary</h2>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-white/40">Pending Orders:</span><span class="text-amber-400 font-bold" id="pendingCount">0</span></div>
            <div class="flex justify-between"><span class="text-white/40">Completed Orders:</span><span class="text-emerald-400 font-bold" id="completedCount">0</span></div>
            <div class="flex justify-between"><span class="text-white/40">Outstanding:</span><span class="text-rose-400 font-bold" id="outstanding">₱0</span></div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="px-6 py-4 border-b border-white/5"><h2 class="text-lg font-semibold text-white/90">Recent Orders</h2></div>
    <table class="w-full">
        <thead><tr class="border-b border-white/10">
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Order #</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Date</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Qty</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Total</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Status</th>
        </tr></thead>
        <tbody id="orderList"></tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
const sc = { pending: 'badge-pending', delivered: 'badge-delivered', completed: 'badge-completed', cancelled: 'badge-cancelled' };
const fmt = n => parseFloat(n||0).toLocaleString('en-US',{minimumFractionDigits:2});
async function load() {
    const res = await fetch('/api/customers/{{ $customer->id }}', { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    document.getElementById('custName').textContent = data.name;
    document.getElementById('custContact').textContent = data.contact || '-';
    document.getElementById('custAddress').textContent = data.address || '-';
    document.getElementById('custNotes').textContent = data.notes || '-';
    document.getElementById('custOrders').textContent = data.total_orders || 0;
    document.getElementById('custSpent').textContent = '₱' + fmt(data.total_spent);
    if (data.bottle_balance) {
        document.getElementById('bOut').textContent = data.bottle_balance.bottles_out;
        document.getElementById('bRet').textContent = data.bottle_balance.bottles_returned;
        const b = data.bottle_balance.balance;
        const el = document.getElementById('bBal');
        el.textContent = b;
        el.className = 'font-bold ' + (b > 0 ? 'text-amber-400' : 'text-emerald-400');
    }
    const orders = data.orders || [];
    document.getElementById('pendingCount').textContent = orders.filter(o=>o.status==='pending').length;
    document.getElementById('completedCount').textContent = orders.filter(o=>o.status==='completed').length;
    document.getElementById('outstanding').textContent = '₱' + fmt(orders.reduce((s,o) => s + parseFloat(o.balance||0), 0));
    document.getElementById('orderList').innerHTML = orders.map(o => `
        <tr class="table-row">
            <td class="px-6 py-4 text-sm font-medium text-white/90">${o.order_number}</td>
            <td class="px-6 py-4 text-sm text-white/60">${o.order_date?.split('T')[0]||''}</td>
            <td class="px-6 py-4 text-sm text-white/70">${o.quantity}</td>
            <td class="px-6 py-4 text-sm font-medium text-emerald-400">₱${parseFloat(o.total).toFixed(2)}</td>
            <td class="px-6 py-4"><span class="badge ${sc[o.status]||''}">${o.status}</span></td>
        </tr>`).join('') || '<tr><td colspan="5" class="px-6 py-8 text-center text-white/30">No orders yet</td></tr>';
}
load();
</script>
@endsection
