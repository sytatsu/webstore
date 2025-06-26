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

    <body class="bg-gradient-to-br from-[#FFF1EA] dark:from-[#12100E] from-10% to-[#FFFFFF] dark:to-[#2B4162] to-90% bg-no-repeat min-h-screen flex flex-col justify-between">

        {{------ ABSOLUTE TOP ------}}


        @include('sytatsu.components.navigation')


        <div class="absolute top-0 right-0 p-5">
            <div class="flex flex-row-reverse gap-4">
                <livewire:sytatsu.components.locale-switcher />

                <button type="button"
                        class="hs-dark-mode-active:hidden flex hs-dark-mode font-medium text-gray-800 rounded-full focus:outline-none dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                        data-hs-theme-click-value="dark">
                  <span class="group inline-flex shrink-0 justify-center items-center size-9 my-auto">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                    </svg>
                  </span>
                </button>
                <button type="button"
                        class="hs-dark-mode-active:flex hidden hs-dark-mode font-medium text-gray-800 rounded-full focus:outline-none dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                        data-hs-theme-click-value="light">
                  <span class="group inline-flex shrink-0 justify-center items-center size-9 my-auto">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="4"></circle>
                      <path d="M12 2v2"></path>
                      <path d="M12 20v2"></path>
                      <path d="m4.93 4.93 1.41 1.41"></path>
                      <path d="m17.66 17.66 1.41 1.41"></path>
                      <path d="M2 12h2"></path>
                      <path d="M20 12h2"></path>
                      <path d="m6.34 17.66-1.41 1.41"></path>
                      <path d="m19.07 4.93-1.41 1.41"></path>
                    </svg>
                  </span>
                </button>
            </div>
        </div>
        {{------ END ABSOLUTE TOP ------}}

        {{------ LAYOUT ------}}
        <div class="flex flex-col flex-grow
{{--        min-h-screen--}}
        ">

            <div class="flex flex-col justify-center content-center my-auto">
                {{ $slot }}
            </div>

        </div>
        {{------ END LAYOUT ------}}

        {{------ FOOTER ------}}
        <div class="sticky-bottom">
            <p class="flex justify-center mx-auto my-0 text-secondary-light py-4">
                {!! __('partials/footer.creator', ['creator' => '<a class="ml-1 underline text-secondary font-bold" href="https://stpronk.nl/">stpronk.nl</a>']) !!}
            </p>
        </div>
        {{------ END FOOTER ------}}
    </body>
</html>
