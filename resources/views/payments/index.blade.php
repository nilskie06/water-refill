@extends('layouts.app')
@section('title', 'Payments - Water Refill Station')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">💰 Payments</h1>
        <p class="text-white/40 text-sm mt-1">Payment history</p>
    </div>
    <a href="/payments/create" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ Record</a>
</div>

<!-- Desktop Table -->
<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">ID</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Order #</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Method</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Date</th>
            </tr>
        </thead>
        <tbody id="paymentTable"></tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="mobile-cards space-y-3" id="paymentCards"></div>

<div id="emptyState" class="hidden text-center py-12">
    <div class="text-4xl mb-3">💰</div>
    <p class="text-white/40">No payments found</p>
</div>
@endsection

@section('scripts')
<script>
async function loadPayments() {
    const res = await fetch('/api/payments', { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    const payments = data.data || [];

    if (payments.length === 0) {
        document.getElementById('emptyState').classList.remove('hidden');
        document.getElementById('paymentTable').innerHTML = '';
        document.getElementById('paymentCards').innerHTML = '';
        return;
    }
    document.getElementById('emptyState').classList.add('hidden');

    // Desktop
    document.getElementById('paymentTable').innerHTML = payments.map(p => `
        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
            <td class="px-6 py-4 text-sm text-white/60">#${p.id}</td>
            <td class="px-6 py-4 text-sm text-white/70">${p.order?.order_number || '-'}</td>
            <td class="px-6 py-4 text-sm text-white/70">${p.order?.customer?.name || '-'}</td>
            <td class="px-6 py-4 text-sm font-medium text-emerald-400">₱${parseFloat(p.amount).toFixed(2)}</td>
            <td class="px-6 py-4"><span class="badge badge-delivered">${p.payment_method}</span></td>
            <td class="px-6 py-4 text-sm text-white/60">${p.payment_date?.split('T')[0] || ''}</td>
        </tr>`).join('');

    // Mobile
    document.getElementById('paymentCards').innerHTML = payments.map(p => `
        <div class="glass-card p-4">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-white font-semibold text-sm">${p.order?.order_number || `Payment #${p.id}`}</h3>
                    <p class="text-white/50 text-xs mt-1">👤 ${p.order?.customer?.name || 'Walk-in'}</p>
                </div>
                <span class="badge badge-delivered">${p.payment_method}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40">Amount</span><p class="text-emerald-400 font-bold">₱${parseFloat(p.amount).toFixed(2)}</p></div>
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40">Date</span><p class="text-white/80 font-medium">${p.payment_date?.split('T')[0] || '-'}</p></div>
            </div>
        </div>`).join('');
}
loadPayments();
</script>
@endsection
