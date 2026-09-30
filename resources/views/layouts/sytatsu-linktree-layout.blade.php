{{--
    Standalone single-page layout for QR-banner style link pages (e.g. /linktree).

    Deliberately does NOT extend layouts.sytatsu-layout: no navigation, notification
    banner, footer or cookie popup — just the bare page. Also deliberately kept out of
    the search index (meta robots + robots.txt, see routes/sytatsu.php and public/robots.txt)
    since it only exists to be reached by scanning a QR code on printed material.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-linear-to-br from-[#FFF1EA] dark:from-[#12100E] from-10% to-[#FFFFFF] dark:to-[#2B4162] to-90% bg-no-repeat">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />

        <title>{{ $title }}</title>

        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet" />

        {{-- Page is reached almost exclusively via the QR code on print banners, so the
             mobile browser chrome (address bar) gets the same tone as the page background. --}}
        <meta name="theme-color" content="#FFF1EA" media="(prefers-color-scheme: light)" />
        <meta name="theme-color" content="#12100E" media="(prefers-color-scheme: dark)" />

        <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicons/favicon-96x96.png') }}" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/favicons/favicon.svg') }}" />
        <link rel="shortcut icon" href="{{ Vite::asset('resources/images/favicons/favicon.ico') }}" />
        <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/images/favicons/apple-touch-icon.png') }}" />
        <meta name="apple-mobile-web-app-title" content="Sytatsu" />

        @php
            $seoDescription = $description ?? config('seo.default_description');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">

        <!-- Vite -->
        @vite([
            'resources/scss/sytatsu.scss',
            'resources/js/sytatsu.js',
        ])

        @livewireStyles

        <script>
            const html = document.querySelector('html');
            const theme = localStorage.getItem('hs_theme') ?? 'auto';
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = theme === 'dark' || (theme === 'auto' && prefersDark);

            html.classList.toggle('dark', isDark);
            html.classList.toggle('light', !isDark);
        </script>
    </head>

    <body class="bg-linear-to-br from-[#FFF1EA] dark:from-[#12100E] from-10% to-[#FFFFFF] dark:to-[#2B4162] to-90% bg-no-repeat min-h-dvh">
        {{-- min-h-dvh (rather than min-h-screen/100vh) so the page fills the real visible
             viewport on mobile instead of the height behind a collapsing address bar. --}}
        <div class="min-h-dvh flex flex-col">
            {{ $slot }}
        </div>
    </body>

    @livewireScripts
</html>
