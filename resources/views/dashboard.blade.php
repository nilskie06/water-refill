@extends('layouts.app')
@section('title', 'Dashboard - Water Refill Station')

@section('content')
<div class="mb-6 lg:mb-8">
    <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">📊 Dashboard</h1>
    <p class="text-white/40 text-xs lg:text-sm mt-1">Welcome back, {{ Auth::user()->name ?? 'User' }}</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
    <div class="stat-card glass-card p-3 lg:p-5">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                <span class="text-emerald-400 text-sm">💰</span>
            </div>
            <p class="text-white/40 text-[10px] lg:text-xs uppercase tracking-wider">Sales</p>
        </div>
        <p class="text-lg lg:text-2xl font-bold text-emerald-400" id="todaySales">₱0.00</p>
    </div>
    <div class="stat-card glass-card p-3 lg:p-5" style="animation-delay: 0.1s">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                <span class="text-cyan-400 text-sm">📦</span>
            </div>
            <p class="text-white/40 text-[10px] lg:text-xs uppercase tracking-wider">Orders</p>
        </div>
        <p class="text-lg lg:text-2xl font-bold text-cyan-400" id="todayOrders">0</p>
    </div>
    <div class="stat-card glass-card p-3 lg:p-5" style="animation-delay: 0.2s">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                <span class="text-amber-400 text-sm">⏳</span>
            </div>
            <p class="text-white/40 text-[10px] lg:text-xs uppercase tracking-wider">Pending</p>
        </div>
        <p class="text-lg lg:text-2xl font-bold text-amber-400" id="outstanding">₱0.00</p>
    </div>
    <div class="stat-card glass-card p-3 lg:p-5" style="animation-delay: 0.3s">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center">
                <span class="text-violet-400 text-sm">🍶</span>
            </div>
            <p class="text-white/40 text-[10px] lg:text-xs uppercase tracking-wider">Bottles</p>
        </div>
        <p class="text-lg lg:text-2xl font-bold text-violet-400" id="bottlesOut">0</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <a href="/orders/create" class="glass-card p-3 flex items-center gap-3 hover:bg-white/[0.06] transition group">
        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center group-hover:bg-cyan-500/20 transition">
            <span class="text-lg">➕</span>
        </div>
        <div>
            <p class="text-white/90 text-sm font-medium">New Order</p>
            <p class="text-white/40 text-[10px]">Create order</p>
        </div>
    </a>
    <a href="/customers" class="glass-card p-3 flex items-center gap-3 hover:bg-white/[0.06] transition group">
        <div class="w-10 h-10 rounded-xl bg-violet-500/10 flex items-center justify-center group-hover:bg-violet-500/20 transition">
            <span class="text-lg">👥</span>
        </div>
        <div>
            <p class="text-white/90 text-sm font-medium">Customers</p>
            <p class="text-white/40 text-[10px]">Manage list</p>
        </div>
    </a>
    <a href="/payments/create" class="glass-card p-3 flex items-center gap-3 hover:bg-white/[0.06] transition group">
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center group-hover:bg-emerald-500/20 transition">
            <span class="text-lg">💳</span>
        </div>
        <div>
            <p class="text-white/90 text-sm font-medium">Payment</p>
            <p class="text-white/40 text-[10px]">Record</p>
        </div>
    </a>
    <a href="/reports" class="glass-card p-3 flex items-center gap-3 hover:bg-white/[0.06] transition group">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center group-hover:bg-amber-500/20 transition">
            <span class="text-lg">📈</span>
        </div>
        <div>
            <p class="text-white/90 text-sm font-medium">Reports</p>
            <p class="text-white/40 text-[10px]">View sales</p>
        </div>
    </a>
</div>

<!-- Recent Orders -->
<div class="glass-card">
    <div class="px-4 lg:px-6 py-4 border-b border-white/5">
        <h2 class="text-base lg:text-lg font-semibold text-white/90">Recent Orders</h2>
    </div>
    <!-- Desktop Table -->
    <table class="w-full desktop-table">
        <thead>
            <tr class="border-b border-white/10">
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Order</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Qty</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
        <tbody id="recentOrdersTable"></tbody>
    </table>
    <!-- Mobile Cards -->
    <div class="mobile-cards p-3 space-y-2" id="recentOrdersCards"></div>
    <div id="noOrders" class="hidden text-center py-8">
        <p class="text-white/30 text-sm">No recent orders</p>
    </div>
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

    const orders = data.recent_orders || [];
    if (orders.length === 0) {
        document.getElementById('noOrders').classList.remove('hidden');
        return;
    }

    // Desktop table
    document.getElementById('recentOrdersTable').innerHTML = orders.map(o => `
        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
            <td class="px-6 py-3 text-sm font-medium text-white/90">${o.order_number}</td>
            <td class="px-6 py-3 text-sm text-white/70">${o.customer?.name || '-'}</td>
            <td class="px-6 py-3 text-sm text-white/70">${o.quantity}</td>
            <td class="px-6 py-3 text-sm font-medium text-emerald-400">₱${parseFloat(o.total).toFixed(2)}</td>
            <td class="px-6 py-3"><span class="badge ${sc[o.status] || ''}">${o.status}</span></td>
        </tr>`).join('');

    // Mobile cards
    document.getElementById('recentOrdersCards').innerHTML = orders.map(o => `
        <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.03] hover:bg-white/[0.05] transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center">
                    <span class="text-xs font-bold text-cyan-400">#${o.id}</span>
                </div>
                <div>
                    <p class="text-white/90 text-sm font-medium">${o.customer?.name || 'Walk-in'}</p>
                    <p class="text-white/40 text-xs">${o.quantity} qty • ₱${parseFloat(o.total).toFixed(2)}</p>
                </div>
            </div>
            <span class="badge ${sc[o.status] || ''} text-[10px]">${o.status}</span>
        </div>`).join('');
}
load();
</script>
@endsection
