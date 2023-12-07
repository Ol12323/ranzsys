<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
 
        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <title>{{ config('app.name') }}</title>
 
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
 
        @filamentStyles
        @vite('resources/css/app.css')
        @livewireStyles
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    </head>
    <body class="antialiased bg-gray-950">
        <header class="">
            <x-nav-bar/>
        </header>
        @yield('content')
        {{-- Contact --}}
        <section class="bg-gray-950 h-screen">
            <div class="container px-6 py-12 mx-auto">
                <div>
                    <p class="font-medium text-blue-500">Contact us</p>

                    <h1 class="mt-2 text-2xl font-semibold text-white md:text-3xl">Get in touch</h1>

                    <p class="mt-3 text-gray-400">Our friendly team is always here to chat.</p>
                </div>

                <div class="grid grid-cols-1 gap-12 mt-10 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <span class="inline-block p-3 text-blue-500 rounded-full bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </span>

                        <h2 class="mt-4 text-lg font-medium text-white">Email</h2>
                        <p class="mt-2 text-gray-400">Our friendly team is here to help.</p>
                        <p class="mt-2 text-blue-400">ranzphotography@yahoo.com</p>
                    </div>

                    <div>
                        <span class="inline-block p-3 text-blue-500 rounded-full bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </span>

                        <h2 class="mt-4 text-lg font-medium text-white">Office</h2>
                        <p class="mt-2 text-gray-400">Come say hello at our office HQ.</p>
                        <p class="mt-2 text-blue-400">Door 1 Grageda Bldg. Quezon St. New Pandan</p>
                    </div>

                    <div>
                        <span class="inline-block p-3 text-blue-500 rounded-full bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                        </span>

                        <h2 class="mt-4 text-lg font-medium text-white">Phone</h2>
                        <p class="mt-2 text-gray-400">Mon-Fri from 8am to 5pm.</p>
                        <p class="mt-2 text-blue-400">0946 052 0523</p>
                    </div>
                </div>
            </div>
        </section>
        {{-- End of contact --}}
        {{-- Footer --}}
        <footer class="bg-transparent">
            <div class="container flex flex-col items-center justify-between p-6 mx-auto space-y-4 sm:space-y-0 sm:flex-row">
                <a href="#">
                    <img class="w-auto h-6 sm:h-7" src="{{asset('images/logo.png')}}" alt="">
                </a>

                <p class="text-sm text-gray-300">© Copyright 2021. All Rights Reserved.</p>

                <div class="flex -mx-2">
                    <a href="https://www.facebook.com/profile.php?id=100064168503688" class="mx-2 text-gray-600 transition-colors duration-300 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400" aria-label="Facebook">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2.00195 12.002C2.00312 16.9214 5.58036 21.1101 10.439 21.881V14.892H7.90195V12.002H10.442V9.80204C10.3284 8.75958 10.6845 7.72064 11.4136 6.96698C12.1427 6.21332 13.1693 5.82306 14.215 5.90204C14.9655 5.91417 15.7141 5.98101 16.455 6.10205V8.56104H15.191C14.7558 8.50405 14.3183 8.64777 14.0017 8.95171C13.6851 9.25566 13.5237 9.68693 13.563 10.124V12.002H16.334L15.891 14.893H13.563V21.881C18.8174 21.0506 22.502 16.2518 21.9475 10.9611C21.3929 5.67041 16.7932 1.73997 11.4808 2.01722C6.16831 2.29447 2.0028 6.68235 2.00195 12.002Z">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        </footer>
        {{-- End of footer --}}
        @stack('scripts')
        @livewireScripts
        @livewire('notifications')
        @filamentScripts
        @vite('resources/js/app.js')
    </body>
</html>