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

    @vite(['resources/scss/stpronk.scss', 'resources/js/stpronk.js'])

</head>
<body>
<main class="container">
    <header class="intro">
        <img class="intro__background" src="{{ asset('images/header.jpeg') }}" alt="stpronk heading"/>
        <div class="intro__wrapper">
            <h1 class="intro-wrapper__heading">
                <span class="--bold">"Hi</span>, my name is
                <br class="intro-heading__break intro-heading__break--second">
                <span class="--primary --bold">Steve</span>
                <br class="intro-heading__break"/>
                and I am
                <br class="intro-heading__break intro-heading__break--second">
                a <span class="--bold">Developer"</span>
            </h1>
            <a class="intro-wrapper__scroll" data-function="target-reader" data-target="about">
                <svg width="150" viewBox="0 0 200 50" style="pointer-events: none">
                    <polygon points="0,0 100,35 200,0 100,27.5  0,0" style="stroke:none;stroke-width:0;fill:white"></polygon>
                </svg>
            </a>
        </div>
    </header>

    <section class="about">
        <img class="about__image" src="{{ asset('images/stevepronk.jpeg') }}" alt="Steve Pronk"/>
        <article class="about__content">
            <h2 class="about-content__heading">
                About <span class="--primary">me</span>
            </h2>
            <p class="about-content__text">
                Hi, my name is Steve Pronk and I am a Full-stack Developer. I love myself some code with coffee, even with some designing from time to time.
            </p>

            <p class="about-content__text --secondary-light">
                My second passion is long boarding. Just scrolling through the city, forest or alongside the beach is something I love to do. For
                me it is a good way to think about problems I come across while coding and most of the time it helps me to find a solution.
            </p>

            <p class="about-content__text">
                If you are looking for a person that can work great within a group or alone, then you have come to the right place!
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
            </div>
        </article>
    </section>

    <section class="skills">
        <h2 class="skills__heading">My <span class="--primary">Skills</span></h2>
        <article class="skills__content">
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/html.svg') }}" alt="HTML5" />
                <span class="skills-item__content skills-item__content--second">HTML5</span>
            </div>
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/css.svg') }}" alt="CSS3" />
                <span class="skills-item__content skills-item__content--second">CSS3</span>
            </div>
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/javascript.svg') }}" alt="Javascript" />
                <span class="skills-item__content skills-item__content--second">Javascript</span>
            </div>
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/php.svg') }}" alt="PHP" />
                <span class="skills-item__content skills-item__content--second">PHP</span>
            </div>
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/illustrator.svg') }}" alt="Illustrator" />
                <span class="skills-item__content skills-item__content--second">Illustrator</span>
            </div>
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/git.svg') }}" alt="Git" />
                <span class="skills-item__content skills-item__content--second">Git</span>
            </div>
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/react.svg') }}" alt="ReactJS" />
                <span class="skills-item__content skills-item__content--second">ReactJS</span>
            </div>
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/laravel.svg') }}" alt="Laravel" />
                <span class="skills-item__content skills-item__content--second">Laravel</span>
            </div>
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/sass.svg') }}" alt="Sass" />
                <span class="skills-item__content skills-item__content--second">Sass</span>
            </div>
            <div class="skills__item">
                <img class="skills-item__content" src="{{ asset('images/bootstrap.svg') }}" alt="Bootstrap" />
                <span class="skills-item__content skills-item__content--second">Bootstrap</span>
            </div>
        </article>
    </section>

    <section class="portfolio">
        <h2 class="portfolio__heading">My <span class="--primary">Portfolio</span></h2>
        <article class="portfolio__content">
            <a class="portfolio__item">
                <img src="{{ asset('images/proncar.jpg') }}"
                     alt="Proncar"
                     class="portfolio-item__image"
                     data-function="target-reader"
                     data-target="http://proncar.nl/"/>
            </a>
            <a class="portfolio__item">
                <img src="{{ asset('images/maritiem.jpg') }}"
                     alt="Maritiem"
                     class="portfolio-item__image"
                     data-function="target-reader"
                     data-target="http://maritiemdistrict.nl/"/>
            </a>
            <a class="portfolio__item">
                <img src="{{ asset('images/coders.jpg') }}"
                     alt="Coders Academy"
                     class="portfolio-item__image"
                     data-function="target-reader"
                     data-target="http://codersacademy.nl/">
            </a>
        </article>
    </section>

    <section class="quotes">
        <span class="quotes__item quotes__item--active">
            “My vision is to build Website’s & Web Applications for the future. Keeping
            in mind the rapid changing technology and being ahead of the rest.”
        </span>
        <span class="quotes__item">
            “Some other quote to show that the other quote is active and this one not :)”
        </span>
    </section>

    <section class="contact">
        <h2 class="contact__heading">
            Now that is enough about me, <br />
            let’s talk about you and your idea’s!
        </h2>
        <article class="contact__content">
            <div class="contact__social">
                <a class="contact-social__item" data-function="contact" data-content="0683776295">
                    <img class="contact-social__icon" src="{{ asset('images/whatsapp.svg') }}" alt="whatsapp" />
                </a>
                <a class="contact-social__item" data-function="contact" data-content="stpronk@gmail.com">
                    <img class="contact-social__icon" src="{{ asset('images/mail.svg') }}" alt="mail" />
                </a>
            </div>
            <div class="contact__information">
                <span class="contact-information__output">0683776295</span>
                <i data-function="contact-copy" class="contact__copy fa fa-files-o"></i>
                <input class="contact__copy-input" value="">
            </div>
        </article>
        <p class="contact__footer">
            Hope to hear from you soon!
        </p>
    </section>

    <footer class="footer">
            <span class="footer__content">
                Made with <i class="fa fa-heart footer__heart"></i> by StPronk
            </span>
    </footer>
</main>
</body>
</html>
