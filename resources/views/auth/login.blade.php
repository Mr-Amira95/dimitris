@extends('layouts.auth')

@section('title', 'Sign In — ' . config('app.name'))

@section('content')
    <h1 class="text-xl font-semibold mb-1" style="color: #f5f5f5;">Sign In</h1>
    <p class="text-sm mb-6" style="color: #6b7280;">Enter your credentials to access the system</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Login field --}}
        <div>
            <label for="login" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">
                Username or Email
            </label>
            <input
                id="login"
                name="login"
                type="text"
                autocomplete="username"
                autofocus
                value="{{ old('login') }}"
                placeholder="username or email@domain.com"
                class="w-full px-4 py-2.5 rounded-lg text-sm outline-none transition-all"
                style="background-color: #111111; border: 1px solid {{ $errors->has('login') ? '#ef4444' : '#2d2d2d' }}; color: #f5f5f5;"
                onfocus="this.style.borderColor='#f59e0b'"
                onblur="this.style.borderColor='{{ $errors->has('login') ? '#ef4444' : '#2d2d2d' }}'"
            >
            @error('login')
                <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password field --}}
        <div>
            <label for="password" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Password</label>
            <div class="relative">
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full px-4 py-2.5 pr-11 rounded-lg text-sm outline-none transition-all"
                    style="background-color: #111111; border: 1px solid {{ $errors->has('password') ? '#ef4444' : '#2d2d2d' }}; color: #f5f5f5;"
                    onfocus="this.style.borderColor='#f59e0b'"
                    onblur="this.style.borderColor='{{ $errors->has('password') ? '#ef4444' : '#2d2d2d' }}'"
                >
                <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2" style="color: #6b7280;" onmouseover="this.style.color='#9ca3af'" onmouseout="this.style.color='#6b7280'">
                    <svg id="eye-show" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg id="eye-hide" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
            @enderror
            <div class="mt-2 flex justify-end">
                <a href="{{ route('password.request') }}" class="text-xs transition-colors font-medium" style="color: #f59e0b;" onmouseover="this.style.color='#fbbf24'" onmouseout="this.style.color='#f59e0b'">
                    Forgot password?
                </a>
            </div>
        </div>

        {{-- Remember me --}}
        <div class="flex items-center gap-2">
            <input id="remember" name="remember" type="checkbox" class="w-4 h-4 rounded" style="accent-color: #f59e0b;">
            <label for="remember" class="text-sm" style="color: #9ca3af;">Remember me</label>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full py-2.5 rounded-lg text-sm font-semibold transition-colors"
            style="background-color: #f59e0b; color: #111111;"
            onmouseover="this.style.backgroundColor='#d97706'"
            onmouseout="this.style.backgroundColor='#f59e0b'"
        >
            Sign In
        </button>
    </form>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const showIcon = document.getElementById('eye-show');
            const hideIcon = document.getElementById('eye-hide');
            if (input.type === 'password') {
                input.type = 'text';
                showIcon.classList.add('hidden');
                hideIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                showIcon.classList.remove('hidden');
                hideIcon.classList.add('hidden');
            }
        }
    </script>
@endsection
