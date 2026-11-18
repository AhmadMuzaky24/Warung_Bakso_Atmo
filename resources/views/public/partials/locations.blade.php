<section id="lokasi" class="section">
    <div class="container">
        <h2 class="section__title">Lokasi</h2>

        <div class="grid grid--2 slider {{ count(config('bakso_atmo.branches')) > 2 ? 'slider--desktop' : '' }}">
            @foreach (config('bakso_atmo.branches') as $branch)
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