<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-slate-500">
            <span class="text-indigo-600">Sistem</span>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800">Status Pengembangan</span>
        </div>
    </x-slot>

    <div class="py-12 md:py-20 min-h-[calc(100vh-65px)] flex items-center justify-center bg-slate-50/50 relative overflow-hidden font-sans">
        
        {{-- Dekorasi Latar Belakang --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-96 bg-indigo-500/10 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="absolute top-20 right-20 w-64 h-64 bg-emerald-500/10 blur-[80px] rounded-full pointer-events-none"></div>
        
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 w-full relative z-10">
            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-2xl shadow-indigo-200/40 rounded-[2.5rem] border border-white/50 group">
                <div class="p-10 md:p-14 text-center flex flex-col items-center relative">
                    
                    {{-- Aksen Sudut --}}
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-indigo-100 to-transparent opacity-50 rounded-bl-full pointer-events-none"></div>

                    {{-- Ikon Animasi Melayang --}}
                    <div class="mb-8 relative">
                        <div class="absolute inset-0 bg-indigo-200 rounded-full blur-xl animate-pulse opacity-50"></div>
                        <div class="relative p-6 rounded-[2rem] bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-xl shadow-indigo-500/30 transform transition-transform duration-500 group-hover:-translate-y-2 group-hover:scale-105 border border-indigo-400">
                            <svg class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l-7.418 7.418a1.148 1.148 0 01-1.607-1.608l7.41-7.41m2.035 1.608a1.144 1.144 0 01-1.607-1.607m1.607 1.607v1.587m0-1.587l6.72-6.72m-6.72 6.72a3 3 0 000-4.242M11.42 10.93a3 3 0 014.242 0M11.42 10.93l-1.921-1.92mM19.5 4.5h.008v.008H19.5V4.5z" />
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight mb-4">Fitur Sedang Disiapkan</h1>
                    
                    <div class="h-1 w-16 bg-gradient-to-r from-indigo-500 to-emerald-500 rounded-full mb-6"></div>

                    <p class="text-slate-500 font-medium mb-10 max-w-md mx-auto leading-relaxed text-sm">
                        Modul ini sedang dalam proses pengembangan & perakitan arsitektur oleh Tim IT. Kami akan segera merilisnya secara resmi dalam pembaruan sistem berikutnya.
                    </p>

                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-slate-900/20 transition-all hover:-translate-y-1 w-full sm:w-auto justify-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Dashboard Utama
                    </a>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center justify-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Sistem Sedang Bekerja
                </p>
            </div>
        </div>
    </div>
</x-app-layout>