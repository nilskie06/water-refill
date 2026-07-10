@extends('layouts.app')
@section('title', 'Reports - Water Refill Station')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">📈 Daily Sales Report</h1>
    <p class="text-white/40 text-sm mt-1">Track your business performance</p>
</div>

<div class="glass-card p-4 mb-6 flex flex-wrap gap-3 items-center">
    <input type="date" id="dateFrom" class="input-field px-4 py-2.5 text-sm">
    <span class="text-white/30">to</span>
    <input type="date" id="dateTo" class="input-field px-4 py-2.5 text-sm">
    <button onclick="loadReport()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">Filter</button>
    <button onclick="setToday()" class="btn-secondary px-5 py-2.5 rounded-xl text-sm">Today</button>
</div>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="stat-card glass-card p-4 text-center"><p class="text-white/40 text-xs uppercase tracking-wider">Orders</p><p class="text-2xl font-bold text-cyan-400 mt-1" id="totalOrders">0</p></div>
    <div class="stat-card glass-card p-4 text-center" style="animation-delay:0.1s"><p class="text-white/40 text-xs uppercase tracking-wider">Bottles</p><p class="text-2xl font-bold text-violet-400 mt-1" id="bottlesSold">0</p></div>
    <div class="stat-card glass-card p-4 text-center" style="animation-delay:0.2s"><p class="text-white/40 text-xs uppercase tracking-wider">Gross Sales</p><p class="text-2xl font-bold text-emerald-400 mt-1" id="grossSales">₱0</p></div>
    <div class="stat-card glass-card p-4 text-center" style="animation-delay:0.3s"><p class="text-white/40 text-xs uppercase tracking-wider">Payments</p><p class="text-2xl font-bold text-cyan-400 mt-1" id="paymentsReceived">₱0</p></div>
    <div class="stat-card glass-card p-4 text-center" style="animation-delay:0.4s"><p class="text-white/40 text-xs uppercase tracking-wider">Outstanding</p><p class="text-2xl font-bold text-rose-400 mt-1" id="outstanding">₱0</p></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="glass-card p-6"><h2 class="text-lg font-semibold text-white/90 mb-4">🏆 Top Customers</h2><div id="topCustomers" class="space-y-3"><p class="text-white/30 text-center py-6">No data</p></div></div>
    <div class="glass-card p-6"><h2 class="text-lg font-semibold text-white/90 mb-4">💳 Payment Methods</h2><div id="paymentsByMethod" class="space-y-3"><p class="text-white/30 text-center py-6">No data</p></div></div>
</div>
@endsection

@section('scripts')
<script>
const fmt = n => parseFloat(n||0).toLocaleString('en-US',{minimumFractionDigits:2});
function setToday() { const t = new Date().toISOString().split('T')[0]; document.getElementById('dateFrom').value = t; document.getElementById('dateTo').value = t; loadReport(); }
async function loadReport() {
    const from = document.getElementById('dateFrom').value, to = document.getElementById('dateTo').value;
    const res = await fetch(`/api/reports/daily-sales?date_from=${from}&date_to=${to}`, { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    document.getElementById('totalOrders').textContent = data.summary?.total_orders||0;
    document.getElementById('bottlesSold').textContent = data.summary?.total_bottles_sold||0;
    document.getElementById('grossSales').textContent = '₱'+fmt(data.summary?.gross_sales);
    document.getElementById('paymentsReceived').textContent = '₱'+fmt(data.summary?.payments_received);
    document.getElementById('outstanding').textContent = '₱'+fmt(data.summary?.outstanding_balance);
    document.getElementById('topCustomers').innerHTML = (data.top_customers||[]).map(c => `<div class="flex justify-between items-center py-3 px-4 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition"><div><span class="font-medium text-white/90">${c.customer?.name}</span><span class="text-white/30 text-xs ml-2">${c.order_count} orders</span></div><span class="font-bold text-emerald-400">₱${fmt(c.total_spent)}</span></div>`).join('') || '<p class="text-white/30 text-center py-6">No data</p>';
    document.getElementById('paymentsByMethod').innerHTML = (data.payments_by_method||[]).map(m => `<div class="flex justify-between items-center py-3 px-4 rounded-xl bg-white/[0.03] hover:bg-white/[0.06] transition"><span class="capitalize text-white/70">${m.payment_method.replace('_',' ')}</span><span class="font-bold text-cyan-400">₱${fmt(m.total)}</span></div>`).join('') || '<p class="text-white/30 text-center py-6">No data</p>';
}
setToday();
</script>
@endsection
