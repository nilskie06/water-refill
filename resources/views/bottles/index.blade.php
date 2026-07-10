@extends('layouts.app')
@section('title', 'Bottle Balances - Water Refill Station')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🍶 Bottle Balances</h1>
    <p class="text-white/40 text-sm mt-1">Track bottles issued and returned</p>
</div>

<div class="table-container">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Bottles Out</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Returned</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Balance</th>
            </tr>
        </thead>
        <tbody id="bottleList"></tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
async function loadBottles() {
    const res = await fetch('/api/customers?per_page=100', { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    let html = '';
    for (const c of data.data) {
        const r = await fetch(`/api/customers/${c.id}`, { credentials: 'same-origin' });
        if (r.ok) {
            const full = await r.json();
            if (full.bottle_balance) {
                const b = full.bottle_balance;
                const balColor = b.balance > 0 ? 'text-amber-400' : 'text-emerald-400';
                html += `<tr class="table-row"><td class="px-6 py-4 font-medium text-white/90">${c.name}</td><td class="px-6 py-4 text-white/60">${b.bottles_out}</td><td class="px-6 py-4 text-white/60">${b.bottles_returned}</td><td class="px-6 py-4 font-bold ${balColor}">${b.balance}</td></tr>`;
            }
        }
    }
    document.getElementById('bottleList').innerHTML = html || '<tr><td colspan="4" class="px-6 py-12 text-center text-white/30">No bottle records found</td></tr>';
}
loadBottles();
</script>
@endsection
