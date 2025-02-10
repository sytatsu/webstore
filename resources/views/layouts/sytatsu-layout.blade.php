@props(['center' => true])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title }}</title>

        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/images/favicons/apple-touch-icon.png') }}" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/images/favicons/favicon-32x32.png') }}" />
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/images/favicons/favicon-16x16.png') }}" />
        <link rel="manifest" href="{{ asset('site.webmanifest') }}" />
        <link rel="mask-icon" href="{{ asset('/images/favicons/safari-pinned-tab.svg') }}" color="#7ad1f1" />
        <link rel="shortcut icon" href="{{ asset('/images/favicons/favicon.ico') }}" />
        <meta name="msapplication-TileColor" content="#7bd2f1" />
        <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}" />
        <meta name="theme-color" content="#ffffff" />

        <!-- Vite -->
        @vite([
            'resources/scss/sytatsu.scss',
            'resources/js/sytatsu.js',
        ])

        @livewireStyles
        @livewireScripts
    </head>

    <body class="bg-slate-50 h-dvh">
        <div class="flex flex-col min-h-screen">
            @if ($center === true || $center === null)
                <div class="flex flex-col justify-center content-center
                            bg-gradient-to-br from-[#FFF1EA] from-10% to-[#FFFBF4] to-90%
                            {{-- Line underneath for the  --}}
                            shadow-lg rounded-bl-[4rem] flex-grow
                            ">
                    {{ $slot }}
                </div>
            @elseif ($center === false)
                <div class="bg-gradient-to-br min-h-screen from-[#FFF1EA] from-10% to-[#FFFBF4] to-90% bg-no-repeat bg-fixed">
                    {{ $slot }}
                </div>
            @endif

            <p class="flex justify-center m-auto text-secondary-light py-4">
                {!! __('partials/footer.creator', ['creator' => '<a class="ml-1 underline text-secondary font-bold" href="https://stpronk.nl/">stpronk.nl</a>']) !!}
            </p>
        </div>
    </body>
</html>
