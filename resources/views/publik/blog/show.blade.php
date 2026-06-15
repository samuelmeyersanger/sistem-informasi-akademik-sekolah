<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $post->judul }} - {{ $schoolProfile->nama_sekolah ?? 'SIAS' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-20 items-center">
                <a href="{{ route('publik.blog.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 flex items-center gap-1">
                    &larr; Kembali ke Blog
                </a>
                <span class="text-xs text-gray-400 font-medium bg-gray-100 px-3 py-1 rounded-full uppercase tracking-wider">
                    {{ $post->kategori->nama ?? 'Umum' }}
                </span>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 flex-grow w-full">
        
        <article class="bg-white rounded-2xl p-6 sm:p-10 border border-gray-100 shadow-sm space-y-6">
            <header class="space-y-3">
                <span class="text-xs text-gray-400 font-medium block">
                    Dipublikasikan pada {{ $post->created_at->translatedFormat('d F Y') }} oleh <span class="font-semibold text-gray-700">{{ $post->user->name ?? 'Admin Sekolah' }}</span>
                </span>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-gray-900 leading-tight">
                    {{ $post->judul }}
                </h1>
            </header>

            @if(!empty($post->gambar))
                <div class="rounded-xl overflow-hidden aspect-video bg-gray-100">
                    <img src="{{ asset('storage/' . $post->gambar) }}" class="w-full h-full object-cover" alt="{{ $post->judul }}">
                </div>
            @endif

            <div class="prose max-w-none text-gray-700 text-sm sm:text-base leading-relaxed space-y-4 pt-4 border-t">
                {!! nl2br(e($post->konten)) !!}
            </div>
        </article>

        <section class="mt-10 bg-white rounded-2xl p-6 sm:p-10 border border-gray-100 shadow-sm space-y-8">
            <h3 class="text-lg font-bold text-gray-900 border-b pb-3">
                Komentar Publik ({{ $comments->count() }})
            </h3>

            @if(session('success_komentar'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm rounded-xl">
                    {{ session('success_komentar') }}
                </div>
            @endif

            <div class="space-y-4">
                @forelse($comments as $comment)
                    <div class="bg-gray-50 rounded-xl p-4 sm:p-5 border border-gray-100 text-sm">
                        <div class="flex justify-between items-start gap-4 mb-2">
                            <h4 class="font-bold text-gray-800">{{ $comment->nama }}</h4>
                            <span class="text-[10px] text-gray-400 font-medium">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-600 leading-relaxed">{{ $comment->komentar }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic text-center py-4">Belum ada komentar di artikel ini. Jadilah yang pertama berkomentar!</p>
                @endforelse
            </div>

            <div class="pt-6 border-t mt-8 space-y-4">
                <h4 class="text-sm font-bold text-gray-900">Tinggalkan Komentar Anda</h4>
                <form action="{{ route('publik.blog.komentar.store', $post->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap *</label>
                            <input type="text" name="nama" value="{{ old('nama') }}" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @error('nama') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @error('email') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Isi Komentar *</label>
                        <textarea name="komentar" rows="4" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="Tulis tanggapan atau pertanyaan Anda mengenai berita ini..."></textarea>
                        @error('komentar') <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 shadow-sm transition-colors">
                        Kirim Komentar
                    </button>
                </form>
            </div>
        </section>

    </main>

    @include('layouts.footer')

</body>
</html>