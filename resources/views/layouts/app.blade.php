<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #111111; color: #f5f5f5; }

        /* Nav tab */
        .nav-tab {
            position: relative;
            padding: 0 14px;
            height: 60px;
            display: inline-flex;
            align-items: center;
            font-size: 13.5px;
            font-weight: 500;
            color: #9ca3af;
            white-space: nowrap;
            transition: color 0.15s;
            text-decoration: none;
        }
        .nav-tab:hover { color: #f5f5f5; }
        .nav-tab.active { color: #f59e0b; }
        .nav-tab.active::after {
            content: '';
            position: absolute;
            bottom: 0; left: 14px; right: 14px;
            height: 2px;
            background-color: #f59e0b;
            border-radius: 2px 2px 0 0;
        }

        /* User dropdown */
        .user-menu { position: relative; }
        .user-menu-dropdown {
            display: none;
            position: absolute;
            right: 0; top: calc(100% + 8px);
            min-width: 200px;
            background: #1e1e1e;
            border: 1px solid #2d2d2d;
            border-radius: 10px;
            padding: 6px;
            z-index: 100;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
        }
        .user-menu.open .user-menu-dropdown { display: block; }
        .user-menu-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px;
            border-radius: 7px;
            font-size: 13.5px;
            color: #d1d5db;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.1s, color 0.1s;
        }
        .user-menu-item:hover { background: #2a2a2a; color: #f5f5f5; }
        .user-menu-item.danger:hover { background: rgba(239,68,68,0.1); color: #ef4444; }
        .menu-divider { border-top: 1px solid #2d2d2d; margin: 5px 0; }

        /* Notification badge */
        .notif-btn { position: relative; }
        .notif-badge {
            position: absolute;
            top: 4px; right: 4px;
            min-width: 16px; height: 16px;
            background: #ef4444;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 0 3px;
        }

        /* Icon button */
        .icon-btn {
            width: 36px; height: 36px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px;
            color: #9ca3af;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            border: none; background: transparent;
        }
        .icon-btn:hover { background: #2a2a2a; color: #f5f5f5; }

        /* Global Modal Overlay Styles */
        .modal-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,0.65); z-index:200;
            align-items:flex-start; justify-content:center; padding-top:60px;
        }
        .modal-overlay.open { display:flex; }
        .modal-box {
            background:#1e1e1e; border:1px solid #2d2d2d; border-radius:14px;
            padding:24px; width:480px; max-width:94vw; max-height:85vh; overflow-y:auto;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            text-align: left;
        }
        .modal-title { font-size:15px; font-weight:600; color:#f5f5f5; margin-bottom:18px; text-align: left; }
        .form-label { display:block; font-size:12px; font-weight:600; color:#9ca3af; margin-bottom:5px; text-transform:uppercase; letter-spacing:.04em; text-align: left; }
        .form-input {
            width:100%; background:#141414; border:1px solid #2d2d2d; border-radius:8px;
            padding:9px 12px; font-size:13.5px; color:#f5f5f5; outline:none; box-sizing:border-box;
            transition:border-color 0.15s;
            text-align: left;
        }
        .form-input:focus { border-color:#f59e0b; }
        .form-input::placeholder { color:#4b5563; }
        .form-group { margin-bottom:14px; text-align: left; }
        .field-error { color:#ef4444; font-size:12px; margin-top:4px; display:block; text-align: left; }
        .modal-actions { display:flex; gap:8px; margin-top:20px; justify-content:flex-end; }
        .btn-amber {
            padding:9px 18px; border-radius:8px; border:none; cursor:pointer;
            font-size:13px; font-weight:600; background:#f59e0b; color:#111; transition:background 0.15s;
        }
        .btn-amber:hover { background:#d97706; }
        .btn-cancel {
            padding:9px 14px; border-radius:8px; border:1px solid #2d2d2d; cursor:pointer;
            font-size:13px; background:transparent; color:#9ca3af; transition:background 0.15s;
        }
        .btn-cancel:hover { background:#2a2a2a; color:#f5f5f5; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">

    {{-- ===== TOP NAVIGATION HEADER ===== --}}
    <header style="height:60px; background:#1a1a1a; border-bottom:1px solid #2d2d2d; flex-shrink:0;">
        <div class="flex items-center h-full px-4 gap-2">

            {{-- LEFT: Logo --}}
            <a href="{{ route('kanban.index') }}" class="flex-shrink-0" style="width:160px; text-decoration:none; display:block;">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-8" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                <div style="display:none; align-items:center; gap:6px;">
                    <div style="width:28px; height:28px; background:rgba(245,158,11,0.15); border-radius:7px; display:flex; align-items:center; justify-content:center;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span style="font-size:13px; font-weight:600; color:#f5f5f5; letter-spacing:-0.2px;">Dimitri's</span>
                </div>
            </a>

            {{-- CENTER: Navigation Tabs --}}
            <nav class="flex-1 flex items-center justify-center overflow-x-auto">
                @if(auth()->user()->hasPermission('kanban'))
                <a href="{{ route('kanban.index') }}"
                   class="nav-tab {{ request()->routeIs('kanban.*') ? 'active' : '' }}">
                    Kanban
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('archive'))
                <a href="{{ route('archived.index') }}" class="nav-tab {{ request()->routeIs('archived.*') ? 'active' : '' }}">
                    Archived
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('reports'))
                <a href="{{ route('reports.index') }}" class="nav-tab {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    Reports
                </a>
                @endif

                @if(auth()->user()->hasPermission('products_view'))
                <a href="{{ route('products.index') }}" class="nav-tab {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    Products
                </a>
                @endif

                @if(auth()->user()->hasPermission('customers_view'))
                <a href="{{ route('customers.index') }}" class="nav-tab {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    Customers
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('users_manage'))
                <a href="{{ route('admin.users.index') }}"
                   class="nav-tab {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    Users
                </a>
                @endif

                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.roles.index') }}" class="nav-tab {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    Roles
                </a>
                @endif
            </nav>

            {{-- RIGHT: Actions --}}
            <div class="flex-shrink-0 flex items-center gap-1" style="width:160px; justify-content:flex-end;">

                {{-- Notifications --}}
                <button class="icon-btn notif-btn" title="Notifications">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

                {{-- User Menu --}}
                <div class="user-menu" id="userMenu">
                    <button class="icon-btn flex items-center gap-1.5 px-2" style="width:auto;" onclick="document.getElementById('userMenu').classList.toggle('open')">
                        <div style="width:28px; height:28px; border-radius:7px; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#f59e0b; flex-shrink:0;">
                            {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                        </div>
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div class="user-menu-dropdown">
                        <div style="padding:10px 12px 8px; border-bottom:1px solid #2d2d2d; margin-bottom:5px;">
                            <p style="font-size:13px; font-weight:600; color:#f5f5f5;">{{ auth()->user()->full_name }}</p>
                            <p style="font-size:11.5px; color:#6b7280; margin-top:1px;">{{ auth()->user()->role?->name ?? 'No Role' }}</p>
                        </div>

                        <button type="button" class="user-menu-item" onclick="openEditProfileModal()" style="width: 100%; text-align: left; background: transparent; border: none; font-family: inherit;">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Edit Profile
                        </button>
                        <button type="button" class="user-menu-item" onclick="openChangePasswordModal()" style="width: 100%; text-align: left; background: transparent; border: none; font-family: inherit;">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Change Password
                        </button>
                        <div class="menu-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="user-menu-item danger" style="width:100%; text-align:left;">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </header>

    {{-- Optional page sub-header (used by admin pages) --}}
    @hasSection('page-title')
    <div class="flex items-center justify-between px-6 py-3" style="background:#161616; border-bottom:1px solid #222; flex-shrink:0;">
        <div>
            <h1 class="text-base font-semibold" style="color:#f5f5f5;">@yield('page-title')</h1>
            @hasSection('page-subtitle')
                <p class="text-xs mt-0.5" style="color:#6b7280;">@yield('page-subtitle')</p>
            @endif
        </div>
        <div class="flex items-center gap-2">@yield('header-actions')</div>
    </div>
    @endif

    {{-- Main content --}}
    <main class="flex-1 flex flex-col min-h-0">

        @if (session('success'))
            <div class="mx-6 mt-4 flex items-start gap-3 px-4 py-3 rounded-lg text-sm" style="background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.25); color:#22c55e;">
                <svg class="flex-shrink-0 mt-0.5" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any() && !$errors->has('login') && !$errors->has('token'))
            <div class="mx-6 mt-4 px-4 py-3 rounded-lg text-sm" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#ef4444;">
                <p class="font-medium mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="@yield('content-class', 'p-6') flex-1">
            @yield('content')
        </div>
    </main>

    @auth
    {{-- ── EDIT PROFILE MODAL ──────────────────────────────────────────────── --}}
    <div class="modal-overlay" id="editProfileModal" onclick="if(event.target===this) closeEditProfileModal()">
        <div class="modal-box">
            <div class="modal-title">Edit Profile</div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="edit_profile">
                
                <div class="form-group">
                    <label class="form-label" for="profile_full_name">Full Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="full_name" id="profile_full_name" class="form-input" 
                           value="{{ auth()->user()->full_name }}" required maxlength="100">
                    @error('full_name')
                        @if(old('_form') === 'edit_profile')
                            <span class="field-error">{{ $message }}</span>
                        @endif
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="profile_username">Username <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="username" id="profile_username" class="form-input" 
                           value="{{ auth()->user()->username }}" required maxlength="50">
                    @error('username')
                        @if(old('_form') === 'edit_profile')
                            <span class="field-error">{{ $message }}</span>
                        @endif
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="profile_email">Email Address <span style="color:#ef4444;">*</span></label>
                    <input type="email" name="email" id="profile_email" class="form-input" 
                           value="{{ auth()->user()->email }}" required maxlength="150">
                    @error('email')
                        @if(old('_form') === 'edit_profile')
                            <span class="field-error">{{ $message }}</span>
                        @endif
                    @enderror
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditProfileModal()">Cancel</button>
                    <button type="submit" class="btn-amber">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── CHANGE PASSWORD MODAL ───────────────────────────────────────────── --}}
    <div class="modal-overlay" id="changePasswordModal" onclick="if(event.target===this) closeChangePasswordModal()">
        <div class="modal-box">
            <div class="modal-title">Change Password</div>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="change_password">
                
                <div class="form-group">
                    <label class="form-label" for="password_current_password">Current Password <span style="color:#ef4444;">*</span></label>
                    <input type="password" name="current_password" id="password_current_password" class="form-input" required>
                    @error('current_password')
                        @if(old('_form') === 'change_password')
                            <span class="field-error">{{ $message }}</span>
                        @endif
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password_new_password">New Password <span style="color:#ef4444;">*</span></label>
                    <input type="password" name="new_password" id="password_new_password" class="form-input" required minlength="8">
                    @error('new_password')
                        @if(old('_form') === 'change_password')
                            <span class="field-error">{{ $message }}</span>
                        @endif
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password_new_password_confirmation">Confirm New Password <span style="color:#ef4444;">*</span></label>
                    <input type="password" name="new_password_confirmation" id="password_new_password_confirmation" class="form-input" required>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeChangePasswordModal()">Cancel</button>
                    <button type="submit" class="btn-amber">Update Password</button>
                </div>
            </form>
        </div>
    </div>
    @endauth

    <script>
        // Close user menu when clicking outside
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userMenu');
            if (menu && !menu.contains(e.target)) menu.classList.remove('open');
        });

        @auth
        function openEditProfileModal() {
            document.getElementById('editProfileModal').classList.add('open');
            const menu = document.getElementById('userMenu');
            if (menu) menu.classList.remove('open');
        }
        function closeEditProfileModal() {
            document.getElementById('editProfileModal').classList.remove('open');
        }

        function openChangePasswordModal() {
            document.getElementById('changePasswordModal').classList.add('open');
            const menu = document.getElementById('userMenu');
            if (menu) menu.classList.remove('open');
        }
        function closeChangePasswordModal() {
            document.getElementById('changePasswordModal').classList.remove('open');
        }

        @if(old('_form') === 'edit_profile')
        document.addEventListener('DOMContentLoaded', () => {
            openEditProfileModal();
        });
        @endif

        @if(old('_form') === 'change_password')
        document.addEventListener('DOMContentLoaded', () => {
            openChangePasswordModal();
        });
        @endif

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeEditProfileModal();
                closeChangePasswordModal();
            }
        });
        @endauth
    </script>
    @stack('scripts')

</body>
</html>
