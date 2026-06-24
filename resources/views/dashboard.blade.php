@extends('layouts.app')

@section('title', 'Dashboard — ' . config('app.name'))
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->full_name)

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl p-5" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
            <p class="text-xs font-medium uppercase tracking-wider mb-2" style="color: #6b7280;">Active Orders</p>
            <p class="text-3xl font-bold" style="color: #f5f5f5;">—</p>
            <p class="text-xs mt-1" style="color: #4b5563;">Orders coming soon</p>
        </div>
        <div class="rounded-xl p-5" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
            <p class="text-xs font-medium uppercase tracking-wider mb-2" style="color: #6b7280;">Pending Dispatch</p>
            <p class="text-3xl font-bold" style="color: #f5f5f5;">—</p>
            <p class="text-xs mt-1" style="color: #4b5563;">Orders coming soon</p>
        </div>
        <div class="rounded-xl p-5" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
            <p class="text-xs font-medium uppercase tracking-wider mb-2" style="color: #6b7280;">Delivered Today</p>
            <p class="text-3xl font-bold" style="color: #f5f5f5;">—</p>
            <p class="text-xs mt-1" style="color: #4b5563;">Orders coming soon</p>
        </div>
    </div>

    <div class="rounded-xl p-8 text-center" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: rgba(245,158,11,0.1);">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
        </div>
        <h2 class="text-lg font-semibold mb-2" style="color: #f5f5f5;">Order Tracking Kanban</h2>
        <p class="text-sm" style="color: #6b7280;">The Kanban board and order management features are coming soon.</p>
    </div>
@endsection
