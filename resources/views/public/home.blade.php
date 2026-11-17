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
  
            $journey = [
                ['title' => '1979',    'text' => 'Berawal dari tahun 1979, Warung Pak Atmo hadir membawa cita rasa bakso yang menjadi bagian dari perjalanan kuliner selama puluhan tahun. Dari awal yang sederhana, Warung Pak Atmo terus melangkah untuk melayani pelanggan hingga saat ini.'],
                ['title' => 'Cita Rasa dan Kualitas',  'text' => 'Kami berkomitmen untuk menyajikan cita rasa bakso yang lezat dan kualitas terbaik setiap hari.'],
                ['title' => 'Lebih dari Sekadar Warung Bakso',  'text' => 'Kini, Warung Pak Atmo tidak hanya melayani pelanggan melalui dua cabang, tetapi juga menyediakan layanan katering untuk berbagai acara. Mulai dari kebutuhan keluarga hingga kegiatan bersama, kami siap menjadi bagian dari momen spesial Anda.'],
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
@endsection