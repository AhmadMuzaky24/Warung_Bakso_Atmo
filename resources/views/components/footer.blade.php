@props(['cta' => true])

<footer class="footer">
    {{-- CTA KATERING --}}
    @if ($cta)
    <div class="cta">
        <div class="container cta__inner">
            <div>
                <h2>Ada acara keluarga atau kantor?</h2>
                <p>Booking katering dengan jadwal minimal {{ config('bakso_atmo.booking.min_days_before') }} hari sebelumnya, pemesanan H-1 ditutup pukul {{ \App\Support\BookingWindow::cutoffTime() }} WIB, dan DP {{ config('bakso_atmo.booking.dp_percent') }}%.</p>
            </div>
            <a href="{{ route('customer.catering.create') }}" class="btn btn--light">Ajukan Pesanan Katering</a>
        </div>
    </div>
    @endif

    <div class="container footer__grid">
        <div>
            <h3 class="footer__brand">{{ config('bakso_atmo.name') }}</h3>
            <p>Mie legendaris sejak 1979, dimasak segar setiap hari.</p>
        </div>

        <div>
            <h4>Navigasi</h4>
            <ul>
               <li><a href="{{ route('public.home') }}">Beranda</a></li>
                <li><a href="{{ route('public.home') }}#menu">Menu &amp; Harga</a></li>
                <li><a href="{{ route('public.home') }}#lokasi">Lokasi</a></li>
                <li><a href="{{ route('public.about') }}">Perjalanan Kami</a></li>
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