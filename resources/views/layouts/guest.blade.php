<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'SIAS Login') }}</title>
    
    {{-- Memuat Font Tambahan yang Lebih Modern --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 antialiased bg-slate-50 relative overflow-x-hidden selection:bg-indigo-500 selection:text-white">
    
    {{-- Elemen Dekoratif: Latar Belakang Orb Menyala (Glassmorphism Effect) --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        {{-- Orb Biru/Indigo Kiri Atas --}}
        <div class="absolute -top-[20%] -left-[10%] w-[50vw] h-[50vw] rounded-full bg-indigo-500/20 blur-[120px] mix-blend-multiply animate-pulse"></div>
        
        {{-- Orb Cyan Kanan Bawah --}}
        <div class="absolute -bottom-[20%] -right-[10%] w-[50vw] h-[50vw] rounded-full bg-cyan-400/20 blur-[120px] mix-blend-multiply animate-pulse" style="animation-delay: 2s;"></div>
        
        {{-- Orb Emerald Tengah (Mengapung) --}}
        <div class="absolute top-[30%] left-[40%] w-[30vw] h-[30vw] rounded-full bg-emerald-400/10 blur-[100px] mix-blend-multiply animate-bounce" style="animation-duration: 8s;"></div>
        
        {{-- Pola Grid Halus (Opsional, memberikan kesan futuristik) --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDAuNWg0ME0wIDM5LjVoNDBNMC41IDB2NDBNMzkuNSAwdi00MCIgc3Ryb2tlPSJyZ2JhKDE1LCAyMywgNDIsIDAuMDMpIiBzdHJva2Utd2lkdGg9IjEiLz48L3N2Zz4=')] opacity-50"></div>
    </div>

    {{-- Kontainer Utama (Berada di atas efek latar belakang) --}}
    <div class="relative z-10 min-h-screen flex flex-col justify-center items-center py-12 sm:pt-0 px-4">
        
        <div class="w-full max-w-md mx-auto flex flex-col items-center justify-center relative">
            {{-- Bayangan Ekstra di Balik Card Auth --}}
            <div class="absolute inset-0 bg-indigo-500/10 blur-[80px] rounded-full z-0 pointer-events-none"></div>
            
            <div class="w-full relative z-10">
                {{ $slot }}
            </div>
        </div>

    </div>
</body>
</html>