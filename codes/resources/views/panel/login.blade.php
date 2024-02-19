<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Document</title>
    <link href="{{ asset('vue/css/app.css') }}" rel="stylesheet"/>
</head>
<body>
<div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
    <div class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1">
        <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
            <div class="">
                <img src="{{config('app.url')}}/images/black-logo.png" class="w-40 h-12 mx-auto"/>
            </div>
            <div class="mt-12 flex flex-col items-center">
                <h1 class="text-2xl xl:text-3xl font-extrabold">
                    Sign in
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
                                <span class="ml-3">Sign In</span>
                            </button>
                        </div>
                    </form>

                    <div class="my-12 border-b text-center">
                        <div class="leading-none px-2 inline-block text-sm text-gray-600 tracking-wide font-medium bg-white transform translate-y-1/2">
                            Or
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        @if (Config::get('icrm.social_auth.google') == true)
                            <a href="{{ route('auth.redirect', ['client' => 'google']) }}"
                               class="w-full max-w-xs font-bold shadow-sm rounded-lg py-3 bg-primary-100 text-gray-800 flex items-center justify-center transition-all duration-300 ease-in-out focus:outline-none hover:shadow focus:shadow-sm focus:shadow-outline"
                            >
                                <div class="bg-white p-2 rounded-full">
                                    <svg class="w-4" viewBox="0 0 533.5 544.3">
                                        <path d="M533.5 278.4c0-18.5-1.5-37.1-4.7-55.3H272.1v104.8h147c-6.1 33.8-25.7 63.7-54.4 82.7v68h87.7c51.5-47.4 81.1-117.4 81.1-200.2z"
                                              fill="#4285f4"/>
                                        <path d="M272.1 544.3c73.4 0 135.3-24.1 180.4-65.7l-87.7-68c-24.4 16.6-55.9 26-92.6 26-71 0-131.2-47.9-152.8-112.3H28.9v70.1c46.2 91.9 140.3 149.9 243.2 149.9z"
                                              fill="#34a853"/>
                                        <path d="M119.3 324.3c-11.4-33.8-11.4-70.4 0-104.2V150H28.9c-38.6 76.9-38.6 167.5 0 244.4l90.4-70.1z" fill="#fbbc04"/>
                                        <path d="M272.1 107.7c38.8-.6 76.3 14 104.4 40.8l77.7-77.7C405 24.6 339.7-.8 272.1 0 169.2 0 75.1 58 28.9 150l90.4 70.1c21.5-64.5 81.8-112.4 152.8-112.4z"
                                              fill="#ea4335"/>
                                    </svg>
                                </div>
                                <span class="ml-4">Sign in with Google</span>
                            </a>
                        @endif
                        @if (Config::get('icrm.social_auth.facebook') == true)
                            <a href="{{ route('auth.redirect', ['client' => 'facebook']) }}"
                               class="w-full max-w-xs font-bold shadow-sm rounded-lg py-3 bg-primary-100 text-gray-800 flex items-center justify-center transition-all duration-300 ease-in-out focus:outline-none hover:shadow focus:shadow-sm focus:shadow-outline mt-5"
                            >
                                <div class="bg-white p-2 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" class="w-5" viewBox="0 0 48 48">
                                        <path fill="#039be5" d="M24 5A19 19 0 1 0 24 43A19 19 0 1 0 24 5Z"></path>
                                        <path fill="#fff" d="M26.572,29.036h4.917l0.772-4.995h-5.69v-2.73c0-2.075,0.678-3.915,2.619-3.915h3.119v-4.359c-0.548-0.074-1.707-0.236-3.897-0.236c-4.573,0-7.254,2.415-7.254,7.917v3.323h-4.701v4.995h4.701v13.729C22.089,42.905,23.032,43,24,43c0.875,0,1.729-0.08,2.572-0.194V29.036z"></path>
                                    </svg>
                                </div>
                                <span class="ml-4">Sign in with Facebook</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-1 bg-primary-100 text-center hidden lg:flex">
            <div class="m-12 xl:m-16 w-full bg-contain bg-center bg-no-repeat" style="background-image: url('{{config('app.url')}}/images/login-bg.svg');"></div>
        </div>
    </div>
</div>
</body>
</html>
