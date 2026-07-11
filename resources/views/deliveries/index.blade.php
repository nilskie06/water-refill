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

<div class="flex flex-wrap gap-3 mb-4">
    <select id="statusFilter" onchange="loadData()" class="input-field px-4 py-2.5 text-sm">
        <option value="">All Status</option>
        <option value="scheduled">📅 Scheduled</option>
        <option value="assigned">👤 Assigned</option>
        <option value="out_for_delivery">🚚 Out for Delivery</option>
        <option value="delivered">✅ Delivered</option>
        <option value="failed">❌ Failed</option>
        <option value="cancelled">🚫 Cancelled</option>
    </select>
    <input type="date" id="dateFilter" onchange="loadData()" class="input-field px-4 py-2.5 text-sm">
</div>

<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-white/10">
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Delivery #</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Customer</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Date</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Driver</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Status</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Actions</th>
        </tr></thead>
        <tbody id="tableBody"></tbody>
    </table>
</div>

<div class="mobile-cards space-y-3" id="cardBody"></div>
<div id="emptyState" class="hidden text-center py-12"><div class="text-4xl mb-3">🚚</div><p class="text-white/40">No deliveries found</p></div>

<!-- Assign Driver Modal -->
<div id="assignModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeAssignModal()">
    <div class="glass-card p-6 w-full sm:max-w-sm sm:mx-4 rounded-t-2xl sm:rounded-2xl" onclick="event.stopPropagation()">
        <h2 class="text-lg font-bold text-white mb-4">Assign Driver</h2>
        <input type="hidden" id="assignId">
        <div class="mb-4"><label class="block text-white/60 text-sm mb-2">Driver</label><select id="assignDriver" class="input-field w-full px-4 py-3 text-sm"></select></div>
        <div class="mb-4"><label class="block text-white/60 text-sm mb-2">Vehicle</label><select id="assignVehicle" class="input-field w-full px-4 py-3 text-sm"></select></div>
        <div class="mb-4"><label class="block text-white/60 text-sm mb-2">Route</label><select id="assignRoute" class="input-field w-full px-4 py-3 text-sm"><option value="morning">🌅 Morning</option><option value="afternoon">☀️ Afternoon</option><option value="evening">🌙 Evening</option></select></div>
        <div class="flex justify-end gap-3"><button onclick="closeAssignModal()" class="btn-secondary px-5 py-2 rounded-xl text-sm">Cancel</button><button onclick="saveAssign()" class="btn-primary px-5 py-2 rounded-xl text-white text-sm">Assign</button></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const sc = { scheduled: 'badge-pending', assigned: 'badge-delivered', out_for_delivery: 'badge-completed', delivered: 'badge-completed', failed: 'badge-cancelled', cancelled: 'badge-cancelled' };

function statusBtns(d) {
    let h = '';
    if (d.status === 'scheduled') h += `<button onclick="openAssignModal(${d.id})" class="flex-1 py-2 rounded-lg bg-cyan-500/10 text-cyan-400 text-xs font-medium">👤 Assign</button>`;
    if (d.status === 'assigned') h += `<button onclick="updateStatus(${d.id},'out_for_delivery')" class="flex-1 py-2 rounded-lg bg-violet-500/10 text-violet-400 text-xs font-medium">🚚 Dispatch</button>`;
    if (d.status === 'out_for_delivery') h += `<button onclick="updateStatus(${d.id},'delivered')" class="flex-1 py-2 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs font-medium">✅ Delivered</button>`;
    if (!['delivered','cancelled'].includes(d.status)) h += `<button onclick="updateStatus(${d.id},'cancelled')" class="flex-1 py-2 rounded-lg bg-rose-500/10 text-rose-400 text-xs font-medium">Cancel</button>`;
    return h;
}

async function loadData() {
    const status = document.getElementById('statusFilter').value;
    const date = document.getElementById('dateFilter').value;
    const res = await fetch(`/api/deliveries?status=${status}&date=${date}`, { credentials: 'same-origin' });
    const data = await res.json();
    const items = data.data || [];
    if (!items.length) { document.getElementById('emptyState').classList.remove('hidden'); document.getElementById('tableBody').innerHTML = ''; document.getElementById('cardBody').innerHTML = ''; return; }
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('tableBody').innerHTML = items.map(d => `<tr class="border-b border-white/5 hover:bg-white/[0.02]"><td class="px-4 py-3 text-sm font-medium text-white/90">${d.delivery_no}</td><td class="px-4 py-3 text-sm text-white/70">${d.customer?.name||'-'}</td><td class="px-4 py-3 text-sm text-white/60">${d.delivery_date}</td><td class="px-4 py-3 text-sm text-white/70">${d.driver?.name||'-'}</td><td class="px-4 py-3"><span class="badge ${sc[d.status]||''}">${d.status}</span></td><td class="px-4 py-3"><div class="flex gap-1">${statusBtns(d)}</div></td></tr>`).join('');
    document.getElementById('cardBody').innerHTML = items.map(d => `<div class="glass-card p-4"><div class="flex justify-between items-start mb-2"><div><h3 class="text-white font-semibold text-sm">${d.delivery_no}</h3><p class="text-white/50 text-xs mt-1">👤 ${d.customer?.name||'-'}</p></div><span class="badge ${sc[d.status]||''}">${d.status}</span></div><div class="grid grid-cols-2 gap-2 text-xs mb-3"><div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40">Date</span><p class="text-white/80 font-medium">${d.delivery_date}</p></div><div class="bg-white/[0.03] rounded-lg p-2"><span class="text-white/40">Driver</span><p class="text-white/80 font-medium">${d.driver?.name||'Unassigned'}</p></div></div><div class="flex gap-2 pt-2 border-t border-white/5">${statusBtns(d)}</div></div>`).join('');
}

async function openAssignModal(id) {
    document.getElementById('assignId').value = id;
    const [drivers, vehicles] = await Promise.all([
        fetch('/api/drivers', {credentials:'same-origin'}).then(r=>r.json()),
        fetch('/api/vehicles', {credentials:'same-origin'}).then(r=>r.json())
    ]);
    document.getElementById('assignDriver').innerHTML = (drivers.data||[]).map(d=>`<option value="${d.id}">${d.name}</option>`).join('');
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