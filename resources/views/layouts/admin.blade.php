<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Bakso Atmo</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a href="{{ route('dashboard') }}" class="brand">
                <span class="brand-mark">BA</span>
                <span class="brand-copy">
                    <strong>Bakso Atmo</strong>
                    <small>Management System</small>
                </span>
            </a>

            <div class="sidebar-section">
                <span class="sidebar-label">MENU UTAMA</span>

                <nav class="sidebar-nav">
                    @if (auth()->user()->role === 'staff')
                        <a href="{{ route('staff.dashboard') }}"
                           class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">⌂</span>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('staff.catering.index') }}"
                           class="nav-link {{ request()->routeIs('staff.catering.*') ? 'active' : '' }}">
                            <span class="nav-icon">☰</span>
                            <span>Pesanan Katering</span>
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">⌂</span>
                            <span>Dashboard</span>
                        </a>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.users.index') }}"
                           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <span class="nav-icon">♙</span>
                            <span>Manajemen Pengguna</span>
                        </a>

                        <a href="{{ route('admin.categories.index') }}"
                           class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <span class="nav-icon">▤</span>
                            <span>Kategori Menu</span>
                        </a>

                        <a href="{{ route('admin.menus.index') }}"
                           class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                            <span class="nav-icon">♨</span>
                            <span>Daftar Menu</span>
                        </a>

                        <a href="{{ route('admin.catering.index') }}"
                           class="nav-link {{ request()->routeIs('admin.catering.*') ? 'active' : '' }}">
                            <span class="nav-icon">☰</span>
                            <span>Pesanan Katering</span>
                        </a>
                    @endif
                </nav>
            </div>

            <div class="sidebar-bottom">
                <span class="sidebar-label">AKUN</span>

                <a href="{{ route('password.edit') }}"
                   class="nav-link {{ request()->routeIs('password.*') ? 'active' : '' }}">
                    <span class="nav-icon">⚙</span>
                    <span>Ubah Password</span>
                </a>

                <div class="sidebar-user">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="user-info">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ ucfirst(auth()->user()->role) }}</small>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-button" type="submit" title="Logout">
                            ↪
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="main-area">
            <header class="topbar">
                <div>
                    <span class="topbar-label">BAKSO ATMO / {{ auth()->user()->role === 'staff' ? 'OPERASIONAL' : 'ADMINISTRATION' }}</span>
                    <span class="topbar-date">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <div class="topbar-status">
                    <span class="status-dot"></span>
                    Sistem Aktif
                </div>
            </header>

            <main class="page-content">
                <div class="page-heading">
                    <div>
                        <span class="page-eyebrow">@yield('eyebrow', 'RUANG KERJA')</span>
                        <h1>@yield('page-title', 'Dashboard')</h1>
                        <p>@yield('page-description', 'Selamat datang di sistem manajemen Bakso Atmo.')</p>
                    </div>

                    @yield('page-actions')
                </div>

                @if (session('success'))
                    <div class="alert alert-success" role="status">
                        <span>✓</span>
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="page-footer">
                <span>© {{ date('Y') }} Bakso Atmo</span>
                <span>Management System</span>
            </footer>
        </div>
    </div>
</body>
</html>