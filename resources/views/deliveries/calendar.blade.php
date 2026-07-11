@extends('layouts.app')
@section('title', 'Delivery Calendar - Water Refill Station')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
    <div>
        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🗓️ Delivery Calendar</h1>
        <p class="text-white/40 text-xs lg:text-sm mt-1">Tap a date to see deliveries</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="goToday()" class="btn-primary px-4 py-2 rounded-xl text-sm font-medium">Today</button>
        <button onclick="changeMonth(-1)" class="btn-secondary w-10 h-10 rounded-xl flex items-center justify-center">‹</button>
        <span id="monthLabel" class="text-white/90 text-sm font-semibold px-2 min-w-[140px] text-center"></span>
        <button onclick="changeMonth(1)" class="btn-secondary w-10 h-10 rounded-xl flex items-center justify-center">›</button>
    </div>
</div>

<!-- Month Stats -->
<div class="grid grid-cols-3 gap-3 mb-4" id="monthStats">
    <div class="glass-card p-3 text-center">
        <p class="text-2xl font-bold text-cyan-400" id="statTotal">0</p>
        <p class="text-white/40 text-[10px] uppercase">Total</p>
    </div>
    <div class="glass-card p-3 text-center">
        <p class="text-2xl font-bold text-emerald-400" id="statDelivered">0</p>
        <p class="text-white/40 text-[10px] uppercase">Delivered</p>
    </div>
    <div class="glass-card p-3 text-center">
        <p class="text-2xl font-bold text-amber-400" id="statPending">0</p>
        <p class="text-white/40 text-[10px] uppercase">Pending</p>
    </div>
</div>

<!-- Calendar Grid -->
<div class="glass-card p-4 lg:p-6">
    <div class="grid grid-cols-7 gap-1 mb-2">
        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
        <div class="text-center text-white/40 text-xs font-semibold py-2 uppercase tracking-wider">{{ $d }}</div>
        @endforeach
    </div>
    <div id="calendarGrid" class="grid grid-cols-7 gap-1"></div>
</div>

<!-- Legend -->
<div class="flex flex-wrap gap-4 mt-3 text-[10px] text-white/40">
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-cyan-400 inline-block"></span> Scheduled</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> Assigned</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-violet-400 inline-block"></span> Out for Delivery</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span> Delivered</span>
    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-400 inline-block"></span> Failed</span>
</div>

<!-- Day Detail Panel -->
<div id="dayPanel" class="mt-6 hidden">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-lg font-bold text-white/90" id="dayTitle"></h2>
            <p class="text-white/40 text-xs" id="daySubtitle"></p>
        </div>
        <button onclick="document.getElementById('dayPanel').classList.add('hidden')" class="text-white/40 hover:text-white/70 text-sm">✕ Close</button>
    </div>

    <!-- Day mini stats -->
    <div class="grid grid-cols-4 gap-2 mb-4" id="dayStats">
        <div class="glass-card p-2 text-center"><p class="text-lg font-bold text-cyan-400" id="dayScheduled">0</p><p class="text-white/40 text-[9px]">Scheduled</p></div>
        <div class="glass-card p-2 text-center"><p class="text-lg font-bold text-amber-400" id="dayAssigned">0</p><p class="text-white/40 text-[9px]">Assigned</p></div>
        <div class="glass-card p-2 text-center"><p class="text-lg font-bold text-violet-400" id="dayOut">0</p><p class="text-white/40 text-[9px]">In Transit</p></div>
        <div class="glass-card p-2 text-center"><p class="text-lg font-bold text-emerald-400" id="dayDelivered">0</p><p class="text-white/40 text-[9px]">Done</p></div>
    </div>

    <!-- Delivery List -->
    <div class="space-y-2" id="dayList"></div>
</div>
@endsection

@section('scripts')
<script>
let currentYear = {{ date('Y') }}, currentMonth = {{ date('m') }} - 1;
let allDeliveries = [];
const sc = { scheduled: 'badge-pending', assigned: 'badge-delivered', out_for_delivery: 'badge-completed', delivered: 'badge-completed', failed: 'badge-cancelled', cancelled: 'badge-cancelled' };
const dotColors = { scheduled: 'bg-cyan-400', assigned: 'bg-amber-400', out_for_delivery: 'bg-violet-400', delivered: 'bg-emerald-400', failed: 'bg-rose-400', cancelled: 'bg-rose-400' };

function dateOnly(d) { return d ? d.substring(0, 10) : ''; }

