<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SISARAYA') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|playfair-display:700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .font-display { font-family: 'Playfair Display', serif; }
            .font-body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="font-body text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-violet-50 via-blue-50 to-emerald-50">
            <div class="mb-6">
                <a href="/" class="block">
                    <img src="{{ asset('logo-no-bg.png') }}" alt="SISARAYA Logo" class="w-32 h-auto hover:scale-105 transition-transform duration-300">
                </a>
                <h1 class="text-center mt-4 text-2xl font-display font-bold bg-gradient-to-r from-violet-600 via-blue-600 to-emerald-500 bg-clip-text text-transparent">
                    SISARAYA
                </h1>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white/90 backdrop-blur-md shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
