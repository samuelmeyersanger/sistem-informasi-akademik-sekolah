<footer class="relative bg-slate-950 text-slate-400 border-t border-slate-900 mt-auto overflow-hidden">
    
    {{-- Cahaya Dekoratif Latar Belakang --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-24 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-emerald-500/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">
            
            {{-- Kolom 1: Identitas Sekolah --}}
            <div class="space-y-6 md:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-4 group cursor-default">
                    <div class="p-3 bg-white/5 backdrop-blur-md rounded-[1.25rem] border border-white/10 shrink-0 shadow-xl shadow-black/50 group-hover:bg-white/10 transition-colors">
                        <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                             class="w-12 h-12 object-contain" 
                             alt="Logo Sekolah"
                             onerror="this.src='https://ui-avatars.com/api/?name=SIAS&background=4f46e5&color=fff'">
                    </div>
                    <div>
                        <span class="font-black text-white text-base block tracking-widest uppercase whitespace-normal leading-none mb-1.5 group-hover:text-indigo-400 transition-colors">
                            {{ $schoolProfile->nama_sekolah ?? 'SIAS' }}
                        </span>
                        <span class="text-[10px] text-indigo-500 font-bold uppercase tracking-[0.2em] block">Sistem Terpadu</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed font-medium">
                    Aplikasi sistem informasi akademik resmi {{ $schoolProfile->nama_sekolah ?? 'Sekolah Kita' }}. Mewujudkan ekosistem digital yang transparan, akuntabel, dan terintegrasi penuh.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    {{-- Opsional: Ikon Sosial Media (Placeholder) --}}
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-slate-400 hover:bg-indigo-500 hover:text-white transition-all transform hover:-translate-y-1"><i class="fa-brands fa-facebook-f text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-slate-400 hover:bg-rose-500 hover:text-white transition-all transform hover:-translate-y-1"><i class="fa-brands fa-instagram text-xs"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-slate-400 hover:bg-red-600 hover:text-white transition-all transform hover:-translate-y-1"><i class="fa-brands fa-youtube text-xs"></i></a>
                </div>
            </div>

            {{-- Kolom 2 & 3: Tautan Terkait --}}
            @if(isset($footerLinks) && $footerLinks->isNotEmpty())
                @foreach($footerLinks as $namaGroup => $links)
                    <div>
                        <h3 class="text-[11px] font-black text-white tracking-[0.2em] uppercase mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            {{ $namaGroup }}
                        </h3>
                        <ul class="space-y-3.5 text-xs">
                            @foreach($links as $link)
                                <li>
                                    <a href="{{ $link->url }}" class="text-slate-400 font-medium hover:text-indigo-400 transition-all flex items-center gap-2 group">
                                        <span class="text-slate-700 group-hover:text-indigo-400 group-hover:translate-x-1 transition-all text-[10px]">▶</span>
                                        {{ $link->judul }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <div>
                    <h3 class="text-[11px] font-black text-white tracking-[0.2em] uppercase mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-700"></span>
                        Tautan Eksternal
                    </h3>
                    <div class="p-4 bg-white/5 backdrop-blur-sm rounded-2xl border border-white/5 inline-flex flex-col gap-2 text-xs text-slate-500">
                        <span class="text-lg opacity-50">🔗</span>
                        <span class="font-medium">Belum ada tautan terkait yang diatur oleh Admin.</span>
                    </div>
                </div>
            @endif

            {{-- Kolom 4: Hubungi Kami --}}
            <div>
                <h3 class="text-[11px] font-black text-white tracking-[0.2em] uppercase mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Hubungi Kami
                </h3>
                <ul class="space-y-5 text-xs">
                    <li class="flex items-start gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover:bg-white/10 transition-colors">
                            <i class="fa-solid fa-phone text-slate-300"></i>
                        </div>
                        <div class="flex-1 pt-1">
                            <span class="block text-[10px] text-slate-500 font-black uppercase tracking-widest mb-0.5">Telepon</span>
                            <span class="text-white font-bold">
                                 {{ $schoolProfile->telepon ?? 'Belum Diatur' }}
                            </span>
                        </div>
                    </li>
                    
                    <li class="flex items-start gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover:bg-white/10 transition-colors">
                            <i class="fa-solid fa-envelope text-slate-300"></i>
                        </div>
                        <div class="flex-1 pt-1">
                            <span class="block text-[10px] text-slate-500 font-black uppercase tracking-widest mb-0.5">Email Resmi</span>
                            <span class="text-indigo-400 font-bold break-all">
                                {{ $contactSettings['email_sekolah'] ?? ($schoolProfile->email ?? 'Belum Diatur') }}
                            </span>
                        </div>
                    </li>
                    
                    <li class="flex items-start gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center shrink-0 group-hover:bg-white/10 transition-colors">
                            <i class="fa-solid fa-map-location-dot text-slate-300"></i>
                        </div>
                        <div class="flex-1 pt-1">
                            <span class="block text-[10px] text-slate-500 font-black uppercase tracking-widest mb-0.5">Lokasi Instansi</span>
                            <span class="text-slate-300 font-medium leading-relaxed block">
                                @if(!empty($schoolProfile->alamat))
                                    {{ $schoolProfile->alamat }}, Kel. {{ $schoolProfile->kelurahan }}, Kec. {{ $schoolProfile->kecamatan }}, {{ $schoolProfile->kota }}, {{ $schoolProfile->provinsi }}, {{ $schoolProfile->kode_pos }}
                                @else
                                    <span class="italic text-slate-500">Alamat belum diatur dalam sistem.</span>
                                @endif
                            </span>
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Baris Hak Cipta & Status --}}
        <div class="mt-16 pt-8 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-6 text-[10px] font-bold uppercase tracking-widest text-slate-500">
            <p class="text-center sm:text-left">
                &copy; {{ date('Y') }} <span class="text-slate-400">{{ $schoolProfile->nama_sekolah ?? 'SIAS Aplikasi Sekolah' }}</span>. Hak Cipta Dilindungi.
            </p>
            <div class="flex flex-wrap justify-center items-center gap-3 text-slate-600">
                <span>Develop By <span class="text-indigo-400">{{ $schoolProfile->nama_pembuat ?? 'SAMUEL MEYER SANGER, MTCRE' }}</span></span>
                <span class="h-1 w-1 rounded-full bg-slate-700 hidden sm:block"></span>
                <span class="text-emerald-500 flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Sistem Online
                </span>
            </div>
        </div>

    </div>
</footer>