async function loadCalendar() {
    const from = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-01`;
    const to = new Date(currentYear, currentMonth+1, 0).toISOString().split('T')[0];
    document.getElementById('monthLabel').textContent = new Date(currentYear, currentMonth).toLocaleDateString('en-US', { month:'long', year:'numeric' });

    const res = await fetch(`/api/delivery/calendar?from=${from}&to=${to}`, { credentials:'same-origin' });
    allDeliveries = await res.json();

    // Month stats
    const delivered = allDeliveries.filter(d => d.status === 'delivered').length;
    const pending = allDeliveries.filter(d => ['scheduled','assigned','out_for_delivery'].includes(d.status)).length;
    document.getElementById('statTotal').textContent = allDeliveries.length;
    document.getElementById('statDelivered').textContent = delivered;
    document.getElementById('statPending').textContent = pending;

    // Group by date
    const grouped = {};
    allDeliveries.forEach(d => {
        const key = dateOnly(d.delivery_date);
        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(d);
    });

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth+1, 0).getDate();
    const today = new Date().toISOString().split('T')[0];
    let html = '';

    for (let i = 0; i < firstDay; i++) html += '<div></div>';

    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const dayDeliveries = grouped[dateStr] || [];
        const isToday = dateStr === today;
        const isPast = dateStr < today;

        // Count by status for dots
        const statuses = {};
        dayDeliveries.forEach(dl => { statuses[dl.status] = (statuses[dl.status]||0) + 1; });
        const dots = Object.entries(statuses).slice(0, 4);

        html += `<div onclick="showDay('${dateStr}')" class="p-1.5 sm:p-2 rounded-xl cursor-pointer transition min-h-[60px] sm:min-h-[70px] flex flex-col ${isToday ? 'bg-cyan-500/15 border border-cyan-500/30 ring-1 ring-cyan-500/10' : isPast ? 'hover:bg-white/[0.04] opacity-70' : 'hover:bg-white/[0.06]'}">
            <div class="flex justify-between items-start">
                <span class="text-xs sm:text-sm font-medium ${isToday ? 'text-cyan-400 font-bold' : 'text-white/80'}">${d}</span>
                ${dayDeliveries.length ? `<span class="text-[9px] bg-white/10 rounded-full px-1.5 py-0.5 font-bold ${isToday ? 'text-cyan-300' : 'text-white/60'}">${dayDeliveries.length}</span>` : ''}
            </div>
            <div class="flex flex-wrap gap-1 mt-auto pt-1">
                ${dots.map(([s, c]) => `<span class="w-1.5 h-1.5 rounded-full ${dotColors[s] || 'bg-white/30'}" title="${s}: ${c}"></span>`).join('')}
            </div>
        </div>`;
    }
    document.getElementById('calendarGrid').innerHTML = html;
}

async function showDay(date) {
    const d = new Date(date + 'T00:00:00');
    document.getElementById('dayTitle').textContent = d.toLocaleDateString('en-US', { weekday:'long', month:'long', day:'numeric', year:'numeric' });
    document.getElementById('daySubtitle').textContent = `${allDeliveries.filter(dl => dateOnly(dl.delivery_date) === date).length} deliveries scheduled`;

    const dayDeliveries = allDeliveries.filter(dl => dateOnly(dl.delivery_date) === date);

    // Day mini stats
    document.getElementById('dayScheduled').textContent = dayDeliveries.filter(dl => dl.status === 'scheduled').length;
    document.getElementById('dayAssigned').textContent = dayDeliveries.filter(dl => dl.status === 'assigned').length;
    document.getElementById('dayOut').textContent = dayDeliveries.filter(dl => dl.status === 'out_for_delivery').length;
    document.getElementById('dayDelivered').textContent = dayDeliveries.filter(dl => dl.status === 'delivered').length;

    document.getElementById('dayPanel').classList.remove('hidden');

    if (!dayDeliveries.length) {
        document.getElementById('dayList').innerHTML = `
            <div class="text-center py-8 glass-card">
                <div class="text-3xl mb-2">📭</div>
                <p class="text-white/40 text-sm">No deliveries for this day</p>
                <a href="/deliveries/create" class="btn-primary inline-block mt-3 px-4 py-2 rounded-xl text-sm text-white">+ Schedule Delivery</a>
            </div>`;
        return;
    }

    document.getElementById('dayList').innerHTML = dayDeliveries.map(dl => `
        <div class="glass-card p-4 hover:bg-white/[0.06] transition">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl ${dl.status === 'delivered' ? 'bg-emerald-500/10' : dl.status === 'out_for_delivery' ? 'bg-violet-500/10' : 'bg-cyan-500/10'} flex items-center justify-center shrink-0">
                        <span class="text-sm">${dl.status === 'delivered' ? '✅' : dl.status === 'out_for_delivery' ? '🚚' : dl.status === 'assigned' ? '👤' : dl.status === 'failed' ? '❌' : '📅'}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-white/90 text-sm font-semibold">${dl.customer?.name || 'Walk-in'}</p>
                        <p class="text-white/50 text-xs mt-0.5 truncate">📍 ${dl.address || '-'}</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="badge ${sc[dl.status] || ''} text-[10px]">${dl.status.replace('_', ' ')}</span>
                    <p class="text-white/40 text-[10px] mt-1">${dl.delivery_time ? '⏰ ' + dl.delivery_time : ''}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 mt-3 pt-2 border-t border-white/5 text-[10px] text-white/40">
                <span>📦 ${dl.quantity || 1} qty</span>
                <span>${dl.delivery_type === 'rush' ? '⚡ Rush' : dl.delivery_type === 'pickup' ? '📦 Pickup' : dl.delivery_type === 'scheduled' ? '📅 Scheduled' : '🚚 Regular'}</span>
                ${dl.driver ? `<span>👤 ${dl.driver.name}</span>` : ''}
                ${dl.route ? `<span>${dl.route === 'morning' ? '🌅' : dl.route === 'afternoon' ? '☀️' : '🌙'} ${dl.route}</span>` : ''}
            </div>
        </div>
    `).join('');
}

function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    if (currentMonth < 0) { currentMonth = 11; currentYear--; }
    document.getElementById('dayPanel').classList.add('hidden');
    loadCalendar();
}

function goToday() {
    const now = new Date();
    currentYear = now.getFullYear();
    currentMonth = now.getMonth();
    loadCalendar();
    // Auto-open today
    const today = now.toISOString().split('T')[0];
    setTimeout(() => showDay(today), 300);
}

loadCalendar();
</script>
@endsection
