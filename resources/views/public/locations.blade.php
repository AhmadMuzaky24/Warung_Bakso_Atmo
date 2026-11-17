@extends('layouts.public')

@section('title', config('bakso_atmo.name') . ' - Lokasi')

@section('content')
<section class="section">
    <div class="container">
        <h1 class="section__title">Lokasi &amp; Kontak</h1>

        <div class="grid grid--2">
            @foreach ($branches as $branch)
                <div class="info-card">
                    <h3>{{ $branch['name'] }}</h3>
                    <p>{{ $branch['address'] }}</p>
                    <p class="info-card__muted">{{ $branch['hours'] }}</p>
                    <a href="{{ $branch['maps'] }}" target="_blank" rel="noopener" class="btn btn--dark btn--sm-dark">
                        Buka di Maps
                    </a>
                </div>
            @endforeach
        </div>

    </div>
</section>
@endsection