<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

        {{-- Kita beri z-[9999] agar ia mutlak berada di lapisan paling atas melompati sidebar/header --}}
        <div id="offline-modal" class="fixed inset-0 z-[9999] hidden bg-gray-950/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white max-w-md w-full rounded-2xl shadow-2xl border-t-4 border-gray-400 p-8 text-center transform transition-all">
                
                {{-- Menggunakan Ikon Bootstrap Icons (bi-wifi-off) yang sudah di-load di head Anda --}}
                <div class="mb-5 p-4 rounded-full bg-gray-100 text-gray-500 w-16 h-16 flex items-center justify-center mx-auto shadow-inner animate-bounce">
                    <i class="bi bi-wifi-off text-3xl"></i>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Koneksi Internet Putus</h3>
                <p class="text-gray-500 text-sm leading-relaxed mb-6">
                    Sistem mendeteksi perangkat Anda kehilangan sinyal internet. Harap periksa kembali sambungan Wi-Fi sekolah atau paket data Anda sebelum melanjutkan tindakan agar data pengisian tidak hilang.
                </p>

                {{-- Indikator Loading Mengambang --}}
                <div class="inline-flex items-center text-xs font-semibold text-gray-500 uppercase tracking-widest">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menunggu jaringan kembali...
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const offlineModal = document.getElementById('offline-modal');

                function checkNetworkStatus() {
                    if (!navigator.onLine) {
                        // Hilangkan class 'hidden' untuk menampilkan modal saat internet putus
                        offlineModal.classList.remove('hidden');
                    } else {
                        // Tambahkan class 'hidden' untuk menyembunyikan modal saat internet tersambung lagi
                        offlineModal.classList.add('hidden');
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