@extends('layouts.website')

@section('meta-seo')
    <title>Showcase at home Checkout</title>
    <meta name="keywords" content="HTML5 Template">
    <meta name="description" content="Riode - Ultimate eCommerce Template">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
@livewire('showcase.myorders')
@endsection