@extends('layouts.app')

@section('title', 'Customers — ' . config('app.name'))
@section('page-title', 'Customers')
@section('page-subtitle', 'Manage the customer directory')

@section('header-actions')
    <button type="button" onclick="openAddModal()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
            style="background-color:#f59e0b; color:#111111;"
            onmouseover="this.style.backgroundColor='#d97706'"
            onmouseout="this.style.backgroundColor='#f59e0b'">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Customer
    </button>
@endsection

@push('styles')
<style>
.search-bar { display:flex; gap:8px; margin-bottom:16px; }
.search-input {
    flex:1; background:#1a1a1a; border:1px solid #2d2d2d; border-radius:9px;
    padding:9px 14px 9px 38px; font-size:13.5px; color:#f5f5f5; outline:none;
    transition:border-color 0.15s;
}
.search-input:focus { border-color:#f59e0b; }
.search-input::placeholder { color:#4b5563; }

.modal-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,0.65); z-index:200;
    align-items:flex-start; justify-content:center; padding-top:60px;
}
.modal-overlay.open { display:flex; }
.modal-box {
    background:#1e1e1e; border:1px solid #2d2d2d; border-radius:14px;
    padding:24px; width:480px; max-width:94vw; max-height:85vh; overflow-y:auto;
}
.modal-title { font-size:15px; font-weight:600; color:#f5f5f5; margin-bottom:18px; }
.form-label { display:block; font-size:12px; font-weight:600; color:#9ca3af; margin-bottom:5px; text-transform:uppercase; letter-spacing:.04em; }
.form-input {
    width:100%; background:#141414; border:1px solid #2d2d2d; border-radius:8px;
    padding:9px 12px; font-size:13.5px; color:#f5f5f5; outline:none; box-sizing:border-box;
    transition:border-color 0.15s;
}
.form-input:focus { border-color:#f59e0b; }
.form-input::placeholder { color:#4b5563; }
.form-group { margin-bottom:14px; }
.multi-row { display:flex; gap:6px; margin-bottom:6px; }
.multi-row .form-input { flex:1; }
.remove-btn {
    width:32px; height:36px; border-radius:8px; border:1px solid #2d2d2d;
    background:transparent; color:#6b7280; cursor:pointer; display:flex; align-items:center; justify-content:center;
    flex-shrink:0; transition:background 0.15s, color 0.15s;
}
.remove-btn:hover { background:rgba(239,68,68,0.1); color:#ef4444; border-color:rgba(239,68,68,0.3); }
.add-link {
    font-size:12.5px; color:#f59e0b; cursor:pointer; background:none; border:none;
    padding:0; display:inline-flex; align-items:center; gap:4px; margin-top:4px;
    transition:opacity 0.15s;
}
.add-link:hover { opacity:0.8; }
.modal-actions { display:flex; gap:8px; margin-top:20px; justify-content:flex-end; }
.btn-amber {
    padding:9px 18px; border-radius:8px; border:none; cursor:pointer;
    font-size:13px; font-weight:600; background:#f59e0b; color:#111; transition:background 0.15s;
}
.btn-amber:hover { background:#d97706; }
.btn-cancel {
    padding:9px 14px; border-radius:8px; border:1px solid #2d2d2d; cursor:pointer;
    font-size:13px; background:transparent; color:#9ca3af; transition:background 0.15s;
}
.btn-cancel:hover { background:#2a2a2a; color:#f5f5f5; }
.phone-chip {
    display:inline-block; font-size:12px; color:#9ca3af;
    background:#1e1e1e; border:1px solid #2a2a2a; border-radius:6px; padding:2px 7px;
    margin-right:4px; margin-bottom:2px;
}
</style>
@endpush

@section('content')
{{-- Search --}}
<form method="GET" action="{{ route('customers.index') }}">
    <div class="search-bar">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ $search }}"
                   class="search-input" placeholder="Search by name, phone, or address…">
        </div>
        <button type="submit" class="btn-amber">Search</button>
        @if ($search)
            <a href="{{ route('customers.index') }}" class="btn-cancel" style="display:inline-flex;align-items:center;">Clear</a>
        @endif
    </div>
</form>

{{-- Table --}}
<div class="rounded-xl overflow-hidden" style="background:#1a1a1a; border:1px solid #2d2d2d;">
    <table class="w-full text-sm">
        <thead>
            <tr style="border-bottom:1px solid #2d2d2d;">
                <th class="text-left px-5 py-3.5 font-medium" style="color:#6b7280;">Name</th>
                <th class="text-left px-5 py-3.5 font-medium" style="color:#6b7280;">Phone Numbers</th>
                <th class="text-left px-5 py-3.5 font-medium" style="color:#6b7280;">Addresses</th>
                <th class="text-right px-5 py-3.5 font-medium" style="color:#6b7280;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr class="group" style="border-bottom:1px solid #222;">
                    <td class="px-5 py-3.5 font-medium" style="color:#f5f5f5;">
                        {{ $customer->name }}
                    </td>
                    <td class="px-5 py-3.5">
                        @forelse ($customer->phones as $phone)
                            <span class="phone-chip">{{ $phone->phone }}</span>
                        @empty
                            <span class="text-xs" style="color:#4b5563;">—</span>
                        @endforelse
                    </td>
                    <td class="px-5 py-3.5" style="color:#9ca3af; max-width:240px;">
                        @if ($customer->addresses->isNotEmpty())
                            <div class="text-xs truncate">{{ $customer->addresses->first()->address }}</div>
                            @if ($customer->addresses->count() > 1)
                                <div class="text-xs" style="color:#6b7280;">+{{ $customer->addresses->count() - 1 }} more</div>
                            @endif
                        @else
                            <span class="text-xs" style="color:#4b5563;">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <button type="button"
                                    onclick="openEditModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', {{ $customer->phones->pluck('phone')->toJson() }}, {{ $customer->addresses->pluck('address')->toJson() }})"
                                    title="Edit"
                                    class="p-1.5 rounded transition-colors"
                                    style="color:#6b7280;"
                                    onmouseover="this.style.color='#f59e0b'; this.style.backgroundColor='rgba(245,158,11,0.1)'"
                                    onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline"
                                  onsubmit="return confirm('Remove customer \'{{ addslashes($customer->name) }}\'?')">
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
                    <td colspan="4" class="px-5 py-12 text-center text-sm" style="color:#6b7280;">
                        @if ($search)
                            No customers match "{{ $search }}".
                        @else
                            No customers yet. <button type="button" onclick="openAddModal()" style="color:#f59e0b; background:none; border:none; cursor:pointer;">Add the first one.</button>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($customers->hasPages())
    <div class="mt-4">{{ $customers->links() }}</div>
@endif

{{-- ── ADD MODAL ───────────────────────────────────────────────────────── --}}
<div class="modal-overlay" id="addModal" onclick="if(event.target===this) closeAddModal()">
    <div class="modal-box">
        <div class="modal-title">New Customer</div>
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-input" placeholder="Customer name" maxlength="150" required>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Numbers <span style="color:#ef4444;">*</span></label>
                <div id="addPhones">
                    <div class="multi-row">
                        <input type="text" name="phones[]" class="form-input" placeholder="e.g. +962 7x xxx xxxx" maxlength="30" required>
                    </div>
                </div>
                <button type="button" class="add-link" onclick="addField('addPhones', 'phones[]', 'Phone number')">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add another phone
                </button>
            </div>

            <div class="form-group">
                <label class="form-label">Addresses</label>
                <div id="addAddresses">
                    <div class="multi-row">
                        <input type="text" name="addresses[]" class="form-input" placeholder="Delivery address" maxlength="500">
                    </div>
                </div>
                <button type="button" class="add-link" onclick="addField('addAddresses', 'addresses[]', 'Address')">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add another address
                </button>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn-amber">Save Customer</button>
            </div>
        </form>
    </div>
</div>

{{-- ── EDIT MODAL ──────────────────────────────────────────────────────── --}}
<div class="modal-overlay" id="editModal" onclick="if(event.target===this) closeEditModal()">
    <div class="modal-box">
        <div class="modal-title">Edit Customer</div>
        <form method="POST" id="editModalForm">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" id="editName" class="form-input" maxlength="150" required>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Numbers <span style="color:#ef4444;">*</span></label>
                <div id="editPhones"></div>
                <button type="button" class="add-link" onclick="addField('editPhones', 'phones[]', 'Phone number')">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add another phone
                </button>
            </div>

            <div class="form-group">
                <label class="form-label">Addresses</label>
                <div id="editAddresses"></div>
                <button type="button" class="add-link" onclick="addField('editAddresses', 'addresses[]', 'Address')">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add another address
                </button>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-amber">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddModal() {
    document.getElementById('addModal').classList.add('open');
}
function closeAddModal() {
    document.getElementById('addModal').classList.remove('open');
}

function openEditModal(id, name, phones, addresses) {
    document.getElementById('editModalForm').action = '/customers/' + id;
    document.getElementById('editName').value = name;

    // Populate phones
    const phonesEl = document.getElementById('editPhones');
    phonesEl.innerHTML = '';
    (phones.length ? phones : ['']).forEach(p => appendRow(phonesEl, 'phones[]', p, 'Phone number', phones.length > 1));

    // Populate addresses
    const addrEl = document.getElementById('editAddresses');
    addrEl.innerHTML = '';
    (addresses.length ? addresses : ['']).forEach(a => appendRow(addrEl, 'addresses[]', a, 'Address', addresses.length > 1));

    document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}

function appendRow(container, fieldName, value, placeholder, showRemove) {
    const row = document.createElement('div');
    row.className = 'multi-row';
    row.innerHTML = `<input type="text" name="${fieldName}" class="form-input" placeholder="${placeholder}" value="${escAttr(value)}" maxlength="${fieldName.startsWith('phones') ? 30 : 500}">` +
        (showRemove ? `<button type="button" class="remove-btn" onclick="this.parentElement.remove()" title="Remove"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>` : '');
    container.appendChild(row);
}

function addField(containerId, fieldName, placeholder) {
    const container = document.getElementById(containerId);
    const isPhone = fieldName.startsWith('phones');
    const row = document.createElement('div');
    row.className = 'multi-row';
    row.innerHTML = `<input type="text" name="${fieldName}" class="form-input" placeholder="${placeholder}" maxlength="${isPhone ? 30 : 500}">` +
        `<button type="button" class="remove-btn" onclick="this.parentElement.remove()" title="Remove"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
    container.appendChild(row);
    row.querySelector('input').focus();
}

function escAttr(s) {
    return String(s).replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeAddModal(); closeEditModal(); }
});
</script>
@endpush
