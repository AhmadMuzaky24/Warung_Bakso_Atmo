<footer class="footer">
    {{-- CTA KATERING --}}
    <div class="cta">
        <div class="container cta__inner">
            <div>
                <h2>Ada acara keluarga atau kantor?</h2>
                <p>Booking katering dengan jadwal H-2 dan DP 50%, konfirmasi lewat WhatsApp.</p>
            </div>
            {{-- TODO: arahkan ke route katering Customer (Sprint 4) --}}
            <a href="#" class="btn btn--light">Ajukan Pesanan Katering</a>
        </div>
    </div>

    <div class="container footer__grid">
        <div>
            <h3 class="footer__brand">{{ config('bakso_atmo.name') }}</h3>
            <p>Mie legendaris sejak 1979, dimasak segar setiap hari.</p>
        </div>

        <div>
            <h4>Navigasi</h4>
            <ul>
                <li><a href="{{ route('public.home') }}">Beranda</a></li>
                <li><a href="{{ url('/menu') }}">Menu &amp; Harga</a></li>
                <li><a href="{{ url('/lokasi') }}">Lokasi</a></li>
            </ul>
        </div>

        <div>
            <h4>Pesan Online</h4>
            <ul>
                <li><a href="{{ config('bakso_atmo.gofood') }}" target="_blank" rel="noopener">GoFood</a></li>
                <li><a href="{{ config('bakso_atmo.shopeefood') }}" target="_blank" rel="noopener">ShopeeFood</a></li>
            </ul>
        </div>

        <div>
            <h4>Lokasi</h4>
            <ul>
                @foreach (config('bakso_atmo.branches') as $branch)
                    <li>
                        <a href="{{ $branch['maps'] }}" target="_blank" rel="noopener">
                            {{ $branch['name'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="footer__copy">
        &copy; {{ date('Y') }} {{ config('bakso_atmo.name') }}. All rights reserved.
    </div>
</footer>