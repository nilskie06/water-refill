@extends('layouts.app')
@section('title', 'Deliveries - Water Refill Station')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🚚 Deliveries</h1>
        <p class="text-white/40 text-xs lg:text-sm mt-1">Manage delivery schedules</p>
    </div>
    <a href="/deliveries/create" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ New</a>
</div>

<!-- Filters -->
<div class="glass-card p-3 mb-4">
    <div class="flex flex-wrap gap-2">
        <input type="text" id="searchInput" placeholder="🔍 Search..." oninput="loadData()" class="input-field px-3 py-2 text-sm flex-1 min-w-[150px]">
        <select id="statusFilter" onchange="loadData()" class="input-field px-3 py-2 text-sm">
            <option value="">All Status</option>
            <option value="scheduled">📅 Scheduled</option>
            <option value="assigned">👤 Assigned</option>
            <option value="out_for_delivery">🚚 Out</option>
            <option value="delivered">✅ Delivered</option>
            <option value="failed">❌ Failed</option>
            <option value="cancelled">🚫 Cancelled</option>
        </select>
        <select id="typeFilter" onchange="loadData()" class="input-field px-3 py-2 text-sm">
            <option value="">All Types</option>
            <option value="regular">🚚 Regular</option>
            <option value="rush">⚡ Rush</option>
            <option value="scheduled">📅 Scheduled</option>
            <option value="pickup">📦 Pickup</option>
        </select>
        <select id="routeFilter" onchange="loadData()" class="input-field px-3 py-2 text-sm">
            <option value="">All Routes</option>
            <option value="morning">🌅 Morning</option>
            <option value="afternoon">☀️ Afternoon</option>
            <option value="evening">🌙 Evening</option>
        </select>
        <input type="date" id="dateFilter" onchange="loadData()" class="input-field px-3 py-2 text-sm">
    </div>
    <!-- Sort -->
    <div class="flex gap-2 mt-2">
        <span class="text-white/40 text-xs py-1">Sort:</span>
        <button onclick="setSort('date')" id="sortDate" class="text-xs px-2 py-1 rounded-lg bg-cyan-500/10 text-cyan-400">Date ↓</button>
        <button onclick="setSort('status')" id="sortStatus" class="text-xs px-2 py-1 rounded-lg bg-white/5 text-white/50">Status</button>
        <button onclick="setSort('type')" id="sortType" class="text-xs px-2 py-1 rounded-lg bg-white/5 text-white/50">Type</button>
        <button onclick="setSort('route')" id="sortRoute" class="text-xs px-2 py-1 rounded-lg bg-white/5 text-white/50">Route</button>
    </div>
</div>

<!-- Desktop Table -->
<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-white/10">
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase cursor-pointer hover:text-white/70" onclick="setSort('date')">Delivery # ↕</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Customer</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Date ↕</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Time</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Type ↕</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Route ↕</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Driver</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Status ↕</th>
            <th class="px-3 py-3 text-left text-xs font-semibold text-white/50 uppercase">Actions</th>
        </tr></thead>
        <tbody id="tableBody"></tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="mobile-cards space-y-3" id="cardBody"></div>
<div id="emptyState" class="hidden text-center py-12"><div class="text-4xl mb-3">🚚</div><p class="text-white/40">No deliveries found</p></div>

<!-- Assign Driver Modal -->
<div id="assignModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeAssignModal()">
    <div class="glass-card p-6 w-full sm:max-w-sm sm:mx-4 rounded-t-2xl sm:rounded-2xl" onclick="event.stopPropagation()">
        <h2 class="text-lg font-bold text-white mb-4">Assign Driver & Vehicle</h2>
        <input type="hidden" id="assignId">
        <div class="mb-3"><label class="block text-white/60 text-sm mb-1">Driver</label><select id="assignDriver" class="input-field w-full px-4 py-2.5 text-sm"></select></div>
        <div class="mb-3"><label class="block text-white/60 text-sm mb-1">Vehicle</label><select id="assignVehicle" class="input-field w-full px-4 py-2.5 text-sm"></select></div>
        <div class="mb-4"><label class="block text-white/60 text-sm mb-1">Route</label><select id="assignRoute" class="input-field w-full px-4 py-2.5 text-sm"><option value="morning">🌅 Morning</option><option value="afternoon">☀️ Afternoon</option><option value="evening">🌙 Evening</option></select></div>
        <div class="flex justify-end gap-3"><button onclick="closeAssignModal()" class="btn-secondary px-5 py-2 rounded-xl text-sm">Cancel</button><button onclick="saveAssign()" class="btn-primary px-5 py-2 rounded-xl text-white text-sm">Assign</button></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function fmtDate(d) { if (!d) return '-'; const dt = new Date(d.substring(0,10)+'T00:00:00'); return dt.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }); }
