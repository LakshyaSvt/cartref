<style>
    .menu > li {
        margin-right: 16px !important;
    }
</style>
<header class="header border-no">
    <div class="sticky-content-wrapper">
        <div class="header-middle sticky-header fix-top sticky-content" style="z-index: 1060;">
            <div class="container-fluid">
                <div class="header-left">
                    <a href="#" class="mobile-menu-toggle">
                        <i class="d-icon-bars2"></i>
                    </a>
                    <a href="{{ route('welcome') }}" class="logo">
                        <img src="{{ Voyager::image(setting('site.new-logo')) }}" alt="{{ env('APP_NAME') }} logo" width="194" height="43">
                    </a>
                    {{--                <div class="showroom-at-home">--}}
                    {{--                    @if(Config::get('icrm.showcase_at_home.feature') == 1)--}}
                    {{--                        @livewire('showcasecount')--}}
                    {{--                    @endif--}}
                    {{--                </div>--}}
                    <!-- End of Logo -->
                </div>
                <div class="header-center">
                    <nav class="main-nav ml-0 mr-0">
                        {{-- @include('components.frontend.headers.01-navs') --}}
                        <ul class="menu">
                            {{ menu('Website', 'components.frontend.headers.01-navs') }}
                        </ul>
                    </nav>
                    <!-- End of Divider -->
                </div>

                <div class="header-right">
                    <div class="header-center">
                        <nav class="main-nav ml-0 mr-4" style="margin: auto !important;display: flex;">
                            <ul class="menu">
                                {{--                            @auth--}}
                                {{--                                <li>--}}
                                {{--                                    <div class="authname">Welcome {{ strtok(auth()->user()->name, ' ') }}!</div>--}}
                                {{--                                </li>--}}
                                {{--                            @endauth--}}
                                @if (count(Config::get('languages')) > 1)
                                    <li class="submenu active">
                                        {{-- <a href="">Select Language</a> --}}
                                        <a href="">{{ Config::get('languages')[App::getLocale()]['display'] }}</a>

                                        <ul>
                                            @foreach (Config::get('languages') as $lang => $language)
                                                @if ($lang != App::getLocale())
                                                    <li data-original-index="{{ $lang }}">
                                                        <a href="{{ route('lang.switch', $lang) }}" tabindex="0" class="" data-tokens="null" role="option" aria-disabled="false"
                                                           aria-selected="false">
                                                            {{$language['display']}}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>

                                    </li>
                                @else
                                    <li></li>
                                @endif
                            </ul>
                        </nav>
                        <!-- End of Divider -->
                    </div>
                    <div class="header-search hs-toggle" style="width: 35rem">
                        <form action="{{ route('search') }}" method="post" class="">
                            @csrf
                            <div class="d-flex">
                                <input type="text" class="form-control" name="search" autocomplete="off" placeholder="Search for Products" value="{{ $_GET['search'] ?? '' }}" required="" style="font-size: 17px"/>
                                <button class="btn btn-search" type="submit" title="submit-button">
                                    <i class="d-icon-search"></i>
                                </button>
                            </div>
                        </form>
                        {{--                    <a href="#" class="search-toggle" title="search">--}}
                        {{--                        <i class="d-icon-search"></i>--}}
                        {{--                    </a>--}}
                        {{--                    <form action="{{ route('search') }}" method="post" class="input-wrapper">--}}
                        {{--                        @csrf--}}
                        {{--                        <input type="text" class="form-control" name="search" autocomplete="off" placeholder="Search your keyword..." value="{{ $_GET['search'] ?? '' }}" required="">--}}
                        {{--                        <button class="btn btn-search" type="submit" title="submit-button">--}}
                        {{--                            <i class="d-icon-search"></i>--}}
                        {{--                        </button>--}}
                        {{--                    </form>--}}
                    </div>
                    <div class="header-search hs-toggle">

                    </div>
                    <!-- End of Header Search -->
                    {{-- <a class="login-link" href="{{ asset('ajax/login.html') }}" data-toggle="login-modal" title="login"><i class="d-icon-user"></i></a> --}}
                    {{-- <a class="login-link" href="{{ route('login') }}" title="login"><i class="d-icon-user"></i></a> --}}
                    <div class="showroom-at-home">
                        @if(Config::get('icrm.showcase_at_home.feature') == 1)
                            @livewire('showcasecount')
                        @endif
                    </div>
                    @livewire('wishlistcount')
                    <div class="mr-4 cart-dropdown d-flex" style="display: block!important;">
                    @auth
                        {{-- if logged in --}}
                        <a href="{{ route('myaccount') }}" class="dropdown cart-dropdown type2 mr-0  wishlist wishlist-dropdown off-canvas d-lg-show justify-content-center" title="My Account">
                            <img src="{{ config('app.url') }}/images/icons/user.png" alt="iCommerce"
                                 style="height: 1.8em;">
                        </a>
                    @else
                        {{-- if not loggedin --}}
                        <a href="{{ route('login') }}" class="dropdown cart-dropdown type2 mr-0  wishlist wishlist-dropdown off-canvas d-lg-show justify-content-center" title="Login or Register">
                            <img src="{{ config('app.url') }}/images/icons/user.png" alt="iCommerce"
                                 style="height: 1.8em;">
                        </a>
                    @endauth

                        <span>Profile</span>
                    </div>



                    @if (Config::get('icrm.customize.feature') == 1)
                        <a href="{{ route('customize.introduction') }}" class="dropdown cart-dropdown type2 mr-4 mr-lg-4" title="Customize">
                            <i class="fas fa-qrcode"></i>
                        </a>
                    @endif

                    @if (Config::get('icrm.customize.feature') == 0 AND Config::get('icrm.showcase_at_home.feature') == 0)
                        <a href="{{ route('aboutus') }}" class="dropdown cart-dropdown type2 mr-4 mr-lg-4" title="About {{ env('APP_NAME') }}">
                            <i class="fas fa-qrcode"></i>
                        </a>
                    @endif

                    @livewire('cartcount')

                </div>
            </div>
        </div>
    </div>
</header>

<div class="notifications" style="background:#000000eb">
    <div class="alert alert-news alert-light alert-round alert-inline" style="padding: 0.2em 0;color:white; display: none">
        @if(Session::get('showcasecity'))
            Products Visible from City {{ Session::get('showcasecity') }}
        @else
            Products Visible from India
        @endif
    </div>
</div>

@include('components.frontend.headers.couponnotifications')
@include('components.frontend.headers.alerts')
