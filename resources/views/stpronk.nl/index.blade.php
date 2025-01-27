<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <meta name="description"
          content="My vision is to build Website’s & Web Applications for the future. Keeping in mind the rapid changing technology and being ahead of the rest!">
    <meta name="keywords"
          content="HTML,CSS,JavaScript,PHP,React,Laravel,Symphony,Scss,Sass,Illustrator,Git,Github,Gitlab,Bootstrap,StPronk,Steve,Pronk,Developer,Designer,Develop,Design,Web,Website,Zuid,Holland,Zuid-Holland,Zoetermeer,Nederland,Maatwerk">
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

    <title>StPronk | Developer</title>

    @vite(['resources/scss/welcome.scss', 'resources/js/welcome.js'])

</head>
<body>
<main class="custom-container">
    <img class="background" src="{{ asset('images/header.jpeg') }}" alt="background"/>
    <div class="content">
        <img class="self-image" src="{{ asset('images/stevepronk.jpeg') }}" alt="Steve Pronk"/>
        <div class="about">
            Hi, my name is Steve Pronk and I am a Full-stack Developer. I love myself some code with coffee, even with some designing from time to time.
            <br />
            My second passion is long boarding. Just scrolling through the city, forest or alongside the beach is something I love to do. For
            me it is a good way to think about problems I come across while coding and most of the time it helps me to find a solution.
            <br />
            If you are looking for a person that can work great within a group or alone, then you have come to the right place!
        </div>

        <div class="buttons">
            <a class="media-link" data-function="target-reader" data-target="https://instagram.com/stpronk">
                <img class="icon" src="{{ asset('images/instagram-white.svg') }}" alt="instagram">
            </a>
            <a class="media-link" data-function="target-reader" data-content="mailto:stpronk@gmail.com">
                <img class="icon" src="{{ asset('images/mail-white.svg') }}" alt="mail" />
            </a>
            <a class="media-link sytatsu avenir-bold text-lg" data-function="target-reader" data-content="https://sytatsui.nl/">
                Sytatsu.nl
            </a>
        </div>
    </div>




    <header class="intro hidden">
        <img class="intro__background" src="{{ asset('images/header.jpeg') }}" alt="stpronk heading"/>
        <div class="intro__wrapper">
            <section class="about">
                <img class="about__image" src="{{ asset('images/stevepronk.jpeg') }}" alt="Steve Pronk"/>
                <article class="about__content">
                    <p class="about-content__text">

                    </p>

                    <p class="about-content__text --secondary-light">
                       </p>

                    <p class="about-content__text">
                    </p>

                    <div class="about-content__social">
                        <a class="about-social__link" data-function="target-reader" data-target="https://facebook.com/steve.pronk">
                            <img class="about-social__icon" src="{{ asset('images/facebook.svg') }}" alt="facebook">
                        </a>
                        <a class="about-social__link" data-function="target-reader" data-target="https://instagram.com/stpronk">
                            <img class="about-social__icon" src="{{ asset('images/instagram.svg') }}" alt="instagram">
                        </a>
                        <a class="about-social__link" data-function="target-reader" data-target="https://twitter.com/stpronk">
                            <img class="about-social__icon" src="{{ asset('images/twitter.svg') }}" alt="twitter">
                        </a>
                        <a class="about-social__link" data-function="target-reader" data-content="mailto:stpronk@gmail.com">
                            <img class="contact-social__icon" src="{{ asset('images/mail.svg') }}" alt="mail" />
                        </a>
                    </div>
                </article>
            </section>
        </div>
    </header>
</main>
</body>
</html>
