<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita & Kegiatan - {{ $schoolProfile->nama_sekolah ?? 'SIAS' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" class="w-12 h-12 object-contain" alt="Logo">
                    <div>
                        <a href="/" class="font-bold text-gray-800 text-base block tracking-tight uppercase leading-none mb-1 hover:text-indigo-600 transition-colors">
                            {{ $schoolProfile->nama_sekolah ?? 'SIAS' }}
                        </a>
                        <span class="text-xs text-gray-500 block">Kembali ke Beranda</span>
                    </div>
                </div>
                <a href="/" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 flex items-center gap-1">
                    &larr; Beranda
                </a>
            </div>
        </div>
    </nav>

    <div class="bg-slate-900 text-white py-12 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Portal Berita Sekolah</h1>
            <p class="mt-2 text-sm sm:text-base text-slate-400">Informasi terpercaya mengenai agenda kegiatan, pengumuman resmi, dan prestasi akademik siswa.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <main class="lg:col-span-8 space-y-8">
                
                <form action="{{ route('publik.blog.index') }}" method="GET" class="lg:hidden block bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berita..." class="w-full text-sm rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">Cari</button>
                    </div>
                </form>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @forelse($posts as $post)
                        <article class="flex flex-col bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
                            <div class="aspect-[16/10] bg-gray-100 overflow-hidden relative">
                                <img src="{{ !empty($post->gambar) ? asset('storage/' . $post->gambar) : 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&q=80&w=600' }}" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300" alt="{{ $post->judul }}">
                                <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded bg-indigo-600 text-white font-semibold text-[10px] uppercase tracking-wider">
                                    {{ $post->kategori->nama ?? 'Umum' }}
                                </span>
                            </div>
                            <div class="p-5 flex flex-col flex-grow justify-between space-y-3">
                                <div class="space-y-1">
                                    <span class="text-[11px] text-gray-400 font-medium block">{{ $post->created_at->translatedFormat('d F Y') }}</span>
                                    <h2 class="text-lg font-bold text-gray-900 leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2">
                                        <a href="{{ route('publik.blog.show', $post->slug) }}">{{ $post->judul }}</a>
                                    </h2>
                                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ strip_tags($post->konten) }}</p>
                                </div>
                                <a href="{{ route('publik.blog.show', $post->slug) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 inline-flex items-center gap-1 pt-2">
                                    Baca Lengkap <span>&rarr;</span>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100 text-gray-500 italic">
                            Tidak ditemukan artikel berita yang sesuai kata kunci atau filter.
                        </div>
                    @endforelse
                </div>

                <div class="pt-6">
                    {{ $posts->links() }}
                </div>
            </main>

            <aside class="lg:col-span-4 space-y-6">
                <div class="hidden lg:block bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Cari Informasi</h3>
                    <form action="{{ route('publik.blog.index') }}" method="GET" class="flex gap-2">
                        @if(request('kategori'))
                            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors">Cari</button>
                    </form>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b">Kategori Berita</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('publik.blog.index') }}" class="text-xs flex justify-between items-center {{ !request('kategori') ? 'text-indigo-600 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">
                                <span>&bull; Semua Berita</span>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('publik.blog.index', ['kategori' => $cat->slug]) }}" class="text-xs flex justify-between items-center {{ request('kategori') === $cat->slug ? 'text-indigo-600 font-bold' : 'text-gray-600 hover:text-indigo-600' }}">
                                    <span>&bull; {{ $cat->nama }}</span>
                                    <span class="bg-gray-100 text-gray-500 text-[10px] px-2 py-0.5 rounded-full font-medium">{{ $cat->blogs_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

        </div>
    </div>

    @include('layouts.footer')

</body>
</html>