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
                    'title' => '1979',
                    'text'  => 'Pak Atmo mulai berjualan mie dan bakso dengan resep sederhana dan bahan segar. Rasa yang jujur membuat pelanggan datang lagi dan lagi.',
                ],
                [
                    'title' => 'Dua Cabang',
                    'text'  => 'Dari satu warung, kini kami hadir di dua cabang di Karawang. Tempatnya bertambah, tapi resep dan cara memasaknya tetap sama.',
                ],
                [
                    'title' => 'Katering',
                    'text'  => 'Sekarang kami juga melayani katering prasmanan untuk acara keluarga dan kantor, dengan rasa yang sama seperti di warung.',
                ],
            ];
        @endphp

        <div class="grid grid--3 slider {{ count($journey) > 3 ? 'slider--desktop' : '' }}">
            @foreach ($journey as $item)
                <div class="journey-card">
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