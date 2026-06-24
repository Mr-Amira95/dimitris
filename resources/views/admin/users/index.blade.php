@extends('layouts.app')

@section('title', 'Users — ' . config('app.name'))
@section('page-title', 'Users')
@section('page-subtitle', 'Manage system accounts and access')

@section('header-actions')
    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
       style="background-color: #f59e0b; color: #111111;"
       onmouseover="this.style.backgroundColor='#d97706'"
       onmouseout="this.style.backgroundColor='#f59e0b'">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New User
    </a>
@endsection

@section('content')
    <div class="rounded-xl overflow-hidden" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid #2d2d2d;">
                    <th class="text-left px-5 py-3.5 font-medium" style="color: #6b7280;">Full Name</th>
                    <th class="text-left px-5 py-3.5 font-medium" style="color: #6b7280;">Username</th>
                    <th class="text-left px-5 py-3.5 font-medium" style="color: #6b7280;">Email</th>
                    <th class="text-left px-5 py-3.5 font-medium" style="color: #6b7280;">Role</th>
                    <th class="text-left px-5 py-3.5 font-medium" style="color: #6b7280;">Status</th>
                    <th class="text-right px-5 py-3.5 font-medium" style="color: #6b7280;">Actions</th>
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
                            @if (! $user->hasPasswordSetup())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.2);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                    Pending Setup
                                </span>
                            @elseif ($user->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: rgba(34,197,94,0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2);">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium"
                                      style="background-color: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2);">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-1">

                                {{-- Reset Password (always available) --}}
                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="inline"
                                      onsubmit="return confirm('Send a password reset link to {{ addslashes($user->email) }}?')">
                                    @csrf
                                    <button type="submit"
                                            title="Reset Password"
                                            class="p-1.5 rounded transition-colors"
                                            style="color: #6b7280;"
                                            onmouseover="this.style.color='#f59e0b'; this.style.backgroundColor='rgba(245,158,11,0.1)'"
                                            onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </button>
                                </form>

                                {{-- Toggle Active/Inactive --}}
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                                class="p-1.5 rounded transition-colors"
                                                style="color: #6b7280;"
                                                onmouseover="this.style.color='{{ $user->is_active ? '#f59e0b' : '#22c55e' }}'; this.style.backgroundColor='{{ $user->is_active ? 'rgba(245,158,11,0.1)' : 'rgba(34,197,94,0.1)' }}'"
                                                onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                            @if ($user->is_active)
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                @endif

                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   title="Edit"
                                   class="p-1.5 rounded transition-colors"
                                   style="color: #6b7280;"
                                   onmouseover="this.style.color='#f59e0b'; this.style.backgroundColor='rgba(245,158,11,0.1)'"
                                   onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>

                                {{-- Soft Delete --}}
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                          onsubmit="return confirm('Delete {{ addslashes($user->full_name) }}? This can be undone by an administrator.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Delete"
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
                            No users found. <a href="{{ route('admin.users.create') }}" style="color: #f59e0b;">Create the first user.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif
@endsection
