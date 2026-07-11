@extends('layouts.app')
@section('title', 'Menu Builder - Admin Panel')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">📋 Menu Builder</h1>
        <p class="text-white/40 text-xs lg:text-sm mt-1">Drag and drop to organize menus & sub-menus</p>
    </div>
    <button onclick="openModal()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ Add Menu</button>
</div>

<!-- Instructions -->
<div class="glass-card p-3 mb-4">
    <div class="flex items-center gap-2 text-white/50 text-xs">
        <span class="text-cyan-400">💡</span>
        <span>Drag items to reorder • Drag right to create sub-menu • Drag to parent to nest • Click ✏️ to edit</span>
    </div>
</div>

<!-- Menu Tree -->
<div class="glass-card p-4" id="menuContainer">
    <div id="menuTree" class="space-y-1"></div>
    <div id="emptyState" class="hidden text-center py-12">
        <div class="text-4xl mb-3">📋</div>
        <p class="text-white/40">No menus yet. Click "+ Add Menu" to start</p>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeModal()">
    <div class="glass-card p-6 w-full sm:max-w-md sm:mx-4 rounded-t-2xl sm:rounded-2xl" onclick="event.stopPropagation()">
        <h2 class="text-xl font-bold text-white mb-6" id="modalTitle">Add Menu</h2>
        <form id="form" onsubmit="saveItem(event)">
            <input type="hidden" id="itemId">
            <input type="hidden" id="fParentId" value="">
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">Menu Name *</label>
                <input type="text" id="fName" required class="input-field w-full px-4 py-3 text-sm" placeholder="e.g. Dashboard">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">URL</label>
                <input type="text" id="fUrl" class="input-field w-full px-4 py-3 text-sm" placeholder="/dashboard">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">Icon (emoji or text)</label>
                <input type="text" id="fIcon" class="input-field w-full px-4 py-3 text-sm" placeholder="🏠 or 📊">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm mb-2">Open in</label>
                <select id="fTarget" class="input-field w-full px-4 py-3 text-sm">
                    <option value="_self">Same window</option>
                    <option value="_blank">New tab</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="fActive" checked class="rounded border-white/20 bg-white/5">
                    <span class="text-white/60 text-sm">Active (visible)</span>
                </label>
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
let menuTree = [];

async function loadData() {
    const res = await fetch('/api/menus/tree', { credentials: 'same-origin' });
    menuTree = await res.json();
    renderTree();
}

function renderTree() {
    const container = document.getElementById('menuTree');
    const empty = document.getElementById('emptyState');

    if (!menuTree.length) {
        container.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }
    empty.classList.add('hidden');
    container.innerHTML = buildHTML(menuTree, 0);
    initSortable();
}

function buildHTML(items, depth) {
    if (!items.length) return '';
    return `<ul class="space-y-1 ${depth > 0 ? 'ml-6 border-l border-white/5 pl-2' : ''}" data-depth="${depth}">
        ${items.map(item => `
        <li class="menu-item" data-id="${item.id}" data-parent="${item.parent_id || ''}">
            <div class="flex items-center gap-2 p-2 rounded-lg bg-white/[0.03] hover:bg-white/[0.06] transition group cursor-move">
                <span class="text-white/20 text-xs cursor-grab handle">⠿</span>
                <span class="text-sm">${item.icon || '📄'}</span>
                <span class="text-white/90 text-sm font-medium flex-1">${item.name}</span>
                ${item.url ? `<span class="text-white/30 text-[10px] hidden sm:inline">${item.url}</span>` : ''}
                ${!item.is_active ? '<span class="text-[9px] px-1.5 py-0.5 rounded bg-rose-500/10 text-rose-400">hidden</span>' : ''}
                ${item.target === '_blank' ? '<span class="text-[9px] px-1.5 py-0.5 rounded bg-violet-500/10 text-violet-400">new tab</span>' : ''}
                <button onclick="addSubMenu(${item.id})" class="text-white/20 hover:text-cyan-400 text-xs opacity-0 group-hover:opacity-100 transition" title="Add sub-menu">+</button>
                <button onclick="editItem(${JSON.stringify(item).replace(/'/g,"&#39;")})" class="text-white/20 hover:text-amber-400 text-xs opacity-0 group-hover:opacity-100 transition" title="Edit">✏️</button>
                <button onclick="deleteItem(${item.id})" class="text-white/20 hover:text-rose-400 text-xs opacity-0 group-hover:opacity-100 transition" title="Delete">🗑️</button>
            </div>
            ${item.children?.length ? buildHTML(item.children, depth + 1) : '<ul class="empty-sublist ml-6 border-l border-dashed border-white/5 pl-2 min-h-[2px]"></ul>'}
        </li>`).join('')}
    </ul>`;
}

function initSortable() {
    document.querySelectorAll('#menuTree ul').forEach(ul => {
        new Sortable(ul, {
            group: 'menus',
            handle: '.handle',
            animation: 200,
            ghostClass: 'bg-cyan-500/10',
            chosenClass: 'bg-cyan-500/5',
            dragClass: 'opacity-50',
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: function(evt) {
                saveOrder();
            }
        });
    });
}

async function saveOrder() {
    const items = [];
    document.querySelectorAll('.menu-item').forEach((li, index) => {
        const parentUl = li.parentElement;
        const parentLi = parentUl.closest('.menu-item');
        items.push({
            id: parseInt(li.dataset.id),
            position: index,
            parent_id: parentLi ? parseInt(parentLi.dataset.id) : null
        });
    });

    await fetch('/api/menus/reorder', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ items })
    });
    loadData();
}

function openModal(parentId = null) {
    document.getElementById('itemId').value = '';
    document.getElementById('fName').value = '';
    document.getElementById('fUrl').value = '';
    document.getElementById('fIcon').value = '';
    document.getElementById('fTarget').value = '_self';
    document.getElementById('fActive').checked = true;
    document.getElementById('fParentId').value = parentId || '';
    document.getElementById('modalTitle').textContent = parentId ? 'Add Sub-Menu' : 'Add Menu';
    document.getElementById('modal').classList.remove('hidden');
}

function addSubMenu(parentId) {
    openModal(parentId);
}

function editItem(item) {
    document.getElementById('itemId').value = item.id;
    document.getElementById('fName').value = item.name;
    document.getElementById('fUrl').value = item.url || '';
    document.getElementById('fIcon').value = item.icon || '';
    document.getElementById('fTarget').value = item.target || '_self';
    document.getElementById('fActive').checked = item.is_active;
    document.getElementById('fParentId').value = item.parent_id || '';
    document.getElementById('modalTitle').textContent = 'Edit Menu';
    document.getElementById('modal').classList.remove('hidden');
}

function closeModal() { document.getElementById('modal').classList.add('hidden'); }

async function saveItem(e) {
    e.preventDefault();
    const id = document.getElementById('itemId').value;
    const data = {
        name: document.getElementById('fName').value,
        url: document.getElementById('fUrl').value || null,
        icon: document.getElementById('fIcon').value || null,
        parent_id: document.getElementById('fParentId').value || null,
        target: document.getElementById('fTarget').value,
        is_active: document.getElementById('fActive').checked,
    };
    const url = id ? `/api/menus/${id}` : '/api/menus';
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
    if (!confirm('Delete this menu item? Children will be moved up.')) return;
    await fetch(`/api/menus/${id}`, { method: 'DELETE', credentials: 'same-origin' });
    loadData();
}

loadData();
</script>
@endsection
