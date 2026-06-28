<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Akses Ditolak') }}
        </h2>
    </x-slot>

    <div class="py-16 min-h-[calc(100vh-65px)] flex items-center justify-center bg-gray-50/50">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 w-full">
            {{-- Card dengan border-t tebal, efek shadow lembut, dan transisi hover --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-red-500 transform transition-all duration-300 hover:shadow-2xl">
                <div class="p-10 text-center flex flex-col items-center">
                    
                    {{-- Badge SVG Lingkungan dengan Efek Glow --}}
                    <div class="mb-6 p-4 rounded-full bg-red-50 border border-red-100 text-red-500 shadow-sm animate-pulse">
                        <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>

                    <h1 class="text-7xl font-black text-red-600 tracking-tight mb-2">403</h1>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 tracking-wide">Izin Akses Diperlukan</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto leading-relaxed text-sm sm:text-base">
                        Maaf, akun Anda tidak memiliki previlese yang cukup untuk membuka halaman ini. Silakan hubungi tim IT Sekolah jika ini merupakan kesalahan sistem.
                    </p>

                    {{-- Tombol dengan Efek Ring & Hover Modern --}}
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-900 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 active:bg-gray-950 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>