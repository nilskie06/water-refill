@extends('layouts.app')
@section('title', 'Customers - Water Refill Station')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">👥 Customers</h1>
        <p class="text-white/40 text-sm mt-1">Manage your customers</p>
    </div>
    <button onclick="openModal()" class="btn-primary px-5 py-2.5 rounded-xl text-white text-sm font-medium">+ Add</button>
</div>

<div class="mb-4">
    <input type="text" id="searchInput" placeholder="🔍 Search customers..." oninput="loadCustomers()" class="input-field w-full px-4 py-3 text-sm">
</div>

<!-- Desktop Table -->
<div class="glass-card desktop-table overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Name</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Contact</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Address</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Orders</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-white/50 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody id="customerTable"></tbody>
    </table>
</div>

<!-- Mobile Cards -->
<div class="mobile-cards space-y-3" id="customerCards"></div>

<!-- Empty State -->
<div id="emptyState" class="hidden text-center py-12">
    <div class="text-4xl mb-3">👤</div>
    <p class="text-white/40">No customers found</p>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center z-50 hidden" onclick="if(event.target===this)closeModal()">
    <div class="glass-card p-6 w-full sm:max-w-md sm:mx-4 rounded-t-2xl sm:rounded-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <h2 class="text-xl font-bold text-white mb-6" id="modalTitle">Add Customer</h2>
        <form id="customerForm" onsubmit="saveCustomer(event)">
            <input type="hidden" id="customerId">
            <div class="mb-4">
                <label class="block text-white/60 text-sm font-medium mb-2">Name *</label>
                <input type="text" id="customerName" required class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm font-medium mb-2">Contact</label>
                <input type="text" id="customerContact" class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div class="mb-4">
                <label class="block text-white/60 text-sm font-medium mb-2">Address</label>
                <textarea id="customerAddress" class="input-field w-full px-4 py-3 text-sm" rows="2"></textarea>
            </div>
            <div class="mb-6">
                <label class="block text-white/60 text-sm font-medium mb-2">Notes</label>
                <textarea id="customerNotes" class="input-field w-full px-4 py-3 text-sm" rows="2"></textarea>
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
async function loadCustomers() {
    const search = document.getElementById('searchInput').value;
    const res = await fetch(`/api/customers?search=${search}`, { credentials: 'same-origin' });
    if (res.status === 401) { window.location.href = '/login'; return; }
    const data = await res.json();
    const customers = data.data || [];

    if (customers.length === 0) {
        document.getElementById('emptyState').classList.remove('hidden');
        document.getElementById('customerTable').innerHTML = '';
        document.getElementById('customerCards').innerHTML = '';
        return;
    }
    document.getElementById('emptyState').classList.add('hidden');

    // Desktop table
    document.getElementById('customerTable').innerHTML = customers.map(c => `
        <tr class="border-b border-white/5 hover:bg-white/[0.02] transition">
            <td class="px-6 py-4 text-sm font-medium text-white/90">${c.name}</td>
            <td class="px-6 py-4 text-sm text-white/70">${c.contact || '-'}</td>
            <td class="px-6 py-4 text-sm text-white/70">${c.address || '-'}</td>
            <td class="px-6 py-4 text-sm text-white/70">${c.orders_count}</td>
            <td class="px-6 py-4 text-sm space-x-3">
                <a href="/customers/${c.id}" class="text-cyan-400 hover:text-cyan-300">View</a>
                <button onclick='editCustomer(${JSON.stringify(c).replace(/'/g,"&#39;")})' class="text-amber-400 hover:text-amber-300">Edit</button>
                <button onclick="deleteCustomer(${c.id})" class="text-rose-400 hover:text-rose-300">Delete</button>
            </td>
        </tr>`).join('');

    // Mobile cards
    document.getElementById('customerCards').innerHTML = customers.map(c => `
        <div class="glass-card p-4">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-white font-semibold text-sm">${c.name}</h3>
                    <p class="text-white/50 text-xs mt-1">📞 ${c.contact || 'No contact'}</p>
                </div>
                <span class="badge badge-pending">${c.orders_count} orders</span>
            </div>
            <p class="text-white/40 text-xs mb-3">📍 ${c.address || 'No address'}</p>
            <div class="flex gap-2 pt-2 border-t border-white/5">
                <a href="/customers/${c.id}" class="flex-1 text-center py-2 rounded-lg bg-cyan-500/10 text-cyan-400 text-xs font-medium hover:bg-cyan-500/20 transition">View</a>
                <button onclick='editCustomer(${JSON.stringify(c).replace(/'/g,"&#39;")})' class="flex-1 py-2 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-medium hover:bg-amber-500/20 transition">Edit</button>
                <button onclick="deleteCustomer(${c.id})" class="flex-1 py-2 rounded-lg bg-rose-500/10 text-rose-400 text-xs font-medium hover:bg-rose-500/20 transition">Delete</button>
            </div>
        </div>`).join('');
}
function openModal() { document.getElementById('customerId').value = ''; document.getElementById('customerName').value = ''; document.getElementById('customerContact').value = ''; document.getElementById('customerAddress').value = ''; document.getElementById('customerNotes').value = ''; document.getElementById('modalTitle').textContent = 'Add Customer'; document.getElementById('modal').classList.remove('hidden'); }
function editCustomer(c) { document.getElementById('customerId').value = c.id; document.getElementById('customerName').value = c.name; document.getElementById('customerContact').value = c.contact || ''; document.getElementById('customerAddress').value = c.address || ''; document.getElementById('customerNotes').value = c.notes || ''; document.getElementById('modalTitle').textContent = 'Edit Customer'; document.getElementById('modal').classList.remove('hidden'); }
function closeModal() { document.getElementById('modal').classList.add('hidden'); }
async function saveCustomer(e) {
    e.preventDefault();
    const id = document.getElementById('customerId').value;
    const data = { name: document.getElementById('customerName').value, contact: document.getElementById('customerContact').value, address: document.getElementById('customerAddress').value, notes: document.getElementById('customerNotes').value };
    const url = id ? `/api/customers/${id}` : '/api/customers';
    await fetch(url, { method: id ? 'PUT' : 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify(data) });
    closeModal(); loadCustomers();
}
async function deleteCustomer(id) { if (!confirm('Delete this customer?')) return; await fetch(`/api/customers/${id}`, { method: 'DELETE', credentials: 'same-origin' }); loadCustomers(); }
loadCustomers();
</script>
@endsection
