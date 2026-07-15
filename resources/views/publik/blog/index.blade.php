<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kabar & Edukasi - {{ $schoolProfile->nama_sekolah ?? 'Sistem Akademik' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    {{-- NAVIGASI PUBLIK --}}
    <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-100 sticky top-0 z-50 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-4">
                    <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                         class="w-12 h-12 object-contain drop-shadow-sm" alt="Logo">
                    <div>
                        <a href="/" class="font-black text-slate-800 text-lg block tracking-tight uppercase leading-none mb-1 hover:text-indigo-600 transition-colors">
                            {{ $schoolProfile->nama_sekolah ?? 'Portal Institusi' }}
                        </a>
                        <span class="text-[10px] font-bold text-slate-400 tracking-widest uppercase">Ruang Publik & Informasi</span>
                    </div>
                </div>
                <a href="/" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider rounded-xl transition-all cursor-pointer flex items-center gap-2">
                    <span>←</span> Beranda
                </a>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <div class="relative overflow-hidden bg-slate-900 py-16 lg:py-20 border-b border-slate-800">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/50 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center lg:text-left flex flex-col lg:flex-row justify-between items-center gap-8">
            <div class="max-w-2xl">
                <span class="inline-block py-1 px-3 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-[10px] font-black uppercase tracking-widest mb-4">
                    Jendela Informasi
                </span>
                <h1 class="text-4xl lg:text-5xl font-black tracking-tight text-white mb-4 leading-tight">
                    Warta Edukasi & <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-emerald-400">Jejak Prestasi</span>
                </h1>
                <p class="text-sm lg:text-base font-medium text-slate-400 leading-relaxed">
                    Telusuri arsip publikasi kami yang mencakup agenda kegiatan, pengumuman resmi akademik, hingga capaian membanggakan siswa-siswi di lingkungan sekolah.
                </p>
            </div>
            
            <div class="hidden lg:block">
                <div class="w-32 h-32 bg-white/5 backdrop-blur-md rounded-[2rem] border border-white/10 flex items-center justify-center shadow-2xl rotate-12 hover:rotate-0 transition-transform duration-500">
                    <span class="text-6xl">📰</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            {{-- KOLOM KIRI: LIST BERITA --}}
            <main class="lg:col-span-8 space-y-8">
                
                {{-- Pencarian Mobile --}}
                <form action="{{ route('publik.blog.index') }}" method="GET" class="lg:hidden block bg-white p-4 rounded-2xl shadow-lg shadow-slate-200/40 border border-slate-100 mb-6">
                    <div class="relative flex items-center group">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari berita atau pengumuman..." 
                               class="w-full text-sm font-medium rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner py-3 pl-4 pr-24 transition-colors">
                        <button type="submit" class="absolute right-2 px-4 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg shadow hover:bg-indigo-700 transition-colors">
                            Cari
                        </button>
                    </div>
                </form>

                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-black text-slate-800 tracking-tight">Katalog Artikel</h2>
                    @if(request('search') || request('kategori'))
                        <a href="{{ route('publik.blog.index') }}" class="text-xs font-bold text-rose-500 hover:text-rose-700 hover:underline">
                            Hapus Filter ✖
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    @forelse($posts as $post)
                        <article class="flex flex-col bg-white rounded-[2rem] overflow-hidden border border-slate-100 shadow-xl shadow-slate-200/30 hover:shadow-2xl hover:shadow-indigo-200/40 transition-all duration-300 group hover:-translate-y-1">
                            <div class="aspect-[16/10] bg-slate-100 overflow-hidden relative">
                                <img src="{{ !empty($post->gambar) ? asset('storage/' . $post->gambar) : 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&q=80&w=600' }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" 
                                     alt="{{ $post->judul }}">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
                                <span class="absolute top-4 left-4 px-3 py-1 rounded-lg bg-white/90 backdrop-blur text-indigo-700 font-black text-[10px] uppercase tracking-widest shadow-sm border border-white/50">
                                    {{ $post->kategori->nama ?? 'Umum' }}
                                </span>
                            </div>
                            
                            <div class="p-6 md:p-8 flex flex-col flex-grow justify-between space-y-4 relative bg-white">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <span>📅</span> {{ $post->created_at->translatedFormat('d F Y') }}
                                    </div>
                                    <h2 class="text-xl font-black text-slate-900 leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2">
                                        <a href="{{ route('publik.blog.show', $post->slug) }}" class="focus:outline-none">
                                            {{ $post->judul }}
                                        </a>
                                    </h2>
                                    <p class="text-sm font-medium text-slate-500 line-clamp-3 leading-relaxed">
                                        {{ strip_tags($post->konten) }}
                                    </p>
                                </div>
                                <a href="{{ route('publik.blog.show', $post->slug) }}" class="text-xs font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest inline-flex items-center gap-2 pt-2 border-t border-slate-100 w-full mt-4">
                                    Baca Narasi Penuh <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full bg-slate-50 rounded-[2rem] p-16 text-center border-2 border-dashed border-slate-200">
                            <span class="text-5xl opacity-50 mb-4 block">📭</span>
                            <h3 class="text-lg font-black text-slate-700 mb-2">Arsip Kosong</h3>
                            <p class="text-sm font-medium text-slate-500">Tidak ada artikel atau berita yang sesuai dengan kata kunci dan kategori yang Anda cari.</p>
                        </div>
                    @endforelse
                </div>

                @if($posts->hasPages())
                    <div class="pt-8">
                        {{ $posts->links() }}
                    </div>
                @endif
            </main>

            {{-- KOLOM KANAN: SIDEBAR --}}
            <aside class="lg:col-span-4 space-y-8 relative">
                <div class="sticky top-28 space-y-8">
                    
                    {{-- Form Pencarian Sidebar --}}
                    <div class="hidden lg:block bg-white p-6 md:p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-indigo-500 to-indigo-600"></div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span>🔍</span> Eksplorasi Arsip
                        </h3>
                        <form action="{{ route('publik.blog.index') }}" method="GET">
                            @if(request('kategori'))
                                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                            @endif
                            <div class="space-y-3">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Ketik topik pencarian..." 
                                       class="w-full text-sm font-medium rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner py-3 px-4 transition-colors">
                                <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-indigo-950 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg transition-colors">
                                    Temukan Berita
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- List Kategori Sidebar --}}
                    <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <span>🗂️</span> Indeks Kategori
                        </h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('publik.blog.index') }}" class="group flex justify-between items-center p-3 rounded-xl transition-all {{ !request('kategori') ? 'bg-indigo-50 border border-indigo-100 shadow-sm' : 'hover:bg-slate-50 border border-transparent hover:border-slate-100' }}">
                                    <span class="text-sm font-bold {{ !request('kategori') ? 'text-indigo-700' : 'text-slate-600 group-hover:text-slate-900' }}">
                                        Semua Topik
                                    </span>
                                </a>
                            </li>
                            @foreach($categories as $cat)
                                <li>
                                    <a href="{{ route('publik.blog.index', ['kategori' => $cat->slug]) }}" class="group flex justify-between items-center p-3 rounded-xl transition-all {{ request('kategori') === $cat->slug ? 'bg-indigo-50 border border-indigo-100 shadow-sm' : 'hover:bg-slate-50 border border-transparent hover:border-slate-100' }}">
                                        <span class="text-sm font-bold {{ request('kategori') === $cat->slug ? 'text-indigo-700' : 'text-slate-600 group-hover:text-slate-900' }}">
                                            {{ $cat->nama }}
                                        </span>
                                        <span class="bg-white shadow-sm border border-slate-200 text-slate-500 text-[10px] font-black px-2.5 py-1 rounded-lg transition-colors {{ request('kategori') === $cat->slug ? 'border-indigo-200 text-indigo-600' : 'group-hover:text-slate-700' }}">
                                            {{ $cat->blogs_count }} Artikel
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </aside>

        </div>
    </div>

    @include('layouts.footer')

</body>
</html>