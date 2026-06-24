@extends('layouts.app')
@section('title', 'Archived Orders — ' . config('app.name'))
@section('page-title', 'Archived Orders')
@section('page-subtitle', 'Orders that have been archived after delivery or cancellation')

@push('styles')
<style>
/* ── Filters ── */
.filter-bar { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
.filter-bar .search-box { width: 360px; position:relative; }
.filter-bar .search-box input { width:100%; padding:7px 10px 7px 32px; background:#1e1e1e; border:1px solid #2d2d2d; border-radius:8px; font-size:13px; color:#f5f5f5; outline:none; transition:border-color .15s; height:34px; box-sizing:border-box; }
.filter-bar .search-box input:focus { border-color:#f59e0b; }
.filter-bar .search-box svg { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#4b5563; pointer-events:none; }
.btn-filter { padding:7px 14px; background:#f59e0b; color:#111; border-radius:8px; font-size:13px; font-weight:600; border:none; cursor:pointer; height:34px; box-sizing:border-box; }
.btn-filter:hover { background:#d97706; }
.btn-reset { padding:7px 12px; background:#1e1e1e; border:1px solid #2d2d2d; border-radius:8px; font-size:13px; color:#6b7280; cursor:pointer; height:34px; box-sizing:border-box; }
.btn-reset:hover { background:#2a2a2a; color:#9ca3af; }

/* Custom Date Range Picker */
.custom-date-range {
    display: inline-flex;
    align-items: center;
    background: #1e1e1e;
    border: 1px solid #2d2d2d;
    border-radius: 8px;
    padding: 0 12px;
    height: 34px;
    box-sizing: border-box;
    transition: border-color 0.15s;
}
.custom-date-range:focus-within {
    border-color: #f59e0b;
}
.custom-date-range .calendar-icon {
    margin-right: 8px;
    flex-shrink: 0;
}
.custom-date-range .date-input {
    background: transparent;
    border: none;
    font-size: 13px;
    color: #f5f5f5;
    outline: none;
    padding: 0;
    width: 125px;
    cursor: pointer;
}
.custom-date-range .date-input::-webkit-calendar-picker-indicator {
    filter: invert(1);
    opacity: 0.5;
    margin-left: 4px;
    cursor: pointer;
}
.custom-date-range .date-input::-webkit-calendar-picker-indicator:hover {
    opacity: 0.8;
}
.custom-date-range .range-separator {
    color: #4b5563;
    font-size: 12px;
    margin: 0 8px;
    user-select: none;
}

/* ── Table ── */
.data-table { width:100%; border-collapse:collapse; }
.data-table th { padding:9px 12px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#4b5563; text-align:left; border-bottom:1px solid #222; white-space:nowrap; }
.data-table td { padding:11px 12px; font-size:13px; color:#d1d5db; border-bottom:1px solid #1a1a1a; vertical-align:middle; }
.data-table tr:hover td { background:#1e1e1e; cursor:pointer; }
.data-table .order-num { font-size:11.5px; font-weight:700; color:#f59e0b; }
.data-table .customer-name { font-weight:600; color:#f5f5f5; }
.data-table .meta { font-size:11.5px; color:#4b5563; }

/* ── Status badge ── */
.status-badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:999px; font-size:11.5px; font-weight:600; }
.s-delivered { background:rgba(34,197,94,.12);   color:#4ade80; }
.s-cancelled { background:rgba(239,68,68,.12);   color:#f87171; }
.s-new       { background:rgba(59,130,246,.12);  color:#60a5fa; }
.s-packing   { background:rgba(245,158,11,.12);  color:#fbbf24; }
.s-dispatch  { background:rgba(249,115,22,.12);  color:#fb923c; }
.s-picked_up { background:rgba(168,85,247,.12);  color:#c084fc; }

/* ── Empty / loading ── */
.empty-state { text-align:center; padding:64px 20px; color:#4b5563; }
.empty-state svg { margin:0 auto 12px; display:block; }
.empty-state p { font-size:14px; margin-top:4px; }

/* ── Modal (reused from kanban) ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.72); z-index:200; align-items:center; justify-content:center; padding:20px; }
.modal-overlay.open { display:flex; }
.modal-box { background:#1a1a1a; border:1px solid #2d2d2d; border-radius:14px; width:100%; max-width:640px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.6); }
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #2d2d2d; flex-shrink:0; }
.modal-title { font-size:15px; font-weight:600; color:#f5f5f5; }
.modal-close { width:30px; height:30px; display:flex; align-items:center; justify-content:center; border-radius:7px; border:none; background:transparent; color:#6b7280; cursor:pointer; }
.modal-close:hover { background:#2a2a2a; color:#f5f5f5; }
.modal-body { flex:1; overflow-y:auto; padding:18px 20px; }
.modal-footer { display:flex; align-items:center; gap:8px; padding:14px 20px; border-top:1px solid #2d2d2d; flex-shrink:0; }
.modal-footer .spacer { flex:1; }
.btn-secondary { padding:7px 14px; background:#222; border:1px solid #2d2d2d; border-radius:8px; font-size:13px; font-weight:500; color:#9ca3af; cursor:pointer; }
.btn-secondary:hover { background:#2a2a2a; color:#f5f5f5; }
.btn-primary { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; background:#f59e0b; color:#111; border-radius:8px; font-size:13px; font-weight:600; border:none; cursor:pointer; }
.btn-primary:hover { background:#d97706; }
.btn-amber-outline { padding:7px 14px; background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.3); border-radius:8px; font-size:13px; font-weight:500; color:#f59e0b; cursor:pointer; }
.btn-amber-outline:hover { background:rgba(245,158,11,.15); }
.detail-row { display:flex; gap:8px; margin-bottom:7px; }
.detail-label { font-size:12px; color:#6b7280; width:140px; flex-shrink:0; }
.detail-value { font-size:12.5px; color:#e5e7eb; font-weight:500; }
.item-row { display:flex; align-items:center; gap:8px; padding:7px 0; border-bottom:1px solid #1e1e1e; font-size:12.5px; }
.item-row:last-child { border-bottom:none; }
.item-name { font-weight:600; color:#e5e7eb; flex:1; }
.item-meta { color:#9ca3af; font-size:11.5px; }
.item-qty  { font-weight:700; color:#f59e0b; font-size:13px; min-width:26px; text-align:right; }
.log-item { position:relative; padding-left:18px; padding-bottom:12px; }
.log-item::before { content:''; position:absolute; left:4px; top:5px; width:8px; height:8px; border-radius:50%; background:#222; border:2px solid #3d3d3d; }
.log-item::after  { content:''; position:absolute; left:7px; top:13px; bottom:0; width:1px; background:#222; }
.log-item:last-child { padding-bottom:0; } .log-item:last-child::after { display:none; }
.log-meta { font-size:10.5px; color:#4b5563; margin-bottom:2px; }
.log-text { font-size:12.5px; color:#9ca3af; }
.form-input { width:100%; padding:8px 11px; background:#141414; border:1px solid #2d2d2d; border-radius:8px; font-size:13.5px; color:#f5f5f5; outline:none; font-family:inherit; }
.form-input:focus { border-color:#f59e0b; }
textarea.form-input { resize:vertical; }
.note-area { margin-top:10px; display:flex; gap:8px; align-items:flex-end; }
.note-area textarea { flex:1; min-height:52px; }
.archived-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:999px; font-size:11px; font-weight:600; background:rgba(107,114,128,.15); color:#6b7280; }

::-webkit-scrollbar { width:5px; height:5px; }
::-webkit-scrollbar-track { background:transparent; }
::-webkit-scrollbar-thumb { background:#2d2d2d; border-radius:3px; }
</style>
@endpush

@section('content')
<div>
    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('archived.index') }}" id="searchForm">
        <div class="filter-bar">
            {{-- LEFT: Orders Count --}}
            <span style="font-size: 13.5px; color: #9ca3af; font-weight: 500; white-space: nowrap;">
                {{ $orders->count() }} {{ Str::plural('order', $orders->count()) }} found
            </span>

            {{-- RIGHT: Filters Group --}}
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-left: auto;">
                <div class="search-box">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search name or order #…" autocomplete="off"
                           oninput="debouncedSearch()">
                </div>
                
                <div class="custom-date-range">
                    <svg class="calendar-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2.5">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="date-input" title="Archived from">
                    <span class="range-separator">to</span>
                    <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="date-input" title="Archived to">
                </div>
                
                <button type="submit" class="btn-filter">Filter</button>
                
                @if(request()->hasAny(['search','date_from','date_to']))
                    <a href="{{ route('archived.index') }}" class="btn-reset" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center; height: 34px; box-sizing: border-box;">Clear</a>
                @endif
            </div>
        </div>
    </form>

    {{-- ── Table ── --}}
    <div style="background:#1a1a1a;border:1px solid #2d2d2d;border-radius:12px;overflow:hidden;">
        @if($orders->isEmpty())
        <div class="empty-state">
            <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#2d2d2d" stroke-width="1.5"><path stroke-linecap="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            <p style="color:#4b5563;font-size:15px;font-weight:500;">No archived orders</p>
            <p>Orders appear here once they're archived after delivery or cancellation.</p>
        </div>
        @else
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Archived</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr onclick="openArchivedDetail({{ $order->id }})">
                        <td><span class="order-num">{{ $order->order_number }}</span></td>
                        <td><span class="customer-name">{{ $order->customer?->name }}</span></td>
                        <td>
                            <span style="font-size:12.5px;">{{ $order->items_count }} {{ Str::plural('item', $order->items_count) }}</span>
                        </td>
                        <td><span class="status-badge s-{{ $order->status }}">{{ $order->status_label }}</span></td>
                        <td><span class="meta">{{ $order->order_date?->format('M d, Y') }}</span></td>
                        <td><span class="meta">{{ $order->archived_at?->diffForHumans() }}</span></td>
                        <td><span class="meta">{{ $order->creator?->full_name }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- ── Detail Modal ── --}}
<div class="modal-overlay" id="archivedDetailModal" onclick="if(event.target===this)closeArchivedDetail()">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Order Details</span>
            <button class="modal-close" onclick="closeArchivedDetail()">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body" id="archivedDetailBody">
            <div style="text-align:center;padding:40px 0;color:#4b5563;font-size:13px;">Loading…</div>
        </div>
        <div class="modal-footer" id="archivedDetailFooter"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let searchTimer;
function debouncedSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        document.getElementById('searchForm').submit();
    }, 400);
}

const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const IS_ADMIN = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
const CAN_VIEW_COST = {{ (auth()->user()->isAdmin() || auth()->user()->hasPermission('view_delivery_cost')) ? 'true' : 'false' }};

async function openArchivedDetail(orderId) {
    const modal = document.getElementById('archivedDetailModal');
    modal.classList.add('open');
    document.getElementById('archivedDetailBody').innerHTML   = '<div style="text-align:center;padding:40px 0;color:#4b5563;font-size:13px;">Loading…</div>';
    document.getElementById('archivedDetailFooter').innerHTML = '';
    try {
        const r    = await fetch(`/kanban/orders/${orderId}`, { headers: { 'X-CSRF-TOKEN': CSRF } });
        const order = await r.json();
        renderArchivedDetail(order);
    } catch {
        document.getElementById('archivedDetailBody').innerHTML = '<p style="color:#ef4444;text-align:center;padding:30px;">Failed to load.</p>';
    }
}
function closeArchivedDetail() { document.getElementById('archivedDetailModal').classList.remove('open'); }

function renderArchivedDetail(order) {
    const dispatchInfo = order.driver_user_name
        ? `<div class="detail-row"><span class="detail-label">Driver</span><span class="detail-value">${escHtml(order.driver_user_name)}</span></div>`
        : order.outsourced_driver_name
            ? `<div class="detail-row"><span class="detail-label">Outsourced Driver</span><span class="detail-value">${escHtml(order.outsourced_driver_name)}${(CAN_VIEW_COST && order.outsourced_delivery_cost != null) ? ' — <span style="color:#f59e0b">'+parseFloat(order.outsourced_delivery_cost).toFixed(3)+' JD</span>' : ''}</span></div>`
            : '';

    const itemsHtml = order.items.length
        ? order.items.map(i => `<div class="item-row"><span class="item-name">${escHtml(i.product_name)}</span><span class="item-meta">${escHtml(i.filling_name)} · ${escHtml(i.grind_name)}</span><span class="item-qty">×${i.qty}</span></div>`).join('')
        : '<p style="color:#4b5563;font-size:12.5px;">No items</p>';

    const logsHtml = (order.logs || []).length
        ? order.logs.map(l => `<div class="log-item"><div class="log-meta">${escHtml(l.user_name||'?')} · ${fmtAgo(new Date(l.created_at))}</div><div class="log-text">${fmtLogText(l)}</div></div>`).join('')
        : '<p style="color:#4b5563;font-size:12px;">No activity.</p>';

    document.getElementById('archivedDetailBody').innerHTML = `
        <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:14px;">
            <div style="flex:1;">
                <div style="font-size:17px;font-weight:700;color:#f5f5f5;margin-bottom:5px;">${escHtml(order.customer_name)}</div>
                <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
                    <span style="font-size:12px;font-weight:600;color:#6b7280;">${order.order_number}</span>
                    <span class="status-badge s-${order.status}">${order.status_label}</span>
                    <span class="archived-badge"><svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>Archived</span>
                    <span style="font-size:11px;color:#4b5563;">${fmtAgo(new Date(order.archived_at))}</span>
                </div>
            </div>
        </div>

        <div style="background:#161616;border:1px solid #222;border-radius:9px;padding:12px 14px;margin-bottom:14px;">
            <div class="detail-row"><span class="detail-label">Phone</span><span class="detail-value">${escHtml(order.phone||'—')}</span></div>
            <div class="detail-row"><span class="detail-label">Delivery Address</span><span class="detail-value">${escHtml(order.delivery_address||'—')}</span></div>
            ${order.preferred_delivery_date?`<div class="detail-row"><span class="detail-label">Preferred Delivery</span><span class="detail-value">${fmtDate(order.preferred_delivery_date)}${order.preferred_delivery_time?' at '+fmtTime(order.preferred_delivery_time):''}</span></div>`:''}
            ${order.internal_notes?`<div class="detail-row"><span class="detail-label">Notes</span><span class="detail-value">${escHtml(order.internal_notes)}</span></div>`:''}
            <div class="detail-row"><span class="detail-label">Created By</span><span class="detail-value">${escHtml(order.created_by_name||'?')}</span></div>
            ${dispatchInfo}
        </div>

        <div style="margin-bottom:14px;">
            <div style="font-size:11px;font-weight:600;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Order Items</div>
            ${itemsHtml}
        </div>

        <div>
            <div style="font-size:11px;font-weight:600;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Activity & Notes</div>
            ${logsHtml}
            <div class="note-area" id="archNoteArea">
                <textarea class="form-input" id="archNoteText" placeholder="Add a note…" rows="2"></textarea>
                <button class="btn-secondary" onclick="submitArchNote(${order.id})">Add Note</button>
            </div>
        </div>`;

    // Footer
    const footer = document.getElementById('archivedDetailFooter');
    footer.innerHTML = '';
    const btn = (label, cls, fn, ml=false) => {
        const b = document.createElement('button');
        b.className = cls; b.textContent = label;
        if (ml) b.style.marginLeft = 'auto';
        b.onclick = fn;
        footer.appendChild(b);
    };
    btn('Close', 'btn-secondary', closeArchivedDetail);
    if (IS_ADMIN) {
        btn('Restore', 'btn-amber-outline', () => doRestore(order.id), true);
    }
}

async function submitArchNote(orderId) {
    const txt = document.getElementById('archNoteText').value.trim();
    if (!txt) return;
    const r = await fetch(`/kanban/orders/${orderId}/notes`, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ note: txt })
    }).then(r => r.json());
    if (r.success) { document.getElementById('archNoteText').value = ''; openArchivedDetail(orderId); }
}

async function doRestore(orderId) {
    const r = await fetch(`/kanban/orders/${orderId}/restore`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(r => r.json());
    if (r.success) {
        closeArchivedDetail();
        const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
        if (row) row.remove();
        window.location.reload();
    } else { alert('Failed to restore.'); }
}

function fmtAgo(d) {
    const s = Math.floor((Date.now() - d) / 1000);
    if (s < 60) return 'Just now';
    if (s < 3600) return Math.floor(s/60) + 'm ago';
    if (s < 86400) return Math.floor(s/3600) + 'h ago';
    return Math.floor(s/86400) + 'd ago';
}
function fmtDate(d) { return new Date(d + 'T00:00:00').toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'}); }
function fmtTime(t) { const [h,m]=t.split(':'); const hr=parseInt(h); return `${hr%12||12}:${m} ${hr<12?'AM':'PM'}`; }
function fmtLogText(l) {
    if (l.action==='created')       return 'Order created';
    if (l.action==='status_changed') return `Status: <strong>${escHtml(l.from_status)}</strong> → <strong>${escHtml(l.to_status)}</strong>`;
    if (l.action==='dispatched')    return `Dispatched`;
    if (l.action==='edited')        return 'Order edited';
    if (l.action==='archived')      return 'Order archived';
    if (l.action==='restored')      return 'Order restored';
    if (l.action==='note_added' && l.note) return escHtml(l.note);
    return l.action;
}
function escHtml(s) { return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
@endpush
