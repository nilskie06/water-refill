@extends('layouts.app')
@section('title', 'Delivery History - Water Refill Station')

@section('content')
<div class="mb-6">
    <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">📋 Delivery History</h1>
    <p class="text-white/40 text-xs lg:text-sm mt-1">Completed deliveries</p>
</div>

<!-- Filters -->
<div class="glass-card p-3 mb-4">
    <div class="flex flex-wrap gap-2">
        <input type="text" id="searchInput" placeholder="🔍 Search customer, delivery #..." oninput="renderData()" class="input-field px-3 py-2 text-sm flex-1 min-w-[150px]">
        <input type="date" id="dateFrom" onchange="renderData()" class="input-field px-3 py-2 text-sm" placeholder="From">
        <input type="date" id="dateTo" onchange="renderData()" class="input-field px-3 py-2 text-sm" placeholder="To">
        <select id="driverFilter" onchange="renderData()" class="input-field px-3 py-2 text-sm">
            <option value="">All Drivers</option>
        </select>
    </div>
    <div class="flex gap-2 mt-2">
        <span class="text-white/40 text-xs py-1">Sort:</span>
        <button onclick="setSort('date')" id="sortDate" class="text-xs px-2 py-1 rounded-lg bg-cyan-500/10 text-cyan-400">Date ↓</button>
        <button onclick="setSort('customer')" id="sortCustomer" class="text-xs px-2 py-1 rounded-lg bg-white/5 text-white/50">Customer</button>
        <button onclick="setSort('driver')" id="sortDriver" class="text-xs px-2 py-1 rounded-lg bg-white/5 text-white/50">Driver</button>
        <button onclick="setSort('qty')" id="sortQty" class="text-xs px-2 py-1 rounded-lg bg-white/5 text-white/50">Qty</button>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-3 gap-3 mb-4">
    <div class="glass-card p-3 text-center">
        <p class="text-2xl font-bold text-emerald-400" id="statTotal">0</p>
        <p class="text-white/40 text-[10px] uppercase">Total Deliveries</p>
    </div>
    <div class="glass-card p-3 text-center">
        <p class="text-2xl font-bold text-cyan-400" id="statQty">0</p>
        <p class="text-white/40 text-[10px] uppercase">Total Bottles</p>
    </div>
    <div class="glass-card p-3 text-center">
        <p class="text-2xl font-bold text-amber-400" id="statDrivers">0</p>
        <p class="text-white/40 text-[10px] uppercase">Drivers</p>
    </div>
</div>

<!-- Desktop Table -->
<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-white/10">
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase cursor-pointer hover:text-white/70" onclick="setSort('date')">Date ↕</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase cursor-pointer hover:text-white/70" onclick="setSort('customer')">Customer ↕</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase cursor-pointer hover:text-white/70" onclick="setSort('driver')">Driver ↕</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase cursor-pointer hover:text-white/70" onclick="setSort('qty')">Qty ↕</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Route</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Type</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Remarks</th>
        </tr></thead>
        <tbody id="tableBody"></tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="mobile-cards space-y-3" id="cardBody"></div>
<div id="emptyState" class="hidden text-center py-12"><div class="text-4xl mb-3">📋</div><p class="text-white/40">No completed deliveries found</p></div>
@endsection

@section('scripts')
<script>
function fmtDate(d) { if (!d) return '-'; const dt = new Date(d.substring(0,10)+'T00:00:00'); return dt.toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric', year:'numeric' }); }
function dateOnly(d) { return d ? d.substring(0, 10) : ''; }
const typeLabels = { regular: '🚚 Regular', rush: '⚡ Rush', scheduled: '📅 Scheduled', pickup: '📦 Pickup' };
const routeLabels = { morning: '🌅 Morning', afternoon: '☀️ Afternoon', evening: '🌙 Evening' };

let allItems = [];
let sortField = 'date', sortDir = 'desc';

function setSort(field) {
    if (sortField === field) sortDir = sortDir === 'desc' ? 'asc' : 'desc';
    else { sortField = field; sortDir = 'desc'; }
    document.querySelectorAll('[id^="sort"]').forEach(b => { b.className = 'text-xs px-2 py-1 rounded-lg bg-white/5 text-white/50'; });
    document.getElementById('sort' + field.charAt(0).toUpperCase() + field.slice(1)).className = 'text-xs px-2 py-1 rounded-lg bg-cyan-500/10 text-cyan-400';
    renderData();
}

function getFiltered() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const driverFilter = document.getElementById('driverFilter').value;

    return allItems.filter(d => {
        const name = (d.customer?.name || '').toLowerCase();
        const dno = (d.delivery_no || '').toLowerCase();
        const driver = (d.driver?.name || '').toLowerCase();
        const dDate = dateOnly(d.delivery_date);

        if (search && !name.includes(search) && !dno.includes(search) && !driver.includes(search)) return false;
        if (dateFrom && dDate < dateFrom) return false;
        if (dateTo && dDate > dateTo) return false;
        if (driverFilter && (d.driver?.name || '') !== driverFilter) return false;
        return true;
    });
}

