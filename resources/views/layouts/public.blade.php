<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('bakso_atmo.name'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/public.css'])
    @stack('styles')
</head>
<body>
    <x-navbar :overlay="View::hasSection('overlay_nav')" />

    <main class="{{ View::hasSection('overlay_nav') ? '' : 'has-solid-nav' }}">
        @yield('content')
    </main>

    <x-footer :cta="! View::hasSection('hide_footer_cta')" />

    <script>
        (function () {
            // NAVBAR: sembunyi saat scroll turun, muncul saat scroll naik
            var nav = document.getElementById('site-navbar');
            if (nav) {
                var menu = nav.querySelector('.navbar__menu');
                var lastY = window.scrollY;
                var ticking = false;

                function update() {
                    var y = window.scrollY;
                    var menuOpen = menu && menu.classList.contains('is-open');

                    nav.classList.toggle('is-scrolled', y > 20);

                    if (y > lastY && y > 120 && !menuOpen) {
                        nav.classList.add('is-hidden');
                    } else if (y < lastY) {
                        nav.classList.remove('is-hidden');
                    }

                    lastY = y;
                    ticking = false;
                }

                window.addEventListener('scroll', function () {
                    if (!ticking) {
                        window.requestAnimationFrame(update);
                        ticking = true;
                    }
                }, { passive: true });

                update();
            }

            // FOOTER: muncul saat masuk layar, memudar saat keluar
            var footer = document.querySelector('.footer');
            if (footer && 'IntersectionObserver' in window) {
                footer.classList.add('reveal');
                new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        footer.classList.toggle('is-visible', e.isIntersecting);
                    });
                }, { threshold: 0.15 }).observe(footer);
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>