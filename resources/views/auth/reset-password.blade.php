@extends('layouts.auth')

@section('title', 'Reset Password — ' . config('app.name'))

@section('content')
    <h1 class="text-xl font-semibold mb-1" style="color: #f5f5f5;">Reset Password</h1>
    <p class="text-sm mb-6" style="color: #6b7280;">Choose a new password for your account</p>

    @if ($errors->has('token'))
        <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background-color: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: #ef4444;">
            {{ $errors->first('token') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Email Address</label>
            <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                value="{{ old('email', $email) }}"
                readonly
                class="w-full px-4 py-2.5 rounded-lg text-sm outline-none"
                style="background-color: #111111; border: 1px solid #2d2d2d; color: #6b7280; cursor: not-allowed;"
            >
            @error('email')
                <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">New Password</label>
            <div class="relative">
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Minimum 8 characters"
                    class="w-full px-4 py-2.5 pr-11 rounded-lg text-sm outline-none transition-all"
                    style="background-color: #111111; border: 1px solid {{ $errors->has('password') ? '#ef4444' : '#2d2d2d' }}; color: #f5f5f5;"
                    onfocus="this.style.borderColor='#f59e0b'"
                    onblur="this.style.borderColor='{{ $errors->has('password') ? '#ef4444' : '#2d2d2d' }}'"
                >
                <button type="button" onclick="togglePass('password','eye1s','eye1h')" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: #6b7280;">
                    <svg id="eye1s" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg id="eye1h" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Confirm New Password</label>
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
                <button type="button" onclick="togglePass('password_confirmation','eye2s','eye2h')" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: #6b7280;">
                    <svg id="eye2s" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg id="eye2h" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
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
            Reset Password
        </button>
    </form>

    <script>
        function togglePass(inputId, showId, hideId) {
            const input = document.getElementById(inputId);
            const show = document.getElementById(showId);
            const hide = document.getElementById(hideId);
            if (input.type === 'password') {
                input.type = 'text';
                show.classList.add('hidden');
                hide.classList.remove('hidden');
            } else {
                input.type = 'password';
                show.classList.remove('hidden');
                hide.classList.add('hidden');
            }
        }
    </script>
@endsection
