@extends('layouts.app')

@section('title', 'New User — ' . config('app.name'))
@section('page-title', 'New User')
@section('page-subtitle', 'Create a new system account')

@section('content')
    <div class="max-w-lg">
        <div class="rounded-xl p-6" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">

            <div class="mb-5 px-4 py-3 rounded-lg flex items-start gap-3" style="background-color: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.15);">
                <svg class="flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm" style="color: #9ca3af;">
                    An invitation email will be sent to the user with a link to set their password. The link expires in <strong style="color: #f59e0b;">48 hours</strong>.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="full_name" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Full Name <span style="color: #ef4444;">*</span></label>
                    <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" placeholder="e.g. John Smith"
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
                    <input id="username" name="username" type="text" value="{{ old('username') }}" placeholder="e.g. jsmith"
                           class="w-full px-4 py-2.5 rounded-lg text-sm outline-none transition-all"
                           style="background-color: #111111; border: 1px solid {{ $errors->has('username') ? '#ef4444' : '#2d2d2d' }}; color: #f5f5f5;"
                           onfocus="this.style.borderColor='#f59e0b'"
                           onblur="this.style.borderColor='{{ $errors->has('username') ? '#ef4444' : '#2d2d2d' }}'">
                    <p class="mt-1 text-xs" style="color: #6b7280;">Letters, numbers, hyphens and underscores only.</p>
                    @error('username')
                        <p class="mt-1 text-xs" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Email Address <span style="color: #ef4444;">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="user@example.com"
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
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}
                                    style="background-color: #1a1a1a; color: #f5f5f5;">
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                            style="background-color: #f59e0b; color: #111111;"
                            onmouseover="this.style.backgroundColor='#d97706'"
                            onmouseout="this.style.backgroundColor='#f59e0b'">
                        Create User &amp; Send Invitation
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
