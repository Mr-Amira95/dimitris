@extends('layouts.app')

@section('title', 'Edit User — ' . config('app.name'))
@section('page-title', 'Edit User')
@section('page-subtitle', $user->full_name)

@section('content')
    <div class="max-w-lg">
        <div class="rounded-xl p-6" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">

            @if (! $user->hasPasswordSetup())
                <div class="mb-5 px-4 py-3 rounded-lg flex items-center justify-between" style="background-color: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2);">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="text-sm" style="color: #f59e0b;">This user has not yet set their password.</span>
                    </div>
                    <form method="POST" action="{{ route('admin.users.resend-invitation', $user) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-medium px-3 py-1 rounded transition-colors"
                                style="background-color: rgba(245,158,11,0.15); color: #f59e0b;"
                                onmouseover="this.style.backgroundColor='rgba(245,158,11,0.25)'"
                                onmouseout="this.style.backgroundColor='rgba(245,158,11,0.15)'">
                            Resend Invitation
                        </button>
                    </form>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="full_name" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Full Name <span style="color: #ef4444;">*</span></label>
                    <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $user->full_name) }}"
                           class="w-full px-4 py-2.5 rounded-lg text-sm outline-none transition-all"
                           style="background-color: #111111; border: 1px solid {{ $errors->has('full_name') ? '#ef4444' : '#2d2d2d' }}; color: #f5f5f5;"
                           onfocus="this.style.borderColor='#f59e0b'"
                           onblur="this.style.borderColor='{{ $errors->has('full_name') ? '#ef4444' : '#2d2d2d' }}'">
                    @error('full_name')
                        <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Username <span style="color: #ef4444;">*</span></label>
                    <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}"
                           class="w-full px-4 py-2.5 rounded-lg text-sm outline-none transition-all"
                           style="background-color: #111111; border: 1px solid {{ $errors->has('username') ? '#ef4444' : '#2d2d2d' }}; color: #f5f5f5;"
                           onfocus="this.style.borderColor='#f59e0b'"
                           onblur="this.style.borderColor='{{ $errors->has('username') ? '#ef4444' : '#2d2d2d' }}'">
                    @error('username')
                        <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Email Address <span style="color: #ef4444;">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2.5 rounded-lg text-sm outline-none transition-all"
                           style="background-color: #111111; border: 1px solid {{ $errors->has('email') ? '#ef4444' : '#2d2d2d' }}; color: #f5f5f5;"
                           onfocus="this.style.borderColor='#f59e0b'"
                           onblur="this.style.borderColor='{{ $errors->has('email') ? '#ef4444' : '#2d2d2d' }}'">
                    @error('email')
                        <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role_id" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Role <span style="color: #ef4444;">*</span></label>
                    <select id="role_id" name="role_id"
                            class="w-full px-4 py-2.5 rounded-lg text-sm outline-none transition-all"
                            style="background-color: #111111; border: 1px solid {{ $errors->has('role_id') ? '#ef4444' : '#2d2d2d' }}; color: #f5f5f5;"
                            onfocus="this.style.borderColor='#f59e0b'"
                            onblur="this.style.borderColor='{{ $errors->has('role_id') ? '#ef4444' : '#2d2d2d' }}'">
                        <option value="" style="background-color: #1a1a1a; color: #6b7280;">— Select a role —</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}
                                    style="background-color: #1a1a1a; color: #f5f5f5;">
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Active toggle --}}
                @if ($user->id !== auth()->id())
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="is_active" class="sr-only peer"
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <div class="w-10 h-5 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:rounded-full after:w-4 after:h-4 after:transition-all"
                                 style="background-color: #333333; --tw-peer-checked-bg: #f59e0b;"
                                 onclick="this.parentElement.querySelector('input[type=checkbox]').checked = !this.parentElement.querySelector('input[type=checkbox]').checked; this.style.backgroundColor = this.parentElement.querySelector('input[type=checkbox]').checked ? '#f59e0b' : '#333333';"
                                 id="toggle-bg"></div>
                        </label>
                        <label for="is_active" class="text-sm" style="color: #d1d5db;">Account Active</label>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const cb = document.getElementById('is_active');
                            const bg = document.getElementById('toggle-bg');
                            bg.style.backgroundColor = cb.checked ? '#f59e0b' : '#333333';
                        });
                    </script>
                @endif

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                            style="background-color: #f59e0b; color: #111111;"
                            onmouseover="this.style.backgroundColor='#d97706'"
                            onmouseout="this.style.backgroundColor='#f59e0b'">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors"
                       style="color: #9ca3af; background-color: #222222;"
                       onmouseover="this.style.color='#f5f5f5'"
                       onmouseout="this.style.color='#9ca3af'">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
