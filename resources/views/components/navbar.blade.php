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
            <a href="{{ route('public.home') }}" class="{{ request()->is('/') ? 'is-active' : '' }}">Beranda</a>
            <a href="{{ url('/menu') }}" class="{{ request()->is('menu*') ? 'is-active' : '' }}">Menu &amp; Harga</a>
            <a href="{{ url('/lokasi') }}" class="{{ request()->is('lokasi*') ? 'is-active' : '' }}">Lokasi</a>
            <a href="{{ Route::has('login') ? route('login') : '#' }}">Masuk</a>

            {{-- TODO: arahkan ke route katering Customer (Sprint 4) --}}
            <a href="#" class="btn btn--light btn--sm">Pesan Katering</a>
        </nav>
    </div>
</header>