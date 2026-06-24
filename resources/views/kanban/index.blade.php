@extends('layouts.app')
@section('title', 'Kanban — ' . config('app.name'))
@section('content-class', '')

@push('styles')
<style>
/* ====== LAYOUT ====== */
.kanban-wrap { display:flex; flex-direction:column; height:calc(100vh - 60px); overflow:hidden; }
.kanban-toolbar { flex-shrink:0; display:flex; align-items:center; gap:10px; padding:10px 16px; border-bottom:1px solid #222; background:#141414; flex-wrap:wrap; }
.kanban-board { flex:1; display:flex; gap:10px; padding:14px 16px; overflow-x:auto; align-items:stretch; }

/* ====== COLUMN ====== */
.kanban-col { flex-shrink:0; width:272px; display:flex; flex-direction:column; border-radius:12px; background:#1a1a1a; border:1px solid #2a2a2a; overflow:hidden; }
.kanban-col-header { display:flex; align-items:center; justify-content:space-between; padding:10px 13px; border-radius:11px 11px 0 0; border-bottom:1px solid #222; flex-shrink:0; }
.kanban-col-title { font-size:12.5px; font-weight:600; letter-spacing:.01em; }
.kanban-col-count { font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; background:rgba(255,255,255,.07); color:#9ca3af; }
.kanban-cards { flex:1; overflow-y:auto; padding:8px; min-height:60px; display:flex; flex-direction:column; gap:7px; }

.col-new .kanban-col-header      { border-top:3px solid #3b82f6; } .col-new .kanban-col-title      { color:#60a5fa; }
.col-packing .kanban-col-header  { border-top:3px solid #f59e0b; } .col-packing .kanban-col-title  { color:#fbbf24; }
.col-dispatch .kanban-col-header { border-top:3px solid #f97316; } .col-dispatch .kanban-col-title { color:#fb923c; }
.col-picked_up .kanban-col-header{ border-top:3px solid #a855f7; } .col-picked_up .kanban-col-title{ color:#c084fc; }
.col-delivered .kanban-col-header{ border-top:3px solid #22c55e; } .col-delivered .kanban-col-title{ color:#4ade80; }
.col-cancelled .kanban-col-header{ border-top:3px solid #ef4444; } .col-cancelled .kanban-col-title{ color:#f87171; }

/* ====== CARDS ====== */
.order-card { background:#222; border:1px solid #2d2d2d; border-radius:9px; padding:11px 13px; cursor:pointer; transition:border-color .15s,background .15s,box-shadow .15s; user-select:none; }
.order-card:hover { border-color:#3d3d3d; background:#262626; box-shadow:0 2px 8px rgba(0,0,0,.3); }
.order-card.sortable-ghost { opacity:.35; }
.order-card.sortable-drag  { opacity:.98; box-shadow:0 8px 24px rgba(0,0,0,.5); transform:rotate(1.5deg); }
.card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:7px; }
.card-order-num { font-size:11px; font-weight:700; color:#f59e0b; letter-spacing:.03em; }
.card-time { font-size:10.5px; color:#4b5563; }
.card-customer { font-size:13.5px; font-weight:600; color:#f5f5f5; margin-bottom:5px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.card-items { font-size:11.5px; color:#9ca3af; margin-bottom:7px; line-height:1.5; }
.card-footer { display:flex; align-items:center; gap:5px; font-size:11px; color:#6b7280; }

/* ====== TOOLBAR ====== */
.search-box { position:relative; flex:1; max-width:300px; }
.search-box input { width:100%; padding:7px 10px 7px 32px; background:#1e1e1e; border:1px solid #2d2d2d; border-radius:8px; font-size:13px; color:#f5f5f5; outline:none; transition:border-color .15s; }
.search-box input:focus { border-color:#f59e0b; }
.search-box svg { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#4b5563; pointer-events:none; }
.stat-pill { display:flex; align-items:center; gap:5px; padding:5px 10px; background:#1e1e1e; border:1px solid #2d2d2d; border-radius:999px; font-size:12px; color:#9ca3af; white-space:nowrap; }
.stat-pill strong { color:#f5f5f5; }
.refresh-btn { display:inline-flex; align-items:center; gap:5px; padding:5px 10px; background:#1e1e1e; border:1px solid #2d2d2d; border-radius:999px; font-size:11.5px; color:#6b7280; cursor:pointer; transition:border-color .15s,color .15s; white-space:nowrap; }
.refresh-btn:hover { border-color:#444; color:#9ca3af; }
.btn-primary { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; background:#f59e0b; color:#111; border-radius:8px; font-size:13px; font-weight:600; border:none; cursor:pointer; transition:background .15s; white-space:nowrap; }
.btn-primary:hover { background:#d97706; }
.btn-primary:disabled { opacity:.5; cursor:default; }

/* ====== MODAL ====== */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.72); z-index:200; align-items:center; justify-content:center; padding:20px; }
.modal-overlay.open { display:flex; }
.modal-box { background:#1a1a1a; border:1px solid #2d2d2d; border-radius:14px; width:100%; max-width:640px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.6); }
.modal-box.wide { max-width:760px; }
.modal-box.narrow { max-width:460px; }
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #2d2d2d; flex-shrink:0; }
.modal-title { font-size:15px; font-weight:600; color:#f5f5f5; }
.modal-close { width:30px; height:30px; display:flex; align-items:center; justify-content:center; border-radius:7px; border:none; background:transparent; color:#6b7280; cursor:pointer; transition:background .1s,color .1s; }
.modal-close:hover { background:#2a2a2a; color:#f5f5f5; }
.modal-body { flex:1; overflow-y:auto; padding:18px 20px; }
.modal-footer { display:flex; align-items:center; gap:8px; padding:14px 20px; border-top:1px solid #2d2d2d; flex-shrink:0; }
.modal-footer .spacer { flex:1; }

/* ====== FORM ====== */
.form-group { margin-bottom:14px; }
.form-label { display:block; font-size:12px; font-weight:500; color:#9ca3af; margin-bottom:5px; }
.form-label .req { color:#ef4444; margin-left:2px; }
.form-input,.form-select { width:100%; padding:8px 11px; background:#141414; border:1px solid #2d2d2d; border-radius:8px; font-size:13.5px; color:#f5f5f5; outline:none; transition:border-color .15s; font-family:inherit; }
.form-input:focus,.form-select:focus { border-color:#f59e0b; }
.form-input:disabled,.form-select:disabled { opacity:.45; cursor:default; }
textarea.form-input { resize:vertical; min-height:72px; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.form-grid .full { grid-column:1/-1; }

/* Search dropdown */
.search-dropdown { position:relative; }
.sdrop-list { display:none; position:absolute; top:calc(100% + 3px); left:0; right:0; background:#1e1e1e; border:1px solid #2d2d2d; border-radius:8px; max-height:190px; overflow-y:auto; z-index:310; box-shadow:0 8px 24px rgba(0,0,0,.4); }
.sdrop-list.open { display:block; }
.sdrop-item { padding:8px 12px; font-size:13px; color:#d1d5db; cursor:pointer; }
.sdrop-item:hover { background:#2a2a2a; color:#f5f5f5; }
.sdrop-empty { padding:10px 12px; font-size:12.5px; color:#4b5563; text-align:center; }

/* Items table */
.items-table { width:100%; border-collapse:separate; border-spacing:0 5px; }
.items-table th { font-size:10.5px; font-weight:600; color:#4b5563; text-transform:uppercase; letter-spacing:.05em; padding:0 5px 3px; text-align:left; }
.items-table td { padding:0 3px; vertical-align:middle; }
.rm-btn { width:26px; height:26px; display:inline-flex; align-items:center; justify-content:center; border-radius:5px; border:none; background:transparent; color:#4b5563; cursor:pointer; transition:background .1s,color .1s; }
.rm-btn:hover { background:rgba(239,68,68,.1); color:#ef4444; }
.btn-add-item { display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border:1px dashed #2d2d2d; border-radius:7px; font-size:12.5px; color:#6b7280; background:transparent; cursor:pointer; transition:border-color .15s,color .15s; margin-top:4px; }
.btn-add-item:hover { border-color:#f59e0b; color:#f59e0b; }

/* Buttons */
.btn-secondary { padding:7px 14px; background:#222; border:1px solid #2d2d2d; border-radius:8px; font-size:13px; font-weight:500; color:#9ca3af; cursor:pointer; transition:background .1s,color .1s; white-space:nowrap; }
.btn-secondary:hover { background:#2a2a2a; color:#f5f5f5; }
.btn-danger { padding:7px 14px; background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25); border-radius:8px; font-size:13px; font-weight:500; color:#ef4444; cursor:pointer; transition:background .15s; white-space:nowrap; }
.btn-danger:hover { background:rgba(239,68,68,.2); }
.btn-amber-outline { padding:7px 14px; background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.3); border-radius:8px; font-size:13px; font-weight:500; color:#f59e0b; cursor:pointer; transition:background .15s; white-space:nowrap; }
.btn-amber-outline:hover { background:rgba(245,158,11,.15); }

/* Order detail */
.detail-row { display:flex; gap:8px; margin-bottom:7px; }
.detail-label { font-size:12px; color:#6b7280; width:140px; flex-shrink:0; }
.detail-value { font-size:12.5px; color:#e5e7eb; font-weight:500; }
.status-badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:999px; font-size:11.5px; font-weight:600; }
.s-new       { background:rgba(59,130,246,.12); color:#60a5fa; }
.s-packing   { background:rgba(245,158,11,.12); color:#fbbf24; }
.s-dispatch  { background:rgba(249,115,22,.12); color:#fb923c; }
.s-picked_up { background:rgba(168,85,247,.12);  color:#c084fc; }
.s-delivered { background:rgba(34,197,94,.12);   color:#4ade80; }
.s-cancelled { background:rgba(239,68,68,.12);   color:#f87171; }
.archived-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:999px; font-size:11px; font-weight:600; background:rgba(107,114,128,.15); color:#6b7280; }
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
.log-text { font-size:12.5px; color:#9ca3af; line-height:1.5; }
.note-area { margin-top:10px; display:flex; gap:8px; align-items:flex-end; }
.note-area textarea { flex:1; min-height:52px; }

/* Driver type toggle */
.driver-type-opts { display:flex; gap:8px; margin-bottom:16px; }
.driver-opt { flex:1; display:flex; align-items:center; gap:8px; padding:10px 12px; border:1px solid #2d2d2d; border-radius:9px; cursor:pointer; transition:border-color .15s,background .15s; }
.driver-opt.active { border-color:rgba(245,158,11,.5); background:rgba(245,158,11,.06); }
.driver-opt input[type=radio] { accent-color:#f59e0b; }
.driver-opt-label { font-size:13px; font-weight:500; color:#d1d5db; }

::-webkit-scrollbar { width:5px; height:5px; }
::-webkit-scrollbar-track { background:transparent; }
::-webkit-scrollbar-thumb { background:#2d2d2d; border-radius:3px; }

/* ====== EMPTY STATE ====== */
.col-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; padding:28px 14px; flex:1; }
.col-empty-label { font-size:12px; color:#3d3d3d; text-align:center; line-height:1.5; }
.col-new-empty .col-empty-label { color:#4b5563; }

/* ====== DATE/TIME INPUT WRAPPERS ====== */
.dt-wrap { position:relative; }
.dt-wrap .dt-icon { position:absolute; left:9px; top:50%; transform:translateY(-50%); color:#4b5563; pointer-events:none; z-index:1; }
.dt-wrap input { padding-left:32px !important; }
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator { filter:invert(0.45); cursor:pointer; }

/* ====== FORM SECTION BLOCKS ====== */
.form-section { background:#141414; border:1px solid #222; border-radius:9px; padding:13px 14px 6px; margin-bottom:14px; }
.form-section-title { font-size:10.5px; font-weight:600; color:#4b5563; text-transform:uppercase; letter-spacing:.07em; margin-bottom:10px; }
.form-grid-3 { display:grid; grid-template-columns:1.2fr 1fr 1.3fr; gap:12px; }
.form-grid-3e { display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; }

/* ====== INLINE FIELD ERRORS ====== */
.field-error { display:block; font-size:11.5px; color:#ef4444; margin-top:3px; min-height:0; }
.field-error:empty { display:none; }
.form-input.has-error,.form-select.has-error { border-color:#ef4444 !important; }

/* ====== SEARCHABLE SELECT (items table) ====== */
.ss-wrap { position:relative; }
.ss-input-row { position:relative; }
.ss-caret { position:absolute; right:7px; top:50%; transform:translateY(-50%); color:#4b5563; pointer-events:none; }
.ss-drop { display:none; position:absolute; top:calc(100% + 2px); left:0; right:0; background:#1e1e1e; border:1px solid #2d2d2d; border-radius:8px; max-height:160px; overflow-y:auto; z-index:400; box-shadow:0 8px 24px rgba(0,0,0,.5); }
.ss-drop.open { display:block; }
.ss-option { padding:7px 10px; font-size:12.5px; color:#d1d5db; cursor:pointer; }
.ss-option:hover,.ss-option.focused { background:#2a2a2a; color:#f5f5f5; }

/* ====== DRAG CONFIRM MODAL ====== */
.confirm-modal-box { background:#1a1a1a; border:1px solid #2d2d2d; border-radius:14px; width:100%; max-width:380px; padding:24px 22px; box-shadow:0 20px 60px rgba(0,0,0,.6); }
.confirm-modal-icon { width:42px; height:42px; border-radius:50%; background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.25); display:flex; align-items:center; justify-content:center; margin:0 auto 14px; }
.confirm-modal-title { font-size:15px; font-weight:700; color:#f5f5f5; text-align:center; margin-bottom:6px; }
.confirm-modal-msg { font-size:13px; color:#9ca3af; text-align:center; margin-bottom:20px; line-height:1.6; }
.confirm-modal-actions { display:flex; gap:8px; justify-content:center; }
</style>
@endpush

@section('content')
<div class="kanban-wrap">

    {{-- Toolbar --}}
    <div class="kanban-toolbar">
        <div class="search-box">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="searchInput" placeholder="Search orders or customers…" autocomplete="off">
        </div>
        <div class="stat-pill">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
            Active: <strong id="activeCount">{{ $activeCount }}</strong>
        </div>
        <div class="stat-pill">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            Archived: <strong>{{ $archivedCount }}</strong>
        </div>
        <button class="refresh-btn" id="refreshBtn" onclick="handleRefresh()">
            <svg id="refreshIcon" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span id="refreshLabel">Just now</span>
        </button>
        <div style="margin-left:auto;">
            <button class="btn-primary" onclick="openOrderForm()">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                New Order
            </button>
        </div>
    </div>

    {{-- Board --}}
    <div class="kanban-board">
        @php $cols = ['new'=>'New Order','packing'=>'Preparation & Packing','dispatch'=>'Dispatch','picked_up'=>'Picked Up','delivered'=>'Delivered','cancelled'=>'Cancelled']; @endphp
        @foreach($cols as $key => $label)
        @php $colOrders = ($orders[$key] ?? collect()); @endphp
        <div class="kanban-col col-{{ $key }}">
            <div class="kanban-col-header">
                <span class="kanban-col-title">{{ $label }}</span>
                <span class="kanban-col-count" id="cnt-{{ $key }}">{{ $colOrders->count() }}</span>
            </div>
            <div class="kanban-cards" id="col-{{ $key }}" data-status="{{ $key }}">
                @foreach($colOrders as $order)
                @include('kanban._card', ['order' => $order])
                @endforeach
                {{-- Empty state --}}
                <div class="col-empty{{ $key === 'new' ? ' col-new-empty' : '' }}" id="empty-{{ $key }}"
                     style="{{ $colOrders->isNotEmpty() ? 'display:none;' : '' }}">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" style="color:#2d2d2d;">
                        @if($key === 'new')
                            <path stroke-linecap="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        @else
                            <path stroke-linecap="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        @endif
                    </svg>
                    @if($key === 'new')
                    <span class="col-empty-label">No new orders yet</span>
                    <button class="btn-primary" style="font-size:12px;padding:6px 12px;" onclick="openOrderForm()">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                        New Order
                    </button>
                    @else
                    <span class="col-empty-label">No {{ strtolower($label) }} orders</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
<script>
// ── Constants ────────────────────────────────────────────────────────────────
const CSRF        = document.querySelector('meta[name="csrf-token"]').content;
const PRODUCTS    = @json($products);
const FILLINGS    = @json($fillings);
const GRINDS      = @json($grinds);
const ME = {
    id:             {{ auth()->id() }},
    role:           '{{ auth()->user()->role?->slug ?? "" }}',
    isAdmin:        {{ auth()->user()->isAdmin() ? 'true' : 'false' }},
    canViewCost:    {{ (auth()->user()->isAdmin() || auth()->user()->hasPermission('view_delivery_cost')) ? 'true' : 'false' }},
};
const STATUS_LABELS = {
    new:'New Order', packing:'Preparation & Packing', dispatch:'Dispatch',
    picked_up:'Picked Up', delivered:'Delivered', cancelled:'Cancelled'
};

// ── Drag-and-drop ─────────────────────────────────────────────────────────────
let pendingDrag = null;

['new','packing','dispatch','picked_up','delivered','cancelled'].forEach(status => {
    const el = document.getElementById('col-' + status);
    if (!el) return;
    new Sortable(el, {
        group:'kanban', animation:150,
        ghostClass:'sortable-ghost', dragClass:'sortable-drag',
        onEnd(evt) {
            const id = evt.item.dataset.orderId;
            const ns = evt.to.dataset.status;
            const os = evt.from.dataset.status;
            if (os !== ns) {
                pendingDrag = { item: evt.item, from: evt.from, to: evt.to, oldIndex: evt.oldDraggableIndex, id, ns, os };
                document.getElementById('dragConfirmFrom').textContent = STATUS_LABELS[os];
                document.getElementById('dragConfirmTo').textContent   = STATUS_LABELS[ns];
                document.getElementById('dragConfirmModal').classList.add('open');
                updateEmptyState(os);
                updateEmptyState(ns);
            }
        }
    });
});

// ── Search ────────────────────────────────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.order-card').forEach(c => {
        const m = !q || c.dataset.customer.includes(q) || c.dataset.num.includes(q);
        c.style.display = m ? '' : 'none';
    });
    recountCols();
});

function recountCols() {
    ['new','packing','dispatch','picked_up','delivered','cancelled'].forEach(s => {
        updateEmptyState(s);
        const col = document.getElementById('col-' + s);
        const el  = document.getElementById('cnt-' + s);
        if (col && el) el.textContent = col.querySelectorAll('.order-card:not([style*="display: none"]):not([style*="display:none"])').length;
    });
}

function updateEmptyState(status) {
    const col   = document.getElementById('col-' + status);
    const empty = document.getElementById('empty-' + status);
    if (!col || !empty) return;
    const hasCards = col.querySelectorAll('.order-card:not([style*="display: none"]):not([style*="display:none"])').length > 0;
    empty.style.display = hasCards ? 'none' : '';
}

// ── Refresh ───────────────────────────────────────────────────────────────────
let refreshStart = Date.now();
function handleRefresh() {
    refreshStart = Date.now();
    location.reload();
}
setInterval(() => {
    const s = Math.floor((Date.now() - refreshStart) / 1000);
    document.getElementById('refreshLabel').textContent =
        s < 60 ? 'Just now' : s < 3600 ? Math.floor(s/60) + 'm ago' : Math.floor(s/3600) + 'h ago';
}, 30000);

// ── API helpers ───────────────────────────────────────────────────────────────
async function api(url, method = 'GET', body = null) {
    const opts = { method, headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF} };
    if (body) opts.body = JSON.stringify(body);
    const r = await fetch(url, opts);
    return r.json();
}

async function apiUpdateStatus(orderId, status) {
    const r = await api(`/kanban/orders/${orderId}/status`, 'PUT', {status});
    if (!r.success) { alert('Failed to update status.'); location.reload(); }
    recountCols();
}

function confirmDragMove() {
    if (!pendingDrag) return;
    const { id, ns } = pendingDrag;
    pendingDrag = null;
    document.getElementById('dragConfirmModal').classList.remove('open');
    apiUpdateStatus(id, ns);
}

function cancelDragMove() {
    if (!pendingDrag) return;
    const { item, from, oldIndex } = pendingDrag;
    const refNode = from.children[oldIndex] || null;
    from.insertBefore(item, refNode);
    recountCols();
    pendingDrag = null;
    document.getElementById('dragConfirmModal').classList.remove('open');
}

// ═══════════════════════════════════════════════════════════════════════════════
// ORDER FORM MODAL (Create + Edit)
// ═══════════════════════════════════════════════════════════════════════════════
let formMode        = 'create';  // 'create' | 'edit'
let editOrderId     = null;
let selectedCustId  = null;
let editItemsOnly   = false;

function openOrderForm(order = null) {
    formMode      = order ? 'edit' : 'create';
    editOrderId   = order ? order.id : null;
    editItemsOnly = false;

    document.getElementById('formTitle').textContent = order ? `Edit ${order.order_number}` : 'New Order';
    document.getElementById('formSubmitBtn').textContent = order ? 'Save Changes' : 'Create Order';
    document.getElementById('formError').textContent = '';
    document.getElementById('addItemError').textContent = '';
    clearFErrors();

    // Determine edit scope
    if (order && !ME.isAdmin && ME.role === 'production' && order.status === 'packing') {
        editItemsOnly = true;
    }

    // Reset / pre-fill header fields
    const lockTop = editItemsOnly;
    ['custSearch','phoneSelect','addrSelect','orderDate','prefDate','prefTime','internalNotes']
        .forEach(id => { const el = document.getElementById(id); if (el) el.disabled = lockTop; });

    if (order) {
        selectedCustId = order.customer_id;
        document.getElementById('custSearch').value = order.customer_name || '';
        document.getElementById('orderDate').value  = order.order_date || '';
        document.getElementById('prefDate').value   = order.preferred_delivery_date || '';
        document.getElementById('prefTime').value   = order.preferred_delivery_time || '';
        document.getElementById('internalNotes').value = order.internal_notes || '';

        // Load phones & addresses then pre-select
        Promise.all([
            fetch('/api/customers/' + order.customer_id + '/phones').then(r => r.json()),
            fetch('/api/customers/' + order.customer_id + '/addresses').then(r => r.json()),
        ]).then(([phones, addrs]) => {
            fillPhoneSelect(phones, order.customer_phone_id);
            fillAddrSelect(addrs, order.customer_address_id);
        });
    } else {
        selectedCustId = null;
        document.getElementById('custSearch').value = '';
        document.getElementById('orderDate').value  = new Date().toISOString().split('T')[0];
        document.getElementById('prefDate').value   = '';
        document.getElementById('prefTime').value   = '';
        document.getElementById('internalNotes').value = '';
        document.getElementById('phoneSelect').innerHTML = '<option value="">Select customer first</option>';
        document.getElementById('addrSelect').innerHTML  = '<option value="">Select customer first</option>';
    }

    // Items
    document.getElementById('itemsBody').innerHTML = '';
    if (order && order.items && order.items.length) {
        order.items.forEach(i => addItemRow(i));
    } else {
        addItemRow();
    }

    document.getElementById('orderFormModal').classList.add('open');
}

function closeOrderForm() { document.getElementById('orderFormModal').classList.remove('open'); }

// Customer search
let custTimer = null;
document.getElementById('custSearch').addEventListener('input', function() {
    clearTimeout(custTimer);
    const q = this.value.trim();
    if (!q) { selectedCustId = null; }
    custTimer = setTimeout(() => loadCustomers(q), 200);
});
document.getElementById('custSearch').addEventListener('focus', function() {
    loadCustomers(this.value.trim());
});

async function loadCustomers(q) {
    const data = await fetch('/api/customers?q=' + encodeURIComponent(q)).then(r => r.json());
    const list = document.getElementById('custDrop');
    list.innerHTML = data.length
        ? data.map(c => `<div class="sdrop-item" onclick="selectCustomer(${c.id},'${escJs(c.name)}')">${escHtml(c.name)}</div>`).join('')
        : '<div class="sdrop-empty">No customers found</div>';
    openSDrop('custDrop');
}

async function selectCustomer(id, name, silent = false) {
    selectedCustId = id;
    if (!silent) document.getElementById('custSearch').value = name;
    closeSDrop('custDrop');
    const [phones, addrs] = await Promise.all([
        fetch('/api/customers/' + id + '/phones').then(r => r.json()),
        fetch('/api/customers/' + id + '/addresses').then(r => r.json()),
    ]);
    fillPhoneSelect(phones);
    fillAddrSelect(addrs);
}

function fillPhoneSelect(phones, selected = null) {
    const s = document.getElementById('phoneSelect');
    s.innerHTML = phones.map(p =>
        `<option value="${p.id}" ${(selected ? p.id == selected : p.is_primary) ? 'selected' : ''}>${escHtml(p.phone)}</option>`
    ).join('') || '<option value="">No phones</option>';
}
function fillAddrSelect(addrs, selected = null) {
    const s = document.getElementById('addrSelect');
    s.innerHTML = addrs.map(a =>
        `<option value="${a.id}" ${(selected ? a.id == selected : a.is_primary) ? 'selected' : ''}>${escHtml(a.address)}</option>`
    ).join('') || '<option value="">No addresses</option>';
}

document.addEventListener('click', e => {
    if (!e.target.closest('#custSearchWrap')) closeSDrop('custDrop');
});

// ── Searchable select helpers (item rows) ─────────────────────────────────────
function makeSS(fieldName, type, opts, selectedId) {
    const ph   = type === 'product' ? 'Product…' : type === 'filling' ? 'Filling…' : 'Grind…';
    const sel  = opts.find(o => o.id == selectedId);
    return `<div class="ss-wrap" data-type="${type}" style="position:relative;">
        <div class="ss-input-row" style="position:relative;">
            <input type="text" class="ss-input form-input" autocomplete="off" placeholder="${ph}"
                   value="${sel ? escHtml(sel.name) : ''}" data-selname="${sel ? escHtml(sel.name) : ''}"
                   style="padding:6px 26px 6px 7px;font-size:12.5px;">
            <svg class="ss-caret" width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        <input type="hidden" name="${fieldName}" value="${selectedId || ''}">
        <div class="ss-drop"></div>
    </div>`;
}

function ssOpts(opts, filter) {
    const f = filter.toLowerCase().trim();
    const r = f ? opts.filter(o => o.name.toLowerCase().includes(f)) : opts;
    if (!r.length) return '<div class="sdrop-empty">No results</div>';
    return r.map(o => `<div class="ss-option" data-id="${o.id}" data-name="${escHtml(o.name)}">${escHtml(o.name)}</div>`).join('');
}

// Event delegation on itemsBody
const _ib = document.getElementById('itemsBody');
_ib.addEventListener('focusin', e => {
    if (!e.target.classList.contains('ss-input')) return;
    const wrap = e.target.closest('.ss-wrap');
    const type = wrap.dataset.type;
    const opts = type === 'product' ? PRODUCTS : type === 'filling' ? FILLINGS : GRINDS;
    wrap.querySelector('.ss-drop').innerHTML = ssOpts(opts, e.target.value);
    wrap.querySelector('.ss-drop').classList.add('open');
});
_ib.addEventListener('input', e => {
    if (!e.target.classList.contains('ss-input')) return;
    const wrap = e.target.closest('.ss-wrap');
    const type = wrap.dataset.type;
    const opts = type === 'product' ? PRODUCTS : type === 'filling' ? FILLINGS : GRINDS;
    wrap.querySelector('input[type=hidden]').value = '';
    e.target.dataset.selname = '';
    wrap.querySelector('.ss-drop').innerHTML = ssOpts(opts, e.target.value);
    wrap.querySelector('.ss-drop').classList.add('open');
});
_ib.addEventListener('click', e => {
    const opt = e.target.closest('.ss-option');
    if (!opt) return;
    const wrap = opt.closest('.ss-wrap');
    const inp  = wrap.querySelector('.ss-input');
    inp.value  = opt.dataset.name;
    inp.dataset.selname = opt.dataset.name;
    wrap.querySelector('input[type=hidden]').value = opt.dataset.id;
    wrap.querySelector('.ss-drop').classList.remove('open');
    e.stopPropagation();
});
_ib.addEventListener('focusout', e => {
    if (!e.target.classList.contains('ss-input')) return;
    setTimeout(() => {
        const wrap = e.target.closest('.ss-wrap');
        if (wrap && !wrap.contains(document.activeElement)) {
            wrap.querySelector('.ss-drop').classList.remove('open');
            if (!wrap.querySelector('input[type=hidden]').value) {
                e.target.value = e.target.dataset.selname || '';
            }
        }
    }, 160);
});
document.addEventListener('click', e => {
    if (!e.target.closest('.ss-wrap')) {
        document.querySelectorAll('#itemsBody .ss-drop.open').forEach(d => d.classList.remove('open'));
    }
});

// Item rows
function addItemRow(item = null) {
    const body = document.getElementById('itemsBody');
    const idx  = body.children.length;
    const tr   = document.createElement('tr');
    tr.innerHTML = `
        <td>${makeSS(`items[${idx}][product_id]`, 'product', PRODUCTS, item ? item.product_id : null)}</td>
        <td>${makeSS(`items[${idx}][filling_id]`, 'filling', FILLINGS, item ? item.filling_id : null)}</td>
        <td>${makeSS(`items[${idx}][grind_id]`,   'grind',   GRINDS,   item ? item.grind_id   : null)}</td>
        <td style="width:62px;"><input type="number" name="items[${idx}][qty]" value="${item ? item.qty : 1}" min="1" max="999" class="form-input" style="padding:6px 7px;font-size:13px;text-align:center;" required></td>
        <td><button type="button" class="rm-btn" onclick="removeItem(this)">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button></td>`;
    body.appendChild(tr);
}

function validateCurrentRow() {
    const rows = document.querySelectorAll('#itemsBody tr');
    if (!rows.length) return true;
    const last = rows[rows.length - 1];
    const p = last.querySelector('[name*="product_id"]').value;
    const f = last.querySelector('[name*="filling_id"]').value;
    const g = last.querySelector('[name*="grind_id"]').value;
    const missing = [];
    if (!p) missing.push('Product');
    if (!f) missing.push('Filling');
    if (!g) missing.push('Grind');
    const errEl = document.getElementById('addItemError');
    if (missing.length) {
        errEl.textContent = `Fill the current item first — missing: ${missing.join(', ')}.`;
        return false;
    }
    errEl.textContent = '';
    return true;
}

function handleAddItem() {
    if (validateCurrentRow()) addItemRow();
}

function removeItem(btn) {
    if (document.getElementById('itemsBody').children.length <= 1) return;
    btn.closest('tr').remove();
    reindex();
    document.getElementById('addItemError').textContent = '';
}
function reindex() {
    document.querySelectorAll('#itemsBody tr').forEach((tr, i) => {
        tr.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/items\[\d+\]/, `items[${i}]`);
        });
    });
}

// Inline field error helpers
function setFErr(id, msg) {
    const el = document.getElementById(id);
    if (el) { el.textContent = msg; }
    const inp = document.getElementById(id.replace('err-',''));
    if (inp) inp.classList.toggle('has-error', !!msg);
}
function clearFErrors() {
    document.querySelectorAll('.field-error').forEach(e => e.textContent = '');
    document.querySelectorAll('.has-error').forEach(e => e.classList.remove('has-error'));
}

// Submit
document.getElementById('orderForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const errEl = document.getElementById('formError');
    errEl.textContent = '';
    clearFErrors();

    let hasErr = false;

    if (!editItemsOnly) {
        if (!selectedCustId) { setFErr('err-custSearch','Please select a customer.'); hasErr = true; }
        const phoneVal = document.getElementById('phoneSelect').value;
        const addrVal  = document.getElementById('addrSelect').value;
        if (!phoneVal) { setFErr('err-phoneSelect','Phone number is required.'); hasErr = true; }
        if (!addrVal)  { setFErr('err-addrSelect','Delivery address is required.'); hasErr = true; }
        if (!document.getElementById('orderDate').value) { setFErr('err-orderDate','Order date is required.'); hasErr = true; }
    }

    const items = [];
    let itemsValid = true;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const p = tr.querySelector('[name*="product_id"]').value;
        const f = tr.querySelector('[name*="filling_id"]').value;
        const g = tr.querySelector('[name*="grind_id"]').value;
        const q = tr.querySelector('[name*="qty"]').value;
        if (!p || !f || !g) itemsValid = false;
        if (p || f || g || q) items.push({ product_id: parseInt(p)||0, filling_id: parseInt(f)||0, grind_id: parseInt(g)||0, qty: parseInt(q)||1 });
    });
    if (!items.length) { errEl.textContent = 'Add at least one item.'; hasErr = true; }
    else if (!itemsValid) { document.getElementById('addItemError').textContent = 'Some items are incomplete — fill all Product, Filling, and Grind fields.'; hasErr = true; }

    if (hasErr) return;

    let payload;
    if (editItemsOnly) {
        payload = { items };
    } else {
        payload = {
            customer_id:             selectedCustId,
            customer_phone_id:       parseInt(phoneVal),
            customer_address_id:     parseInt(addrVal),
            order_date:              document.getElementById('orderDate').value,
            preferred_delivery_date: document.getElementById('prefDate').value || null,
            preferred_delivery_time: document.getElementById('prefTime').value || null,
            internal_notes:          document.getElementById('internalNotes').value || null,
            items,
        };
    }

    const btn = document.getElementById('formSubmitBtn');
    btn.disabled = true; btn.textContent = formMode === 'create' ? 'Creating…' : 'Saving…';

    try {
        const url    = formMode === 'create' ? '/kanban/orders' : `/kanban/orders/${editOrderId}`;
        const method = formMode === 'create' ? 'POST' : 'PUT';
        const r      = await api(url, method, payload);

        if (!r.success) {
            errEl.textContent = r.message || (r.errors ? Object.values(r.errors).flat().join(' ') : 'Failed.');
        } else {
            closeOrderForm();
            if (formMode === 'create') {
                prependCardToBoard(r.order);
            } else {
                // Reload detail to reflect changes
                openOrderDetail(editOrderId);
                // Update card on board
                const card = document.querySelector(`.order-card[data-order-id="${editOrderId}"]`);
                if (card) refreshCardDOM(card, r.order);
            }
        }
    } catch { errEl.textContent = 'Network error.'; }
    finally {
        btn.disabled = false;
        btn.textContent = formMode === 'create' ? 'Create Order' : 'Save Changes';
    }
});

function prependCardToBoard(order) {
    const col = document.getElementById('col-new');
    if (!col) return;
    const el = buildCardElement(order);
    col.prepend(el);
    updateEmptyState('new');
    const cnt = document.getElementById('cnt-new');
    if (cnt) cnt.textContent = parseInt(cnt.textContent||0) + 1;
    const ac = document.getElementById('activeCount');
    if (ac) ac.textContent = parseInt(ac.textContent||0) + 1;
}

function buildCardElement(order) {
    const el = document.createElement('div');
    el.className = 'order-card';
    el.dataset.orderId  = order.id;
    el.dataset.customer = (order.customer_name || '').toLowerCase();
    el.dataset.num      = (order.order_number  || '').toLowerCase();
    el.onclick = () => openOrderDetail(order.id);
    const items2 = (order.items || []).slice(0, 2).map(i => `${i.product_name} · ${i.filling_name} · ${i.grind_name} × ${i.qty}`).join('<br>');
    const moreCount = (order.items || []).length - 2;
    const footerHtml = order.preferred_delivery_date
        ? `<div class="card-footer"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>${fmtDate(order.preferred_delivery_date)}</div>` : '';
    el.innerHTML = `
        <div class="card-header">
            <span class="card-order-num">${order.order_number}</span>
            <span class="card-time">Just now</span>
        </div>
        <div class="card-customer">${escHtml(order.customer_name)}</div>
        <div class="card-items">${items2 || 'No items'}${moreCount > 0 ? `<span style="color:#4b5563"> +${moreCount} more</span>` : ''}</div>
        ${footerHtml}`;
    return el;
}

function refreshCardDOM(card, order) {
    const items2 = (order.items || []).slice(0, 2).map(i => `${i.product_name} · ${i.filling_name} · ${i.grind_name} × ${i.qty}`).join('<br>');
    const moreCount = (order.items || []).length - 2;
    card.querySelector('.card-customer').textContent = order.customer_name || '';
    card.querySelector('.card-items').innerHTML = (items2 || 'No items') + (moreCount > 0 ? `<span style="color:#4b5563"> +${moreCount} more</span>` : '');
}

// ═══════════════════════════════════════════════════════════════════════════════
// ORDER DETAIL MODAL
// ═══════════════════════════════════════════════════════════════════════════════
async function openOrderDetail(orderId) {
    const modal = document.getElementById('detailModal');
    modal.classList.add('open');
    document.getElementById('detailBody').innerHTML   = '<div style="text-align:center;padding:40px 0;color:#4b5563;font-size:13px;">Loading…</div>';
    document.getElementById('detailFooter').innerHTML = '';
    try {
        const order = await api(`/kanban/orders/${orderId}`);
        renderDetail(order);
    } catch { document.getElementById('detailBody').innerHTML = '<p style="color:#ef4444;text-align:center;padding:30px;">Failed to load.</p>'; }
}
function closeDetail() { document.getElementById('detailModal').classList.remove('open'); }

function renderDetail(order) {
    const isArchived   = order.is_archived;
    const isCreator    = order.created_by_id === ME.id;
    const isAssigned   = order.driver_user_id === ME.id;
    const isOutsourced = order.is_outsourced;

    // Build info section
    const dispatchInfo = order.driver_user_name
        ? `<div class="detail-row"><span class="detail-label">Driver</span><span class="detail-value">${escHtml(order.driver_user_name)}</span></div>`
        : order.outsourced_driver_name
            ? `<div class="detail-row"><span class="detail-label">Outsourced Driver</span><span class="detail-value">${escHtml(order.outsourced_driver_name)}${(ME.canViewCost && order.outsourced_delivery_cost != null) ? ' — <span style="color:#f59e0b">' + parseFloat(order.outsourced_delivery_cost).toFixed(3) + ' JD</span>' : ''}</span></div>`
            : '';

    const itemsHtml = order.items.length
        ? order.items.map(i => `<div class="item-row"><span class="item-name">${escHtml(i.product_name)}</span><span class="item-meta">${escHtml(i.filling_name)} · ${escHtml(i.grind_name)}</span><span class="item-qty">×${i.qty}</span></div>`).join('')
        : '<p style="color:#4b5563;font-size:12.5px;">No items</p>';

    const logsHtml = (order.logs || []).length
        ? order.logs.map(l => `<div class="log-item"><div class="log-meta">${escHtml(l.user_name||'?')} · ${fmtAgo(new Date(l.created_at))}</div><div class="log-text">${fmtLogText(l)}</div></div>`).join('')
        : '<p style="color:#4b5563;font-size:12px;">No activity yet.</p>';

    document.getElementById('detailBody').innerHTML = `
        <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:14px;">
            <div style="flex:1;">
                <div style="font-size:17px;font-weight:700;color:#f5f5f5;margin-bottom:5px;">${escHtml(order.customer_name)}</div>
                <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
                    <span style="font-size:12px;font-weight:600;color:#6b7280;">${order.order_number}</span>
                    <span class="status-badge s-${order.status}">${order.status_label}</span>
                    ${isArchived ? '<span class="archived-badge"><svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>Archived</span>' : ''}
                    <span style="font-size:11px;color:#4b5563;">${fmtAgo(new Date(order.created_at))}</span>
                </div>
            </div>
        </div>

        <div style="background:#161616;border:1px solid #222;border-radius:9px;padding:12px 14px;margin-bottom:14px;">
            <div class="detail-row"><span class="detail-label">Phone</span><span class="detail-value">${escHtml(order.phone||'—')}</span></div>
            <div class="detail-row"><span class="detail-label">Delivery Address</span><span class="detail-value">${escHtml(order.delivery_address||'—')}</span></div>
            ${order.preferred_delivery_date ? `<div class="detail-row"><span class="detail-label">Preferred Delivery</span><span class="detail-value">${fmtDate(order.preferred_delivery_date)}${order.preferred_delivery_time?' at '+fmtTime(order.preferred_delivery_time):''}</span></div>` : ''}
            ${order.internal_notes ? `<div class="detail-row"><span class="detail-label">Internal Notes</span><span class="detail-value">${escHtml(order.internal_notes)}</span></div>` : ''}
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
            <div class="note-area" id="noteArea" data-oid="${order.id}">
                <textarea class="form-input" id="noteText" placeholder="Add a note…" rows="2"></textarea>
                <button class="btn-secondary" onclick="submitNote(${order.id})">Add Note</button>
            </div>
        </div>`;

    // ── Footer action buttons ──────────────────────────────────────────────────
    const footer = document.getElementById('detailFooter');
    footer.innerHTML = '';
    const add = (label, cls, fn, ml = false) => {
        const b = document.createElement('button');
        b.className = cls; b.textContent = label;
        if (ml) b.style.marginLeft = 'auto';
        b.onclick = fn;
        footer.appendChild(b);
    };

    if (isArchived) {
        // Archived state: Restore only (admin)
        if (ME.isAdmin) {
            add('Restore', 'btn-amber-outline', () => doRestore(order.id), true);
        }
    } else if (order.status === 'delivered' || order.status === 'cancelled') {
        // Completed, not yet archived
        if (ME.isAdmin) {
            add('Archive', 'btn-amber-outline', () => doArchive(order.id), true);
        }
    } else {
        // Active order ─────────────────────────────────────────────────────────
        // Cancel (admin only)
        if (ME.isAdmin) {
            add('Cancel Order', 'btn-danger', () => doCancel(order.id));
        }

        // Edit
        const canEdit = ME.isAdmin
            || (order.status === 'new'     && (ME.role === 'sales' && isCreator))
            || (order.status === 'packing' && ME.role === 'production');
        if (canEdit) {
            add('Edit', 'btn-secondary', () => { closeDetail(); openOrderForm(order); });
        }

        // Advance / Dispatch
        const spacer = document.createElement('div');
        spacer.className = 'spacer';
        footer.appendChild(spacer);

        if (order.status === 'new') {
            const can = ME.isAdmin || ME.role === 'production';
            if (can) add('Preparation & Packing →', 'btn-primary', () => doAdvance(order.id, 'packing'));
        }
        else if (order.status === 'packing') {
            const can = ME.isAdmin || ME.role === 'fleet';
            if (can) add('Dispatch →', 'btn-primary', () => { closeDetail(); openDispatchModal(order.id); });
        }
        else if (order.status === 'dispatch') {
            const can = ME.isAdmin || isAssigned || (isOutsourced && ME.role === 'fleet');
            if (can) add('Picked Up →', 'btn-primary', () => doAdvance(order.id, 'picked_up'));
        }
        else if (order.status === 'picked_up') {
            const can = ME.isAdmin || isAssigned || (isOutsourced && ME.role === 'fleet');
            if (can) add('Delivered →', 'btn-primary', () => doAdvance(order.id, 'delivered'));
        }
    }
}

// ── Order actions ─────────────────────────────────────────────────────────────
async function doAdvance(orderId, status) {
    const r = await api(`/kanban/orders/${orderId}/status`, 'PUT', { status });
    if (!r.success) { alert('Failed.'); return; }
    moveCardOnBoard(orderId, status);
    closeDetail();
}

async function doCancel(orderId) {
    if (!confirm('Cancel this order?')) return;
    const r = await api(`/kanban/orders/${orderId}/status`, 'PUT', { status: 'cancelled' });
    if (!r.success) { alert('Failed.'); return; }
    moveCardOnBoard(orderId, 'cancelled');
    closeDetail();
}

async function doArchive(orderId) {
    const r = await api(`/kanban/orders/${orderId}/archive`, 'POST');
    if (!r.success) { alert('Failed.'); return; }
    const card = document.querySelector(`.order-card[data-order-id="${orderId}"]`);
    if (card) { card.remove(); recountCols(); }
    closeDetail();
    const ac = document.getElementById('activeCount');
    if (ac) ac.textContent = Math.max(0, parseInt(ac.textContent||0) - 1);
}

async function doRestore(orderId) {
    const r = await api(`/kanban/orders/${orderId}/restore`, 'POST');
    if (!r.success) { alert('Failed.'); return; }
    closeDetail();
    alert('Order restored to the Kanban board.');
}

async function submitNote(orderId) {
    const txt = document.getElementById('noteText').value.trim();
    if (!txt) return;
    const r = await api(`/kanban/orders/${orderId}/notes`, 'POST', { note: txt });
    if (r.success) {
        document.getElementById('noteText').value = '';
        openOrderDetail(orderId); // reload
    }
}

function moveCardOnBoard(orderId, newStatus) {
    const card   = document.querySelector(`.order-card[data-order-id="${orderId}"]`);
    const newCol = document.getElementById('col-' + newStatus);
    if (!card || !newCol) return;
    const oldStatus = card.closest('.kanban-cards')?.dataset?.status;
    newCol.prepend(card);
    recountCols();
    if (oldStatus) updateEmptyState(oldStatus);
    updateEmptyState(newStatus);
    // active count
    const active = ['new','packing','dispatch','picked_up'];
    const wasActive = active.includes(oldStatus);
    const isActive  = active.includes(newStatus);
    const ac = document.getElementById('activeCount');
    if (ac) {
        const delta = (isActive ? 1 : 0) - (wasActive ? 1 : 0);
        ac.textContent = Math.max(0, parseInt(ac.textContent||0) + delta);
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
// DISPATCH MODAL
// ═══════════════════════════════════════════════════════════════════════════════
let dispatchOrderId  = null;
let driverType       = 'our';

async function openDispatchModal(orderId) {
    dispatchOrderId = orderId;
    driverType      = 'our';
    document.getElementById('dispatchError').textContent = '';
    document.getElementById('outsourcedName').value = '';
    document.getElementById('outsourcedCost').value = '';
    setDriverType('our');

    // Open immediately — load drivers async
    document.getElementById('dispatchModal').classList.add('open');

    const sel = document.getElementById('driverSelect');
    sel.innerHTML = '<option value="">Loading drivers…</option>';
    try {
        const drivers = await fetch('/api/drivers').then(r => { if (!r.ok) throw new Error(r.status); return r.json(); });
        sel.innerHTML = drivers.length
            ? drivers.map(d => `<option value="${d.id}">${escHtml(d.full_name)}</option>`).join('')
            : '<option value="">No drivers available</option>';
    } catch {
        sel.innerHTML = '<option value="">Failed to load drivers</option>';
        document.getElementById('dispatchError').textContent = 'Could not load driver list. Please refresh and try again.';
    }
}
function closeDispatch() { document.getElementById('dispatchModal').classList.remove('open'); }

function setDriverType(type) {
    driverType = type;
    document.getElementById('optOur').classList.toggle('active', type === 'our');
    document.getElementById('optOut').classList.toggle('active', type === 'outsourced');
    document.getElementById('ourSection').style.display      = type === 'our'        ? '' : 'none';
    document.getElementById('outsourcedSection').style.display = type === 'outsourced' ? '' : 'none';
}

async function confirmDispatch() {
    const errEl = document.getElementById('dispatchError');
    errEl.textContent = '';

    let payload;
    if (driverType === 'our') {
        const did = document.getElementById('driverSelect').value;
        if (!did) { errEl.textContent = 'Please select a driver.'; return; }
        payload = { driver_type: 'our', driver_user_id: parseInt(did) };
    } else {
        const name = document.getElementById('outsourcedName').value.trim();
        const cost = document.getElementById('outsourcedCost').value;
        if (!name) { errEl.textContent = 'Driver / company name is required.'; return; }
        if (cost === '' || cost < 0) { errEl.textContent = 'Delivery cost is required.'; return; }
        payload = { driver_type: 'outsourced', outsourced_driver_name: name, outsourced_delivery_cost: parseFloat(cost) };
    }

    const btn = document.getElementById('dispatchConfirmBtn');
    btn.disabled = true; btn.textContent = 'Dispatching…';
    try {
        const r = await api(`/kanban/orders/${dispatchOrderId}/dispatch`, 'POST', payload);
        if (!r.success) { errEl.textContent = r.message || 'Failed.'; }
        else {
            closeDispatch();
            moveCardOnBoard(dispatchOrderId, 'dispatch');
        }
    } catch { errEl.textContent = 'Network error.'; }
    finally { btn.disabled = false; btn.textContent = 'Confirm & Dispatch'; }
}

// ── Utility ───────────────────────────────────────────────────────────────────
function openSDrop(id)  { document.getElementById(id)?.classList.add('open'); }
function closeSDrop(id) { document.getElementById(id)?.classList.remove('open'); }
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
    if (l.action==='dispatched')    return `Dispatched (${escHtml(l.to_status)})`;
    if (l.action==='edited')        return 'Order edited';
    if (l.action==='archived')      return 'Order archived';
    if (l.action==='restored')      return 'Order restored';
    if (l.action==='note_added' && l.note) return escHtml(l.note);
    return l.action;
}
function escHtml(s) { return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function escJs(s)   { return (s||'').replace(/'/g,"\\'"); }
</script>

{{-- ── Order Form Modal (Create + Edit) ── --}}
<div class="modal-overlay wide" id="orderFormModal" onclick="if(event.target===this)closeOrderForm()">
    <div class="modal-box wide">
        <div class="modal-header">
            <span class="modal-title" id="formTitle">New Order</span>
            <button class="modal-close" onclick="closeOrderForm()"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="modal-body">
            <form id="orderForm" onsubmit="return false;">

                {{-- Customer Details Section --}}
                <div class="form-section">
                    <div class="form-section-title">Customer Details</div>
                    <div class="form-grid-3">
                        <div class="form-group" style="margin-bottom:6px;">
                            <label class="form-label">Customer <span class="req">*</span></label>
                            <div class="search-dropdown" id="custSearchWrap">
                                <input type="text" id="custSearch" class="form-input" placeholder="Click to select customer…" autocomplete="off">
                                <div class="sdrop-list" id="custDrop"></div>
                            </div>
                            <span class="field-error" id="err-custSearch"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:6px;">
                            <label class="form-label">Phone Number <span class="req">*</span></label>
                            <select id="phoneSelect" name="phone" class="form-select"><option value="">Select customer first</option></select>
                            <span class="field-error" id="err-phoneSelect"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:6px;">
                            <label class="form-label">Delivery Address <span class="req">*</span></label>
                            <select id="addrSelect" name="delivery_address" class="form-select"><option value="">Select customer first</option></select>
                            <span class="field-error" id="err-addrSelect"></span>
                        </div>
                    </div>
                </div>

                {{-- Preferred Delivery Section --}}
                <div class="form-section">
                    <div class="form-section-title">Delivery Schedule</div>
                    <div class="form-grid-3e">
                        <div class="form-group" style="margin-bottom:6px;">
                            <label class="form-label">Order Date <span class="req">*</span></label>
                            <div class="dt-wrap">
                                <svg class="dt-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <input type="date" id="orderDate" class="form-input" required>
                            </div>
                            <span class="field-error" id="err-orderDate"></span>
                        </div>
                        <div class="form-group" style="margin-bottom:6px;">
                            <label class="form-label">Preferred Delivery Date</label>
                            <div class="dt-wrap">
                                <svg class="dt-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <input type="date" id="prefDate" class="form-input">
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:6px;">
                            <label class="form-label">Preferred Time</label>
                            <div class="dt-wrap">
                                <svg class="dt-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 12"/></svg>
                                <input type="time" id="prefTime" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="form-group">
                    <label class="form-label">Internal Notes <span style="font-size:11px;color:#4b5563;">(optional)</span></label>
                    <textarea id="internalNotes" class="form-input" placeholder="Optional internal notes…" style="min-height:60px;"></textarea>
                </div>

                {{-- Items --}}
                <div style="border-top:1px solid #222;padding-top:12px;margin-top:2px;">
                    <p class="form-label" style="margin-bottom:8px;">Order Items <span class="req">*</span></p>
                    <table class="items-table">
                        <thead><tr>
                            <th>Product</th><th>Filling</th><th>Grind</th><th style="width:65px;">Qty</th><th style="width:30px;"></th>
                        </tr></thead>
                        <tbody id="itemsBody"></tbody>
                    </table>
                    <div style="display:flex;align-items:center;gap:10px;margin-top:6px;">
                        <button type="button" class="btn-add-item" onclick="handleAddItem()">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                            Add Item
                        </button>
                        <span id="addItemError" style="font-size:12px;color:#ef4444;"></span>
                    </div>
                </div>

                <p id="formError" style="color:#ef4444;font-size:12.5px;margin-top:10px;min-height:16px;"></p>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeOrderForm()">Cancel</button>
            <div class="spacer"></div>
            <button class="btn-primary" id="formSubmitBtn" onclick="document.getElementById('orderForm').dispatchEvent(new Event('submit'))">Create Order</button>
        </div>
    </div>
</div>

{{-- ── Order Detail Modal ── --}}
<div class="modal-overlay" id="detailModal" onclick="if(event.target===this)closeDetail()">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Order Details</span>
            <button class="modal-close" onclick="closeDetail()"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="modal-body" id="detailBody"></div>
        <div class="modal-footer" id="detailFooter"></div>
    </div>
</div>

{{-- ── Dispatch Modal ── --}}
<div class="modal-overlay" id="dispatchModal" onclick="if(event.target===this)closeDispatch()">
    <div class="modal-box narrow">
        <div class="modal-header">
            <span class="modal-title">Assign Driver & Dispatch</span>
            <button class="modal-close" onclick="closeDispatch()"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="modal-body">
            <div class="driver-type-opts">
                <label class="driver-opt active" id="optOur" onclick="setDriverType('our')">
                    <input type="radio" name="dtype" value="our" checked style="display:none;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="driver-opt-label">Our Driver</span>
                </label>
                <label class="driver-opt" id="optOut" onclick="setDriverType('outsourced')">
                    <input type="radio" name="dtype" value="outsourced" style="display:none;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span class="driver-opt-label">Outsourced</span>
                </label>
            </div>
            <div id="ourSection">
                <div class="form-group">
                    <label class="form-label">Select Driver <span class="req">*</span></label>
                    <select id="driverSelect" class="form-select"><option value="">Loading…</option></select>
                </div>
            </div>
            <div id="outsourcedSection" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Driver / Company Name <span class="req">*</span></label>
                    <input type="text" id="outsourcedName" class="form-input" placeholder="e.g. Ahmed Delivery Co.">
                </div>
                <div class="form-group">
                    <label class="form-label">Delivery Cost (JD) <span class="req">*</span></label>
                    <input type="number" id="outsourcedCost" class="form-input" placeholder="0.000" step="0.001" min="0">
                </div>
            </div>
            <p id="dispatchError" style="color:#ef4444;font-size:12.5px;min-height:16px;"></p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeDispatch()">Cancel</button>
            <div class="spacer"></div>
            <button class="btn-primary" id="dispatchConfirmBtn" onclick="confirmDispatch()">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
                Confirm & Dispatch
            </button>
        </div>
    </div>
</div>

{{-- ── Drag-and-Drop Confirmation Modal ── --}}
<div class="modal-overlay" id="dragConfirmModal" style="z-index:300;">
    <div class="confirm-modal-box">
        <div class="confirm-modal-icon">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        </div>
        <div class="confirm-modal-title">Move Order?</div>
        <div class="confirm-modal-msg">
            Move this order from<br>
            <strong style="color:#f5f5f5;" id="dragConfirmFrom"></strong>
            &nbsp;→&nbsp;
            <strong style="color:#f59e0b;" id="dragConfirmTo"></strong>
        </div>
        <div class="confirm-modal-actions">
            <button class="btn-secondary" onclick="cancelDragMove()">Cancel</button>
            <button class="btn-primary" onclick="confirmDragMove()">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M5 13l4 4L19 7"/></svg>
                Confirm Move
            </button>
        </div>
    </div>
</div>
@endpush
