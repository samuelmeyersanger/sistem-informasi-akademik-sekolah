<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🎨</span> {{ __('Pengaturan Logo') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <style>
        /* Pola checkerboard halus untuk preview gambar transparan */
        .bg-transparency-grid {
            background-color: #f8fafc;
            background-image: linear-gradient(45deg, #e2e8f0 25%, transparent 25%), 
                              linear-gradient(-45deg, #e2e8f0 25%, transparent 25%), 
                              linear-gradient(45deg, transparent 75%, #e2e8f0 75%), 
                              linear-gradient(-45deg, transparent 75%, #e2e8f0 75%);
            background-size: 16px 16px;
            background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
        }
    </style>

    <div class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6 relative z-10">
            
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <div>
                        <div class="mb-2 text-base font-black">Gagal memvalidasi aset gambar!</div>
                        <ul class="list-disc list-inside text-xs font-medium text-rose-700 space-y-1 bg-rose-100/50 p-3 rounded-xl border border-rose-200/50">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 bg-white/50 backdrop-blur-sm relative z-10">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                        <span class="text-indigo-500">📸</span> Form Unggah Atribut Resmi
                    </h3>
                    <p class="text-sm font-medium text-slate-500 mt-2 leading-relaxed max-w-3xl">
                        Berkas gambar transparan (PNG) di bawah ini digunakan otomatis oleh sistem untuk membuat <strong class="text-slate-700">kop surat administrasi, cetak lembar Rapor Siswa, piagam, serta kartu pelajar</strong>. 
                        Pastikan ukuran file di bawah 2MB.
                    </p>
                </div>

                <form action="{{ route('master.pengaturan-logo.save') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-8 relative z-10">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                        
                        {{-- KARTU: LOGO PEMDA --}}
                        <div class="group border border-slate-200 rounded-[1.5rem] p-5 md:p-6 bg-white hover:bg-slate-50 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <label class="flex items-center gap-2 text-xs font-black text-slate-800 uppercase tracking-widest mb-1.5">
                                    <span class="text-lg">🏛️</span> Logo Pemerintah Daerah (PEMDA)
                                </label>
                                <p class="text-[11px] font-medium text-slate-500 mb-4 leading-relaxed">
                                    Tampil di pojok kiri atas pada Kop Surat kedinasan. Wajib berformat PNG transparan.
                                </p>
                                <input type="file" name="logo_pemda" accept="image/png, image/jpeg" 
                                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 hover:file:text-indigo-800 cursor-pointer transition-colors shadow-sm focus:outline-none">
                            </div>
                            <div class="mt-5 flex items-center justify-center p-4 rounded-xl border border-dashed border-slate-300 bg-transparency-grid min-h-[140px] relative overflow-hidden group-hover:border-indigo-300 transition-colors">
                                @if($logoSetting && $logoSetting->logo_pemda)
                                    <img src="{{ asset('storage/' . $logoSetting->logo_pemda) }}" class="max-h-24 object-contain drop-shadow-md hover:scale-105 transition-transform duration-300" alt="Logo Pemda">
                                @else
                                    <span class="text-xs font-bold text-slate-400 bg-white/80 px-3 py-1 rounded-lg backdrop-blur-sm">Belum terunggah</span>
                                @endif
                            </div>
                        </div>

                        {{-- KARTU: LOGO SEKOLAH --}}
                        <div class="group border border-slate-200 rounded-[1.5rem] p-5 md:p-6 bg-white hover:bg-slate-50 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <label class="flex items-center gap-2 text-xs font-black text-slate-800 uppercase tracking-widest mb-1.5">
                                    <span class="text-lg">🏫</span> Logo Resmi Institusi
                                </label>
                                <p class="text-[11px] font-medium text-slate-500 mb-4 leading-relaxed">
                                    Dipakai untuk Favicon, sisi kanan Kop Surat, dan tengah halaman Rapor. Gunakan PNG transparan beresolusi tinggi.
                                </p>
                                <input type="file" name="logo_sekolah" accept="image/png, image/jpeg" 
                                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 hover:file:text-indigo-800 cursor-pointer transition-colors shadow-sm focus:outline-none">
                            </div>
                            <div class="mt-5 flex items-center justify-center p-4 rounded-xl border border-dashed border-slate-300 bg-transparency-grid min-h-[140px] relative overflow-hidden group-hover:border-indigo-300 transition-colors">
                                @if($logoSetting && $logoSetting->logo_sekolah)
                                    <img src="{{ asset('storage/' . $logoSetting->logo_sekolah) }}" class="max-h-24 object-contain drop-shadow-md hover:scale-105 transition-transform duration-300" alt="Logo Sekolah">
                                @else
                                    <span class="text-xs font-bold text-slate-400 bg-white/80 px-3 py-1 rounded-lg backdrop-blur-sm">Belum terunggah</span>
                                @endif
                            </div>
                        </div>

                        {{-- KARTU: BANNER KOP SURAT (FULL WIDTH) --}}
                        <div class="md:col-span-2 group border border-slate-200 rounded-[1.5rem] p-5 md:p-6 bg-white hover:bg-slate-50 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300">
                            <label class="flex items-center gap-2 text-xs font-black text-slate-800 uppercase tracking-widest mb-1.5">
                                <span class="text-lg">🖼️</span> Gambar Banner Kop Surat Kustom (Opsional)
                            </label>
                            <p class="text-xs font-medium text-slate-500 mb-4 leading-relaxed max-w-4xl">
                                Jika Anda telah mendesain sendiri banner/header Kop Surat dalam format utuh (landscape/memanjang), unggah ke sini. 
                                Sistem cetak dokumen (PDF) akan menimpa format standar dengan *banner* statis ini.
                            </p>
                            <input type="file" name="kop_surat" accept="image/png, image/jpeg" 
                                   class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer transition-colors shadow-sm focus:outline-none w-full sm:w-auto">
                            
                            <div class="mt-5 flex items-center justify-center p-4 rounded-xl border border-dashed border-slate-300 bg-transparency-grid min-h-[140px] group-hover:border-indigo-300 transition-colors w-full">
                                @if($logoSetting && $logoSetting->kop_surat)
                                    <img src="{{ asset('storage/' . $logoSetting->kop_surat) }}" class="w-full max-h-32 object-contain drop-shadow-sm hover:scale-[1.02] transition-transform duration-300" alt="Banner Kop Surat">
                                @else
                                    <span class="text-xs font-bold text-slate-400 bg-white/80 px-3 py-1 rounded-lg backdrop-blur-sm">Belum ada banner terunggah (Memakai format Kop Surat Standar)</span>
                                @endif
                            </div>
                        </div>

                        {{-- KARTU: TTD KEPSEK --}}
                        <div class="group border border-slate-200 rounded-[1.5rem] p-5 md:p-6 bg-white hover:bg-slate-50 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <label class="flex items-center gap-2 text-xs font-black text-slate-800 uppercase tracking-widest mb-1.5">
                                    <span class="text-lg">✍️</span> Tanda Tangan Kepsek (Digital)
                                </label>
                                <p class="text-[11px] font-medium text-slate-500 mb-4 leading-relaxed">
                                    Potong rapi (crop) tanpa menyertakan nama dan gelar. Wajib background transparan murni.
                                </p>
                                <input type="file" name="ttd_kepala_sekolah" accept="image/png" 
                                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 hover:file:text-emerald-800 cursor-pointer transition-colors shadow-sm focus:outline-none">
                            </div>
                            <div class="mt-5 flex items-center justify-center p-4 rounded-xl border border-dashed border-slate-300 bg-transparency-grid min-h-[140px] relative overflow-hidden group-hover:border-indigo-300 transition-colors">
                                @if($logoSetting && $logoSetting->ttd_kepala_sekolah)
                                    <img src="{{ asset('storage/' . $logoSetting->ttd_kepala_sekolah) }}" class="max-h-20 object-contain drop-shadow-md hover:scale-105 transition-transform duration-300" alt="TTD Kepsek">
                                @else
                                    <span class="text-xs font-bold text-slate-400 bg-white/80 px-3 py-1 rounded-lg backdrop-blur-sm">Belum terunggah</span>
                                @endif
                            </div>
                        </div>

                        {{-- KARTU: STEMPEL SEKOLAH --}}
                        <div class="group border border-slate-200 rounded-[1.5rem] p-5 md:p-6 bg-white hover:bg-slate-50 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300 flex flex-col justify-between">
                            <div>
                                <label class="flex items-center gap-2 text-xs font-black text-slate-800 uppercase tracking-widest mb-1.5">
                                    <span class="text-lg">🛑</span> Stempel / Cap Resmi Sekolah
                                </label>
                                <p class="text-[11px] font-medium text-slate-500 mb-4 leading-relaxed">
                                    Potong tepat di pinggir lingkaran stempel. Disarankan warna biru/ungu instansi (Transparan PNG).
                                </p>
                                <input type="file" name="stempel_sekolah" accept="image/png" 
                                       class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 hover:file:text-amber-800 cursor-pointer transition-colors shadow-sm focus:outline-none">
                            </div>
                            <div class="mt-5 flex items-center justify-center p-4 rounded-xl border border-dashed border-slate-300 bg-transparency-grid min-h-[140px] relative overflow-hidden group-hover:border-indigo-300 transition-colors">
                                @if($logoSetting && $logoSetting->stempel_sekolah)
                                    <img src="{{ asset('storage/' . $logoSetting->stempel_sekolah) }}" class="max-h-24 object-contain drop-shadow-md hover:scale-105 transition-transform duration-300" alt="Stempel Sekolah">
                                @else
                                    <span class="text-xs font-bold text-slate-400 bg-white/80 px-3 py-1 rounded-lg backdrop-blur-sm">Belum terunggah</span>
                                @endif
                            </div>
                        </div>

                        {{-- KARTU: KOMBINASI (TTD + STEMPEL) --}}
                        <div class="md:col-span-2 group border border-slate-200 rounded-[1.5rem] p-5 md:p-6 bg-white hover:bg-slate-50 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-500/5 transition-all duration-300">
                            <label class="flex items-center gap-2 text-xs font-black text-slate-800 uppercase tracking-widest mb-1.5">
                                <span class="text-lg">🔗</span> Mode Pintas: TTD & Stempel Menyatu (Menimpa Gambar)
                            </label>
                            <p class="text-xs font-medium text-slate-500 mb-4 leading-relaxed max-w-4xl">
                                Opsi tingkat lanjut: Jika peletakan posisi terpisah (CSS) sulit dikonfigurasi, Anda bisa mengunggah 1 file gambar yang <strong class="text-slate-700">sudah berisi Tanda Tangan ditimpa (overlap) Stempel</strong>. 
                                Sistem akan memprioritaskan aset ini untuk Rapor dan Surat Masal.
                            </p>
                            <input type="file" name="ttd_dan_stempel" accept="image/png" 
                                   class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer transition-colors shadow-sm focus:outline-none w-full sm:w-auto">
                            
                            <div class="mt-5 flex items-center justify-center p-4 rounded-xl border border-dashed border-slate-300 bg-transparency-grid min-h-[140px] group-hover:border-indigo-300 transition-colors w-full">
                                @if($logoSetting && $logoSetting->ttd_dan_stempel)
                                    <img src="{{ asset('storage/' . $logoSetting->ttd_dan_stempel) }}" class="max-h-28 object-contain drop-shadow-md hover:scale-105 transition-transform duration-300" alt="Kombinasi TTD dan Stempel">
                                @else
                                    <span class="text-xs font-bold text-slate-400 bg-white/80 px-3 py-1 rounded-lg backdrop-blur-sm">Tidak ada gambar kombinasi. (Menggunakan pemisahan default)</span>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="pt-8 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="text-xs font-bold text-slate-500 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100 w-full md:w-auto text-center md:text-left">
                            💡 Pastikan cache browser di-clear (Ctrl + F5) jika logo baru tidak langsung muncul setelah disimpan.
                        </div>
                        <button type="submit" class="w-full md:w-auto px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2">
                            <span>💾</span> Simpan Seluruh Aset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>