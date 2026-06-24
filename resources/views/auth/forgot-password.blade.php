@extends('layouts.auth')

@section('title', 'Forgot Password — ' . config('app.name'))

@section('content')
    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('login') }}" class="transition-colors" style="color: #6b7280;" onmouseover="this.style.color='#f59e0b'" onmouseout="this.style.color='#6b7280'">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold" style="color: #f5f5f5;">Forgot Password</h1>
            <p class="text-sm" style="color: #6b7280;">Enter your username or email to receive a reset link</p>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-lg text-sm" style="background-color: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.3); color: #22c55e;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="login" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">
                Username or Email
            </label>
            <input
                id="login"
                name="login"
                type="text"
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

        <button
            type="submit"
            class="w-full py-2.5 rounded-lg text-sm font-semibold transition-colors"
            style="background-color: #f59e0b; color: #111111;"
            onmouseover="this.style.backgroundColor='#d97706'"
            onmouseout="this.style.backgroundColor='#f59e0b'"
        >
            Send Reset Link
        </button>
    </form>
@endsection
