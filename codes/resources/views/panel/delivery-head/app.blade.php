<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ Voyager::image(setting('site.site_icon')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Flaticon css -->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.0.0/uicons-solid-straight/css/uicons-solid-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.0.0/uicons-thin-straight/css/uicons-thin-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.0.0/uicons-thin-rounded/css/uicons-thin-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.1.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <!--tailwind stylesheet -->
    <link href="{{ asset('vue/css/app.css') }}" rel="stylesheet">

    <title>Delivery Head | {{ config('app.name') }}</title>
</head>

<body class="bg-primary-50 scrollbar">
<!--Vue App-->
<div id="app">
    <main-app :user="{{auth()->user()}}"></main-app>
</div>

<!-- Toast msg -->
<div id="toast-div"></div>

<!--script -->
<script src="{{ asset('vue/js/deliveryHeadApp.js') }}" defer></script>

</body>
</html>
