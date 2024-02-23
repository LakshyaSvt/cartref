<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ Config::get('seo.login.title') }}</title>
    <meta name="keywords" content="{{ Config::get('seo.login.keywords') }}">
    <meta name="description" content="{{ Config::get('seo.login.description') }}">
    <link href="{{ asset('vue/css/app.css') }}" rel="stylesheet"/>
</head>
<body>
<div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
    <div class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1">
        <div class="flex-1 bg-primary-100 text-center hidden lg:flex">
            <div class="m-12 xl:m-16 w-full bg-contain bg-center bg-no-repeat" style="background-image: url('{{config('app.url')}}/images/login-bg.svg');"></div>
        </div>
        <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
            <div class="">
                <img src="{{config('app.url')}}/images/full-logo.jpeg" class="w-72 h-16 mx-auto"/>
            </div>
            <div class="mt-48 md:mt-12 flex flex-col items-center">
                <h1 class="text-2xl xl:text-3xl font-extrabold">
                    Merchant Login
                </h1>
                <div class="w-full flex-1 mt-8">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mx-auto max-w-xs">
                            <input
                                    class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white"
                                    type="email" name="email" value="{{ old('email') }}"
                                    placeholder="Username or Email Address *" required
                            />
                            @error('email')
                                <span class="text-red-500 tex-base">{{$message}}</span>
                            @enderror
                            <input
                                    class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-gray-400 focus:bg-white mt-5" name="password"
                                    type="password" autocomplete="current-password"
                                    placeholder="Password *" required
                            />
                            @error('password')
                            <span class="text-red-500 tex-base">{{$message}}</span>
                            @enderror
                            <button
                                    class="mt-5 tracking-wide font-semibold bg-primary-500 text-gray-100 w-full py-4 rounded-lg hover:bg-primary-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none"
                            >
                                <svg class="w-6 h-6 -ml-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                    <circle cx="8.5" cy="7" r="4"/>
                                    <path d="M20 8v6M23 11h-6"/>
                                </svg>
                                <span class="ml-3">Login</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
