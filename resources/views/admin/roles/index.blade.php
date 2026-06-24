@extends('layouts.app')

@section('title', 'Roles — ' . config('app.name'))
@section('page-title', 'Roles')
@section('page-subtitle', 'Define what each role can access and do')

@push('styles')
<style>
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
.modal-title { font-size:15px; font-weight:600; color:#f5f5f5; margin-bottom:12px; }
.modal-subtitle { font-size:13.5px; color:#9ca3af; margin-bottom:20px; line-height:1.5; }
.modal-actions { display:flex; gap:8px; justify-content:flex-end; }
.btn-cancel {
    padding:9px 14px; border-radius:8px; border:1px solid #2d2d2d; cursor:pointer;
    font-size:13px; background:transparent; color:#9ca3af; transition:background 0.15s;
}
.btn-cancel:hover { background:#2a2a2a; color:#f5f5f5; }
.btn-delete {
    padding:9px 18px; border-radius:8px; border:none; cursor:pointer;
    font-size:13px; font-weight:600; background:#ef4444; color:#fff; transition:background 0.15s;
}
.btn-delete:hover { background:#dc2626; }
</style>
@endpush

@section('header-actions')
<form method="GET" action="{{ route('admin.roles.index') }}" id="searchForm" class="flex gap-2 items-center">
    <div class="relative">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Search by role name…"
               id="searchInput"
               style="background:#141414; border:1px solid #2d2d2d; border-radius:8px; padding:7px 12px 7px 34px; font-size:13px; color:#f5f5f5; outline:none; width:360px; transition:border-color 0.15s;"
               oninput="debouncedSearch()">
        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2" width="14" height="14" fill="none"
             viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/>
        </svg>
    </div>
    @if($search)
        <a href="{{ route('admin.roles.index') }}"
           style="padding:7px 14px; border-radius:8px; border:1px solid #2d2d2d; font-size:13px; background:transparent; color:#9ca3af; text-decoration:none;"
           onmouseover="this.style.background='#2a2a2a'; this.style.color='#f5f5f5'"
           onmouseout="this.style.background='transparent'; this.style.color='#9ca3af'">Clear</a>
    @endif
</form>
<a href="{{ route('admin.roles.create') }}"
   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
   style="background-color: #f59e0b; color: #111111; height:34px; box-sizing:border-box;"
   onmouseover="this.style.backgroundColor='#d97706'"
   onmouseout="this.style.backgroundColor='#f59e0b'">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    New Role
</a>
@endsection

@section('content')
<div class="rounded-xl overflow-hidden" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
    <table class="w-full text-sm">
        <thead>
            <tr style="border-bottom: 1px solid #222222; background-color: #161616;">
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af; width: 220px;">Role</th>
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af; width: 100px;">Users</th>
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af;">Permissions</th>
                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $role)
                <tr class="group" style="border-bottom: 1px solid #222222;">
                    <td class="px-5 py-3.5 font-medium" style="color: #f5f5f5; width: 220px;">
                        <div class="flex items-center gap-2">
                            <span>{{ $role->name }}</span>
                            @if ($role->is_system)
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold" style="background-color: rgba(107,114,128,0.15); color: #9ca3af;">System</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5" style="width: 100px;">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold" style="background-color: rgba(245,158,11,0.1); color: #f59e0b; white-space: nowrap;">
                            {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        @php
                            $perms = $role->permissions ?? [];
                            $allPerms = \App\Models\Role::PERMISSIONS;
                        @endphp
                        @if (count($perms) > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach ($perms as $perm)
                                    @if (isset($allPerms[$perm]))
                                        <span class="px-2 py-0.5 rounded text-[11px]" style="background-color: rgba(34,197,94,0.08); color: #4ade80; border: 1px solid rgba(34,197,94,0.15); white-space: nowrap; margin-bottom: 2px;">
                                            {{ $allPerms[$perm] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs" style="color: #4b5563;">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.roles.edit', $role) }}"
                               class="p-1.5 rounded transition-colors"
                               style="color: #6b7280;"
                               title="Edit role"
                               onmouseover="this.style.color='#f59e0b'; this.style.backgroundColor='rgba(245,158,11,0.1)'"
                               onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>

                            @if (! $role->is_system)
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            title="Delete role"
                                            onclick="openDeleteModal('{{ addslashes($role->name) }}', {{ $role->users_count }}, this.closest('form'))"
                                            class="p-1.5 rounded transition-colors"
                                            style="color: #6b7280; background: transparent; border: none; cursor: pointer;"
                                            onmouseover="this.style.color='#ef4444'; this.style.backgroundColor='rgba(239,68,68,0.1)'"
                                            onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center text-sm" style="color: #6b7280;">
                        @if ($search)
                            No roles match "{{ $search }}".
                        @else
                            No roles found. <a href="{{ route('admin.roles.create') }}" style="color: #f59e0b;">Create the first role.</a>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── DELETE CONFIRMATION MODAL ───────────────────────────────────────── --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this) closeDeleteModal()">
    <div class="modal-box" style="max-height:unset;">
        <div class="modal-title">Delete Role</div>
        <p class="modal-subtitle" id="deleteModalMsg"></p>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn-delete" onclick="confirmDeleteSubmit()">Delete</button>
        </div>
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

let pendingDeleteForm = null;
function openDeleteModal(roleName, usersCount, form) {
    pendingDeleteForm = form;
    let msg = 'Are you sure you want to remove the role "' + roleName + '"?';
    if (usersCount > 0) {
        msg += ' ' + usersCount + ' user(s) will lose this role.';
    }
    document.getElementById('deleteModalMsg').textContent = msg;
    document.getElementById('deleteModal').classList.add('open');
}
function closeDeleteModal() {
    pendingDeleteForm = null;
    document.getElementById('deleteModal').classList.remove('open');
}
function confirmDeleteSubmit() {
    if (pendingDeleteForm) pendingDeleteForm.submit();
    closeDeleteModal();
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
@endpush
