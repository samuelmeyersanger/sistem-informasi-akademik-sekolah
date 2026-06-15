<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                         class="w-12 h-12 object-contain" 
                         alt="Logo Sekolah">
                    <div>
                        <span class="font-bold text-gray-800 text-sm block tracking-wider uppercase">
                            {{ !empty($schoolProfile->nama_sekolah) ? Str::limit($schoolProfile->nama_sekolah, 15) : 'SIAS' }}
                        </span>
                        <span class="text-xs text-gray-500 block">Sistem Informasi Sekolah</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Aplikasi sistem informasi akademik resmi {{ $schoolProfile->nama_sekolah ?? 'Sekolah Kita' }}. Mewujudkan ekosistem digital yang transparan dan terintegrasi.
                </p>
            </div>

            @if(isset($footerLinks) && $footerLinks->isNotEmpty())
                @foreach($footerLinks as $namaGroup => $links)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase mb-4">
                            {{ $namaGroup }}
                        </h3>
                        <ul class="space-y-2">
                            @foreach($links as $link)
                                <li>
                                    <a href="{{ $link->url }}" class="text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                        {{ $link->judul }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase mb-4">Tautan</h3>
                    <p class="text-sm text-gray-500 italic">Belum ada tautan diatur.</p>
                </div>
            @endif

            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase mb-4">
                    Hubungi Kami
                </h3>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <span class="font-medium flex-shrink-0">Telp:</span>
                        <span>{{ $contactSettings['telepon_sekolah'] ?? ($schoolProfile->telepon ?? 'Belum diatur') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-medium flex-shrink-0">Email:</span>
                        <span class="break-all">{{ $contactSettings['email_sekolah'] ?? ($schoolProfile->email ?? 'Belum diatur') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="font-medium flex-shrink-0">Alamat:</span>
                        <span>
                            @if(!empty($schoolProfile->alamat))
                                {{ $schoolProfile->alamat }}, Kel. {{ $schoolProfile->kelurahan }}, Kec. {{ $schoolProfile->kecamatan }}, {{ $schoolProfile->kota }}, {{ $schoolProfile->provinsi }}, {{ $schoolProfile->kode_pos }}
                            @else
                                Belum diatur
                            @endif
                        </span>
                    </li>
                </ul>
            </div>

        </div>

        <div class="mt-12 pt-8 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ $schoolProfile->nama_sekolah ?? 'SIAS Aplikasi Sekolah' }}. All rights reserved.
            </p>
            <div class="text-xs text-gray-400">
                Created by SAMUEL MEYER SANGER, MTCRE.
            </div>
        </div>
    </div>
</footer>