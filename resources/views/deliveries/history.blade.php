@extends('layouts.app')
@section('title', 'Delivery History - Water Refill Station')

@section('content')
<div class="mb-6">
    <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">📋 Delivery History</h1>
    <p class="text-white/40 text-xs lg:text-sm mt-1">Completed deliveries</p>
</div>

<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-white/10">
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Date</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Delivery #</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Customer</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Driver</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Qty</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Remarks</th>
        </tr></thead>
        <tbody id="tableBody"></tbody>
    </table>
</div>

<div class="mobile-cards space-y-3" id="cardBody"></div>
<div id="emptyState" class="hidden text-center py-12"><div class="text-4xl mb-3">📋</div><p class="text-white/40">No completed deliveries</p></div>
@endsection

@section('scripts')
<script>
async function loadData() {
    const res = await fetch('/api/delivery/history', { credentials:'same-origin' });
    const data = await res.json();
    const items = data.data || [];
    if (!items.length) { document.getElementById('emptyState').classList.remove('hidden'); document.getElementById('tableBody').innerHTML = ''; document.getElementById('cardBody').innerHTML = ''; return; }
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('tableBody').innerHTML = items.map(d => `<tr class="border-b border-white/5 hover:bg-white/[0.02]"><td class="px-4 py-3 text-sm text-white/60">${d.delivery_date}</td><td class="px-4 py-3 text-sm font-medium text-white/90">${d.delivery_no}</td><td class="px-4 py-3 text-sm text-white/70">${d.customer?.name||'-'}</td><td class="px-4 py-3 text-sm text-white/70">${d.driver?.name||'-'}</td><td class="px-4 py-3 text-sm text-white/70">${d.quantity}</td><td class="px-4 py-3 text-sm text-white/50">${d.remarks||'-'}</td></tr>`).join('');
    document.getElementById('cardBody').innerHTML = items.map(d => `<div class="glass-card p-4"><div class="flex justify-between items-start mb-2"><div><h3 class="text-white font-semibold text-sm">${d.delivery_no}</h3><p class="text-white/50 text-xs mt-1">👤 ${d.customer?.name||'-'}</p></div><span class="badge badge-completed">delivered</span></div><div class="grid grid-cols-3 gap-2 text-xs"><div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40">Date</span><p class="text-white/80">${d.delivery_date}</p></div><div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40">Driver</span><p class="text-white/80">${d.driver?.name||'-'}</p></div><div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40">Qty</span><p class="text-white/80">${d.quantity}</p></div></div></div>`).join('');
}
loadData();
</script>
@endsection