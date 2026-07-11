@extends('layouts.app')
@section('title', 'Delivery Calendar - Water Refill Station')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🗓️ Delivery Calendar</h1>
        <p class="text-white/40 text-xs lg:text-sm mt-1">View deliveries by date</p>
    </div>
    <div class="flex gap-2">
        <button onclick="changeMonth(-1)" class="btn-secondary px-3 py-2 rounded-xl text-sm">←</button>
        <span id="monthLabel" class="text-white/90 text-sm font-medium px-3 py-2"></span>
        <button onclick="changeMonth(1)" class="btn-secondary px-3 py-2 rounded-xl text-sm">→</button>
    </div>
</div>

<div class="glass-card p-4 lg:p-6">
    <div class="grid grid-cols-7 gap-1 mb-2">
        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
        <div class="text-center text-white/40 text-xs font-semibold py-2">{{ $d }}</div>
        @endforeach
    </div>
    <div id="calendarGrid" class="grid grid-cols-7 gap-1"></div>
</div>

<div id="dayDeliveries" class="mt-4 hidden">
    <h2 class="text-lg font-semibold text-white/90 mb-3" id="dayTitle"></h2>
    <div class="space-y-2" id="dayList"></div>
</div>
@endsection

@section('scripts')
<script>
let currentYear = {{ date('Y') }}, currentMonth = {{ date('m') }} - 1;
const sc = { scheduled: 'badge-pending', assigned: 'badge-delivered', out_for_delivery: 'badge-completed', delivered: 'badge-completed', failed: 'badge-cancelled', cancelled: 'badge-cancelled' };

async function loadCalendar() {
    const from = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-01`;
    const to = new Date(currentYear, currentMonth+1, 0).toISOString().split('T')[0];
    document.getElementById('monthLabel').textContent = new Date(currentYear, currentMonth).toLocaleDateString('en-US', { month:'long', year:'numeric' });

    const res = await fetch(`/api/delivery/calendar?from=${from}&to=${to}`, { credentials:'same-origin' });
    const deliveries = await res.json();
    const grouped = {};
    deliveries.forEach(d => { grouped[d.delivery_date] = (grouped[d.delivery_date]||0) + 1; });

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth+1, 0).getDate();
    const today = new Date().toISOString().split('T')[0];
    let html = '';
    for (let i=0; i<firstDay; i++) html += '<div></div>';
    for (let d=1; d<=daysInMonth; d++) {
        const dateStr = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const count = grouped[dateStr] || 0;
        const isToday = dateStr === today;
        html += `<div onclick="showDay('${dateStr}')" class="p-2 rounded-lg cursor-pointer transition text-center ${isToday ? 'bg-cyan-500/20 border border-cyan-500/30' : 'hover:bg-white/[0.06]'}">
            <div class="text-white/90 text-sm font-medium ${isToday ? 'text-cyan-400' : ''}">${d}</div>
            ${count ? `<div class="text-[10px] text-cyan-400 mt-1">${count} delivery${count>1?'s':''}</div>` : ''}
        </div>`;
    }
    document.getElementById('calendarGrid').innerHTML = html;
}

async function showDay(date) {
    document.getElementById('dayTitle').textContent = new Date(date+'T00:00:00').toLocaleDateString('en-US', { weekday:'long', month:'long', day:'numeric' });
    const res = await fetch(`/api/deliveries?date=${date}`, { credentials:'same-origin' });
    const data = await res.json();
    const items = data.data || [];
    document.getElementById('dayDeliveries').classList.remove('hidden');
    if (!items.length) { document.getElementById('dayList').innerHTML = '<p class="text-white/40 text-sm">No deliveries</p>'; return; }
    document.getElementById('dayList').innerHTML = items.map(d => `<div class="glass-card p-3 flex items-center justify-between"><div><p class="text-white/90 text-sm font-medium">${d.delivery_no} - ${d.customer?.name||'-'}</p><p class="text-white/50 text-xs">⏰ ${d.delivery_time||'-'} • 📍 ${d.address||'-'}</p></div><span class="badge ${sc[d.status]||''}">${d.status}</span></div>`).join('');
}

function changeMonth(delta) { currentMonth += delta; if(currentMonth>11){currentMonth=0;currentYear++;}if(currentMonth<0){currentMonth=11;currentYear--;} loadCalendar(); }
loadCalendar();
</script>
@endsection