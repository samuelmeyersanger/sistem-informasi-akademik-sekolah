<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 bg-slate-50 selection:bg-indigo-500 selection:text-white">
        
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
            
            @include('layouts.navigation')

            <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden min-h-screen relative">
                
                @include('layouts.header')

                <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
                
                <div class="w-full px-4 sm:px-6 lg:px-8 mt-auto relative z-10">
                    @include('layouts.footer')
                </div>
            </div>
        </div>

        {{-- PREMIUM UI: Modal Deteksi Offline Kaca (Glassmorphism) z-[9999] --}}
        <div id="offline-modal" class="fixed inset-0 z-[9999] hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 transition-all duration-300">
            
            {{-- Efek Glow di Belakang Modal --}}
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-96 h-96 bg-rose-500/20 rounded-full blur-[100px] animate-pulse"></div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl max-w-md w-full rounded-[2.5rem] shadow-2xl shadow-rose-900/40 border border-white/50 p-10 text-center transform transition-all relative overflow-hidden group">
                
                {{-- Aksen Sudut Merah --}}
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-rose-100 to-transparent opacity-50 rounded-bl-full pointer-events-none"></div>

                {{-- Ikon Animasi Berdenyut --}}
                <div class="mb-8 relative">
                    <div class="absolute inset-0 bg-rose-200 rounded-full blur-xl animate-pulse opacity-60"></div>
                    <div class="relative p-6 rounded-[2rem] bg-gradient-to-br from-rose-500 to-rose-600 text-white w-20 h-20 flex items-center justify-center mx-auto shadow-xl shadow-rose-500/30 transform transition-transform duration-500 group-hover:scale-105 border border-rose-400">
                        <i class="bi bi-wifi-off text-4xl"></i>
                    </div>
                </div>

                <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-3">Koneksi Terputus</h3>
                
                <div class="h-1 w-12 bg-gradient-to-r from-rose-500 to-amber-500 rounded-full mx-auto mb-6"></div>

                <p class="text-slate-500 text-sm font-medium leading-relaxed mb-10 px-2">
                    Sistem mendeteksi perangkat Anda kehilangan sinyal internet. Harap periksa kembali sambungan Wi-Fi sekolah atau jaringan seluler Anda sebelum melanjutkan agar data Anda aman.
                </p>

                {{-- Indikator Loading Modern Mengambang --}}
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-[10px] font-black text-rose-500 uppercase tracking-widest shadow-inner">
                    <svg class="animate-spin h-4 w-4 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menunggu Sambungan...
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const offlineModal = document.getElementById('offline-modal');

                function checkNetworkStatus() {
                    if (!navigator.onLine) {
                        // Tampilkan modal elegan dengan animasi
                        offlineModal.classList.remove('hidden');
                        offlineModal.classList.add('flex');
                    } else {
                        // Sembunyikan modal
                        offlineModal.classList.add('hidden');
                        offlineModal.classList.remove('flex');
                    }
                }

                // Daftarkan event listener bawaan browser
                window.addEventListener('offline', checkNetworkStatus);
                window.addEventListener('online', checkNetworkStatus);

                // Jalankan pengecekan pertama kali saat halaman di-load
                checkNetworkStatus();
            });
        </script>
    </body>
</html>