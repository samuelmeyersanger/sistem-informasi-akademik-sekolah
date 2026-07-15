<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }} - {{ $schoolProfile->nama_sekolah ?? 'Sistem Akademik' }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    {{-- NAVIGASI PUBLIK --}}
    <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-100 sticky top-0 z-50 transition-all">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-20 items-center">
                <a href="/" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all cursor-pointer flex items-center gap-2">
                    <span>←</span> Beranda Utama
                </a>
                
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                    <span>📑</span> Informasi Sekolah
                </span>
            </div>
        </div>
    </nav>

    {{-- KONTEN DOKUMEN STATIS --}}
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 lg:py-16 flex-grow w-full space-y-10 relative">
        
        <article class="bg-white rounded-[2.5rem] p-6 md:p-12 lg:p-16 border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-indigo-500 to-indigo-600"></div>
            <div class="absolute right-0 top-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <header class="space-y-4 relative z-10 mb-10 pb-10 border-b border-slate-100/80 text-center md:text-left">
                <h1 class="text-3xl md:text-5xl font-black tracking-tight text-slate-900 leading-[1.15]">
                    {{ $page->title }}
                </h1>
                <div class="inline-flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 mt-2">
                    <span class="text-slate-400">🕒</span>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
                        Diperbarui: {{ $page->updated_at->translatedFormat('d F Y') }}
                    </p>
                </div>
            </header>

            <div class="prose prose-slate prose-lg md:prose-xl max-w-none text-slate-700 leading-relaxed md:leading-loose relative z-10 prose-headings:font-black prose-a:text-indigo-600 prose-img:rounded-2xl prose-table:rounded-xl prose-table:overflow-hidden prose-th:bg-slate-50 prose-td:border-b prose-td:border-slate-100">
                {!! $page->content !!} 
            </div>
        </article>

    </main>

    @include('layouts.footer')

</body>
</html>