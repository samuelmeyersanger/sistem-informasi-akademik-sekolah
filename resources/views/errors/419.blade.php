<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gangguan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-16 min-h-[calc(100vh-65px)] flex items-center justify-center bg-gray-50/50">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 w-full">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-rose-600 transform transition-all duration-300 hover:shadow-2xl">
                <div class="p-10 text-center flex flex-col items-center">
                    
                    <div class="mb-6 p-4 rounded-full bg-rose-50 border border-rose-100 text-rose-600 shadow-sm">
                        <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l-7.418 7.418a1.148 1.148 0 01-1.607-1.608l7.41-7.41m2.035 1.608a1.144 1.144 0 01-1.607-1.607m1.607 1.607v1.587m0-1.587l6.72-6.72m-6.72 6.72a3 3 0 000-4.242M11.42 10.93a3 3 0 014.242 0M11.42 10.93l-1.921-1.92mM19.5 4.5h.008v.008H19.5V4.5z" />
                        </svg>
                    </div>

                    <h1 class="text-7xl font-black text-rose-600 tracking-tight mb-2">500</h1>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 tracking-wide">Terjadi Kesalahan Server</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto leading-relaxed text-sm sm:text-base">
                        Ups! Server kami mendeteksi adanya *crash* logika tak terduga dalam memproses data. Tim pengembang IT sekolah telah dikirimi log laporan otomatis.
                    </p>

                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 bg-rose-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-rose-700 active:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg w-full sm:w-auto justify-center">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>