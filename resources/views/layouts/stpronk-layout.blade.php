<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <title>{{ $title }}</title>

        <meta name="description" content="My vision is to build Website’s & Web Applications for the future. Keeping in mind the rapid changing technology and being ahead of the rest!">
        <meta name="keywords" content="HTML,CSS,JavaScript,PHP,React,Laravel,Symphony,Scss,Sass,Illustrator,Git,Github,Gitlab,Bootstrap,StPronk,Steve,Pronk,Developer,Designer,Develop,Design,Web,Website,Zuid,Holland,Zuid-Holland,Zoetermeer,Nederland,Maatwerk">
        <meta name="author" content="Steve Pronk">
        <meta name="google-site-verification" content="xfuyR1cAs5xv7EvMWANbPC7PMb7G1xlk8G5pMl2tnlI"/>

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{--    ~~~ Favicon is not within the project at the moment ~~~ --}}
        {{--    <link rel="apple-touch-icon" sizes="57x57" href="images/icons/favicon/apple-icon-57x57.png">--}}
        {{--    <link rel="apple-touch-icon" sizes="60x60" href="images/icons/favicon/apple-icon-60x60.png">--}}
        {{--    <link rel="apple-touch-icon" sizes="72x72" href="images/icons/favicon/apple-icon-72x72.png">--}}
        {{--    <link rel="apple-touch-icon" sizes="76x76" href="images/icons/favicon/apple-icon-76x76.png">--}}
        {{--    <link rel="apple-touch-icon" sizes="114x114" href="images/icons/favicon/apple-icon-114x114.png">--}}
        {{--    <link rel="apple-touch-icon" sizes="120x120" href="images/icons/favicon/apple-icon-120x120.png">--}}
        {{--    <link rel="apple-touch-icon" sizes="144x144" href="images/icons/favicon/apple-icon-144x144.png">--}}
        {{--    <link rel="apple-touch-icon" sizes="152x152" href="images/icons/favicon/apple-icon-152x152.png">--}}
        {{--    <link rel="apple-touch-icon" sizes="180x180" href="images/icons/favicon/apple-icon-180x180.png">--}}
        {{--    <link rel="icon" type="image/png" sizes="192x192"  href="images/icons/favicon/android-icon-192x192.png">--}}
        {{--    <link rel="icon" type="image/png" sizes="32x32" href="images/icons/favicon/favicon-32x32.png">--}}
        {{--    <link rel="icon" type="image/png" sizes="96x96" href="images/icons/favicon/favicon-96x96.png">--}}
        {{--    <link rel="icon" type="image/png" sizes="16x16" href="images/icons/favicon/favicon-16x16.png">--}}
        {{--    <meta name="msapplication-TileImage" content="images/icons/favicon/ms-icon-144x144.png">--}}
        <meta name="msapplication-TileColor" content="#000">
        <meta name="theme-color" content="#000">

        @vite([
            'resources/scss/stpronk.scss',
            'resources/js/stpronk.js'
        ])

        @livewireStyles
        @livewireScripts
    </head>

    <body>
        {{ $slot }}
    </body>
</html>
