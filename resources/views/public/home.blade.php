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
            <a href="{{ url('/menu') }}" class="btn btn--light">Lihat Menu &amp; Harga</a>
            {{-- TODO: route katering Sprint 4 --}}
            <a href="#" class="btn btn--dark">Pesan Katering</a>
        </div>
    </div>
</section>

{{-- PERJALANAN KAMI --}}
<section class="section">
    <div class="container">
        <h2 class="section__title">Perjalanan kami</h2>

        @php
            // TODO: ganti dengan isi sebenarnya
            $journey = [
                ['title' => '1979',    'text' => 'Awal berjualan'],
                ['title' => 'BLABLA',  'text' => 'BLABLA'],
                ['title' => 'BLABLA',  'text' => 'BLABLABLA'],
            ];
        @endphp

        <div class="grid grid--3">
            @foreach ($journey as $item)
                <div class="journey-card {{ $loop->first ? 'journey-card--accent' : '' }}">
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- GALERI --}}
<section class="section section--tight">
    <div class="container">
        <h2 class="section__title">Galeri</h2>

        <div class="grid grid--4">
            {{-- TODO: ganti div ini dengan <img src="{{ asset('images/galeri/1.jpg') }}" alt="..."> --}}
            @for ($i = 1; $i <= 4; $i++)
                <div class="gallery-item"></div>
            @endfor
        </div>
    </div>
</section>

{{-- CTA KATERING --}}
<section class="cta">
    <div class="container cta__inner">
        <div>
            <h2>Ada acara keluarga atau kantor?</h2>
            <p>Booking katering dengan jadwal H-2 dan DP 50%, konfirmasi lewat WhatsApp.</p>
        </div>
        {{-- TODO: route katering Sprint 4 --}}
        <a href="#" class="btn btn--light">Ajukan Pesanan Katering</a>
    </div>
</section>

@endsection