@extends('layouts.app')

@section('title', 'Users — ' . config('app.name'))
@section('page-title', 'Users')
@section('page-subtitle', 'Manage system accounts and access')

@section('header-actions')
<form method="GET" action="{{ route('admin.users.index') }}" id="searchForm" class="flex gap-2 items-center">
    <div class="relative">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Search by name, username, or email…"
               id="searchInput"
               style="background:#141414; border:1px solid #2d2d2d; border-radius:8px; padding:7px 12px 7px 34px; font-size:13px; color:#f5f5f5; outline:none; width:360px; transition:border-color 0.15s;"
               oninput="debouncedSearch()">
        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2" width="14" height="14" fill="none"
             viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/>
        </svg>
    </div>
    @if($search)
        <a href="{{ route('admin.users.index') }}"
           style="padding:7px 14px; border-radius:8px; border:1px solid #2d2d2d; font-size:13px; background:transparent; color:#9ca3af; text-decoration:none;"
           onmouseover="this.style.background='#2a2a2a'; this.style.color='#f5f5f5'"
           onmouseout="this.style.background='transparent'; this.style.color='#9ca3af'">Clear</a>
    @endif
</form>
<button type="button" onclick="openAddModal()"
        style="padding:7px 14px; border-radius:8px; border:none; cursor:pointer; font-size:13px; font-weight:600; background:#f59e0b; color:#111; white-space:nowrap; transition:background 0.15s;"
        onmouseover="this.style.background='#d97706'"
        onmouseout="this.style.background='#f59e0b'">
    + New User
