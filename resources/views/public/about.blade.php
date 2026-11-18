@extends('layouts.public')

@section('title', config('bakso_atmo.name') . ' - Perjalanan Kami')

@section('content')

{{-- PERJALANAN KAMI --}}
<section class="section">
    <div class="container">
        <h1 class="section__title">Perjalanan kami</h1>

        @php
    $journey = [
        [
            'icon'  => '<path d="M12 2c1 3 4 4.5 4 8a4 4 0 0 1-8 0c0-1.5.6-2.5 1.5-3.5C10 8 11 8.5 12 8c-.5-2 0-4 0-6z"/><path d="M5 21h14"/><path d="M7 21a5 5 0 0 1 10 0"/>',
            'title' => '1979',
            'text'  => 'Pak Atmo mulai berjualan mie dan bakso dengan resep sederhana dan bahan segar. Rasa yang jujur membuat pelanggan datang lagi dan lagi.',
        ],
        [
            'icon'  => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
            'title' => 'Dua Cabang',
            'text'  => 'Dari satu warung, kini kami hadir di dua cabang di Karawang. Tempatnya bertambah, tapi resep dan cara memasaknya tetap sama.',
        ],
        [
            'icon'  => '<path d="M3 13h18a9 9 0 0 0-18 0z"/><path d="M12 4V3"/><path d="M2 17h20"/><path d="M4 20h16"/>',
            'title' => 'Katering',
            'text'  => 'Sekarang kami juga melayani katering prasmanan untuk acara keluarga dan kantor, dengan rasa yang sama seperti di warung.',
        ],
    ];
@endphp

<div class="grid grid--3 slider {{ count($journey) > 3 ? 'slider--desktop' : '' }}">
    @foreach ($journey as $item)
        <div class="journey-card">
            <div class="journey-card__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    {!! $item['icon'] !!}
                </svg>
            </div>
            <h3>{{ $item['title'] }}</h3>
            <p>{{ $item['text'] }}</p>
        </div>
    @endforeach
</div>

{{-- GALERI --}}
<section class="section section--tight">
    <div class="container">
        <h2 class="section__title">Galeri</h2>

        @php
            $photos = [
                ['file' => '1.jpg', 'alt' => 'Semangkuk bakso urat'],
                ['file' => '2.jpg', 'alt' => 'Suasana warung'],
                ['file' => '3.jpg', 'alt' => 'Proses memasak mie'],
                ['file' => '4.jpg', 'alt' => 'Paket katering'],
            ];
        @endphp

        <div class="grid grid--4 slider {{ count($photos) > 4 ? 'slider--desktop' : '' }}">
            @foreach ($photos as $photo)
                <div class="gallery-item">
                    <img src="{{ asset('images/galeri/' . $photo['file']) }}"
                         alt="{{ $photo['alt'] }}" loading="lazy">
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection