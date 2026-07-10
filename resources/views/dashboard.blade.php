@extends('layouts.app')
@section('title', 'Dashboard - Water Refill Station')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">📊 Dashboard</h1>
    <p class="text-white/40 text-sm mt-1">Welcome back, {{ Auth::user()->name ?? 'User' }}</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card glass-card p-5">
        <p class="text-white/40 text-xs uppercase tracking-wider">Today's Sales</p>
        <p class="text-2xl font-bold text-emerald-400 mt-2" id="todaySales">₱0.00</p>
    </div>
    <div class="stat-card glass-card p-5" style="animation-delay: 0.1s">
        <p class="text-white/40 text-xs uppercase tracking-wider">Today's Orders</p>
        <p class="text-2xl font-bold text-cyan-400 mt-2" id="todayOrders">0</p>
    </div>
    <div class="stat-card glass-card p-5" style="animation-delay: 0.2s">
        <p class="text-white/40 text-xs uppercase tracking-wider">Outstanding</p>
        <p class="text-2xl font-bold text-amber-400 mt-2" id="outstanding">₱0.00</p>
    </div>
    <div class="stat-card glass-card p-5" style="animation-delay: 0.3s">
        <p class="text-white/40 text-xs uppercase tracking-wider">Bottles Out</p>
        <p class="text-2xl font-bold text-violet-400 mt-2" id="bottlesOut">0</p>
    </div>
</div>

<div class="table-container">
    <div class="px-6 py-4 border-b border-white/5">
        <h2 class="text-lg font-semibold text-white/90">Recent Orders</h2>
    </div>
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Order #</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Qty</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody id="recentOrders"></tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
const sc = { pending: 'badge-pending', delivered: 'badge-delivered', completed: 'badge-completed', cancelled: 'badge-cancelled' };
async function load() {
    const res = await fetch('/api/dashboard', { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    document.getElementById('todaySales').textContent = '₱' + parseFloat(data.today_sales || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('todayOrders').textContent = data.today_orders || 0;
    document.getElementById('outstanding').textContent = '₱' + parseFloat(data.outstanding_payments || 0).toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('bottlesOut').textContent = data.bottles_out || 0;
    document.getElementById('recentOrders').innerHTML = (data.recent_orders || []).map(o => `
        <tr class="table-row">
            <td class="px-6 py-4 text-sm font-medium text-white/90">${o.order_number}</td>
            <td class="px-6 py-4 text-sm text-white/70">${o.customer?.name || '-'}</td>
            <td class="px-6 py-4 text-sm text-white/70">${o.quantity}</td>
            <td class="px-6 py-4 text-sm font-medium text-emerald-400">₱${parseFloat(o.total).toFixed(2)}</td>
            <td class="px-6 py-4"><span class="badge ${sc[o.status] || ''}">${o.status}</span></td>
        </tr>`).join('');
}
load();
</script>
@endsection
