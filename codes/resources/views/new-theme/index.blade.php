@extends('new-theme.app')

@section('content')
    <header>
        <div class="head-wrapper">
            <div class="logo-wrapper">
                <img class="logo" src="{{ config('app.url') }}/images/black-logo.png" alt="">
            </div>
            <div class="category-wrapper">
                <div class="item">MEN</div>
                <div class="item">WOMEN</div>
                <div class="item">KIDS</div>
                <div class="item">HOME DECOR</div>
                <div class="item">PRODUCTS</div>
            </div>
            <div class="search-wrapper">
                <i class="d-icon-search"></i>
                <input type="text" placeholder="Search for products" name="search">
            </div>
            <div class="cart-wrapper">
                <div class="showroom-btn">
                    <img src="{{ config('app.url') }}/images/icons/showroom.png" alt="">
                </div>
                <div class="wishlist-btn">
                    <img src="{{ config('app.url') }}/images/icons/wishlist.png" alt="">
                </div>
                <div class="profile-btn">
                    <img src="{{ config('app.url') }}/images/icons/user.png" alt="">
                </div>
                <div class="bag-btn">
                    <img src="{{ config('app.url') }}/images/icons/bag.png" alt="">
                </div>
            </div>
        </div>
    </header>
@endsection