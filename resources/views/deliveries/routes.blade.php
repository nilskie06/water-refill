@extends('layouts.app')
@section('title', 'Route Planning - Water Refill Station')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-xl lg:text-3xl font-bold bg-gradient-to-r from-cyan-400 to-violet-400 bg-clip-text text-transparent">🗺️ Route Planning</h1>
        <p class="text-white/40 text-xs lg:text-sm mt-1">Organize deliveries by route</p>
    </div>
    <input type="date" id="routeDate" onchange="loadRoutes()" value="{{ date('Y-m-d') }}" class="input-field px-4 py-2.5 text-sm">
</div>

<div id="routeContainer" class="space-y-6"></div>
<div id="emptyState" class="hidden text-center py-12"><div class="text-4xl mb-3">🗺️</div><p class="text-white/40">No routes for this date</p></div>
@endsection

@section('scripts')
<script>
const routeEmojis = { morning: '🌅', afternoon: '☀️', evening: '🌙' };
const routeColors = { morning: 'border-amber-500/20 bg-amber-500/5', afternoon: 'border-cyan-500/20 bg-cyan-500/5', evening: 'border-violet-500/20 bg-violet-500/5' };
const sc = { scheduled: 'badge-pending', assigned: 'badge-delivered', out_for_delivery: 'badge-completed', delivered: 'badge-completed' };

async function loadRoutes() {
    const date = document.getElementById('routeDate').value;
    const res = await fetch(`/api/delivery/routes?date=${date}`, { credentials:'same-origin' });
    const data = await res.json();
    const routes = data.routes || {};
    const routeKeys = ['morning', 'afternoon', 'evening'];
    let html = '';

    routeKeys.forEach(key => {
        const deliveries = routes[key] || [];
        html += `<div class="glass-card p-4 border-l-4 ${routeColors[key] || ''}">
            <h2 class="text-lg font-semibold text-white/90 mb-3">${routeEmojis[key]||'📍'} ${key.charAt(0).toUpperCase()+key.slice(1)} Route <span class="text-white/40 text-sm font-normal">(${deliveries.length} stops)</span></h2>
            <div class="space-y-2">`;
        if (!deliveries.length) {
            html += '<p class="text-white/30 text-sm pl-8">No deliveries</p>';
        } else {
            deliveries.forEach((d, i) => {
                html += `<div class="flex items-center gap-3 p-3 rounded-xl bg-white/[0.03]">
                    <div class="w-8 h-8 rounded-full bg-cyan-500/10 flex items-center justify-center shrink-0"><span class="text-cyan-400 text-xs font-bold">${i+1}</span></div>
                    <div class="flex-1 min-w-0"><p class="text-white/90 text-sm font-medium truncate">${d.customer?.name||'Walk-in'}</p><p class="text-white/50 text-xs truncate">📍 ${d.address||'-'}</p></div>
                    <span class="badge ${sc[d.status]||''} text-[10px]">${d.status}</span>
                </div>`;
            });
        }
        html += '</div></div>';
    });

    document.getElementById('routeContainer').innerHTML = html;
    document.getElementById('emptyState').classList.toggle('hidden', routeKeys.every(k => !routes[k]?.length));
}
loadRoutes();
</script>
@endsection