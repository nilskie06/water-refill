@extends('layouts.app')
@section('title', 'Record Payment - Water Refill Station')

@section('content')
<div class="mb-6"><a href="/payments" class="text-cyan-400 hover:text-cyan-300 text-sm">← Back to Payments</a></div>

<div class="glass-card p-6 lg:p-8 max-w-2xl">
    <h1 class="text-2xl font-bold text-white mb-6">💰 Record Payment</h1>
    <form id="paymentForm" onsubmit="submitPayment(event)">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Order *</label>
                <select id="orderId" required class="input-field w-full px-4 py-3 text-sm">
                    <option value="">Select Order</option>
                    @foreach($orders as $o)<option value="{{ $o->id }}">{{ $o->order_number }} - {{ $o->customer->name }} (₱{{ $o->total }})</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Amount *</label>
                <input type="number" id="amount" step="0.01" min="0.01" required class="input-field w-full px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Payment Method *</label>
                <select id="paymentMethod" required class="input-field w-full px-4 py-3 text-sm">
                    <option value="cash">Cash</option><option value="gcash">GCash</option><option value="maya">Maya</option><option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            <div>
                <label class="block text-white/60 text-sm font-medium mb-2">Payment Date *</label>
                <input type="date" id="paymentDate" required class="input-field w-full px-4 py-3 text-sm">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-8">
            <a href="/payments" class="btn-secondary px-6 py-2.5 rounded-xl text-sm">Cancel</a>
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-white text-sm font-medium">Record Payment</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
async function submitPayment(e) {
    e.preventDefault();
    const data = { order_id: document.getElementById('orderId').value, amount: parseFloat(document.getElementById('amount').value), payment_method: document.getElementById('paymentMethod').value, payment_date: document.getElementById('paymentDate').value };
    const res = await fetch('/api/payments', { method: 'POST', headers: {'Content-Type':'application/json'}, credentials: 'same-origin', body: JSON.stringify(data) });
    if (res.ok) window.location.href = '/payments'; else { const err = await res.json(); alert(err.message || 'Error'); }
}
</script>
@endsection
