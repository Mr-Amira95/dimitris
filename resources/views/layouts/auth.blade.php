<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center" style="background-color: #111111;">

    <div class="w-full px-4 py-8" style="max-width: @yield('container-max-width', '448px'); margin: 0 auto;">

        {{-- Logo --}}
        <div class="flex justify-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-14">
        </div>

        {{-- Flash messages --}}
        @if (session('status'))
            <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background-color: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.3); color: #22c55e;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('token'))
            <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background-color: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: #ef4444;">
                {{ $errors->first('token') }}
            </div>
        @endif

        {{-- Card --}}
        @hasSection('auth-card-override')
            @yield('content')
        @else
            <div class="rounded-xl p-8" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
                @yield('content')
            </div>
        @endif

        {{-- Footer --}}
        <p class="mt-6 text-center text-xs" style="color: #4b5563;">
            {{ config('app.name') }}
        </p>
    </div>

</body>
</html>