</button>
@endsection

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
.modal-title { font-size:15px; font-weight:600; color:#f5f5f5; margin-bottom:18px; }
.modal-subtitle { font-size:13.5px; color:#9ca3af; margin-bottom:16px; line-height:1.5; }
.form-label { display:block; font-size:12px; font-weight:600; color:#9ca3af; margin-bottom:5px; text-transform:uppercase; letter-spacing:.04em; }
.form-input {
    width:100%; background:#141414; border:1px solid #2d2d2d; border-radius:8px;
    padding:9px 12px; font-size:13.5px; color:#f5f5f5; outline:none; box-sizing:border-box;
    transition:border-color 0.15s;
}
.form-input:focus { border-color:#f59e0b; }
.form-input::placeholder { color:#4b5563; }
.form-group { margin-bottom:14px; }
.field-error { color:#ef4444; font-size:12px; margin-top:4px; display:block; }
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
.btn-delete {
    padding:9px 18px; border-radius:8px; border:none; cursor:pointer;
    font-size:13px; font-weight:600; background:#ef4444; color:#fff; transition:background 0.15s;
}
.btn-delete:hover { background:#dc2626; }

/* Switch Toggle Styling */
.switch {
    position: relative;
    display: inline-block;
    width: 36px;
    height: 20px;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #333333;
    transition: 0.2s;
    border-radius: 20px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: #9ca3af;
    transition: 0.2s;
    border-radius: 50%;
}
input:checked + .slider {
    background-color: #f59e0b;
}
input:checked + .slider:before {
    transform: translateX(16px);
    background-color: #111111;
}
input:disabled + .slider {
    opacity: 0.4;
    cursor: not-allowed;
}
</style>
@endpush

@section('content')
@php
    $addFormFailed = old('_form') === 'add_user';
    $oldFullName  = $addFormFailed ? old('full_name', '') : '';
    $oldUsername  = $addFormFailed ? old('username', '') : '';
    $oldEmail     = $addFormFailed ? old('email', '') : '';
    $oldRoleId    = $addFormFailed ? old('role_id', '') : '';

    $editFormFailed = old('_form') === 'edit_user';
    $oldEditUserId  = $editFormFailed ? old('edit_user_id', '') : '';
@endphp

{{-- Table --}}
<div class="rounded-xl overflow-hidden" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
    <table class="w-full text-sm">
        <thead>
            <tr style="border-bottom: 1px solid #222222; background-color: #161616;">
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af;">Name</th>
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af;">Username</th>
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af;">Email</th>
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af;">Role</th>
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af;">Status</th>
                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: #9ca3af; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr class="group" style="border-bottom: 1px solid #222222;">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                 style="background-color: rgba(245,158,11,0.15); color: #f59e0b;">
                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                            </div>
                            <span class="font-medium" style="color: #f5f5f5;">{{ $user->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5" style="color: #9ca3af;">{{ $user->username }}</td>
                    <td class="px-5 py-3.5" style="color: #9ca3af;">{{ $user->email }}</td>
                    <td class="px-5 py-3.5">
                        @if ($user->role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                  style="background-color: rgba(245,158,11,0.12); color: #f59e0b;">
                                {{ $user->role->name }}
                            </span>
                        @else
                            <span class="text-xs" style="color: #4b5563;">No role</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="inline-block align-middle">
                                @csrf
                                <label class="switch" title="{{ $user->id === auth()->id() ? 'You cannot deactivate your own account' : ($user->is_active ? 'Deactivate' : 'Activate') }}">
                                    <input type="checkbox" {{ $user->is_active ? 'checked' : '' }} 
                                           {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                           onchange="this.form.submit()">
                                    <span class="slider"></span>
                                </label>
                            </form>
                            @if (! $user->hasPasswordSetup())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.2);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                    Pending Setup
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Reset Password (always available) --}}
                            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="inline">
                                @csrf
                                <button type="button"
                                        title="Reset Password"
                                        onclick="openConfirmResetModal('{{ addslashes($user->email) }}', this.closest('form'))"
                                        class="p-1.5 rounded transition-colors"
                                        style="color: #6b7280;"
                                        onmouseover="this.style.color='#f59e0b'; this.style.backgroundColor='rgba(245,158,11,0.1)'"
                                        onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </button>
                            </form>

                            {{-- Edit --}}
                            <button type="button"
                                    onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->full_name) }}', '{{ addslashes($user->username) }}', '{{ addslashes($user->email) }}', {{ $user->role_id ?? 'null' }}, {{ $user->is_active ? 1 : 0 }}, {{ $user->hasPasswordSetup() ? 1 : 0 }})"
                                    title="Edit"
                                    class="p-1.5 rounded transition-colors"
                                    style="color: #6b7280;"
                                    onmouseover="this.style.color='#f59e0b'; this.style.backgroundColor='rgba(245,158,11,0.1)'"
                                    onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>

                            {{-- Soft Delete --}}
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            title="Delete"
                                            onclick="openDeleteModal('{{ addslashes($user->full_name) }}', this.closest('form'))"
                                            class="p-1.5 rounded transition-colors"
                                            style="color: #6b7280;"
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
                    <td colspan="6" class="px-5 py-12 text-center text-sm" style="color: #6b7280;">
                        @if ($search)
                            No users match "{{ $search }}".
                        @else
                            No users found. <button type="button" onclick="openAddModal()" style="color:#f59e0b; background:none; border:none; cursor:pointer;">Create the first user.</button>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($users->hasPages())
    <div class="mt-4">{{ $users->links() }}</div>
@endif

{{-- ── ADD MODAL ───────────────────────────────────────────────────────── --}}
<div class="modal-overlay" id="addModal" onclick="if(event.target===this) closeAddModal()">
    <div class="modal-box">
        <div class="modal-title">New User</div>
        
        <div class="mb-4 px-3 py-2.5 rounded-lg flex items-start gap-2.5" style="background-color: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.15);">
            <svg class="flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs" style="color: #9ca3af; line-height: 1.4;">
                An invitation email will be sent to the user to set their password. The link expires in <strong style="color: #f59e0b;">48 hours</strong>.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <input type="hidden" name="_form" value="add_user">

            <div class="form-group">
                <label class="form-label" for="add_full_name">Full Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="full_name" id="add_full_name" class="form-input" placeholder="e.g. John Smith"
                       maxlength="100" required value="{{ $oldFullName }}">
                @if($addFormFailed)
                    @error('full_name') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="add_username">Username <span style="color:#ef4444;">*</span></label>
                <input type="text" name="username" id="add_username" class="form-input" placeholder="e.g. jsmith"
                       maxlength="50" required value="{{ $oldUsername }}">
                <span style="font-size: 11px; color: #6b7280; display: block; margin-top: 2px;">Letters, numbers, hyphens and underscores only.</span>
                @if($addFormFailed)
                    @error('username') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="add_email">Email Address <span style="color:#ef4444;">*</span></label>
                <input type="email" name="email" id="add_email" class="form-input" placeholder="user@example.com"
                       maxlength="150" required value="{{ $oldEmail }}">
                @if($addFormFailed)
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="add_role_id">Role <span style="color:#ef4444;">*</span></label>
                <select name="role_id" id="add_role_id" class="form-input" required style="appearance: none; background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 width%3D%2212%22 height%3D%2212%22 fill%3D%22none%22 viewBox%3D%220 0 24 24%22 stroke%3D%22%239ca3af%22 stroke-width%3D%222.5%22%3E%3Cpath stroke-linecap%3D%22round%22 stroke-linejoin%3D%22round%22 d%3D%22M19 9l-7 7-7-7%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 12px center; background-size: 12px; padding-right: 32px;">
                    <option value="" style="background-color: #1a1a1a; color: #6b7280;">— Select a role —</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ $oldRoleId == $role->id ? 'selected' : '' }} style="background-color: #1a1a1a; color: #f5f5f5;">
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @if($addFormFailed)
                    @error('role_id') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
                <button type="submit" class="btn-amber">Create User</button>
            </div>
        </form>
    </div>
</div>

{{-- ── EDIT MODAL ──────────────────────────────────────────────────────── --}}
<div class="modal-overlay" id="editModal" onclick="if(event.target===this) closeEditModal()">
    <div class="modal-box">
        <div class="modal-title">Edit User</div>
        
        <div id="editPendingSetupWarning" class="mb-4 px-3 py-2 rounded-lg flex items-center justify-between" style="display: none; background-color: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2);">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-xs font-medium" style="color: #f59e0b;">This user has not yet set their password.</span>
            </div>
            <form method="POST" id="resendInvitationForm" class="inline">
                @csrf
                <button type="submit" class="text-[11px] font-semibold px-2.5 py-0.5 rounded transition-colors"
                        style="background-color: rgba(245,158,11,0.15); color: #f59e0b; border: none; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='rgba(245,158,11,0.25)'"
                        onmouseout="this.style.backgroundColor='rgba(245,158,11,0.15)'">
                    Resend
                </button>
            </form>
        </div>

        <form method="POST" id="editModalForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="_form" value="edit_user">
            <input type="hidden" name="edit_user_id" id="editUserId" value="{{ $oldEditUserId }}">
            <input type="hidden" name="has_password_setup" id="editHasPasswordSetup">

            <div class="form-group">
                <label class="form-label" for="edit_full_name">Full Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="full_name" id="edit_full_name" class="form-input" required maxlength="100">
                @if($editFormFailed)
                    @error('full_name') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="edit_username">Username <span style="color:#ef4444;">*</span></label>
                <input type="text" name="username" id="edit_username" class="form-input" required maxlength="50">
                @if($editFormFailed)
                    @error('username') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="edit_email">Email Address <span style="color:#ef4444;">*</span></label>
                <input type="email" name="email" id="edit_email" class="form-input" required maxlength="150">
                @if($editFormFailed)
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="edit_role_id">Role <span style="color:#ef4444;">*</span></label>
                <select name="role_id" id="edit_role_id" class="form-input" required style="appearance: none; background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 width%3D%2212%22 height%3D%2212%22 fill%3D%22none%22 viewBox%3D%220 0 24 24%22 stroke%3D%22%239ca3af%22 stroke-width%3D%222.5%22%3E%3Cpath stroke-linecap%3D%22round%22 stroke-linejoin%3D%22round%22 d%3D%22M19 9l-7 7-7-7%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 12px center; background-size: 12px; padding-right: 32px;">
                    <option value="" style="background-color: #1a1a1a; color: #6b7280;">— Select a role —</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" style="background-color: #1a1a1a; color: #f5f5f5;">
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @if($editFormFailed)
                    @error('role_id') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            {{-- Active Toggle inside modal --}}
            <div class="form-group" id="editActiveToggleGroup" style="margin-top: 18px; margin-bottom: 6px;">
                <div class="flex items-center gap-3">
                    <label class="switch">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="edit_is_active">
                        <span class="slider"></span>
                    </label>
                    <label for="edit_is_active" class="text-sm" style="color: #9ca3af; font-weight: 500;">Account Active</label>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-amber">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- ── RESET PASSWORD CONFIRMATION MODAL ───────────────────────────────── --}}
<div class="modal-overlay" id="confirmResetModal" onclick="if(event.target===this) closeConfirmResetModal()">
    <div class="modal-box" style="max-height:unset;">
        <div class="modal-title">Reset Password</div>
        <p class="modal-subtitle" id="confirmResetMsg"></p>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeConfirmResetModal()">Cancel</button>
            <button type="button" class="btn-amber" onclick="confirmResetSubmit()">Send Link</button>
        </div>
    </div>
</div>

{{-- ── DELETE CONFIRMATION MODAL ───────────────────────────────────────── --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this) closeDeleteModal()">
    <div class="modal-box" style="max-height:unset;">
        <div class="modal-title">Delete User</div>
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
const authUserId = {{ auth()->id() }};

function openAddModal() {
    document.getElementById('addModal').classList.add('open');
}
function closeAddModal() {
    document.getElementById('addModal').classList.remove('open');
}

function openEditModal(id, fullName, username, email, roleId, isActive, hasPasswordSetup) {
    document.getElementById('editModalForm').action = '/admin/users/' + id;
    document.getElementById('editUserId').value = id;
    document.getElementById('edit_full_name').value = fullName;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role_id').value = roleId || '';
    document.getElementById('editHasPasswordSetup').value = hasPasswordSetup ? 1 : 0;

    const isSelf = (parseInt(id) === authUserId);
    const toggleGroup = document.getElementById('editActiveToggleGroup');
    if (isSelf) {
        toggleGroup.style.display = 'none';
        document.getElementById('edit_is_active').checked = true;
    } else {
        toggleGroup.style.display = 'block';
        document.getElementById('edit_is_active').checked = (isActive == 1 || isActive === true || isActive === '1');
    }

    const resendWarning = document.getElementById('editPendingSetupWarning');
    if (!hasPasswordSetup && id) {
        resendWarning.style.display = 'flex';
        document.getElementById('resendInvitationForm').action = '/admin/users/' + id + '/resend-invitation';
    } else {
        resendWarning.style.display = 'none';
    }

    document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}

let pendingResetForm = null;
function openConfirmResetModal(email, form) {
    pendingResetForm = form;
    document.getElementById('confirmResetMsg').textContent = 'Are you sure you want to send a password reset link to "' + email + '"?';
    document.getElementById('confirmResetModal').classList.add('open');
}
function closeConfirmResetModal() {
    pendingResetForm = null;
    document.getElementById('confirmResetModal').classList.remove('open');
}
function confirmResetSubmit() {
    if (pendingResetForm) pendingResetForm.submit();
    closeConfirmResetModal();
}

let pendingDeleteForm = null;
function openDeleteModal(fullName, form) {
    pendingDeleteForm = form;
    document.getElementById('deleteModalMsg').textContent = 'Are you sure you want to remove "' + fullName + '"? This can be undone by an administrator.';
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

let searchTimer;
function debouncedSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        document.getElementById('searchForm').submit();
    }, 400);
}

@if($addFormFailed && $errors->any())
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('addModal').classList.add('open');
});
@endif

@if($editFormFailed && $errors->any())
document.addEventListener('DOMContentLoaded', () => {
    openEditModal(
        '{{ $oldEditUserId }}',
        '{{ old('full_name') }}',
        '{{ old('username') }}',
        '{{ old('email') }}',
        '{{ old('role_id') }}',
        '{{ old('is_active') }}',
        {{ old('has_password_setup', 1) }}
    );
});
@endif

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
        closeConfirmResetModal();
        closeDeleteModal();
    }
});
</script>
@endpush