function fmtTime(t) { if (!t) return '-'; const [h,m] = t.split(':'); const hr = parseInt(h); return `${hr>12?hr-12:hr||12}:${m} ${hr>=12?'PM':'AM'}`; }
function dateOnly(d) { return d ? d.substring(0, 10) : ''; }

const sc = { scheduled: 'badge-pending', assigned: 'badge-delivered', out_for_delivery: 'badge-completed', delivered: 'badge-completed', failed: 'badge-cancelled', cancelled: 'badge-cancelled' };
const typeLabels = { regular: '🚚 Regular', rush: '⚡ Rush', scheduled: '📅 Scheduled', pickup: '📦 Pickup' };
let sortField = 'date', sortDir = 'desc';
let allItems = [];

function setSort(field) {
    if (sortField === field) sortDir = sortDir === 'desc' ? 'asc' : 'desc';
    else { sortField = field; sortDir = 'desc'; }
    document.querySelectorAll('[id^="sort"]').forEach(b => { b.className = 'text-xs px-2 py-1 rounded-lg bg-white/5 text-white/50'; });
    document.getElementById('sort' + field.charAt(0).toUpperCase() + field.slice(1)).className = 'text-xs px-2 py-1 rounded-lg bg-cyan-500/10 text-cyan-400';
    renderData();
}

function sortItems(items) {
    return [...items].sort((a, b) => {
        let va, vb;
        if (sortField === 'date') { va = dateOnly(a.delivery_date); vb = dateOnly(b.delivery_date); }
        else if (sortField === 'status') { va = a.status; vb = b.status; }
        else if (sortField === 'type') { va = a.delivery_type; vb = b.delivery_type; }
        else if (sortField === 'route') { va = a.route || 'zzz'; vb = b.route || 'zzz'; }
        else { va = a.delivery_no; vb = b.delivery_no; }
        if (va < vb) return sortDir === 'asc' ? -1 : 1;
        if (va > vb) return sortDir === 'asc' ? 1 : -1;
        return 0;
    });
}

function statusBtns(d) {
    let h = '';
    if (d.status === 'scheduled') h += `<button onclick="openAssignModal(${d.id})" class="px-2 py-1 rounded-lg bg-cyan-500/10 text-cyan-400 text-[10px] font-medium">👤 Assign</button>`;
    if (d.status === 'assigned') h += `<button onclick="updateStatus(${d.id},'out_for_delivery')" class="px-2 py-1 rounded-lg bg-violet-500/10 text-violet-400 text-[10px] font-medium">🚚 Dispatch</button>`;
    if (d.status === 'out_for_delivery') h += `<button onclick="updateStatus(${d.id},'delivered')" class="px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 text-[10px] font-medium">✅ Done</button>`;
    if (!['delivered','cancelled'].includes(d.status)) h += `<button onclick="updateStatus(${d.id},'cancelled')" class="px-2 py-1 rounded-lg bg-rose-500/10 text-rose-400 text-[10px] font-medium">✕</button>`;
    return h;
}

