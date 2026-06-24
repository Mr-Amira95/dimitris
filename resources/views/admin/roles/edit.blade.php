@extends('layouts.app')

@section('title', 'Edit Role — ' . config('app.name'))
@section('page-title', 'Edit Role')
@section('page-subtitle', $role->name)

@section('content')
    <div class="max-w-2xl">
        <div class="rounded-xl p-6" style="background-color: #1a1a1a; border: 1px solid #2d2d2d;">

            @if ($role->is_system)
                <div class="mb-5 px-4 py-3 rounded-lg flex items-center gap-2 text-sm" style="background-color: rgba(107,114,128,0.1); border: 1px solid rgba(107,114,128,0.2); color: #9ca3af;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    This is a system role. Permissions can be updated but the role name cannot be changed.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium mb-1.5" style="color: #d1d5db;">Role Name <span style="color: #ef4444;">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $role->name) }}"
                           {{ $role->is_system ? 'readonly' : '' }}
                           class="w-full px-4 py-2.5 rounded-lg text-sm outline-none transition-all max-w-sm"
                           style="background-color: #111111; border: 1px solid {{ $errors->has('name') ? '#ef4444' : '#2d2d2d' }}; color: {{ $role->is_system ? '#6b7280' : '#f5f5f5' }}; {{ $role->is_system ? 'cursor: not-allowed;' : '' }}"
                           onfocus="{{ $role->is_system ? '' : 'this.style.borderColor=\'#f59e0b\'' }}"
                           onblur="{{ $role->is_system ? '' : 'this.style.borderColor=\''.($errors->has('name') ? '#ef4444' : '#2d2d2d').'\'' }}">
                    @error('name')
                        <p class="mt-1.5 text-xs" style="color: #ef4444;">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <p class="text-sm font-medium mb-3" style="color: #d1d5db;">Permissions</p>

                    @php
                        $groups = [
                            'Orders'       => ['kanban', 'orders_advance', 'orders_cancel', 'orders_edit_full', 'orders_edit_items', 'assign_driver', 'view_delivery_cost'],
                            'Customers'    => ['customers_view', 'customers_manage'],
                            'Products'     => ['products_view', 'products_manage'],
                            'Reports'      => ['archive', 'reports'],
                            'Admin'        => ['users_manage', 'settings_manage'],
                        ];
                        $currentPerms = old('permissions', $role->permissions ?? []);
                    @endphp

                    <div class="space-y-4">
                        @foreach ($groups as $group => $keys)
                            <div class="rounded-lg p-4" style="background-color: #111111; border: 1px solid #2a2a2a;">
                                <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #6b7280;">{{ $group }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach ($keys as $key)
                                        @if (isset($permissions[$key]))
                                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                                <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                                       {{ in_array($key, $currentPerms) ? 'checked' : '' }}
                                                       class="w-4 h-4 rounded" style="accent-color: #f59e0b;">
                                                <span class="text-sm transition-colors group-hover:text-amber-400" style="color: #9ca3af;">{{ $permissions[$key] }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <button type="submit"
                            class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                            style="background-color: #f59e0b; color: #111111;"
                            onmouseover="this.style.backgroundColor='#d97706'"
                            onmouseout="this.style.backgroundColor='#f59e0b'">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.roles.index') }}"
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
