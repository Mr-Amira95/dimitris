@extends('layouts.app')

@section('title', 'Products — ' . config('app.name'))
@section('page-title', 'Products')
@section('page-subtitle', 'Manage products, filling options, and grind options')

@section('content')
@push('styles')
<style>
.tag-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 10px 5px 12px;
    border-radius: 20px;
    font-size: 13px; font-weight: 500;
    background: #1e1e1e; border: 1px solid #2d2d2d;
    color: #d1d5db;
}
.tag-pill.deleted { opacity: 0.45; text-decoration: line-through; }
.tag-btn {
    width: 20px; height: 20px;
    border-radius: 50%; border: none; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    background: transparent; color: #6b7280; transition: background 0.15s, color 0.15s;
    padding: 0; flex-shrink: 0;
}
.tag-btn:hover { background: rgba(245,158,11,0.15); color: #f59e0b; }
.tag-btn.del:hover { background: rgba(239,68,68,0.15); color: #ef4444; }
.section-card {
    background: #1a1a1a; border: 1px solid #2d2d2d; border-radius: 12px; overflow: hidden;
}
.section-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; border-bottom: 1px solid #2d2d2d;
}
.section-title { font-size: 14px; font-weight: 600; color: #f5f5f5; }
.section-body { padding: 16px 18px; }
/* Inline add form */
.add-row { display: flex; gap: 8px; margin-top: 12px; }
.add-input {
    flex: 1; background: #141414; border: 1px solid #2d2d2d; border-radius: 8px;
    padding: 7px 12px; font-size: 13px; color: #f5f5f5; outline: none;
    transition: border-color 0.15s;
}
.add-input:focus { border-color: #f59e0b; }
.add-input::placeholder { color: #4b5563; }
.btn-amber {
    padding: 7px 14px; border-radius: 8px; border: none; cursor: pointer;
    font-size: 13px; font-weight: 600;
    background: #f59e0b; color: #111; transition: background 0.15s;
}
.btn-amber:hover { background: #d97706; }
/* Edit modal */
.modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.65); z-index: 200;
    align-items: center; justify-content: center;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: #1e1e1e; border: 1px solid #2d2d2d; border-radius: 14px;
    padding: 24px; width: 360px; max-width: 92vw;
}
.modal-title { font-size: 15px; font-weight: 600; color: #f5f5f5; margin-bottom: 14px; }
.modal-input {
    width: 100%; background: #141414; border: 1px solid #2d2d2d; border-radius: 8px;
    padding: 9px 12px; font-size: 13.5px; color: #f5f5f5; outline: none; box-sizing: border-box;
    transition: border-color 0.15s;
}
.modal-input:focus { border-color: #f59e0b; }
.modal-actions { display: flex; gap: 8px; margin-top: 14px; justify-content: flex-end; }
.btn-cancel {
    padding: 7px 14px; border-radius: 8px; border: 1px solid #2d2d2d; cursor: pointer;
    font-size: 13px; background: transparent; color: #9ca3af; transition: background 0.15s;
}
.btn-cancel:hover { background: #2a2a2a; color: #f5f5f5; }
</style>
@endpush

<div class="space-y-6">

    {{-- ── PRODUCTS ─────────────────────────────────────────────────────── --}}
    <div class="section-card">
        <div class="section-header">
            <span class="section-title">Products</span>
            {{-- Search --}}
            <form method="GET" action="{{ route('products.index') }}" class="flex gap-2 items-center">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Search products…"
                           class="add-input" style="width:220px; padding-left:34px;">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2" width="14" height="14" fill="none"
                         viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <button type="submit" class="btn-amber" style="padding:7px 12px;">Search</button>
                @if($search)
                    <a href="{{ route('products.index') }}" class="btn-cancel">Clear</a>
                @endif
            </form>
        </div>

        <div class="section-body" style="padding: 0;">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid #222;">
                        <th class="text-left px-5 py-3 font-medium" style="color:#6b7280;">Product Name</th>
                        <th class="text-right px-5 py-3 font-medium" style="color:#6b7280;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="group" style="border-bottom: 1px solid #1e1e1e;">
                            <td class="px-5 py-3" style="color:#f5f5f5;">{{ $product->name }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button"
                                            onclick="openEditModal('product', {{ $product->id }}, '{{ addslashes($product->name) }}')"
                                            title="Edit"
                                            class="p-1.5 rounded transition-colors"
                                            style="color:#6b7280;"
                                            onmouseover="this.style.color='#f59e0b'; this.style.backgroundColor='rgba(245,158,11,0.1)'"
                                            onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                          onsubmit="return confirm('Remove product \'{{ addslashes($product->name) }}\'?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Delete"
                                                class="p-1.5 rounded transition-colors"
                                                style="color:#6b7280;"
                                                onmouseover="this.style.color='#ef4444'; this.style.backgroundColor='rgba(239,68,68,0.1)'"
                                                onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-5 py-10 text-center text-sm" style="color:#6b7280;">
                                No products yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Add product inline --}}
        <div style="padding:12px 18px; border-top:1px solid #222;">
            <form method="POST" action="{{ route('products.store') }}" class="add-row">
                @csrf
                <input type="text" name="name" class="add-input" placeholder="New product name…"
                       value="{{ old('name') }}" maxlength="100" required>
                <button type="submit" class="btn-amber">+ Add</button>
            </form>
        </div>

        @if ($products->hasPages())
            <div style="padding:10px 18px;">{{ $products->links() }}</div>
        @endif
    </div>

    {{-- ── FILLINGS + GRINDS (side by side) ────────────────────────────── --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        {{-- FILLINGS --}}
        <div class="section-card">
            <div class="section-header">
                <span class="section-title">Filling Options</span>
                <span class="text-xs" style="color:#6b7280;">{{ $fillings->count() }} options</span>
            </div>
            <div class="section-body">
                <div style="display:flex; flex-wrap:wrap; gap:8px; min-height:36px;">
                    @forelse ($fillings as $filling)
                        <span class="tag-pill {{ $filling->trashed() ? 'deleted' : '' }}">
                            {{ $filling->name }}
                            <button type="button" class="tag-btn"
                                    onclick="openEditModal('filling', {{ $filling->id }}, '{{ addslashes($filling->name) }}')"
                                    title="Edit">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('fillings.destroy', $filling) }}" style="display:inline;"
                                  onsubmit="return confirm('Remove filling \'{{ addslashes($filling->name) }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="tag-btn del" title="Remove">
                                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </span>
                    @empty
                        <span class="text-sm" style="color:#4b5563;">No filling options yet.</span>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('fillings.store') }}" class="add-row">
                    @csrf
                    <input type="text" name="name" class="add-input" placeholder="New filling option…" maxlength="100" required>
                    <button type="submit" class="btn-amber">+ Add</button>
                </form>
            </div>
        </div>

        {{-- GRINDS --}}
        <div class="section-card">
            <div class="section-header">
                <span class="section-title">Grind Options</span>
                <span class="text-xs" style="color:#6b7280;">{{ $grinds->count() }} options</span>
            </div>
            <div class="section-body">
                <div style="display:flex; flex-wrap:wrap; gap:8px; min-height:36px;">
                    @forelse ($grinds as $grind)
                        <span class="tag-pill {{ $grind->trashed() ? 'deleted' : '' }}">
                            {{ $grind->name }}
                            <button type="button" class="tag-btn"
                                    onclick="openEditModal('grind', {{ $grind->id }}, '{{ addslashes($grind->name) }}')"
                                    title="Edit">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('grinds.destroy', $grind) }}" style="display:inline;"
                                  onsubmit="return confirm('Remove grind option \'{{ addslashes($grind->name) }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="tag-btn del" title="Remove">
                                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </span>
                    @empty
                        <span class="text-sm" style="color:#4b5563;">No grind options yet.</span>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('grinds.store') }}" class="add-row">
                    @csrf
                    <input type="text" name="name" class="add-input" placeholder="New grind option…" maxlength="100" required>
                    <button type="submit" class="btn-amber">+ Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modal (shared for product / filling / grind) --}}
<div class="modal-overlay" id="editModal" onclick="if(event.target===this) closeEditModal()">
    <div class="modal-box">
        <div class="modal-title" id="editModalTitle">Edit</div>
        <form method="POST" id="editModalForm">
            @csrf @method('PUT')
            <input type="text" name="name" id="editModalInput" class="modal-input" maxlength="100" required>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-amber">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const ROUTES = {
    product: (id) => `/products/${id}`,
    filling: (id) => `/fillings/${id}`,
    grind:   (id) => `/grinds/${id}`,
};
const TITLES = { product: 'Edit Product', filling: 'Edit Filling', grind: 'Edit Grind Option' };

function openEditModal(type, id, name) {
    document.getElementById('editModalTitle').textContent = TITLES[type] || 'Edit';
    document.getElementById('editModalForm').action = ROUTES[type](id);
    document.getElementById('editModalInput').value = name;
    document.getElementById('editModal').classList.add('open');
    document.getElementById('editModalInput').focus();
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditModal(); });
</script>
@endpush
@endsection
