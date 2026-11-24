@props(['overlay' => false])

<header id="site-navbar" class="navbar {{ $overlay ? 'navbar--overlay' : 'navbar--solid' }}">
    <div class="container navbar__inner">
        <a href="{{ route('public.home') }}" class="navbar__brand">
            {{ config('bakso_atmo.name') }}
        </a>

        <button class="navbar__toggle" type="button" aria-label="Menu"
                onclick="document.querySelector('.navbar__menu').classList.toggle('is-open')">
            ☰
        </button>

        <nav class="navbar__menu">
            <a href="{{ route('public.home') }}" class="{{ request()->routeIs('public.home') ? 'is-active' : '' }}">Beranda</a>
            <a href="{{ route('public.home') }}#menu">Menu &amp; Harga</a>
            <a href="{{ route('public.home') }}#lokasi">Lokasi</a>
            <a href="{{ route('public.about') }}" class="{{ request()->routeIs('public.about') ? 'is-active' : '' }}">Perjalanan Kami</a>
            @guest
                <a href="{{ Route::has('login') ? route('login') : '#' }}">Masuk</a>
            @endguest

            @auth
                @if (auth()->user()->role === 'customer')
                    <a href="{{ route('customer.dashboard') }}" class="{{ request()->routeIs('customer.dashboard') ? 'is-active' : '' }}">Dashboard</a>
                @elseif (auth()->user()->role === 'staff')
                    <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.*') ? 'is-active' : '' }}">Dashboard</a>
                @else
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">Dashboard</a>
                @endif

                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn--light btn--sm" style="min-height:44px">Keluar</button>
                </form>
            @endauth
        </nav>
    </div>
</header>