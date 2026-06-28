<footer class="bg-slate-900 text-slate-400 border-t border-slate-800 mt-auto">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10">
            
            <div class="space-y-4 md:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/5 rounded-xl border border-white/10 shrink-0">
                        <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                             class="w-10 h-10 object-contain" 
                             alt="Logo Sekolah"
                             onerror="this.src='https://ui-avatars.com/api/?name=SIAS&background=4f46e5&color=fff'">
                    </div>
                    <div>
                        <span class="font-bold text-white text-sm block tracking-wider uppercase whitespace-normal leading-tight">
                            {{ $schoolProfile->nama_sekolah ?? 'SIAS' }}
                        </span>
                        <span class="text-xs text-indigo-400 font-medium block mt-0.5">Sistem Informasi Sekolah</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed pt-1">
                    Aplikasi sistem informasi akademik resmi {{ $schoolProfile->nama_sekolah ?? 'Sekolah Kita' }}. Mewujudkan ekosistem digital yang transparan, akuntabel, dan terintegrasi.
                </p>
            </div>

            @if(isset($footerLinks) && $footerLinks->isNotEmpty())
                @foreach($footerLinks as $namaGroup => $links)
                    <div>
                        <h3 class="text-xs font-bold text-slate-200 tracking-widest uppercase border-b border-slate-800 pb-2 mb-4">
                            {{ $namaGroup }}
                        </h3>
                        <ul class="space-y-2.5 text-xs">
                            @foreach($links as $link)
                                <li>
                                    <a href="{{ $link->url }}" class="text-slate-400 hover:text-indigo-400 transition-colors flex items-center gap-1.5 group">
                                        <span class="text-slate-600 group-hover:text-indigo-400 transition-colors">🔗</span>
                                        {{ $link->judul }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <div>
                    <h3 class="text-xs font-bold text-slate-200 tracking-widest uppercase border-b border-slate-800 pb-2 mb-4">
                        Tautan
                    </h3>
                    <div class="p-3 bg-slate-950/40 rounded-xl border border-slate-800/60 inline-flex items-center gap-2 text-xs italic text-slate-500">
                        <span>ℹ️</span> Belum ada tautan diatur.
                    </div>
                </div>
            @endif

            <div>
                <h3 class="text-xs font-bold text-slate-200 tracking-widest uppercase border-b border-slate-800 pb-2 mb-4">
                    Hubungi Kami
                </h3>
                <ul class="space-y-3 text-xs">
                    <li class="flex items-start gap-3">
                        <span class="text-base leading-none pt-0.5 select-none">📞</span>
                        <div class="flex-1">
                            <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Telepon</span>
                            <span class="text-slate-300 font-medium">
                                 {{ $schoolProfile->telepon ?? '-' }}
                            </span>
                        </div>
                    </li>
                    
                    <li class="flex items-start gap-3">
                        <span class="text-base leading-none pt-0.5 select-none">✉️</span>
                        <div class="flex-1">
                            <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Email Resmi</span>
                            <span class="text-indigo-400 font-medium break-all block">
                                {{ $contactSettings['email_sekolah'] ?? ($schoolProfile->email ?? 'Belum diatur') }}
                            </span>
                        </div>
                    </li>
                    
                    <li class="flex items-start gap-3">
                        <span class="text-base leading-none pt-0.5 select-none">📍</span>
                        <div class="flex-1">
                            <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider">Alamat Instansi</span>
                            <span class="text-slate-300 leading-relaxed block">
                                @if(!empty($schoolProfile->alamat))
                                    {{ $schoolProfile->alamat }}, Kel. {{ $schoolProfile->kelurahan }}, Kec. {{ $schoolProfile->kecamatan }}, {{ $schoolProfile->kota }}, {{ $schoolProfile->provinsi }}, {{ $schoolProfile->kode_pos }}
                                @else
                                    Belum diatur
                                @endif
                            </span>
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        <div class="mt-12 pt-6 border-t border-slate-800/80 flex flex-col sm:flex-row justify-between items-center gap-4 text-[11px] text-slate-500 font-medium">
            <p>
                &copy; {{ date('Y') }} {{ $schoolProfile->nama_sekolah ?? 'SIAS Aplikasi Sekolah' }}. All rights reserved.
            </p>
            <div class="flex items-center gap-1.5 text-slate-600">
                <span>Created by {{ $schoolProfile->nama_pembuat ?? 'SAMUEL MEYER SANGER, MTCRE.' }}</span>
                <span class="h-3 w-px bg-slate-800"></span>
                <span class="text-emerald-500 flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Production Server
                </span>
            </div>
        </div>

    </div>
</footer>