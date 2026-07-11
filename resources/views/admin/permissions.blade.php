@extends('layouts.app')
@section('title', 'Permissions - Admin Panel')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🔑 Permissions</h1>
        <p class="text-white/40 text-xs lg:text-sm mt-1">Manage access permissions</p>
    </div>
    <button onclick="openModal()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ Add Permission</button>
</div>

<!-- Quick Add -->
<div class="glass-card p-4 mb-4">
    <p class="text-white/50 text-xs mb-2">Permissions control what actions users can perform. Assign them to roles in the Roles page.</p>
    <div class="flex flex-wrap gap-2 mt-2">
        @foreach(['view','create','edit','delete','export'] as $action)
        @foreach(['customers','orders','deliveries','payments','reports','drivers','vehicles'] as $module)
        @php $perm = $action.'_'.$module; @endphp
        @endforeach
        @endforeach
    </div>
</div>

<!-- Permissions by Group -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4" id="permGroups"></div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeModal()">
    <div class="glass-card p-6 w-full sm:max-w-md sm:mx-4 rounded-t-2xl sm:rounded-2xl" onclick="event.stopPropagation()">
        <h2 class="text-xl font-bold text-white mb-6" id="modalTitle">Add Permission</h2>
        <form id="form" onsubmit="saveItem(event)">
            <input type="hidden" id="itemId">
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">Permission Name *</label>
                <input type="text" id="fName" required class="input-field w-full px-4 py-3 text-sm" placeholder="e.g. view_orders">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">Group</label>
                <input type="text" id="fGroup" class="input-field w-full px-4 py-3 text-sm" placeholder="e.g. orders" list="groupList">
                <datalist id="groupList"></datalist>
            </div>
            <div class="mb-6">
                <label class="block text-white/60 text-sm mb-2">Description</label>
                <input type="text" id="fDescription" class="input-field w-full px-4 py-3 text-sm" placeholder="What this permission allows">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="btn-secondary px-5 py-2.5 rounded-xl text-sm">Cancel</button>
                <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Add Modal -->
<div id="bulkModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeBulkModal()">
    <div class="glass-card p-6 w-full sm:max-w-lg sm:mx-4 rounded-t-2xl sm:rounded-2xl" onclick="event.stopPropagation()">
        <h2 class="text-xl font-bold text-white mb-4">Quick Add Permissions</h2>
        <p class="text-white/40 text-xs mb-4">Select modules and actions to create permissions in bulk</p>
        <div class="mb-4">
            <label class="block text-white/60 text-sm mb-2">Modules</label>
            <div class="flex flex-wrap gap-2">
                @foreach(['customers','orders','deliveries','payments','reports','drivers','vehicles'] as $m)
                <label class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white/[0.05] cursor-pointer hover:bg-white/[0.08]">
                    <input type="checkbox" class="module-check rounded border-white/20 bg-white/5" value="{{ $m }}"> <span class="text-white/70 text-xs">{{ $m }}</span>
                </label>
                @endforeach
            </div>
        </div>
        <div class="mb-6">
            <label class="block text-white/60 text-sm mb-2">Actions</label>
            <div class="flex flex-wrap gap-2">
                @foreach(['view','create','edit','delete','export'] as $a)
                <label class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white/[0.05] cursor-pointer hover:bg-white/[0.08]">
                    <input type="checkbox" class="action-check rounded border-white/20 bg-white/5" value="{{ $a }}"> <span class="text-white/70 text-xs">{{ $a }}</span>
                </label>
                @endforeach
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeBulkModal()" class="btn-secondary px-5 py-2.5 rounded-xl text-sm">Cancel</button>
            <button onclick="bulkCreate()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">Create All</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allPermissions = [];

async function loadData() {
    const res = await fetch('/api/permissions', { credentials: 'same-origin' });
    allPermissions = await res.json();
    renderGroups();
}

function renderGroups() {
    const groups = {};
    allPermissions.forEach(p => {
        if (!groups[p.group]) groups[p.group] = [];
        groups[p.group].push(p);
    });

    // Update datalist
    const dl = document.getElementById('groupList');
    dl.innerHTML = Object.keys(groups).map(g => `<option value="${g}">`).join('');

    const container = document.getElementById('permGroups');
    if (!Object.keys(groups).length) {
        container.innerHTML = `<div class="col-span-2 glass-card p-12 text-center"><div class="text-4xl mb-3">🔑</div><p class="text-white/40">No permissions yet. Use Quick Add to create them.</p>
        <button onclick="openBulkModal()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium mt-4">Quick Add Permissions</button></div>`;
        return;
    }

    container.innerHTML = Object.entries(groups).map(([group, perms]) => `
        <div class="glass-card p-4">
            <h3 class="text-sm font-semibold text-white/90 mb-3 capitalize">${group}</h3>
            <div class="space-y-1">
                ${perms.map(p => `
                    <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-white/[0.03] hover:bg-white/[0.06] group">
                        <div>
                            <span class="text-white/80 text-sm">${p.name}</span>
                            ${p.description ? `<span class="text-white/30 text-[10px] ml-2">${p.description}</span>` : ''}
                        </div>
                        <button onclick="deleteItem(${p.id})" class="text-rose-400/0 group-hover:text-rose-400 text-xs transition">Delete</button>
                    </div>
                `).join('')}
            </div>
        </div>
    `).join('');
}

function openModal() {
    document.getElementById('itemId').value = '';
    document.getElementById('fName').value = '';
    document.getElementById('fGroup').value = '';
    document.getElementById('fDescription').value = '';
    document.getElementById('modalTitle').textContent = 'Add Permission';
    document.getElementById('modal').classList.remove('hidden');
}

function closeModal() { document.getElementById('modal').classList.add('hidden'); }
function openBulkModal() { document.getElementById('bulkModal').classList.remove('hidden'); }
function closeBulkModal() { document.getElementById('bulkModal').classList.add('hidden'); }

async function saveItem(e) {
    e.preventDefault();
    const id = document.getElementById('itemId').value;
    const data = {
        name: document.getElementById('fName').value,
        group: document.getElementById('fGroup').value || 'general',
        description: document.getElementById('fDescription').value
    };
    const url = id ? `/api/permissions/${id}` : '/api/permissions';
    await fetch(url, {
        method: id ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    });
    closeModal();
    loadData();
}

async function bulkCreate() {
    const modules = [...document.querySelectorAll('.module-check:checked')].map(cb => cb.value);
    const actions = [...document.querySelectorAll('.action-check:checked')].map(cb => cb.value);

    if (!modules.length || !actions.length) {
        alert('Select at least one module and one action');
        return;
    }

    for (const mod of modules) {
        for (const act of actions) {
            await fetch('/api/permissions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ name: `${act}_${mod}`, group: mod, description: `${act} ${mod}` })
            });
        }
    }
    closeBulkModal();
    loadData();
}

async function deleteItem(id) {
    if (!confirm('Delete this permission?')) return;
    await fetch(`/api/permissions/${id}`, { method: 'DELETE', credentials: 'same-origin' });
    loadData();
}

loadData();
</script>
@endsection
