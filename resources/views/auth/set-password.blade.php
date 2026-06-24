@extends('layouts.auth')

@section('title', 'Set Your Password — ' . config('app.name'))

@section('content')
    <div class="mb-5">
        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full mb-3" style="background-color: rgba(245,158,11,0.15);">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
        <h1 class="text-xl font-semibold" style="color: #f5f5f5;">Welcome, {{ $user->full_name }}!</h1>
        <p class="text-sm mt-1" style="color: #6b7280;">Set a password to activate your account and sign in.</p>
    </div>

    <div class="mb-5 px-4 py-3 rounded-lg text-sm" style="background-color: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2);">
        <p class="text-xs mb-0.5" style="color: #9ca3af;">Signing in as</p>
        <p class="font-medium" style="color: #f59e0b;">{{ $user->email }}</p>
    </div>

    <form method="POST" action="{{ route('auth.set-password', $token) }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Password</label>
            <div class="relative">
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    autofocus
                    placeholder="Minimum 8 characters"
                    class="w-full px-4 py-2.5 pr-11 rounded-lg text-sm outline-none transition-all"
                    style="background-color: #111111; border: 1px solid {{ $errors->has('password') ? '#ef4444' : '#2d2d2d' }}; color: #f5f5f5;"
                    onfocus="this.style.borderColor='#f59e0b'"
                    onblur="this.style.borderColor='{{ $errors->has('password') ? '#ef4444' : '#2d2d2d' }}'"
                >
                <button type="button" onclick="togglePass('password','e1s','e1h')" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: #6b7280;">
                    <svg id="e1s" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg id="e1h" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Confirm Password</label>
            <div class="relative">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Repeat your password"
                    class="w-full px-4 py-2.5 pr-11 rounded-lg text-sm outline-none transition-all"
                    style="background-color: #111111; border: 1px solid #2d2d2d; color: #f5f5f5;"
                    onfocus="this.style.borderColor='#f59e0b'"
                    onblur="this.style.borderColor='#2d2d2d'"
                >
                <button type="button" onclick="togglePass('password_confirmation','e2s','e2h')" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: #6b7280;">
                    <svg id="e2s" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg id="e2h" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                </button>
            </div>
        </div>

        <button
            type="submit"
            class="w-full py-2.5 rounded-lg text-sm font-semibold transition-colors"
            style="background-color: #f59e0b; color: #111111;"
            onmouseover="this.style.backgroundColor='#d97706'"
            onmouseout="this.style.backgroundColor='#f59e0b'"
        >
            Activate Account
        </button>
    </form>

    <script>
        function togglePass(inputId, showId, hideId) {
            const input = document.getElementById(inputId);
            document.getElementById(showId).classList.toggle('hidden');
            document.getElementById(hideId).classList.toggle('hidden');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
@endsection
