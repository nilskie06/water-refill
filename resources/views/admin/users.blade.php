@extends('layouts.app')
@section('title', 'Users - Admin Panel')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">👤 Users</h1>
        <p class="text-white/40 text-xs lg:text-sm mt-1">Manage system users and roles</p>
    </div>
    <button onclick="openModal()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ Add User</button>
</div>

<div class="glass-card p-3 mb-4">
    <div class="flex flex-wrap gap-2">
        <input type="text" id="searchInput" placeholder="🔍 Search name or email..." oninput="loadData()" class="input-field px-3 py-2 text-sm flex-1 min-w-[150px]">
        <select id="roleFilter" onchange="loadData()" class="input-field px-3 py-2 text-sm">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
        </select>
    </div>
</div>

<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead><tr class="border-b border-white/10">
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Name</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Email</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Role</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Permissions</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-white/50 uppercase">Actions</th>
        </tr></thead>
        <tbody id="tableBody"></tbody>
    </table>
</div>

<div class="mobile-cards space-y-3" id="cardBody"></div>
<div id="emptyState" class="hidden text-center py-12"><div class="text-4xl mb-3">👤</div><p class="text-white/40">No users found</p></div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeModal()">
    <div class="glass-card p-6 w-full sm:max-w-md sm:mx-4 rounded-t-2xl sm:rounded-2xl" onclick="event.stopPropagation()">
        <h2 class="text-xl font-bold text-white mb-6" id="modalTitle">Add User</h2>
        <form id="form" onsubmit="saveItem(event)">
            <input type="hidden" id="itemId">
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">Name *</label>
                <input type="text" id="fName" required class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">Email *</label>
                <input type="email" id="fEmail" required class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2" id="passwordLabel">Password *</label>
                <input type="password" id="fPassword" class="input-field w-full px-4 py-3 text-sm" placeholder="Leave blank to keep current">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">System Role *</label>
                <select id="fRole" required class="input-field w-full px-4 py-3 text-sm">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-white/60 text-sm mb-2">Access Role</label>
                <select id="fRoleId" class="input-field w-full px-4 py-3 text-sm">
                    <option value="">None</option>
                </select>
                <p class="text-white/30 text-[10px] mt-1">Assign a role to define permissions</p>
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
let allRoles = [];
const sc = { admin: 'badge-cancelled', staff: 'badge-pending' };

async function loadData() {
    const search = document.getElementById('searchInput').value;
    const role = document.getElementById('roleFilter').value;
    const [usersRes, rolesRes] = await Promise.all([
        fetch(`/api/users?search=${search}&role=${role}`, { credentials: 'same-origin' }),
        fetch('/api/roles', { credentials: 'same-origin' })
    ]);
    const users = await usersRes.json();
    const items = users.data || [];
    allRoles = await rolesRes.json();

    // Update role dropdown
    document.getElementById('fRoleId').innerHTML = '<option value="">None</option>' +
        allRoles.map(r => `<option value="${r.id}">${r.name}</option>`).join('');

    if (!items.length) {
        document.getElementById('emptyState').classList.remove('hidden');
        document.getElementById('tableBody').innerHTML = '';
        document.getElementById('cardBody').innerHTML = '';
        return;
    }
    document.getElementById('emptyState').classList.add('hidden');

    document.getElementById('tableBody').innerHTML = items.map(u => `
        <tr class="border-b border-white/5 hover:bg-white/[0.02]">
            <td class="px-4 py-3 text-sm font-medium text-white/90">${u.name}</td>
            <td class="px-4 py-3 text-sm text-white/60">${u.email}</td>
            <td class="px-4 py-3"><span class="badge ${sc[u.role]||''} text-[10px]">${u.role}</span></td>
            <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">${(u.role_entry?.permissions||[]).slice(0,3).map(p => `<span class="text-[9px] px-1.5 py-0.5 rounded bg-cyan-500/10 text-cyan-400">${p.name}</span>`).join('')}
                ${(u.role_entry?.permissions||[]).length > 3 ? `<span class="text-[9px] text-white/30">+${u.role_entry.permissions.length - 3}</span>` : ''}
                </div>
            </td>
            <td class="px-4 py-3 text-sm space-x-2">
                <button onclick='editItem(${JSON.stringify(u).replace(/'/g,"&#39;")})' class="text-amber-400">Edit</button>
                <button onclick="deleteItem(${u.id})" class="text-rose-400">Delete</button>
            </td>
        </tr>`).join('');

    document.getElementById('cardBody').innerHTML = items.map(u => `
        <div class="glass-card p-4">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-white font-semibold text-sm">${u.name}</h3>
                    <p class="text-white/50 text-xs mt-0.5">📧 ${u.email}</p>
                </div>
                <span class="badge ${sc[u.role]||''} text-[10px]">${u.role}</span>
            </div>
            <div class="flex flex-wrap gap-1 mb-3">${(u.role_entry?.permissions||[]).slice(0,4).map(p => `<span class="text-[9px] px-1.5 py-0.5 rounded bg-cyan-500/10 text-cyan-400">${p.name}</span>`).join('')}
                ${(u.role_entry?.permissions||[]).length > 4 ? `<span class="text-[9px] text-white/30">+${u.role_entry.permissions.length - 4}</span>` : ''}
            </div>
            <div class="flex gap-2 pt-2 border-t border-white/5">
                <button onclick='editItem(${JSON.stringify(u).replace(/'/g,"&#39;")})' class="flex-1 py-2 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-medium">Edit</button>
                <button onclick="deleteItem(${u.id})" class="flex-1 py-2 rounded-lg bg-rose-500/10 text-rose-400 text-xs font-medium">Delete</button>
            </div>
        </div>`).join('');
}

function openModal() {
    document.getElementById('itemId').value = '';
    document.getElementById('fName').value = '';
    document.getElementById('fEmail').value = '';
    document.getElementById('fPassword').value = '';
    document.getElementById('fRole').value = 'staff';
    document.getElementById('fRoleId').value = '';
    document.getElementById('modalTitle').textContent = 'Add User';
    document.getElementById('passwordLabel').textContent = 'Password *';
    document.getElementById('modal').classList.remove('hidden');
}

function editItem(u) {
    document.getElementById('itemId').value = u.id;
    document.getElementById('fName').value = u.name;
    document.getElementById('fEmail').value = u.email;
    document.getElementById('fPassword').value = '';
    document.getElementById('fRole').value = u.role;
    document.getElementById('fRoleId').value = u.role_id || '';
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('passwordLabel').textContent = 'Password (leave blank to keep)';
    document.getElementById('modal').classList.remove('hidden');
}

function closeModal() { document.getElementById('modal').classList.add('hidden'); }

async function saveItem(e) {
    e.preventDefault();
    const id = document.getElementById('itemId').value;
    const data = {
        name: document.getElementById('fName').value,
        email: document.getElementById('fEmail').value,
        role: document.getElementById('fRole').value,
        role_id: document.getElementById('fRoleId').value || null,
    };
    const pw = document.getElementById('fPassword').value;
    if (pw) data.password = pw;

    const url = id ? `/api/users/${id}` : '/api/users';
    const res = await fetch(url, {
        method: id ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    });
    if (res.ok) { closeModal(); loadData(); }
    else { const err = await res.json(); alert(err.message || 'Error'); }
}

async function deleteItem(id) {
    if (!confirm('Delete this user?')) return;
    const res = await fetch(`/api/users/${id}`, { method: 'DELETE', credentials: 'same-origin' });
    if (res.ok) loadData();
    else { const err = await res.json(); alert(err.message || 'Error'); }
}

loadData();
</script>
@endsection
