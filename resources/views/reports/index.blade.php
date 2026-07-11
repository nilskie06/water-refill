@extends('layouts.app')
@section('title', 'Reports - Water Refill Station')

@section('content')
<div class="mb-6">
    <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">📈 Reports</h1>
    <p class="text-white/40 text-xs lg:text-sm mt-1">Business performance overview</p>
</div>

<!-- Filters -->
<div class="glass-card p-3 mb-4 flex flex-wrap gap-2 items-center">
    <input type="date" id="dateFrom" class="input-field px-3 py-2 text-sm">
    <span class="text-white/30 text-sm">to</span>
    <input type="date" id="dateTo" class="input-field px-3 py-2 text-sm">
    <button onclick="loadReport()" class="btn-primary px-4 py-2 rounded-xl text-sm font-medium">Filter</button>
    <button onclick="setToday()" class="btn-secondary px-4 py-2 rounded-xl text-sm">Today</button>
    <button onclick="setWeek()" class="btn-secondary px-4 py-2 rounded-xl text-sm">This Week</button>
    <button onclick="setMonth()" class="btn-secondary px-4 py-2 rounded-xl text-sm">This Month</button>
</div>

<!-- Sales Summary -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
    <div class="stat-card glass-card p-3 text-center">
        <div class="text-2xl mb-1">📦</div>
        <p class="text-2xl font-bold text-cyan-400" id="totalOrders">0</p>
        <p class="text-white/40 text-[10px] uppercase">Orders</p>
    </div>
    <div class="stat-card glass-card p-3 text-center" style="animation-delay:0.1s">
        <div class="text-2xl mb-1">🍶</div>
        <p class="text-2xl font-bold text-violet-400" id="bottlesSold">0</p>
        <p class="text-white/40 text-[10px] uppercase">Bottles Sold</p>
    </div>
    <div class="stat-card glass-card p-3 text-center" style="animation-delay:0.2s">
        <div class="text-2xl mb-1">💰</div>
        <p class="text-2xl font-bold text-emerald-400" id="grossSales">₱0</p>
        <p class="text-white/40 text-[10px] uppercase">Gross Sales</p>
    </div>
    <div class="stat-card glass-card p-3 text-center" style="animation-delay:0.3s">
        <div class="text-2xl mb-1">💳</div>
        <p class="text-2xl font-bold text-cyan-400" id="paymentsReceived">₱0</p>
        <p class="text-white/40 text-[10px] uppercase">Payments</p>
    </div>
    <div class="stat-card glass-card p-3 text-center" style="animation-delay:0.4s">
        <div class="text-2xl mb-1">⚠️</div>
        <p class="text-2xl font-bold text-rose-400" id="outstanding">₱0</p>
        <p class="text-white/40 text-[10px] uppercase">Outstanding</p>
    </div>
</div>

<!-- Delivery Summary -->
<div class="glass-card p-4 mb-4">
    <h2 class="text-base font-semibold text-white/90 mb-3">🚚 Delivery Performance</h2>
    <div class="grid grid-cols-3 lg:grid-cols-7 gap-2">
        <div class="text-center p-2 rounded-lg bg-white/[0.03]">
            <p class="text-xl font-bold text-white/90" id="dTotal">0</p>
            <p class="text-white/40 text-[9px]">Total</p>
        </div>
        <div class="text-center p-2 rounded-lg bg-emerald-500/5">
            <p class="text-xl font-bold text-emerald-400" id="dCompleted">0</p>
            <p class="text-white/40 text-[9px]">Completed</p>
        </div>
        <div class="text-center p-2 rounded-lg bg-amber-500/5">
            <p class="text-xl font-bold text-amber-400" id="dPending">0</p>
            <p class="text-white/40 text-[9px]">Pending</p>
        </div>
        <div class="text-center p-2 rounded-lg bg-violet-500/5">
            <p class="text-xl font-bold text-violet-400" id="dOut">0</p>
            <p class="text-white/40 text-[9px]">In Transit</p>
        </div>
        <div class="text-center p-2 rounded-lg bg-rose-500/5">
            <p class="text-xl font-bold text-rose-400" id="dFailed">0</p>
            <p class="text-white/40 text-[9px]">Failed</p>
        </div>
        <div class="text-center p-2 rounded-lg bg-white/[0.03]">
            <p class="text-xl font-bold text-white/90" id="dCancelled">0</p>
            <p class="text-white/40 text-[9px]">Cancelled</p>
        </div>
        <div class="text-center p-2 rounded-lg bg-cyan-500/5">
            <p class="text-xl font-bold text-cyan-400" id="dBottles">0</p>
            <p class="text-white/40 text-[9px]">Bottles Delivered</p>
        </div>
    </div>
    <!-- Completion Rate -->
    <div class="mt-3">
        <div class="flex justify-between text-xs text-white/50 mb-1">
            <span>Completion Rate</span>
            <span id="completionRate">0%</span>
        </div>
        <div class="w-full h-2 rounded-full bg-white/10">
            <div id="completionBar" class="h-2 rounded-full bg-gradient-to-r from-emerald-400 to-cyan-400 transition-all" style="width: 0%"></div>
        </div>
    </div>
