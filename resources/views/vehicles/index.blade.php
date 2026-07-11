@extends('layouts.app')
@section('title', 'Vehicles - Water Refill Station')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🚐 Vehicles</h1>
        <p class="text-white/40 text-xs lg:text-sm mt-1">Manage delivery vehicles</p>
    </div>
    <button onclick="openModal()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ Add</button>
</div>

<div class="mb-4">
    <input type="text" id="searchInput" placeholder="🔍 Search plate number..." oninput="loadData()" class="input-field w-full px-4 py-3 text-sm">
</div>

<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-white/10">
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase">Plate #</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase">Description</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase">Capacity</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase">Status</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase">Actions</th>
        </tr></thead>
        <tbody id="tableBody"></tbody>
    </table>
</div>

<div class="mobile-cards space-y-3" id="cardBody"></div>
<div id="emptyState" class="hidden text-center py-12"><div class="text-4xl mb-3">🚐</div><p class="text-white/40">No vehicles found</p></div>

<div id="modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeModal()">
    <div class="glass-card p-6 w-full sm:max-w-md sm:mx-4 rounded-t-2xl sm:rounded-2xl" onclick="event.stopPropagation()">
        <h2 class="text-xl font-bold text-white mb-6" id="modalTitle">Add Vehicle</h2>
        <form id="form" onsubmit="saveItem(event)">
            <input type="hidden" id="itemId">
            <div class="mb-4"><label class="block text-white/60 text-sm mb-2">Plate Number *</label><input type="text" id="fPlate" required class="input-field w-full px-4 py-3 text-sm" placeholder="ABC 1234"></div>
            <div class="mb-4"><label class="block text-white/60 text-sm mb-2">Description</label><input type="text" id="fDesc" class="input-field w-full px-4 py-3 text-sm" placeholder="Honda TMX 155"></div>
            <div class="mb-4"><label class="block text-white/60 text-sm mb-2">Capacity (bottles)</label><input type="number" id="fCapacity" class="input-field w-full px-4 py-3 text-sm" min="0"></div>
            <div class="mb-6"><label class="block text-white/60 text-sm mb-2">Status</label>
                <select id="fStatus" class="input-field w-full px-4 py-3 text-sm"><option value="available">Available</option><option value="in_use">In Use</option><option value="maintenance">Maintenance</option></select></div>
            <div class="flex justify-end gap-3"><button type="button" onclick="closeModal()" class="btn-secondary px-5 py-2.5 rounded-xl text-sm">Cancel</button><button type="submit" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">Save</button></div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const sc = { available: 'badge-completed', in_use: 'badge-delivered', maintenance: 'badge-pending' };
async function loadData() {
    const s = document.getElementById('searchInput').value;
    const res = await fetch(`/api/vehicles?search=${s}`, { credentials: 'same-origin' });
    const data = await res.json();
    const items = data.data || [];
    if (!items.length) { document.getElementById('emptyState').classList.remove('hidden'); document.getElementById('tableBody').innerHTML = ''; document.getElementById('cardBody').innerHTML = ''; return; }
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('tableBody').innerHTML = items.map(i => `<tr class="border-b border-white/5 hover:bg-white/[0.02]"><td class="px-6 py-4 text-sm font-medium text-white/90">${i.plate_number}</td><td class="px-6 py-4 text-sm text-white/70">${i.description||'-'}</td><td class="px-6 py-4 text-sm text-white/70">${i.capacity||0}</td><td class="px-6 py-4"><span class="badge ${sc[i.status]||''}">${i.status}</span></td><td class="px-6 py-4 text-sm space-x-2"><button onclick='editItem(${JSON.stringify(i).replace(/'/g,"&#39;")})' class="text-amber-400">Edit</button><button onclick="deleteItem(${i.id})" class="text-rose-400">Delete</button></td></tr>`).join('');
    document.getElementById('cardBody').innerHTML = items.map(i => `<div class="glass-card p-4"><div class="flex justify-between items-start mb-2"><div><h3 class="text-white font-semibold text-sm">${i.plate_number}</h3><p class="text-white/50 text-xs mt-1">🚐 ${i.description||'No description'}</p></div><span class="badge ${sc[i.status]||''}">${i.status}</span></div><p class="text-white/40 text-xs mb-3">📦 Capacity: ${i.capacity||0} bottles</p><div class="flex gap-2 pt-2 border-t border-white/5"><button onclick='editItem(${JSON.stringify(i).replace(/'/g,"&#39;")})' class="flex-1 py-2 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-medium">Edit</button><button onclick="deleteItem(${i.id})" class="flex-1 py-2 rounded-lg bg-rose-500/10 text-rose-400 text-xs font-medium">Delete</button></div></div>`).join('');
}
function openModal() { document.getElementById('itemId').value=''; document.getElementById('fPlate').value=''; document.getElementById('fDesc').value=''; document.getElementById('fCapacity').value=''; document.getElementById('fStatus').value='available'; document.getElementById('modalTitle').textContent='Add Vehicle'; document.getElementById('modal').classList.remove('hidden'); }
function editItem(i) { document.getElementById('itemId').value=i.id; document.getElementById('fPlate').value=i.plate_number; document.getElementById('fDesc').value=i.description||''; document.getElementById('fCapacity').value=i.capacity||''; document.getElementById('fStatus').value=i.status; document.getElementById('modalTitle').textContent='Edit Vehicle'; document.getElementById('modal').classList.remove('hidden'); }
function closeModal() { document.getElementById('modal').classList.add('hidden'); }
async function saveItem(e) { e.preventDefault(); const id=document.getElementById('itemId').value; const d={plate_number:document.getElementById('fPlate').value,description:document.getElementById('fDesc').value,capacity:document.getElementById('fCapacity').value||0,status:document.getElementById('fStatus').value}; const url=id?`/api/vehicles/${id}`:'/api/vehicles'; await fetch(url,{method:id?'PUT':'POST',headers:{'Content-Type':'application/json'},credentials:'same-origin',body:JSON.stringify(d)}); closeModal(); loadData(); }
async function deleteItem(id) { if(!confirm('Delete?'))return; await fetch(`/api/vehicles/${id}`,{method:'DELETE',credentials:'same-origin'}); loadData(); }
loadData();
</script>
@endsection
