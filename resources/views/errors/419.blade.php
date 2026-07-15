<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-3xl">🛠️</span> {{ __('Gangguan Sistem Server') }}
        </h2>
    </x-slot>

    <div class="py-16 min-h-[calc(100vh-65px)] flex items-center justify-center bg-slate-50 relative overflow-hidden">
        
        {{-- Dekorasi Background Modern (Warna Rose/Merah) --}}
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-rose-400 rounded-full mix-blend-multiply filter blur-3xl opacity-15 animate-blob"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-red-400 rounded-full mix-blend-multiply filter blur-3xl opacity-15 animate-blob animation-delay-2000"></div>

        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 w-full relative z-10">
            <div class="bg-white overflow-hidden shadow-2xl shadow-rose-900/5 sm:rounded-[2rem] border border-gray-100 transform transition-all duration-500 hover:-translate-y-1 hover:shadow-rose-900/10 text-center relative">
                
                {{-- Aksen Garis Atas Tipis --}}
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-rose-500 to-red-500"></div>

                <div class="px-10 py-16 flex flex-col items-center">
                    
                    {{-- Ikon Premium --}}
                    <div class="mb-8 relative">
                        <div class="absolute inset-0 bg-rose-100 rounded-full animate-ping opacity-75"></div>
                        <div class="relative w-24 h-24 bg-rose-50 rounded-full border-2 border-rose-200 flex items-center justify-center shadow-inner">
                            <span class="text-5xl">💥</span>
                        </div>
                    </div>

                    {{-- Teks 500 dengan Gradien --}}
                    <h1 class="text-8xl font-black bg-clip-text text-transparent bg-gradient-to-br from-rose-500 to-red-600 tracking-tighter mb-4 leading-none">
                        500
                    </h1>
                    
                    <h3 class="text-2xl font-black text-gray-900 mb-4 tracking-tight">Terjadi Kesalahan Internal</h3>
                    
                    <p class="text-gray-500 mb-10 max-w-md mx-auto leading-relaxed text-sm font-medium">
                        Ups! Server kami mendeteksi adanya <em>crash</em> atau logika tak terduga dalam memproses data. Laporan log otomatis telah dikirim ke Tim Pengembang IT Sekolah untuk diperbaiki.
                    </p>

                    {{-- Tombol Aksi Premium --}}
                    <a href="{{ url('/dashboard') }}" class="group relative inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-rose-600 to-red-600 rounded-2xl overflow-hidden transition-all hover:from-rose-700 hover:to-red-700 shadow-lg shadow-rose-500/30 w-full sm:w-auto transform hover:-translate-y-0.5">
                        <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-56 group-hover:h-56 opacity-20"></span>
                        <span class="relative flex items-center gap-2">
                            Kembali ke Dashboard Aman
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                    </a>

                </div>
            </div>
            
            <div class="text-center mt-8">
                <p class="text-xs font-semibold text-gray-400">Kode Error: HTTP 500 Internal Server Error</p>
            </div>
        </div>
    </div>

    <style>
        /* Animasi Blob Background */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</x-app-layout>