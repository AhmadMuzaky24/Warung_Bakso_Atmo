@extends('layouts.public')

@section('title', config('bakso_atmo.name') . ' - Beranda')
@section('overlay_nav', true)

@section('content')

{{-- HERO --}}
<section class="hero">
    <div class="container">
        <span class="badge">Sejak 1979</span>
        <h1>Mie legendaris, dimasak segar setiap hari</h1>
        <p>Lihat menu, harga, dan lokasi dua cabang sebelum berangkat. Butuh makanan untuk acara? Pesan katering prasmanan dari sini.</p>
        <div class="hero__actions">
            <a href="#menu" class="btn btn--light">Lihat Menu &amp; Harga</a>
            <a href="{{ route('customer.catering.create') }}" class="btn btn--dark">Pesan Katering</a>
        </div>
    </div>
</section>

{{-- MENU & HARGA --}}
<section id="menu" class="section">
    <div class="container">
        <h2 class="section__title">Menu &amp; Harga</h2>

        @if ($categories->isEmpty())
            <p>Menu belum tersedia.</p>
        @else
            <div class="filter-tabs" id="menu-filter">
                <button type="button" class="filter-tab is-active" data-filter="all">Semua</button>
                @foreach ($categories as $category)
                    <button type="button" class="filter-tab" data-filter="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            @foreach ($categories as $category)
                <div class="menu-group" data-category="{{ $category->id }}">
                    <h3 class="menu-group__title">{{ $category->name }}</h3>
                    @if ($category->description)
                        <p class="menu-group__desc">{{ $category->description }}</p>
                    @endif

                    <div class="grid grid--3 slider {{ $category->menus->count() > 3 ? 'slider--desktop' : '' }}">
                        @foreach ($category->menus as $menu)
                            <x-menu-card :menu="$menu" />
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

        <div style="margin-top: 28px">
            <x-marketplace-buttons />
        </div>
    </div>
</section>

@push('scripts')
<script>
    (function () {
        var tabs = document.querySelectorAll('#menu-filter .filter-tab');
        var groups = document.querySelectorAll('.menu-group');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var f = tab.dataset.filter;
                tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
                groups.forEach(function (g) {
                    g.hidden = !(f === 'all' || g.dataset.category === f);
                });
            });
        });
    })();
</script>
@endpush

{{-- LOKASI --}}
@include('public.partials.locations')

@endsection