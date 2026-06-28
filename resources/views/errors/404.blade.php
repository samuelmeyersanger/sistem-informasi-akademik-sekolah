<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Halaman Tidak Ditemukan') }}
        </h2>
    </x-slot>

    <div class="py-16 min-h-[calc(100vh-65px)] flex items-center justify-center bg-gray-50/50">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 w-full">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-amber-500 transform transition-all duration-300 hover:shadow-2xl">
                <div class="p-10 text-center flex flex-col items-center">
                    
                    <div class="mb-6 p-4 rounded-full bg-amber-50 border border-amber-100 text-amber-500 shadow-sm">
                        <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <h1 class="text-7xl font-black text-amber-500 tracking-tight mb-2">404</h1>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 tracking-wide">Halaman Hilang / Rusak</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto leading-relaxed text-sm sm:text-base">
                        Aduh! Alamat URL yang Anda tuju tidak ditemukan di sistem ERP Sekolah. Tautan mungkin sudah kedaluwarsa atau salah ketik.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <button onclick="history.back()" class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150 justify-center">
                            Kembali
                        </button>
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 bg-amber-500 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg justify-center">
                            Ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>