function renderData() {
    const items = sortItems(allItems);
    if (!items.length) { document.getElementById('emptyState').classList.remove('hidden'); document.getElementById('tableBody').innerHTML = ''; document.getElementById('cardBody').innerHTML = ''; return; }
    document.getElementById('emptyState').classList.add('hidden');

    document.getElementById('tableBody').innerHTML = items.map(d => `<tr class="border-b border-white/5 hover:bg-white/[0.02]">
        <td class="px-3 py-3 text-xs font-medium text-white/90">${d.delivery_no}</td>
        <td class="px-3 py-3 text-xs text-white/70">${d.customer?.name||'-'}</td>
        <td class="px-3 py-3 text-xs text-white/60">${fmtDate(d.delivery_date)}</td>
        <td class="px-3 py-3 text-xs text-white/60">${fmtTime(d.delivery_time)}</td>
        <td class="px-3 py-3 text-xs text-white/60">${typeLabels[d.delivery_type]||d.delivery_type}</td>
        <td class="px-3 py-3 text-xs text-white/60">${d.route ? (d.route==='morning'?'🌅':'')+(d.route==='afternoon'?'☀️':'')+(d.route==='evening'?'🌙':'')+' '+d.route : '-'}</td>
        <td class="px-3 py-3 text-xs text-white/70">${d.driver?.name||'-'}</td>
        <td class="px-3 py-3"><span class="badge ${sc[d.status]||''} text-[10px]">${d.status.replace('_',' ')}</span></td>
        <td class="px-3 py-3"><div class="flex gap-1">${statusBtns(d)}</div></td>
    </tr>`).join('');

    document.getElementById('cardBody').innerHTML = items.map(d => `
        <div class="glass-card p-4">
            <div class="flex justify-between items-start mb-2">
                <div><h3 class="text-white font-semibold text-sm">${d.delivery_no}</h3><p class="text-white/50 text-xs mt-0.5">👤 ${d.customer?.name||'-'}</p></div>
                <span class="badge ${sc[d.status]||''} text-[10px]">${d.status.replace('_',' ')}</span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-xs mb-3">
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40 block">Date</span><p class="text-white/80">${fmtDate(d.delivery_date)}</p></div>
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40 block">Time</span><p class="text-white/80">${fmtTime(d.delivery_time)}</p></div>
                <div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40 block">Qty</span><p class="text-white/80">${d.quantity}</p></div>
            </div>
            <div class="flex items-center gap-2 text-[10px] text-white/40 mb-2">
                <span>${typeLabels[d.delivery_type]||d.delivery_type}</span>
                ${d.route ? `<span>• ${d.route}</span>` : ''}
                ${d.driver ? `<span>• 👤 ${d.driver.name}</span>` : ''}
            </div>
            <div class="flex gap-1 pt-2 border-t border-white/5">${statusBtns(d)}</div>
        </div>`).join('');
}

async function loadData() {
    const status = document.getElementById('statusFilter').value;
    const date = document.getElementById('dateFilter').value;
    const search = document.getElementById('searchInput').value;
    const type = document.getElementById('typeFilter').value;
    const route = document.getElementById('routeFilter').value;
    const res = await fetch(`/api/deliveries?status=${status}&date=${date}&search=${search}`, { credentials:'same-origin' });
    const data = await res.json();
    allItems = (data.data || []).filter(d => {
        if (type && d.delivery_type !== type) return false;
        if (route && d.route !== route) return false;
        return true;
    });
    renderData();
}

async function openAssignModal(id) {
    document.getElementById('assignId').value = id;
    const [drivers, vehicles] = await Promise.all([
        fetch('/api/drivers', {credentials:'same-origin'}).then(r=>r.json()),
        fetch('/api/vehicles', {credentials:'same-origin'}).then(r=>r.json())
    ]);
    document.getElementById('assignDriver').innerHTML = (drivers.data||[]).filter(d=>d.status==='active').map(d=>`<option value="${d.id}">${d.name}</option>`).join('');
    document.getElementById('assignVehicle').innerHTML = (vehicles.data||[]).filter(v=>v.status==='available').map(v=>`<option value="${v.id}">${v.plate_number} - ${v.description||''}</option>`).join('');
    document.getElementById('assignModal').classList.remove('hidden');
}
function closeAssignModal() { document.getElementById('assignModal').classList.add('hidden'); }
async function saveAssign() {
    const id = document.getElementById('assignId').value;
    await fetch(`/api/deliveries/${id}`, { method:'PUT', headers:{'Content-Type':'application/json'}, credentials:'same-origin', body:JSON.stringify({ driver_id:document.getElementById('assignDriver').value, vehicle_id:document.getElementById('assignVehicle').value, route:document.getElementById('assignRoute').value }) });
    closeAssignModal(); loadData();
}
async function updateStatus(id, status) {
    await fetch(`/api/deliveries/${id}`, { method:'PUT', headers:{'Content-Type':'application/json'}, credentials:'same-origin', body:JSON.stringify({ status }) });
    loadData();
}
loadData();
</script>
@endsection
