<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $schoolProfile->nama_sekolah ?? 'Selamat Datang - SIAS' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media (min-width: 1024px) {
            .menu-laptop-pasti-muncul {
                display: flex !important;
            }
            .tombol-hp-pasti-hilang {
                display: none !important;
            }
        }
        
        /* Custom Scrollbar for Premium Feel */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    {{-- NAVIGATION (GLASSMORPHISM) --}}
    <nav class="bg-white/80 backdrop-blur-xl shadow-sm shadow-slate-200/50 sticky top-0 z-50 border-b border-slate-100 transition-all duration-300" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24 items-center transition-all duration-300" :class="scrolled ? 'h-16' : 'h-24'"> 
                
                {{-- Logo & Brand --}}
                <div class="flex items-center gap-4 relative z-10 group cursor-pointer">
                    <div class="relative w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center p-2 group-hover:shadow-md transition-all group-hover:scale-105 duration-300 overflow-hidden" :class="scrolled ? 'w-10 h-10 sm:w-12 sm:h-12' : 'w-12 h-12 sm:w-14 sm:h-14'">
                        <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                             class="w-full h-full object-contain transform transition-transform group-hover:scale-110" alt="Logo">
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="font-black text-slate-900 text-sm sm:text-base md:text-lg tracking-tight uppercase leading-none group-hover:text-indigo-600 transition-colors">
                            {{ $schoolProfile->nama_sekolah ?? 'SMP NEGERI 4 CIBITUNG' }}
                        </span>
                        <span class="text-[10px] sm:text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">NPSN: {{ $schoolProfile->npsn ?? '-' }}</span>
                    </div>
                </div>

                {{-- Desktop Menu --}}
                <div class="hidden lg:flex menu-laptop-pasti-muncul items-center space-x-1 h-full">
                    <a href="#" class="px-4 py-2 rounded-xl text-sm font-bold text-indigo-600 bg-indigo-50 transition-colors">Beranda</a>
                    
                    @if(isset($dynamicPages) && (is_array($dynamicPages) || $dynamicPages instanceof \Countable) && count($dynamicPages) > 0)
                        @foreach($dynamicPages as $pge)
                            <a href="/pages/{{ $pge->slug }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/50 transition-colors">
                                {{ $pge->title }}
                            </a>
                        @endforeach
                    @endif

                    <a href="#hubungi-kami" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/50 transition-colors">Kontak</a>

                    <div class="flex items-center pl-6 ml-2 border-l-2 border-slate-100 h-8">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-white bg-slate-900 hover:bg-slate-800 rounded-xl shadow-lg shadow-slate-900/20 transition-all hover:-translate-y-0.5">
                                Dashboard Panel
                            </a>
                        @else
                            <a href="/login" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-white bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5">
                                Portal Masuk
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Mobile Menu Button --}}
                <div class="flex lg:hidden tombol-hp-pasti-hilang items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-3 rounded-2xl text-slate-500 bg-slate-50 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none transition-colors border border-slate-100">
                        <span class="sr-only">Buka Menu</span>
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <svg x-show="mobileMenuOpen" style="display: none;" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        {{-- Mobile Menu Panel --}}
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="lg:hidden tombol-hp-pasti-hilang absolute top-full left-0 w-full bg-white/95 backdrop-blur-xl border-b border-slate-200 shadow-xl" style="display: none;">
            <div class="pt-4 pb-6 space-y-2 px-4 max-w-sm mx-auto">
                <a href="#" @click="mobileMenuOpen = false" class="block px-5 py-3 rounded-2xl text-base font-black text-indigo-700 bg-indigo-50 border border-indigo-100">Beranda</a>
                
                @if(isset($dynamicPages))
                    @foreach($dynamicPages as $pge)
                        <a href="/pages/{{ $pge->slug }}" @click="mobileMenuOpen = false" class="block px-5 py-3 rounded-2xl text-base font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 border border-transparent transition-colors">
                            {{ $pge->title }}
                        </a>
                    @endforeach
                @endif

                <a href="#hubungi-kami" @click="mobileMenuOpen = false" class="block px-5 py-3 rounded-2xl text-base font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 border border-transparent transition-colors">Kontak</a>
                
                <div class="pt-4 mt-2 border-t border-slate-100">
                    @auth
                        <a href="{{ url('/dashboard') }}" @click="mobileMenuOpen = false" class="block w-full text-center px-6 py-4 text-sm font-black uppercase tracking-widest text-white bg-slate-900 hover:bg-slate-800 rounded-2xl shadow-lg transition-colors">
                            Dashboard Panel
                        </a>
                    @else
                        <a href="/login" @click="mobileMenuOpen = false" class="block w-full text-center px-6 py-4 text-sm font-black uppercase tracking-widest text-white bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 rounded-2xl shadow-lg shadow-indigo-500/30 transition-colors">
                            Portal Masuk Pengguna
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <header class="relative bg-slate-950 pt-20 pb-24 md:pt-32 md:pb-36 lg:py-40 overflow-hidden flex items-center min-h-[auto] lg:min-h-[85vh]">
        
        {{-- Latar Belakang & Cahaya Kosmik --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-1/2 -right-1/4 w-[1000px] h-[1000px] bg-gradient-to-b from-indigo-500/20 to-transparent rounded-full blur-3xl opacity-50 mix-blend-screen"></div>
            <div class="absolute -bottom-1/2 -left-1/4 w-[800px] h-[800px] bg-gradient-to-t from-emerald-500/10 to-transparent rounded-full blur-3xl opacity-50 mix-blend-screen"></div>
            
            {{-- Pola Grid Halus --}}
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] [mask-image:linear-gradient(to_bottom,white,transparent)]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                
                {{-- Teks & Kalimat Pembuka --}}
                <div class="space-y-8 text-center lg:text-left lg:col-span-7 order-2 lg:order-1 relative z-20">
                    
                    <div class="inline-flex items-center px-4 py-2 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md shadow-2xl">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 mr-3 animate-pulse"></span>
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Selamat Datang di Portal Resmi</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black tracking-tighter text-white leading-[1.1]">
                        {{ $about->judul ?? 'Pendidikan Berkarakter & Berteknologi' }}
                    </h1>
                    
                    <p class="text-base sm:text-lg md:text-xl text-slate-400 max-w-2xl mx-auto lg:mx-0 font-medium leading-relaxed">
                        {{ $about->deskripsi ?? 'Bersama kami, wujudkan potensi terbaik putra-putri Anda menuju masa depan cemerlang di era ekosistem digital terintegrasi.' }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                        @if(!empty($about->tombol_url))
                            <a href="{{ $about->tombol_url }}" class="w-full sm:w-auto px-8 py-4 bg-white hover:bg-slate-100 text-slate-900 font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2">
                                {{ $about->tombol_teks ?? 'Eksplorasi Profil' }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @endif
                        @if(!empty($about->video_url))
                            <a href="{{ $about->video_url }}" target="_blank" class="w-full sm:w-auto px-8 py-4 bg-white/5 hover:bg-white/10 text-white font-black text-xs uppercase tracking-widest rounded-2xl border border-white/10 backdrop-blur-md transition-all flex items-center justify-center gap-3">
                                <span class="bg-indigo-500 text-white w-6 h-6 rounded-full flex items-center justify-center pl-0.5"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4l12 6-12 6z"></path></svg></span>
                                Tonton Video
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Gambar Presentasi --}}
                <div class="relative mx-auto lg:ml-auto w-full max-w-lg lg:max-w-none lg:col-span-5 order-1 lg:order-2 group">
                    <div class="absolute -inset-4 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-[3rem] opacity-30 blur-2xl group-hover:opacity-50 transition-opacity duration-700"></div>
                    
                    <div class="relative bg-slate-900 rounded-[2.5rem] border border-white/10 overflow-hidden shadow-2xl aspect-[4/3] flex flex-col items-center justify-center p-4 transform transition-transform duration-700 hover:scale-[1.02]">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent z-10 opacity-60"></div>
                        
                        @if(!empty($about->gambar))
                            <img src="{{ asset('storage/' . $about->gambar) }}" class="w-full h-full object-cover absolute inset-0 transition-transform duration-1000 group-hover:scale-110" alt="Foto Sekolah">
                        @else
                            <div class="relative z-20 text-center flex flex-col items-center justify-center w-full h-full bg-slate-800/50 backdrop-blur-sm rounded-[2rem] border border-slate-700 border-dashed">
                                <span class="text-6xl mb-4 opacity-50">🏫</span>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest max-w-[200px]">
                                    Visual Utama Belum Diatur
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Elemen Dekoratif Melayang --}}
                    <div class="absolute -bottom-6 -left-6 bg-white/10 backdrop-blur-xl border border-white/20 p-4 rounded-3xl shadow-2xl z-20 animate-bounce" style="animation-duration: 3s;">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Sistem</p>
                                <p class="text-sm font-bold text-white">Akademik Terpadu</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        {{-- Gelombang Pembatas Bawah --}}
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-[0]">
            <svg class="relative block w-[calc(100%+1.3px)] h-[50px] md:h-[80px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118,137.9,132,214.2,120,250.31,114.3,286.4,98.6,321.39,56.44Z" class="fill-slate-50"></path>
            </svg>
        </div>
    </header>

    {{-- KONTEN UTAMA --}}
    <main class="flex-grow py-16 md:py-24 bg-slate-50 space-y-24">
        
        {{-- SECTION BERITA --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 md:mb-16 gap-6">
                <div class="text-center md:text-left">
                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2 block">Informasi Publik</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-slate-900">
                        Berita & Kegiatan
                    </h2>
                </div>
                <div class="text-center md:text-right hidden md:block">
                    <a href="/blog" class="px-6 py-3 bg-white border border-slate-200 hover:border-indigo-300 hover:shadow-lg shadow-sm text-slate-700 hover:text-indigo-600 text-xs font-black uppercase tracking-widest rounded-xl inline-flex items-center gap-2 transition-all group">
                        Lihat Semua Artikel <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($latestPosts as $post)
                    <article class="flex flex-col bg-white rounded-[2.5rem] overflow-hidden border border-slate-100 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group">
                        <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative">
                            <img src="{{ !empty($post->gambar) ? asset('storage/' . $post->gambar) : 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&q=80&w=800' }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
                                 alt="{{ $post->judul }}">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
                            
                            <span class="absolute top-5 left-5 inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black bg-white/90 text-indigo-600 shadow-lg backdrop-blur-md uppercase tracking-widest">
                                {{ $post->kategori->nama ?? 'Umum' }}
                            </span>
                            
                            <div class="absolute bottom-5 left-5 right-5">
                                <span class="text-[10px] text-white/90 font-bold tracking-widest uppercase flex items-center gap-2 drop-shadow-md">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $post->created_at->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="p-8 flex flex-col flex-grow justify-between relative">
                            {{-- Dekorasi Titik Sudut --}}
                            <div class="absolute top-0 right-8 w-16 h-16 bg-gradient-to-br from-indigo-50 to-transparent -translate-y-1/2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            <div class="space-y-4 relative z-10">
                                <h3 class="text-xl font-black text-slate-900 leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2">
                                    <a href="/blog/{{ $post->slug }}" class="focus:outline-none">
                                        <span class="absolute inset-0" aria-hidden="true"></span>
                                        {{ $post->judul }}
                                    </a>
                                </h3>
                                <p class="text-sm text-slate-500 font-medium line-clamp-3 leading-relaxed">
                                    {{ strip_tags($post->konten) }}
                                </p>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full bg-white rounded-[2.5rem] p-12 text-center border-2 border-dashed border-slate-200 shadow-sm">
                        <span class="text-6xl mb-4 block opacity-50">📰</span>
                        <h4 class="text-xl font-black text-slate-700 mb-2">Belum Ada Publikasi</h4>
                        <p class="text-slate-500 font-medium text-sm">Artikel berita atau kegiatan akan ditampilkan di area ini.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-10 text-center md:hidden">
                <a href="/blog" class="px-8 py-4 bg-slate-900 text-white w-full shadow-xl text-xs font-black uppercase tracking-widest rounded-2xl inline-flex justify-center items-center gap-2">
                    Jelajahi Semua Artikel
                </a>
            </div>
        </div>

        {{-- SECTION KONTAK --}}
        <div id="hubungi-kami" class="relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden relative"
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
                     
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        
                        {{-- Sisi Kiri: Informasi --}}
                        <div class="bg-slate-900 p-10 md:p-16 relative overflow-hidden text-white flex flex-col justify-center">
                            <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-indigo-500 rounded-full blur-[100px] opacity-30"></div>
                            
                            <div class="relative z-10">
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-4 block">Pusat Bantuan</span>
                                <h3 class="text-3xl sm:text-4xl font-black tracking-tight mb-6">Hubungi Kami</h3>
                                <p class="text-sm text-slate-300 font-medium leading-relaxed mb-10 max-w-sm">
                                    Punya pertanyaan mengenai pendaftaran, program belajar, atau urusan administratif? Jangan ragu untuk meninggalkan pesan.
                                </p>
                                
                                <div class="space-y-8">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/10 shrink-0">📍</div>
                                        <div>
                                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Alamat Resmi</h4>
                                            <p class="text-sm font-bold text-white">{{ $schoolProfile->alamat ?? 'Perum. Pesona Gading Blok I No.1 Wanajaya-Cibitung' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/10 shrink-0">📞</div>
                                        <div>
                                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Telepon Panggilan</h4>
                                            <p class="text-sm font-bold text-white">{{ $schoolProfile->telepon ?? 'Belum tersedia' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center border border-white/10 shrink-0">✉️</div>
                                        <div>
                                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Surat Elektronik</h4>
                                            <p class="text-sm font-bold text-white">{{ $schoolProfile->email ?? 'smpnegericibitung4@gmail.com' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sisi Kanan: Formulir --}}
                        <div class="p-10 md:p-16 flex flex-col justify-center">
                            
                            <div x-show="successMessage" x-transition class="p-5 mb-8 text-sm font-bold text-emerald-800 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center gap-3" style="display: none;">
                                <span class="text-2xl">✅</span> <span x-text="successMessage"></span>
                            </div>

                            <div x-show="errorMessage" x-transition class="p-5 mb-8 text-sm font-bold text-rose-800 bg-rose-50 rounded-2xl border border-rose-100 flex items-center gap-3" style="display: none;">
                                <span class="text-2xl">⚠️</span> <span x-text="errorMessage"></span>
                            </div>

                            <form @submit.prevent="submitForm" class="space-y-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                                        <input type="text" name="nama" required placeholder="Tuliskan nama Anda" class="w-full font-bold text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 shadow-sm bg-slate-50 py-3.5 px-4 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Alamat Email <span class="text-rose-500">*</span></label>
                                        <input type="email" name="email" required placeholder="nama@email.com" class="w-full font-bold text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 shadow-sm bg-slate-50 py-3.5 px-4 transition-all">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Subjek Pesan <span class="text-rose-500">*</span></label>
                                    <input type="text" name="subject" required placeholder="Maksud & tujuan pesan..." class="w-full font-bold text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 shadow-sm bg-slate-50 py-3.5 px-4 transition-all">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Isi Pesan / Pertanyaan <span class="text-rose-500">*</span></label>
                                    <textarea name="pesan" required rows="4" placeholder="Tuliskan detail pertanyaan atau keluhan di sini secara jelas..." class="w-full font-bold text-sm rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 shadow-sm bg-slate-50 py-3.5 px-4 transition-all resize-y"></textarea>
                                </div>

                                <button type="submit" :disabled="loading" class="w-full py-4 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-1 disabled:opacity-50 disabled:transform-none cursor-pointer flex justify-center items-center mt-4">
                                    <span x-show="!loading" class="flex items-center gap-2"><span>🚀</span> Kirim Pesan Sekarang</span>
                                    <span x-show="loading" style="display: none;" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Memproses...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('layouts.footer')

</body>
</html>