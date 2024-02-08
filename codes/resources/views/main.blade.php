<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    @if (Config::get('icrm.seo.feature') == true)
        @include('components.frontend.seo.seo')
    @endif
    <link rel="stylesheet" href="/css/new-main.css">
</head>
<body>
<header>
    <img src="{{ Voyager::image(setting('footer.site_icon')) }}" class="logo" alt="cartrefs_logo">
</header>
<main>
    <div class="container">
        <div class="intro-wrapper">
            <div class="intro1">
                <span>INTRODUCING</span>
                <span>SHOW</span><span>-PING</span>
            </div>
            <div class="intro2">
                <span>BY CARTREF</span><span>S</span>
            </div>
        </div>
        <div class="description-wrapper">
            <div class="desc-1">
                Tired of scrolling over images ?
            </div>
            <div class="desc-2">
                Say goodbye to mindless scrolling and hello to a tangible, hands-on shopping experience at home!
            </div>
        </div>
        <div class="image">
            <img src="{{ Voyager::image(setting('site.cover_image')) }}" alt="">
        </div>
        <div class="footer-desc">
            <div class="fdesc-1">Lets Curate Your Shopping Experience</div>
            <div class="fdesc-2">Lets get you everything you need to be a showstopper...</div>
        </div>
        <div class="footer">
            <div class="btn-1" onclick="window.location.href='{{ route('showcase.getstarted') }}'">
                <a href="{{ route('showcase.getstarted') }}">SHOWROOM AT HOME <img src="/images/arrow-right.png" alt=""></a>
            </div>
            <div class="btn-2" onclick="window.location.href='{{ route('welcome') }}'">
                <a href="{{ route('welcome') }}">CARTREFS GLOBAL STORE <img src="/images/arrow-right.png" alt=""></a>
            </div>
        </div>
    </div>
</main>
</body>
</html>