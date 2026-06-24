@extends('layouts.app')
@section('title', 'Reports — ' . config('app.name'))
@section('page-title', 'Reports')
@section('page-subtitle', 'Order statistics and delivery insights')

@push('styles')
<style>
/* ── Date filter ── */
.reports-filter { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:22px; padding:14px 16px; background:#1a1a1a; border:1px solid #2d2d2d; border-radius:12px; }
.filter-label { font-size:12px; font-weight:500; color:#6b7280; white-space:nowrap; }
.filter-date { padding:7px 10px; background:#141414; border:1px solid #2d2d2d; border-radius:8px; font-size:13px; color:#f5f5f5; outline:none; }
.filter-date:focus { border-color:#f59e0b; }
.btn-filter { padding:7px 14px; background:#f59e0b; color:#111; border-radius:8px; font-size:13px; font-weight:600; border:none; cursor:pointer; }
.btn-filter:hover { background:#d97706; }

/* ── Stat cards ── */
.stats-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(190px,1fr)); gap:12px; margin-bottom:22px; }
.stat-card { padding:16px 18px; background:#1a1a1a; border:1px solid #2d2d2d; border-radius:12px; }
.stat-card-label { font-size:11.5px; font-weight:500; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; margin-bottom:8px; }
.stat-card-value { font-size:28px; font-weight:700; color:#f5f5f5; line-height:1; }
.stat-card-sub   { font-size:12px; color:#4b5563; margin-top:5px; }
.stat-card.c-amber  .stat-card-value { color:#f59e0b; }
.stat-card.c-green  .stat-card-value { color:#22c55e; }
.stat-card.c-red    .stat-card-value { color:#ef4444; }
.stat-card.c-purple .stat-card-value { color:#a855f7; }
.stat-card.c-blue   .stat-card-value { color:#3b82f6; }

/* ── Charts ── */
.charts-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:22px; }
@media (max-width: 900px) { .charts-row { grid-template-columns:1fr; } }
.chart-card { background:#1a1a1a; border:1px solid #2d2d2d; border-radius:12px; padding:18px 18px 14px; }
.chart-title { font-size:13px; font-weight:600; color:#f5f5f5; margin-bottom:14px; }

/* ── Table ── */
.section-title { font-size:13px; font-weight:600; color:#f5f5f5; margin-bottom:10px; }
.data-table { width:100%; border-collapse:collapse; }
.data-table th { padding:9px 12px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#4b5563; text-align:left; border-bottom:1px solid #222; white-space:nowrap; }
.data-table td { padding:10px 12px; font-size:13px; color:#d1d5db; border-bottom:1px solid #1a1a1a; }
.data-table .order-num { font-size:11.5px; font-weight:700; color:#f59e0b; }
.cost-val { font-weight:600; color:#f59e0b; }
</style>
@endpush

@section('content')
{{-- ── Date Range Filter ── --}}
<form method="GET" action="{{ route('reports.index') }}">
    <div class="reports-filter">
        <span class="filter-label">Date range:</span>
        <input type="date" name="date_from" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}" class="filter-date">
        <span style="color:#4b5563;font-size:13px;">to</span>
        <input type="date" name="date_to"   value="{{ request('date_to',   now()->format('Y-m-d')) }}" class="filter-date">
        <button type="submit" class="btn-filter">Apply</button>
        <a href="{{ route('reports.index') }}" style="font-size:12.5px;color:#6b7280;text-decoration:none;margin-left:4px;">Reset</a>
        <span style="margin-left:auto;font-size:12px;color:#4b5563;">
            {{ $dateFrom->format('M d') }} – {{ $dateTo->format('M d, Y') }}
        </span>
    </div>
</form>

{{-- ── Stats Cards ── --}}
<div class="stats-grid">
    <div class="stat-card c-blue">
        <div class="stat-card-label">Active Orders</div>
        <div class="stat-card-value">{{ $totalActive }}</div>
        <div class="stat-card-sub">Currently in pipeline</div>
    </div>
    <div class="stat-card c-red">
        <div class="stat-card-label">Delayed</div>
        <div class="stat-card-value">{{ $delayed }}</div>
        <div class="stat-card-sub">Past preferred delivery date</div>
    </div>
    <div class="stat-card c-green">
        <div class="stat-card-label">Delivered</div>
        <div class="stat-card-value">{{ $deliveredInRange }}</div>
        <div class="stat-card-sub">In selected range</div>
    </div>
    <div class="stat-card c-red">
        <div class="stat-card-label">Cancelled</div>
        <div class="stat-card-value">{{ $cancelledInRange }}</div>
        <div class="stat-card-sub">In selected range</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Total Archived</div>
        <div class="stat-card-value">{{ $totalArchived }}</div>
        <div class="stat-card-sub">All time</div>
    </div>
    <div class="stat-card c-amber">
        <div class="stat-card-label">Orders in Range</div>
        <div class="stat-card-value">{{ $totalInRange }}</div>
        <div class="stat-card-sub">Created in period</div>
    </div>
</div>

{{-- ── Charts ── --}}
<div class="charts-row">
    <div class="chart-card">
        <div class="chart-title">Orders by Status</div>
        <canvas id="barChart" height="220"></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-title">Orders per Day</div>
        <canvas id="lineChart" height="220"></canvas>
    </div>
</div>

{{-- ── Outsourced Deliveries (admin / fleet) ── --}}
@if(auth()->user()->isAdmin() || auth()->user()->hasPermission('view_delivery_cost'))
<div style="background:#1a1a1a;border:1px solid #2d2d2d;border-radius:12px;padding:18px 0 0;overflow:hidden;">
    <div style="padding:0 18px 12px;display:flex;align-items:center;justify-content:space-between;">
        <p class="section-title" style="margin:0;">Outsourced Deliveries</p>
        <span style="font-size:13px;color:#f59e0b;font-weight:600;">
            Total cost: {{ number_format($outsourcedTotal, 3) }} JD
        </span>
    </div>
    @if($outsourcedOrders->isEmpty())
        <div style="padding:28px;text-align:center;color:#4b5563;font-size:13px;">No outsourced deliveries in this period.</div>
    @else
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Driver / Company</th>
                    <th>Cost (JD)</th>
                    <th>Dispatched</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outsourcedOrders as $o)
                <tr>
                    <td><span class="order-num">{{ $o->order_number }}</span></td>
                    <td>{{ $o->customer?->name }}</td>
                    <td>{{ $o->outsourced_driver_name }}</td>
                    <td><span class="cost-val">{{ number_format($o->outsourced_delivery_cost, 3) }}</span></td>
                    <td style="font-size:11.5px;color:#4b5563;">{{ $o->dispatched_at?->format('M d, Y') }}</td>
                    <td>
                        <span style="display:inline-flex;align-items:center;padding:3px 9px;border-radius:999px;font-size:11.5px;font-weight:600;
                            background:{{ $o->status==='delivered' ? 'rgba(34,197,94,.12)' : 'rgba(239,68,68,.12)' }};
                            color:{{ $o->status==='delivered' ? '#4ade80' : '#f87171' }};">
                            {{ $o->status_label }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color         = '#6b7280';
Chart.defaults.borderColor   = '#222';
Chart.defaults.font.family   = "Inter, sans-serif";
Chart.defaults.font.size     = 12;

// ── Bar chart: orders by status ────────────────────────────────────────────────
const BY_STATUS = @json($byStatus);
const BAR_COLORS = {
    new:'#3b82f6', packing:'#f59e0b', dispatch:'#f97316',
    picked_up:'#a855f7', delivered:'#22c55e', cancelled:'#ef4444'
};
const STATUS_LABELS_MAP = {
    new:'New', packing:'Packing', dispatch:'Dispatch',
    picked_up:'Picked Up', delivered:'Delivered', cancelled:'Cancelled'
};

new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: BY_STATUS.map(r => STATUS_LABELS_MAP[r.status] || r.status),
        datasets: [{
            data:            BY_STATUS.map(r => r.total),
            backgroundColor: BY_STATUS.map(r => BAR_COLORS[r.status] || '#6b7280'),
            borderRadius:    5,
            borderWidth:     0,
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        plugins: { legend: { display:false }, tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} orders` } } },
        scales: {
            x: { grid: { color:'rgba(255,255,255,.04)' }, ticks: { color:'#6b7280' } },
            y: { beginAtZero:true, grid: { color:'rgba(255,255,255,.06)' }, ticks: { color:'#6b7280', precision:0 } }
        }
    }
});

// ── Line chart: orders per day ─────────────────────────────────────────────────
const PER_DAY = @json($perDay);
new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels:   PER_DAY.map(r => {
            const d = new Date(r.date + 'T00:00:00');
            return d.toLocaleDateString('en-GB', {month:'short', day:'numeric'});
        }),
        datasets: [{
            data:            PER_DAY.map(r => r.total),
            borderColor:     '#f59e0b',
            backgroundColor: 'rgba(245,158,11,0.08)',
            fill:            true,
            tension:         0.35,
            pointRadius:     3,
            pointBackgroundColor: '#f59e0b',
        }]
    },
    options: {
        responsive:true, maintainAspectRatio:false,
        plugins: { legend: { display:false }, tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} orders` } } },
        scales: {
            x: { grid: { color:'rgba(255,255,255,.04)' }, ticks: { color:'#6b7280', maxRotation:45 } },
            y: { beginAtZero:true, grid: { color:'rgba(255,255,255,.06)' }, ticks: { color:'#6b7280', precision:0 } }
        }
    }
});
</script>
@endpush
