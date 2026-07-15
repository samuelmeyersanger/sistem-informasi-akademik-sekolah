<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->judul }} - {{ $schoolProfile->nama_sekolah ?? 'Sistem Akademik' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    {{-- NAVIGASI PUBLIK --}}
    <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-100 sticky top-0 z-50 transition-all">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-20 items-center">
                <a href="{{ route('publik.blog.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all cursor-pointer flex items-center gap-2">
                    <span>←</span> Indeks Berita
                </a>
                
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                    <span>🏷️</span> {{ $post->kategori->nama ?? 'Topik Umum' }}
                </span>
            </div>
        </div>
    </nav>

    {{-- KONTEN ARTIKEL --}}
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 lg:py-16 flex-grow w-full space-y-10">
        
        <article class="bg-white rounded-[2.5rem] p-6 md:p-12 border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-indigo-500 to-emerald-500"></div>
            <div class="absolute right-0 top-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <header class="space-y-4 relative z-10 mb-8 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest justify-center md:justify-start">
                    <span class="flex items-center justify-center md:justify-start gap-1.5">
                        <span>📅</span> {{ $post->created_at->translatedFormat('d F Y') }}
                    </span>
                    <span class="hidden md:inline text-slate-300">•</span>
                    <span class="flex items-center justify-center md:justify-start gap-1.5 text-indigo-500">
                        <span>✍️</span> {{ $post->user->name ?? 'Admin Sekolah' }}
                    </span>
                </div>
                
                <h1 class="text-3xl md:text-5xl font-black tracking-tight text-slate-900 leading-[1.15]">
                    {{ $post->judul }}
                </h1>
            </header>

            @if(!empty($post->gambar))
                <div class="rounded-3xl overflow-hidden aspect-[16/9] lg:aspect-[21/9] bg-slate-100 relative z-10 shadow-lg border border-slate-200 mb-10 group-hover:shadow-xl transition-shadow duration-500">
                    <img src="{{ asset('storage/' . $post->gambar) }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-1000" alt="{{ $post->judul }}">
                </div>
            @endif

            <div class="prose prose-slate prose-lg md:prose-xl max-w-none text-slate-700 leading-relaxed md:leading-loose relative z-10 prose-headings:font-black prose-a:text-indigo-600 prose-img:rounded-2xl">
                {!! nl2br(e($post->konten)) !!}
            </div>
        </article>

        {{-- RUANG DISKUSI KOMENTAR --}}
        <section class="bg-white rounded-[2.5rem] p-6 md:p-12 border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl opacity-60 translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
            
            <div class="relative z-10">
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-widest flex items-center gap-3 mb-8">
                    <span>💬</span> Respon Publik 
                    <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-sm">{{ $comments->count() }}</span>
                </h3>

                @if(session('success_komentar'))
                    <div class="p-4 mb-8 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                        <span class="text-xl">✅</span> {{ session('success_komentar') }}
                    </div>
                @endif

                {{-- Daftar Komentar --}}
                <div class="space-y-5 mb-12">
                    @forelse($comments as $comment)
                        <div class="bg-slate-50 rounded-[1.5rem] p-5 md:p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 mb-3 border-b border-slate-200/60 pb-3">
                                <h4 class="font-black text-slate-800 text-base flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($comment->nama, 0, 1)) }}
                                    </span>
                                    {{ $comment->nama }}
                                </h4>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest bg-white px-2.5 py-1 rounded-md border border-slate-100">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-slate-600 leading-relaxed text-sm font-medium">{{ $comment->komentar }}</p>
                        </div>
                    @empty
                        <div class="bg-slate-50 rounded-[1.5rem] p-8 md:p-12 border-2 border-dashed border-slate-200 text-center">
                            <span class="text-4xl opacity-50 block mb-3">💭</span>
                            <h4 class="font-black text-slate-700 text-base mb-1">Belum Ada Jejak</h4>
                            <p class="text-sm text-slate-500 font-medium">Jadilah yang pertama memberikan tanggapan untuk artikel ini!</p>
                        </div>
                    @endforelse
                </div>

                {{-- Form Komentar Baru --}}
                <div class="bg-indigo-900 rounded-[2rem] p-6 md:p-10 shadow-2xl relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                    
                    <div class="relative z-10">
                        <div class="mb-6">
                            <h4 class="text-xl font-black text-white tracking-tight">Kirim Tanggapan</h4>
                            <p class="text-xs text-indigo-300 font-medium mt-1">Alamat email Anda tidak akan dipublikasikan secara umum.</p>
                        </div>
                        
                        <form action="{{ route('publik.blog.komentar.store', $post->id) }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1.5">Nama Lengkap *</label>
                                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Misal: Budi Santoso"
                                           class="w-full text-sm font-bold text-slate-800 rounded-xl border-transparent focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/20 bg-white/95 py-3 px-4 shadow-inner">
                                    @error('nama') <p class="text-[10px] text-rose-300 mt-1 font-bold">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1.5">Alamat Email *</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Misal: budi@gmail.com"
                                           class="w-full text-sm font-bold text-slate-800 rounded-xl border-transparent focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/20 bg-white/95 py-3 px-4 shadow-inner">
                                    @error('email') <p class="text-[10px] text-rose-300 mt-1 font-bold">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1.5">Isi Komentar *</label>
                                <textarea name="komentar" rows="4" required placeholder="Tulis pandangan atau pertanyaan Anda mengenai topik ini secara sopan..."
                                          class="w-full text-sm font-medium text-slate-800 rounded-xl border-transparent focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/20 bg-white/95 py-3 px-4 shadow-inner"></textarea>
                                @error('komentar') <p class="text-[10px] text-rose-300 mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-400 hover:from-emerald-600 hover:to-emerald-500 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-500/40 transition-all hover:-translate-y-1 cursor-pointer flex items-center justify-center gap-2">
                                    <span>🚀</span> Tayangkan Komentar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </main>

    @include('layouts.footer')

</body>
</html>