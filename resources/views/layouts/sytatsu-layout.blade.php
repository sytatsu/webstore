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


        <div class="flex flex-col min-h-screen">
            <div class="flex flex-col justify-center content-center my-auto">

    <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Grid -->
        <div class="grid max-w-[40rem] lg:max-w-full lg:grid-cols-7 lg:gap-x-8 xl:gap-x-12 lg:items-center">
            <div class="lg:col-span-3">
                <h1 class="block text-3xl font-bold text-gray-800 sm:text-4xl md:text-5xl lg:text-6xl dark:text-white sr-only">
                    Sytatsu, Print & Shop
                </h1>

                <img src="{{ asset('/images/brands/brand.svg') }}" alt="brand" width="700" class="m-auto">
                <p class="mt-3 text-lg text-gray-800 dark:text-neutral-400">
                    While we are working hard behind the scenes, we are already building beautiful things.
                    Feel free to get in touch with us or check out our social media!
                </p>

                <div class="mt-5 lg:mt-8 flex flex-col items-center gap-2 lg:flex-row sm:gap-3">
                    <div class="w-full lg:w-auto">
                        <label for="hero-input" class="sr-only">Get in touch</label>
                        <input type="text" id="hero-input" name="hero-input" class="py-3 px-4 block w-full min-w-80 border-gray-200 rounded-md text-sm focus:border-primary-dark focus:ring-primary-dark disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="E-mail">
                    </div>
                    <a class="w-full lg:w-auto py-3 px-4 mb-3 lg:mt-auto lg:mb-auto inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary-dark text-white hover:bg-primary focus:outline-none focus:bg-primary disabled:opacity-50 disabled:pointer-events-none" href="#">
                        {{ __('Get in touch') }}
                    </a>
                    <livewire:sytatsu.components.social-collection />
                </div>

            </div>

            <div class="lg:col-span-4 mt-10 lg:mt-0 hidden lg:block">
                <img class="w-full rounded-xl shadow-lg dark:shadow-primary" src="https://images.unsplash.com/photo-1665686376173-ada7a0031a85?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=900&h=450&q=80" alt="Hero Image">
            </div>
            <!-- End Col -->
        </div>
        <!-- End Grid -->
    </div>
    <!-- End Hero -->

            </div>
        </div>

{{--        <div class="flex flex-col min-h-screen">--}}
{{--            @if ($center === true || $center === null)--}}
{{--                <div class="flex flex-col justify-center content-center--}}
{{--                            bg-gradient-to-br from-[#FFF1EA] from-10% to-[#FFFBF4] to-90%--}}
{{--                            --}}{{-- Line underneath for the  --}}
{{--                            shadow-lg rounded-bl-[4rem] flex-grow--}}
{{--                            ">--}}
{{--                    {{ $slot }}--}}
{{--                </div>--}}
{{--            @elseif ($center === false)--}}
{{--                <div class="bg-gradient-to-br min-h-screen from-[#FFF1EA] from-10% to-[#FFFBF4] to-90% bg-no-repeat bg-fixed">--}}
{{--                    {{ $slot }}--}}
{{--                </div>--}}
{{--            @endif--}}

{{--            <p class="flex justify-center m-auto text-secondary-light py-4">--}}
{{--                {!! __('partials/footer.creator', ['creator' => '<a class="ml-1 underline text-secondary font-bold" href="https://stpronk.nl/">stpronk.nl</a>']) !!}--}}
{{--            </p>--}}
{{--        </div>--}}
    </body>
</html>
