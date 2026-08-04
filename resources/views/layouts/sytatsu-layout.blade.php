@props(['center' => true])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-linear-to-br from-[#FFF1EA] dark:from-[#12100E] from-10% to-[#FFFFFF] dark:to-[#2B4162] to-90% bg-no-repeat">
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

        @php
            $seoDescription = $description ?? config('seo.default_description');
            $seoImage = $image ?? Vite::asset(config('seo.default_image'));
        @endphp

        <meta name="description" content="{{ $seoDescription }}">
        <meta name="keywords" content="{{ config('seo.default_keywords') }}">
        <meta name="author" content="Sytatsu">
        <meta name="google-site-verification" content="{{ config('seo.google-site-verification') }}"/>

        <link rel="canonical" href="{{ url()->current() }}" />

        @php $localeAlternates = \App\Support\LocaleAwareUrlGenerator::alternatesForCurrentRoute(); @endphp
        @foreach($localeAlternates as $altLocale => $altUrl)
            <link rel="alternate" hreflang="{{ $altLocale }}" href="{{ $altUrl }}" />
        @endforeach
        @if(isset($localeAlternates['nl']))
            <link rel="alternate" hreflang="x-default" href="{{ $localeAlternates['nl'] }}" />
        @endif

        <meta property="og:site_name" content="Sytatsu">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $title }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ $seoImage }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $title }}">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        <meta name="twitter:image" content="{{ $seoImage }}">

        @php
            $organizationSchema = [
                '@' . 'context' => 'https://schema.org',
                '@' . 'type' => 'Organization',
                'name' => 'Sytatsu',
                'url' => route('sytatsu.webstore.welcome'),
                'logo' => Vite::asset(config('seo.default_image')),
                'sameAs' => array_values(array_filter([
                    config('socials.sytatsu.facebook.enabled') ? config('socials.sytatsu.facebook.href') : null,
                    config('socials.sytatsu.instagram.enabled') ? config('socials.sytatsu.instagram.href') : null,
                ])),
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES) !!}</script>

        @stack('head')

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

    <body class="bg-linear-to-br from-[#FFF1EA] dark:from-[#12100E] from-10% to-[#FFFFFF] dark:to-[#2B4162] to-90% bg-no-repeat min-h-screen">
        <div class="min-h-screen flex flex-col justify-between">
            <livewire:sytatsu.components.navigation />
            <livewire:sytatsu.components.notification-banner />

            <div class="flex flex-col grow">
                <div class="flex flex-col justify-center content-center px-4 sm:px-6 lg:px-8 py-8 md:py-12">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @include('sytatsu.components.footer')

        <x-cookie-policy-popup />
    </body>

    @livewireScripts
</html>
