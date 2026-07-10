@extends('layouts.app')
@section('title', 'Payments - Water Refill Station')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">💰 Payments</h1>
        <p class="text-white/40 text-sm mt-1">Payment history</p>
    </div>
    <a href="/payments/create" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ Record Payment</a>
</div>

<div class="table-container">
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
        <tbody id="paymentList"></tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
async function loadPayments() {
    const res = await fetch('/api/payments', { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    document.getElementById('paymentList').innerHTML = data.data.map(p => `
        <tr class="table-row">
            <td class="px-6 py-4 text-sm text-white/60">#${p.id}</td>
            <td class="px-6 py-4 text-sm text-white/70">${p.order?.order_number || '-'}</td>
            <td class="px-6 py-4 text-sm text-white/70">${p.order?.customer?.name || '-'}</td>
            <td class="px-6 py-4 text-sm font-medium text-emerald-400">₱${parseFloat(p.amount).toFixed(2)}</td>
            <td class="px-6 py-4"><span class="badge badge-delivered">${p.payment_method}</span></td>
            <td class="px-6 py-4 text-sm text-white/60">${p.payment_date?.split('T')[0] || ''}</td>
        </tr>`).join('');
}
loadPayments();
</script>
@endsection
