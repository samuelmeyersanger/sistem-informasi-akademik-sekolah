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
    <body class="font-sans antialiased text-gray-900 bg-gray-50">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
            
            @include('layouts.navigation')

            <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden min-h-screen">
                
                @include('layouts.header')

                <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
                
                <div class="w-full px-4 sm:px-6 lg:px-8">
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    </body>
</html>