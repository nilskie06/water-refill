@extends('layouts.app')
@section('title', 'Roles - Admin Panel')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🔐 Roles</h1>
        <p class="text-white/40 text-xs lg:text-sm mt-1">Manage user roles and access levels</p>
    </div>
    <button onclick="openModal()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ Add Role</button>
</div>

<div class="glass-card p-4 mb-4">
    <p class="text-white/50 text-xs">Roles define what users can access. Each role has specific permissions assigned to it.</p>
</div>

<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-white/10">
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Role</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Description</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Permissions</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Users</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Actions</th>
        </tr></thead>
        <tbody id="tableBody"></tbody>
    </table>
</div>

<div class="mobile-cards space-y-3" id="cardBody"></div>
<div id="emptyState" class="hidden text-center py-12"><div class="text-4xl mb-3">🔐</div><p class="text-white/40">No roles created yet</p></div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeModal()">
    <div class="glass-card p-6 w-full sm:max-w-lg sm:mx-4 rounded-t-2xl sm:rounded-2xl max-h-[85vh] overflow-y-auto" onclick="event.stopPropagation()">
        <h2 class="text-xl font-bold text-white mb-6" id="modalTitle">Add Role</h2>
        <form id="form" onsubmit="saveItem(event)">
            <input type="hidden" id="itemId">
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">Role Name *</label>
                <input type="text" id="fName" required class="input-field w-full px-4 py-3 text-sm" placeholder="e.g. Manager">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">Description</label>
                <input type="text" id="fDescription" class="input-field w-full px-4 py-3 text-sm" placeholder="What this role can do">
            </div>
            <div class="mb-6">
                <label class="block text-white/60 text-sm mb-2">Permissions</label>
                <div id="permissionsList" class="space-y-2 max-h-48 overflow-y-auto p-3 rounded-xl bg-white/[0.03]">
                    <p class="text-white/30 text-xs">Loading permissions...</p>
                </div>
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
let allPermissions = [];

async function loadData() {
    const [rolesRes, permsRes] = await Promise.all([
        fetch('/api/roles', { credentials: 'same-origin' }),
        fetch('/api/permissions', { credentials: 'same-origin' })
    ]);
    const roles = await rolesRes.json();
    allPermissions = await permsRes.json();

    if (!roles.length) {
        document.getElementById('emptyState').classList.remove('hidden');
        document.getElementById('tableBody').innerHTML = '';
        document.getElementById('cardBody').innerHTML = '';
        return;
    }
    document.getElementById('emptyState').classList.add('hidden');

    document.getElementById('tableBody').innerHTML = roles.map(r => `
        <tr class="border-b border-white/5 hover:bg-white/[0.02]">
            <td class="px-4 py-3 text-sm font-medium text-white/90">${r.name}</td>
            <td class="px-4 py-3 text-sm text-white/60">${r.description || '-'}</td>
            <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">${(r.permissions||[]).map(p => `<span class="text-[9px] px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400">${p.name}</span>`).join('')}</div>
            </td>
            <td class="px-4 py-3 text-sm text-white/60">${r.users_count || 0}</td>
            <td class="px-4 py-3 text-sm space-x-2">
                <button onclick='editItem(${JSON.stringify(r).replace(/'/g,"&#39;")})' class="text-amber-400">Edit</button>
                <button onclick="deleteItem(${r.id})" class="text-rose-400">Delete</button>
            </td>
        </tr>`).join('');

    document.getElementById('cardBody').innerHTML = roles.map(r => `
        <div class="glass-card p-4">
            <div class="flex justify-between items-start mb-2">
                <div><h3 class="text-white font-semibold text-sm">${r.name}</h3><p class="text-white/50 text-xs mt-0.5">${r.description || '-'}</p></div>
                <span class="text-white/40 text-xs">${r.users_count || 0} users</span>
            </div>
            <div class="flex flex-wrap gap-1 mb-3">${(r.permissions||[]).map(p => `<span class="text-[9px] px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400">${p.name}</span>`).join('')}</div>
            <div class="flex gap-2 pt-2 border-t border-white/5">
                <button onclick='editItem(${JSON.stringify(r).replace(/'/g,"&#39;")})' class="flex-1 py-2 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-medium">Edit</button>
                <button onclick="deleteItem(${r.id})" class="flex-1 py-2 rounded-lg bg-rose-500/10 text-rose-400 text-xs font-medium">Delete</button>
            </div>
        </div>`).join('');
}

function renderPermissionsList(selectedIds = []) {
    const groups = {};
    allPermissions.forEach(p => {
        if (!groups[p.group]) groups[p.group] = [];
        groups[p.group].push(p);
    });

    let html = '';
    Object.entries(groups).forEach(([group, perms]) => {
        html += `<div class="mb-2"><p class="text-white/40 text-[10px] uppercase tracking-wider mb-1">${group}</p>`;
        perms.forEach(p => {
            html += `<label class="flex items-center gap-2 py-1 cursor-pointer hover:bg-white/[0.04] rounded px-2">
                <input type="checkbox" name="perms[]" value="${p.id}" ${selectedIds.includes(p.id) ? 'checked' : ''} class="rounded border-white/20 bg-white/5">
                <span class="text-white/70 text-xs">${p.name}</span>
            </label>`;
        });
        html += '</div>';
    });
    document.getElementById('permissionsList').innerHTML = html || '<p class="text-white/30 text-xs">No permissions available</p>';
}

function openModal() {
    document.getElementById('itemId').value = '';
    document.getElementById('fName').value = '';
    document.getElementById('fDescription').value = '';
    document.getElementById('modalTitle').textContent = 'Add Role';
    renderPermissionsList();
    document.getElementById('modal').classList.remove('hidden');
}

function editItem(r) {
    document.getElementById('itemId').value = r.id;
    document.getElementById('fName').value = r.name;
    document.getElementById('fDescription').value = r.description || '';
    document.getElementById('modalTitle').textContent = 'Edit Role';
    renderPermissionsList((r.permissions||[]).map(p => p.id));
    document.getElementById('modal').classList.remove('hidden');
}

function closeModal() { document.getElementById('modal').classList.add('hidden'); }

async function saveItem(e) {
    e.preventDefault();
    const id = document.getElementById('itemId').value;
    const perms = [...document.querySelectorAll('input[name="perms[]"]:checked')].map(cb => parseInt(cb.value));
    const data = {
        name: document.getElementById('fName').value,
        description: document.getElementById('fDescription').value,
        permissions: perms
    };
    const url = id ? `/api/roles/${id}` : '/api/roles';
    await fetch(url, {
        method: id ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    });
    closeModal();
    loadData();
}

async function deleteItem(id) {
    if (!confirm('Delete this role?')) return;
    await fetch(`/api/roles/${id}`, { method: 'DELETE', credentials: 'same-origin' });
    loadData();
}

loadData();
</script>
@endsection