function renderData() {
    const filtered = getFiltered();
    const sorted = [...filtered].sort((a, b) => {
        let va, vb;
        if (sortField === 'date') { va = dateOnly(a.delivery_date); vb = dateOnly(b.delivery_date); }
        else if (sortField === 'customer') { va = (a.customer?.name||'').toLowerCase(); vb = (b.customer?.name||'').toLowerCase(); }
        else if (sortField === 'driver') { va = (a.driver?.name||'zzz').toLowerCase(); vb = (b.driver?.name||'zzz').toLowerCase(); }
        else if (sortField === 'qty') { va = a.quantity; vb = b.quantity; }
        else { va = a.id; vb = b.id; }
        if (va < vb) return sortDir === 'asc' ? -1 : 1;
        if (va > vb) return sortDir === 'asc' ? 1 : -1;
        return 0;
    });

    // Stats
    document.getElementById('statTotal').textContent = filtered.length;
    document.getElementById('statQty').textContent = filtered.reduce((s, d) => s + (d.quantity || 0), 0);
    document.getElementById('statDrivers').textContent = new Set(filtered.map(d => d.driver?.name).filter(Boolean)).size;

    if (!sorted.length) {
        document.getElementById('emptyState').classList.remove('hidden');
        document.getElementById('tableBody').innerHTML = '';
        document.getElementById('cardBody').innerHTML = '';
        return;
    }
    document.getElementById('emptyState').classList.add('hidden');

    document.getElementById('tableBody').innerHTML = sorted.map(d => `<tr class="border-b border-white/5 hover:bg-white/[0.02]">
        <td class="px-3 py-3 text-xs text-white/60">${fmtDate(d.delivery_date)}</td>
        <td class="px-3 py-3 text-xs font-medium text-white/90">${d.customer?.name||'-'}</td>
        <td class="px-3 py-3 text-xs text-white/70">${d.driver?.name||'-'}</td>
        <td class="px-3 py-3 text-xs text-white/70">${d.quantity}</td>
        <td class="px-3 py-3 text-xs text-white/60">${routeLabels[d.route]||'-'}</td>
        <td class="px-3 py-3 text-xs text-white/60">${typeLabels[d.delivery_type]||'-'}</td>
        <td class="px-3 py-3 text-xs text-white/50 truncate max-w-[150px]">${d.remarks||'-'}</td>
    </tr>`).join('');

    document.getElementById('cardBody').innerHTML = sorted.map(d => `
        <div class="glass-card p-4">
            <div class="flex justify-between items-start mb-2">
                <div><h3 class="text-white font-semibold text-sm">${d.customer?.name||'Walk-in'}</h3><p class="text-white/50 text-xs mt-0.5">📍 ${d.address||'-'}</p></div>
                <span class="badge badge-completed text-[10px]">✅ delivered</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40 block">Date</span><p class="text-white/80">${fmtDate(d.delivery_date)}</p></div>
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40 block">Driver</span><p class="text-white/80">${d.driver?.name||'-'}</p></div>
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40 block">Qty</span><p class="text-white/80">${d.quantity} bottles</p></div>
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40 block">Route</span><p class="text-white/80">${routeLabels[d.route]||'-'}</p></div>
            </div>
            ${d.remarks ? `<p class="text-white/40 text-xs bg-white/[0.03] rounded-lg p-2">💬 ${d.remarks}</p>` : ''}
        </div>`).join('');
}

async function loadData() {
    const res = await fetch('/api/delivery/history', { credentials:'same-origin' });
    const data = await res.json();
    allItems = data.data || [];

    // Populate driver filter
    const drivers = [...new Set(allItems.map(d => d.driver?.name).filter(Boolean))].sort();
    const sel = document.getElementById('driverFilter');
    const current = sel.value;
    sel.innerHTML = '<option value="">All Drivers</option>' + drivers.map(d => `<option value="${d}" ${d===current?'selected':''}>${d}</option>`).join('');

    renderData();
}
loadData();
</script>
@endsection
