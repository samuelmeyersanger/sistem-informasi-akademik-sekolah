<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Fitur Dalam Pengembangan') }}
        </h2>
    </x-slot>

    <div class="py-16 min-h-[calc(100vh-65px)] flex items-center justify-center bg-gray-50/50">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 w-full">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-indigo-500 transform transition-all duration-300 hover:shadow-2xl">
                <div class="p-10 text-center flex flex-col items-center">
                    
                    {{-- Ikon Animasi Kunci Inggris & Obeng (Warna Indigo) --}}
                    <div class="mb-6 p-4 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 shadow-sm animate-pulse">
                        <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l-7.418 7.418a1.148 1.148 0 01-1.607-1.608l7.41-7.41m2.035 1.608a1.144 1.144 0 01-1.607-1.607m1.607 1.607v1.587m0-1.587l6.72-6.72m-6.72 6.72a3 3 0 000-4.242M11.42 10.93a3 3 0 014.242 0M11.42 10.93l-1.921-1.92mM19.5 4.5h.008v.008H19.5V4.5z" />
                        </svg>
                    </div>

                    <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-3">Fitur Sedang Disiapkan</h1>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto leading-relaxed text-sm sm:text-base">
                        Modul ini sedang dalam proses pengerjaan dan uji kelayakan oleh Tim IT Sekolah. Kami akan segera merilis fitur ini dalam pembaruan sistem berikutnya!
                    </p>

                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>