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

    <style>
        #toast-div {
            position: fixed;
            bottom: 15px;
            left: 15px;
            z-index: 99;
        }

        /* Vue MultiSelect */
        .multiselect__tag {
            background: rgb(59 130 246) !important; /* bg-primary-500*/
            border: 0.1px solid #80808075;
        }
        .multiselect__tag .color-backdrop{
            background: white;
            padding: 2px 8px;
            border-radius: 4px;
            border: 0.1px solid #80808024;
        }
        .multiselect__tag .color{
            color: black;
            line-height: 15px !important;
        }
        .multiselect__tag-icon::after {
            color: rgb(29 78 216) !important; /* bg-primary-700*/
        }

        .multiselect__tag-icon:focus, .multiselect__tag-icon:hover {
            background-color: black !important; /* bg-primary-600*/
            color: white !important;
            /*@apply !text-primary-600;*/
        }

        .multiselect__tag-icon:focus::after, .multiselect__tag-icon:hover::after {
            color: white !important;
        }

        .multiselect__option--highlight {
            background: rgb(59 130 246) !important; /* bg-primary-500*/
            outline: none !important;
            color: #fff !important;
        }

        .multiselect__option--selected.multiselect__option--highlight {
            background: rgb(29 78 216) !important; /* bg-red-500*/
            color: #fff !important;
            /*@apply bg-primary-700*/
        }
        /* Vue MultiSelect */

    </style>
    <title>Vendor | {{ config('app.name') }}</title>
</head>

<body class="bg-primary-50">
<!--Vue App-->
<div id="app">
    <main-app :user="{{auth()->user()}}"></main-app>
</div>

<!-- Toast msg -->
<div id="toast-div"></div>

<!--script -->
<script src="{{ asset('vue/js/vendorApp.js') }}" defer></script>

</body>
</html>
