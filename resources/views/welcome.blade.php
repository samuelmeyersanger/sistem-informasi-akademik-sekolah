<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $schoolProfile->nama_sekolah ?? 'Selamat Datang - SIAS' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <style>
        @media (min-width: 1024px) {
            .menu-laptop-pasti-muncul {
                display: flex !important;
            }
            .tombol-hp-pasti-hilang {
                display: none !important;
            }
        }
    </style>

    <nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center"> 
                
                <div class="flex items-center gap-3">
                    <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                         class="w-10 h-10 sm:w-12 sm:h-12 object-contain" alt="Logo">
                    <div>
                        <span class="font-bold text-gray-800 text-xs sm:text-sm md:text-base block tracking-tight uppercase leading-none mb-1">
                            {{ $schoolProfile->nama_sekolah ?? 'SMP NEGERI 4 CIBITUNG' }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-gray-500 block">NPSN: {{ $schoolProfile->npsn ?? '-' }}</span>
                    </div>
                </div>

                <div class="hidden lg:flex menu-laptop-pasti-muncul items-center space-x-8 h-full">
                    <a href="#" class="text-sm font-semibold text-indigo-600 border-b-2 border-indigo-600 h-full flex items-center px-1">Beranda</a>
                    
                    @if(isset($dynamicPages) && (is_array($dynamicPages) || $dynamicPages instanceof \Countable) && count($dynamicPages) > 0)
                        @foreach($dynamicPages as $pge)
                            <a href="/pages/{{ $pge->slug }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 border-b-2 border-transparent hover:border-indigo-600 h-full flex items-center px-1 transition-all">
                                {{ $pge->title }}
                            </a>
                        @endforeach
                    @endif

                    <a href="#hubungi-kami" class="text-sm font-medium text-gray-600 hover:text-indigo-600 border-b-2 border-transparent hover:border-indigo-600 h-full flex items-center px-1 transition-all">Kontak</a>

                    <div class="flex items-center pl-4 border-l border-gray-200 h-8">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="/login" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors">
                                Masuk
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="flex lg:hidden tombol-hp-pasti-hilang items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none transition-colors">
                        <span class="sr-only">Buka Menu</span>
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg x-show="mobileMenuOpen" style="display: none;" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <div x-show="mobileMenuOpen" x-transition class="lg:hidden tombol-hp-pasti-hilang bg-white border-b border-gray-200" style="display: none;">
            <div class="pt-2 pb-4 space-y-1 px-4">
                <a href="#" @click="mobileMenuOpen = false" class="block pl-3 pr-4 py-2 border-l-4 border-indigo-500 text-base font-medium text-indigo-700 bg-indigo-50">Beranda</a>
                
                @if(isset($dynamicPages))
                    @foreach($dynamicPages as $pge)
                        <a href="/pages/{{ $pge->slug }}" @click="mobileMenuOpen = false" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
                            {{ $pge->title }}
                        </a>
                    @endforeach
                @endif

                <a href="#hubungi-kami" @click="mobileMenuOpen = false" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">Kontak</a>
                
                <div class="pt-4 pb-2 border-t border-gray-200 mt-4">
                    <div class="px-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" @click="mobileMenuOpen = false" class="block w-full text-center px-4 py-2 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="/login" @click="mobileMenuOpen = false" class="block w-full text-center px-4 py-2 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors">
                                Masuk
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <header class="bg-gradient-to-r from-indigo-950 to-slate-900 text-white py-12 md:py-16 lg:py-24 relative overflow-hidden flex items-center min-h-[auto] lg:min-h-[500px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                <div class="space-y-6 text-center lg:text-left lg:col-span-7 order-2 lg:order-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-wider">
                        Selamat Datang
                    </span>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-white">
                        {{ $about->judul ?? 'Pendidikan Berkarakter & Berteknologi' }}
                    </h1>
                    <p class="text-sm sm:text-base md:text-lg text-indigo-100/80 max-w-xl mx-auto lg:mx-0 font-light leading-relaxed">
                        {{ $about->deskripsi ?? 'Bersama kami, wujudkan potensi terbaik putra-putri Anda menuju masa depan cemerlang di era ekosistem digital terintegrasi.' }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start pt-2">
                        @if(!empty($about->tombol_url))
                            <a href="{{ $about->tombol_url }}" class="w-full sm:w-auto text-center px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm rounded-lg shadow-lg hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                                {{ $about->tombol_teks ?? 'Pelajari Selengkapnya' }}
                            </a>
                        @endif
                        @if(!empty($about->video_url))
                            <a href="{{ $about->video_url }}" target="_blank" class="w-full sm:w-auto text-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium text-sm rounded-lg border border-white/20 backdrop-blur-sm transition-colors inline-flex items-center justify-center gap-2">
                                🎬 Tonton Video Profil
                            </a>
                        @endif
                    </div>
                </div>

                <div class="relative mx-auto lg:ml-auto w-full max-w-md lg:max-w-none lg:col-span-5 order-1 lg:order-2">
                    <div class="absolute -inset-1 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 opacity-20 blur-xl"></div>
                    <div class="relative bg-slate-900/50 backdrop-blur rounded-2xl border border-white/10 overflow-hidden shadow-2xl aspect-video flex flex-col items-center justify-center p-4 md:p-6 text-center">
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

    <main class="flex-grow py-12 md:py-20 bg-white space-y-16 md:space-y-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 md:mb-12 gap-4">
                <div class="text-center md:text-left">
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        Berita & Kegiatan Terbaru
                    </h2>
                    <p class="mt-2 md:mt-3 text-sm sm:text-base md:text-lg text-gray-500">
                        Ikuti terus perkembangan informasi, prestasi, dan agenda kegiatan resmi sekolah kami.
                    </p>
                </div>
                <div class="text-center md:text-right">
                    <a href="/blog" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 inline-flex items-center gap-1 whitespace-nowrap group">
                        Lihat Semua Artikel <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
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

                        <div class="p-5 md:p-6 flex flex-col flex-grow justify-between space-y-4">
                            <div class="space-y-2">
                                <span class="text-xs text-gray-400 font-medium block">
                                    {{ $post->created_at->translatedFormat('d F Y') }}
                                </span>
                                <h3 class="text-lg md:text-xl font-bold text-gray-900 leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2">
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
                    <div class="col-span-full bg-gray-50 rounded-2xl p-8 md:p-12 text-center border border-dashed border-gray-200">
                        <p class="text-gray-500 text-sm italic">Belum ada artikel berita yang dipublikasikan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div id="hubungi-kami" class="bg-slate-50 py-12 md:py-16 border-t border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start"
                     x-data="{
                        loading: false,
                        successMessage: '',
                        errorMessage: '',
                        errors: {},
                        
                        async submitForm(e) {
                            this.loading = true;
                            this.successMessage = '';
                            this.errorMessage = '';
                            this.errors = {};

                            let formData = new FormData(e.target);

                            try {
                                let response = await fetch('{{ route('publik.kontak.store') }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });

                                let result = await response.json();

                                if (response.ok) {
                                    this.successMessage = result.message;
                                    e.target.reset();
                                } else {
                                    if(result.errors) {
                                        this.errors = result.errors;
                                    }
                                    this.errorMessage = result.message || 'Gagal mengirim pesan.';
                                }
                            } catch (error) {
                                this.errorMessage = 'Terjadi gangguan jaringan, silakan coba sesaat lagi.';
                            } finally {
                                this.loading = false;
                            }
                        }
                     }">
                    
                    <div class="lg:col-span-5 space-y-4 text-center lg:text-left">
                        <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight">Hubungi Kami</h3>
                        <p class="text-sm text-gray-500 leading-relaxed max-w-xl mx-auto lg:mx-0">
                            Memiliki pertanyaan terkait operasional sekolah atau kerja sama? Kirim pesan langsung melalui form terpadu ini.
                        </p>
                        <div class="pt-4 text-xs space-y-3 text-gray-600 font-medium text-left max-w-xl mx-auto lg:mx-0 bg-white lg:bg-transparent p-4 lg:p-0 rounded-xl border lg:border-none shadow-sm lg:shadow-none">
                            <div class="flex items-start gap-2"><span>📍</span> <span><strong class="text-gray-900">Alamat:</strong> {{ $schoolProfile->alamat ?? 'Perum. Pesona Gading Blok I No.1 Wanajaya-Cibitung' }}</span></div>
                            <div class="flex items-center gap-2"><span>📞</span> <span><strong class="text-gray-900">Telepon:</strong> {{ $schoolProfile->telepon ?? '-' }}</span></div>
                            <div class="flex items-center gap-2"><span>✉️</span> <span><strong class="text-gray-900">Email:</strong> {{ $schoolProfile->email ?? 'smpnegericibitung4@gmail.com' }}</span></div>
                        </div>
                    </div>

                    <div class="lg:col-span-7 bg-white p-5 md:p-6 rounded-2xl border border-gray-200/80 shadow-sm w-full">
                        <div x-show="successMessage" x-transition class="p-3 mb-4 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-lg border border-emerald-200" style="display: none;">
                            ✅ <span x-text="successMessage"></span>
                        </div>

                        <div x-show="errorMessage" x-transition class="p-3 mb-4 text-xs font-semibold text-rose-700 bg-rose-50 rounded-lg border border-rose-200" style="display: none;">
                            ⚠️ <span x-text="errorMessage"></span>
                        </div>

                        <form @submit.prevent="submitForm" class="space-y-4 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nama" required placeholder="Nama Anda..." class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" required placeholder="anda@email.com" class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-gray-700 mb-1">Subjek / Perihal <span class="text-rose-500">*</span></label>
                                <input type="text" name="subject" required placeholder="Contoh: Informasi Mutasi" class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block font-bold text-gray-700 mb-1">Isi Pesan <span class="text-rose-500">*</span></label>
                                <textarea name="pesan" required rows="4" placeholder="Tuliskan detail pertanyaan disini..." class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>

                            <button type="submit" :disabled="loading" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg cursor-pointer transition-all disabled:opacity-50 text-center shadow-md">
                                <span x-show="!loading">🚀 Kirim Pesan</span>
                                <span x-show="loading" style="display: none;">⏳ Mengirimkan Pesan...</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')

</body>
</html>