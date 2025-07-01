@props(['center' => true])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title }}</title>

        <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicons/favicon-96x96.png') }}" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/images/favicons/favicon.svg') }}" />
        <link rel="shortcut icon" href="{{ Vite::asset('resources/images/favicons/favicon.ico') }}" />
        <link rel="apple-touch-icon" sizes="180x180" href="{{ Vite::asset('resources/images/favicons/apple-touch-icon.png') }}" />
        <meta name="apple-mobile-web-app-title" content="Sytatsu" />
        <link rel="manifest" href="{{ asset('resources/site.webmanifest') }}" />

        <meta name="description" content="Print & Shop by Sytatsu. We create models & brind them to life!">
        <meta name="keywords" content="HTML,CSS,JavaScript,PHP,React,Laravel,Symphony,Scss,Sass,Illustrator,Git,Github,Gitlab,Bootstrap,StPronk,Steve,Pronk,Developer,Designer,Develop,Design,Web,Website,Zuid,Holland,Zuid-Holland,Zoetermeer,Nederland,Maatwerk,3D,Print,Printing,3dprint,3dprinting,Model,Modeling,Tatsugiri,Dondozo,Sytatsu,Syaritatsu,Tatsu">
        <meta name="author" content="Sytatsu">
        <meta name="google-site-verification" content="{{ config('seo.google-site-verification') }}"/>

        <!-- Vite -->
        @vite([
            'resources/scss/sytatsu.scss',
            'resources/js/sytatsu.js',
        ])

        @livewireStyles
        @livewireScripts

        <script>
            const html = document.querySelector('html');
            const isLightOrAuto = localStorage.getItem('hs_theme') === 'light' || (localStorage.getItem('hs_theme') === 'auto' && !window.matchMedia('(prefers-color-scheme: dark)').matches);
            const isDarkOrAuto = localStorage.getItem('hs_theme') === 'dark' || (localStorage.getItem('hs_theme') === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isLightOrAuto && html.classList.contains('dark')) html.classList.remove('dark');
            else if (isDarkOrAuto && html.classList.contains('light')) html.classList.remove('light');
            else if (isDarkOrAuto && !html.classList.contains('dark')) html.classList.add('dark');
            else if (isLightOrAuto && !html.classList.contains('light')) html.classList.add('light');
        </script>
    </head>

    <body class="bg-gradient-to-br from-[#FFF1EA] dark:from-[#12100E] from-10% to-[#FFFFFF] dark:to-[#2B4162] to-90% bg-no-repeat min-h-screen">
        <div class="min-h-screen flex flex-col justify-between">
            @include('sytatsu.components.navigation')

            <div class="flex flex-col flex-grow">
                <div class="flex flex-col justify-center content-center my-auto">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @include('sytatsu.components.footer')
    </body>
</html>
