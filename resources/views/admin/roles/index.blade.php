@extends('layouts.app')

@section('title', 'Roles & Permissions — ' . config('app.name'))
@section('page-title', 'Roles & Permissions')
@section('page-subtitle', 'Define what each role can access and do')

@section('header-actions')
    <a href="{{ route('admin.roles.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
       style="background-color: #f59e0b; color: #111111;"
       onmouseover="this.style.backgroundColor='#d97706'"
       onmouseout="this.style.backgroundColor='#f59e0b'">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Role
    </a>
@endsection

@section('content')
    <div class="space-y-3">
        @forelse ($roles as $role)
            <div class="rounded-xl p-5" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-semibold" style="color: #f5f5f5;">{{ $role->name }}</h3>
                            @if ($role->is_system)
                                <span class="px-2 py-0.5 rounded text-xs" style="background-color: rgba(107,114,128,0.15); color: #6b7280;">System</span>
                            @endif
                            <span class="px-2 py-0.5 rounded text-xs" style="background-color: rgba(245,158,11,0.1); color: #f59e0b;">
                                {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                            </span>
                        </div>

                        @php
                            $perms = $role->permissions ?? [];
                            $allPerms = \App\Models\Role::PERMISSIONS;
                        @endphp

                        @if (count($perms) > 0)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($perms as $perm)
                                    @if (isset($allPerms[$perm]))
                                        <span class="px-2 py-0.5 rounded text-xs" style="background-color: rgba(34,197,94,0.08); color: #4ade80; border: 1px solid rgba(34,197,94,0.15);">
                                            {{ $allPerms[$perm] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs" style="color: #4b5563;">No permissions assigned.</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('admin.roles.edit', $role) }}"
                           class="p-1.5 rounded transition-colors"
                           style="color: #6b7280;"
                           title="Edit role"
                           onmouseover="this.style.color='#f59e0b'; this.style.backgroundColor='rgba(245,158,11,0.1)'"
                           onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>

                        @if (! $role->is_system)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline"
                                  onsubmit="return confirm('Delete role \'{{ addslashes($role->name) }}\'? {{ $role->users_count > 0 ? $role->users_count . ' user(s) will lose this role.' : '' }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Delete role"
                                        class="p-1.5 rounded transition-colors"
                                        style="color: #6b7280;"
                                        onmouseover="this.style.color='#ef4444'; this.style.backgroundColor='rgba(239,68,68,0.1)'"
                                        onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl p-12 text-center" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
                <p class="text-sm" style="color: #6b7280;">No roles yet. <a href="{{ route('admin.roles.create') }}" style="color: #f59e0b;">Create the first role.</a></p>
            </div>
        @endforelse
    </div>
@endsection
