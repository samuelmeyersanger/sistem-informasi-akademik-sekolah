<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $schoolProfile->nama_sekolah ?? 'Selamat Datang - SIAS' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center"> <div class="flex items-center gap-3">
                    <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                         class="w-12 h-12 object-contain" alt="Logo">
                    <div>
                        <span class="font-bold text-gray-800 text-sm sm:text-base block tracking-tight uppercase leading-none mb-1">
                            {{ !empty($schoolProfile->nama_sekolah) ? $schoolProfile->nama_sekolah : 'SIAS' }}
                        </span>
                        <span class="text-xs text-gray-500 block">NPSN: {{ $schoolProfile->npsn ?? '-' }}</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8 h-full">
                    <a href="#" class="text-sm font-semibold text-indigo-600 border-b-2 border-indigo-600 h-full flex items-center px-1">Beranda</a>
                    
                    @foreach($dynamicPages as $pge)
                        <a href="/pages/{{ $pge->slug }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 border-b-2 border-transparent hover:border-indigo-600 h-full flex items-center px-1 transition-all">
                            {{ $pge->title }}
                        </a>
                    @endforeach

                    @if (Route::has('login'))
                        <div class="flex items-center space-x-6 pl-4 border-l border-gray-200 h-8">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors">
                                        PPDB
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <header class="bg-gradient-to-r from-indigo-950 to-slate-900 text-white py-16 lg:py-24 relative overflow-hidden flex items-center min-h-[500px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="space-y-6 text-center lg:text-left lg:col-span-7">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-wider">
                        Selamat Datang
                    </span>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-white">
                        {{ $about->judul ?? 'Pendidikan Berkarakter & Berteknologi' }}
                    </h1>
                    <p class="text-base sm:text-lg text-indigo-100/80 max-w-xl mx-auto lg:mx-0 font-light leading-relaxed">
                        {{ $about->deskripsi ?? 'Bersama kami, wujudkan potensi terbaik putra-putri Anda menuju masa depan cemerlang di era ekosistem digital terintegrasi.' }}
                    </p>
                    
                    <div class="flex flex-wrap gap-4 justify-center lg:justify-start pt-2">
                        @if(!empty($about->tombol_url))
                            <a href="{{ $about->tombol_url }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm rounded-lg shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                                {{ $about->tombol_teks ?? 'Pelajari Selengkapnya' }}
                            </a>
                        @endif
                        @if(!empty($about->video_url))
                            <a href="{{ $about->video_url }}" target="_blank" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium text-sm rounded-lg border border-white/20 backdrop-blur-sm transition-colors inline-flex items-center gap-2">
                                🎬 Tonton Video Profil
                            </a>
                        @endif
                    </div>
                </div>

                <div class="relative mx-auto lg:ml-auto w-full max-w-md lg:max-w-none lg:col-span-5">
                    <div class="absolute -inset-1 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 opacity-20 blur-xl"></div>
                    <div class="relative bg-slate-900/50 backdrop-blur rounded-2xl border border-white/10 overflow-hidden shadow-2xl aspect-video flex flex-col items-center justify-center p-6 text-center">
                        @if(!empty($about->gambar))
                            <img src="{{ asset('storage/' . $about->gambar) }}" class="w-full h-full object-cover" alt="Foto Sekolah">
                        @else
                            <span class="text-3xl mb-2">🏫</span>
                            <p class="text-xs italic text-indigo-200/60 max-w-[200px]">
                                Preview gambar utama sekolah (Belum diunggah).
                            </p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </header>

    <main class="flex-grow py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        Berita & Kegiatan Terbaru
                    </h2>
                    <p class="mt-3 text-lg text-gray-500">
                        Ikuti terus perkembangan informasi, prestasi, dan agenda kegiatan resmi sekolah kami.
                    </p>
                </div>
                <a href="/blog" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 inline-flex items-center gap-1 whitespace-nowrap group">
                    Lihat Semua Artikel <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($latestPosts as $post)
                    <article class="flex flex-col bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow group">
                        <div class="aspect-[16/10] bg-gray-200 overflow-hidden relative">
                            <img src="{{ !empty($post->gambar) ? asset('storage/' . $post->gambar) : 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&q=80&w=600' }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                                 alt="{{ $post->judul }}">
                            <span class="absolute top-4 left-4 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-white/90 text-indigo-600 shadow-sm backdrop-blur-sm uppercase tracking-wider font-semibold">
                                {{ $post->kategori->nama ?? 'Umum' }}
                            </span>
                        </div>

                        <div class="p-6 flex flex-col flex-grow justify-between space-y-4">
                            <div class="space-y-2">
                                <span class="text-xs text-gray-400 font-medium block">
                                    {{ $post->created_at->translatedFormat('d F Y') }}
                                </span>
                                <h3 class="text-xl font-bold text-gray-900 leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2">
                                    <a href="/blog/{{ $post->slug }}">{{ $post->judul }}</a>
                                </h3>
                                <p class="text-sm text-gray-600 line-clamp-3 leading-relaxed">
                                    {{ strip_tags($post->konten) }}
                                </p>
                            </div>
                            <div class="pt-4 border-t border-gray-200/60">
                                <a href="/blog/{{ $post->slug }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 inline-flex items-center gap-1">
                                    Baca Selengkapnya <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full bg-gray-50 rounded-2xl p-12 text-center border border-dashed border-gray-200">
                        <p class="text-gray-500 italic">Belum ada artikel berita yang dipublikasikan.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </main>

    @include('layouts.footer')

</body>
</html>