</div>

<!-- Details Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Top Customers -->
    <div class="glass-card p-4">
        <h2 class="text-sm font-semibold text-white/90 mb-3">🏆 Top Customers</h2>
        <div id="topCustomers" class="space-y-2"><p class="text-white/30 text-xs text-center py-4">No data</p></div>
    </div>

    <!-- Payment Methods -->
    <div class="glass-card p-4">
        <h2 class="text-sm font-semibold text-white/90 mb-3">💳 Payments by Method</h2>
        <div id="paymentsByMethod" class="space-y-2"><p class="text-white/30 text-xs text-center py-4">No data</p></div>
    </div>

    <!-- Top Drivers -->
    <div class="glass-card p-4">
        <h2 class="text-sm font-semibold text-white/90 mb-3">👤 Top Drivers</h2>
        <div id="topDrivers" class="space-y-2"><p class="text-white/30 text-xs text-center py-4">No data</p></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const fmt = n => parseFloat(n||0).toLocaleString('en-US',{minimumFractionDigits:2});

function setToday() {
    const t = new Date().toISOString().split('T')[0];
    document.getElementById('dateFrom').value = t;
    document.getElementById('dateTo').value = t;
    loadReport();
}

function setWeek() {
    const now = new Date();
    const first = new Date(now.setDate(now.getDate() - now.getDay()));
    const last = new Date(first);
    last.setDate(first.getDate() + 6);
    document.getElementById('dateFrom').value = first.toISOString().split('T')[0];
    document.getElementById('dateTo').value = last.toISOString().split('T')[0];
    loadReport();
}

function setMonth() {
    const now = new Date();
    const first = new Date(now.getFullYear(), now.getMonth(), 1);
    const last = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    document.getElementById('dateFrom').value = first.toISOString().split('T')[0];
    document.getElementById('dateTo').value = last.toISOString().split('T')[0];
    loadReport();
}

async function loadReport() {
    const from = document.getElementById('dateFrom').value;
    const to = document.getElementById('dateTo').value;
    const res = await fetch(`/api/reports/daily-sales?date_from=${from}&date_to=${to}`, { credentials:'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    const s = data.summary || {};
    const d = data.deliveries || {};

    // Sales stats
    document.getElementById('totalOrders').textContent = s.total_orders || 0;
    document.getElementById('bottlesSold').textContent = s.total_bottles_sold || 0;
    document.getElementById('grossSales').textContent = '₱' + fmt(s.gross_sales);
    document.getElementById('paymentsReceived').textContent = '₱' + fmt(s.payments_received);
    document.getElementById('outstanding').textContent = '₱' + fmt(s.outstanding_balance);

    // Delivery stats
    document.getElementById('dTotal').textContent = d.total || 0;
    document.getElementById('dCompleted').textContent = d.completed || 0;
    document.getElementById('dPending').textContent = d.pending || 0;
    document.getElementById('dOut').textContent = d.out_for_delivery || 0;
    document.getElementById('dFailed').textContent = d.failed || 0;
    document.getElementById('dCancelled').textContent = d.cancelled || 0;
    document.getElementById('dBottles').textContent = d.bottles_delivered || 0;

    // Completion rate
    const rate = d.total > 0 ? Math.round((d.completed / d.total) * 100) : 0;
    document.getElementById('completionRate').textContent = rate + '%';
    document.getElementById('completionBar').style.width = rate + '%';

    // Top customers
    document.getElementById('topCustomers').innerHTML = (data.top_customers||[]).map(c =>
        `<div class="flex justify-between items-center py-2 px-3 rounded-lg bg-white/[0.03]">
            <div><span class="text-white/90 text-sm font-medium">${c.customer?.name||'-'}</span><span class="text-white/30 text-[10px] ml-2">${c.order_count} orders</span></div>
            <span class="font-bold text-emerald-400 text-sm">₱${fmt(c.total_spent)}</span>
        </div>`
    ).join('') || '<p class="text-white/30 text-xs text-center py-4">No data</p>';

    // Payments by method
    document.getElementById('paymentsByMethod').innerHTML = (data.payments_by_method||[]).map(m =>
        `<div class="flex justify-between items-center py-2 px-3 rounded-lg bg-white/[0.03]">
            <span class="text-white/70 text-sm capitalize">${m.payment_method.replace('_',' ')}</span>
            <span class="font-bold text-cyan-400 text-sm">₱${fmt(m.total)}</span>
        </div>`
    ).join('') || '<p class="text-white/30 text-xs text-center py-4">No data</p>';

    // Top drivers
    document.getElementById('topDrivers').innerHTML = (data.top_drivers||[]).map(dr =>
        `<div class="flex justify-between items-center py-2 px-3 rounded-lg bg-white/[0.03]">
            <div><span class="text-white/90 text-sm font-medium">${dr.driver?.name||'-'}</span><span class="text-white/30 text-[10px] ml-2">${dr.delivery_count} deliveries</span></div>
            <span class="font-bold text-violet-400 text-sm">${dr.total_bottles} bottles</span>
        </div>`
    ).join('') || '<p class="text-white/30 text-xs text-center py-4">No data</p>';
}

setToday();
</script>
@endsection
