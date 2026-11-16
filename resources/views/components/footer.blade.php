<footer class="footer">
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
            <h4>Kontak</h4>
            {{-- TODO: ganti dengan data kontak asli --}}
            <p>Alamat: (isi alamat)<br>WhatsApp: (isi nomor)</p>
        </div>
    </div>

    <div class="footer__copy">
        &copy; {{ date('Y') }} {{ config('bakso_atmo.name') }}. All rights reserved.
    </div>
</footer>