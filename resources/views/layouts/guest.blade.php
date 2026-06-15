<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            
            <div class="flex items-center justify-center gap-4 mb-2">
                @if(!empty($logoSetting->logo_pemda))
                    <img src="{{ asset('storage/' . $logoSetting->logo_pemda) }}" 
                         class="w-16 h-16 object-contain" 
                         alt="Logo Pemda">
                @endif

                <a href="/">
                    <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                         class="w-20 h-20 object-contain" 
                         alt="Logo Sekolah">
                </a>
            </div>

            <h1 class="text-center font-bold text-gray-700 text-lg uppercase tracking-wider mb-2">
                Sistem Informasi Akademik Sekolah
            </h1>
            <div class="w-full sm:max-w-md mt-4 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>