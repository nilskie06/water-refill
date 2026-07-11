@extends('layouts.app')
@section('title', 'Drivers - Water Refill Station')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🧑‍✈️ Drivers</h1>
        <p class="text-white/40 text-sm mt-1">Manage your delivery drivers</p>
    </div>
    <button onclick="openModal()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ Add</button>
</div>

<div class="mb-4">
    <input type="text" id="searchInput" placeholder="🔍 Search drivers..." oninput="loadDrivers()" class="input-field w-full px-4 py-3 text-sm">
</div>

<!-- Desktop Table -->
<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Name</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Contact</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">License</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody id="driverTable"></tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="mobile-cards space-y-3" id="driverCards"></div>

<!-- Empty State -->
<div id="emptyState" class="hidden text-center py-12">
    <div class="text-4xl mb-3">🧑‍✈️</div>
    <p class="text-white/40">No drivers found</p>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeModal()">
    <div class="glass-card p-6 w-full sm:max-w-md sm:mx-4 rounded-t-2xl sm:rounded-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <h2 class="text-xl font-bold text-white mb-6" id="modalTitle">Add Driver</h2>
        <form id="driverForm" onsubmit="saveDriver(event)">
            <input type="hidden" id="driverId">
            <div class="mb-4">
                <label class="block text-white/60 text-sm font-medium mb-2">Name *</label>
                <input type="text" id="driverName" required class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm font-medium mb-2">Contact Number</label>
                <input type="text" id="driverContact" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm font-medium mb-2">License Number</label>
                <input type="text" id="driverLicense" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div class="mb-6">
                <label class="block text-white/60 text-sm font-medium mb-2">Status</label>
                <select id="driverStatus" class="input-field w-full px-4 py-3 text-sm">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="on_leave">On Leave</option>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="btn-secondary px-5 py-2.5 rounded-xl text-sm">Cancel</button>
                <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const statusClasses = { active: 'badge-completed', inactive: 'badge-cancelled', on_leave: 'badge-pending' };
const statusLabels = { active: 'Active', inactive: 'Inactive', on_leave: 'On Leave' };

async function loadDrivers() {
    const search = document.getElementById('searchInput').value;
    const res = await fetch(`/api/drivers?search=${search}`, { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    const drivers = data.data || [];

    if (drivers.length === 0) {
        document.getElementById('emptyState').classList.remove('hidden');
        document.getElementById('driverTable').innerHTML = '';
        document.getElementById('driverCards').innerHTML = '';
        return;
    }
    document.getElementById('emptyState').classList.add('hidden');

    // Desktop table
    document.getElementById('driverTable').innerHTML = drivers.map(d => `
        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
            <td class="px-6 py-4 text-sm font-medium text-white/90">${d.name}</td>
            <td class="px-6 py-4 text-sm text-white/70">${d.contact_number || '-'}</td>
            <td class="px-6 py-4 text-sm text-white/70">${d.license_number || '-'}</td>
            <td class="px-6 py-4"><span class="badge ${statusClasses[d.status] || ''}">${statusLabels[d.status] || d.status}</span></td>
            <td class="px-6 py-4 text-sm space-x-3">
                <button onclick='editDriver(${JSON.stringify(d).replace(/'/g,"&#39;")})' class="text-amber-400 hover:text-amber-300">Edit</button>
                <button onclick="deleteDriver(${d.id})" class="text-rose-400 hover:text-rose-300">Delete</button>
            </td>
        </tr>`).join('');

    // Mobile cards
    document.getElementById('driverCards').innerHTML = drivers.map(d => `
        <div class="glass-card p-4">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-white font-semibold text-sm">${d.name}</h3>
                    <p class="text-white/50 text-xs mt-1">📞 ${d.contact_number || 'No contact'}</p>
                </div>
                <span class="badge ${statusClasses[d.status] || ''}">${statusLabels[d.status] || d.status}</span>
            </div>
            <p class="text-white/40 text-xs mb-3">🪪 ${d.license_number || 'No license'}</p>
            <div class="flex gap-2 pt-2 border-t border-white/5">
                <button onclick='editDriver(${JSON.stringify(d).replace(/'/g,"&#39;")})' class="flex-1 py-2 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-medium hover:bg-amber-500/20 transition">Edit</button>
                <button onclick="deleteDriver(${d.id})" class="flex-1 py-2 rounded-lg bg-rose-500/10 text-rose-400 text-xs font-medium hover:bg-rose-500/20 transition">Delete</button>
            </div>
        </div>`).join('');
}
function openModal() {
    document.getElementById('driverId').value = '';
    document.getElementById('driverName').value = '';
    document.getElementById('driverContact').value = '';
    document.getElementById('driverLicense').value = '';
    document.getElementById('driverStatus').value = 'active';
    document.getElementById('modalTitle').textContent = 'Add Driver';
    document.getElementById('modal').classList.remove('hidden');
}
function editDriver(d) {
    document.getElementById('driverId').value = d.id;
    document.getElementById('driverName').value = d.name;
    document.getElementById('driverContact').value = d.contact_number || '';
    document.getElementById('driverLicense').value = d.license_number || '';
    document.getElementById('driverStatus').value = d.status || 'active';
    document.getElementById('modalTitle').textContent = 'Edit Driver';
    document.getElementById('modal').classList.remove('hidden');
}
function closeModal() { document.getElementById('modal').classList.add('hidden'); }
async function saveDriver(e) {
    e.preventDefault();
    const id = document.getElementById('driverId').value;
    const data = {
        name: document.getElementById('driverName').value,
        contact_number: document.getElementById('driverContact').value,
        license_number: document.getElementById('driverLicense').value,
        status: document.getElementById('driverStatus').value
    };
    const url = id ? `/api/drivers/${id}` : '/api/drivers';
    await fetch(url, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify(data) });
    closeModal(); loadDrivers();
}
async function deleteDriver(id) {
    if (!confirm('Delete this driver?')) return;
    await fetch(`/api/drivers/${id}`, { method: 'DELETE', credentials: 'same-origin' });
    loadDrivers();
}
loadDrivers();
</script>
@endsection
