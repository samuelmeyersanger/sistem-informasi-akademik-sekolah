<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🗂️</span> {{ __('Pusat Cetak & Unduhan') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Sistem pencetakan dokumen administratif, daftar hadir, dan pelaporan terpadu.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12 relative z-10">

            {{-- BANNER UTAMA --}}
            <div class="relative overflow-hidden bg-slate-900 rounded-[2rem] shadow-2xl p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-8 border border-slate-800 group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/50 via-slate-900 to-slate-900"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>
                
                <div class="relative z-10 text-center md:text-left flex-1 max-w-2xl">
                    <span class="inline-flex items-center gap-2 bg-indigo-500/20 border border-indigo-400/30 px-3 py-1.5 rounded-full text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-4">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Generator Berkas
                    </span>
                    <h3 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-3">Portal Dokumen Resmi</h3>
                    <p class="text-sm text-slate-400 leading-relaxed font-medium">
                        Selamat datang di Pusat Unduhan. Seluruh berkas laporan administratif, matriks jadwal, hingga formulir absensi kosong dapat Anda ekstrak secara <em class="text-indigo-300">real-time</em> dalam format PDF (cetak langsung) maupun MS. Excel (olahan data).
                    </p>
                </div>
                <div class="relative z-10 hidden md:flex items-center justify-center w-32 h-32 bg-white/5 backdrop-blur-md rounded-[2rem] border border-white/10 shadow-2xl rotate-12 hover:rotate-0 transition-transform duration-500">
                    <span class="text-6xl">🖨️</span>
                </div>
            </div>

            {{-- KATEGORI 1: LAPORAN GLOBAL --}}
            <div class="space-y-6">
                <div class="flex items-center gap-4 border-b border-slate-200/60 pb-3">
                    <div class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl font-black text-sm uppercase tracking-widest flex items-center gap-2 shadow-sm border border-indigo-200">
                        <span class="text-lg">🌐</span> Laporan Global & Master Data
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Card: Kode Guru --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-200/40 transition-all duration-300 group relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-sky-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                        <div>
                            <div class="w-14 h-14 rounded-[1rem] bg-gradient-to-br from-sky-100 to-sky-50 flex items-center justify-center text-3xl mb-5 shadow-sm border border-sky-100 group-hover:scale-110 transition-transform">👩‍🏫</div>
                            <h4 class="font-black text-slate-900 text-lg tracking-tight mb-2">Daftar Kode Guru</h4>
                            <p class="text-xs font-medium text-slate-500 leading-relaxed h-12">Unduh daftar lengkap seluruh guru pendidik beserta kode mengajarnya untuk validasi akademik.</p>
                        </div>
                        <form action="{{ route('pusat_download.kode_guru') }}" method="GET" target="_blank" class="flex gap-3 mt-6 pt-6 border-t border-slate-100">
                            @csrf
                            <button type="submit" name="format" value="excel" class="flex-1 px-4 py-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-black uppercase tracking-wider rounded-xl border border-emerald-200 transition-colors shadow-sm text-center">📊 Excel</button>
                            <button type="submit" name="format" value="pdf" class="flex-1 px-4 py-3 bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-black uppercase tracking-wider rounded-xl border border-rose-200 transition-colors shadow-sm text-center">📄 PDF</button>
                        </form>
                    </div>

                    {{-- Card: Rekap Jumlah Siswa --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-200/40 transition-all duration-300 group relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-purple-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                        <div>
                            <div class="w-14 h-14 rounded-[1rem] bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center text-3xl mb-5 shadow-sm border border-purple-100 group-hover:scale-110 transition-transform">📈</div>
                            <h4 class="font-black text-slate-900 text-lg tracking-tight mb-2">Rekap Statistik Siswa</h4>
                            <p class="text-xs font-medium text-slate-500 leading-relaxed h-12">Data agregat total siswa secara keseluruhan per tingkat, jenis kelamin, dan distribusi kelas.</p>
                        </div>
                        <form action="{{ route('pusat_download.rekap_siswa') }}" method="GET" target="_blank" class="flex gap-3 mt-6 pt-6 border-t border-slate-100">
                            @csrf
                            <button type="submit" name="format" value="excel" class="flex-1 px-4 py-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-black uppercase tracking-wider rounded-xl border border-emerald-200 transition-colors shadow-sm text-center">📊 Excel</button>
                            <button type="submit" name="format" value="pdf" class="flex-1 px-4 py-3 bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-black uppercase tracking-wider rounded-xl border border-rose-200 transition-colors shadow-sm text-center">📄 PDF</button>
                        </form>
                    </div>

                    {{-- Card: Jadwal Global --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-200/40 transition-all duration-300 group relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-amber-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                        <div>
                            <div class="w-14 h-14 rounded-[1rem] bg-gradient-to-br from-amber-100 to-amber-50 flex items-center justify-center text-3xl mb-5 shadow-sm border border-amber-100 group-hover:scale-110 transition-transform">🗓️</div>
                            <h4 class="font-black text-slate-900 text-lg tracking-tight mb-2">Matriks Jadwal Global</h4>
                            <p class="text-xs font-medium text-slate-500 leading-relaxed h-12">Peta matriks jam pelajaran untuk seluruh rombel/kelas dalam satu dokumen terpadu.</p>
                        </div>
                        <form action="{{ route('pusat_download.jadwal_global') }}" method="GET" target="_blank" class="flex gap-3 mt-6 pt-6 border-t border-slate-100">
                            @csrf
                            <button type="submit" name="format" value="excel" class="flex-1 px-4 py-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-black uppercase tracking-wider rounded-xl border border-emerald-200 transition-colors shadow-sm text-center">📊 Excel</button>
                            <button type="submit" name="format" value="pdf" class="flex-1 px-4 py-3 bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-black uppercase tracking-wider rounded-xl border border-rose-200 transition-colors shadow-sm text-center">📄 PDF</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KATEGORI 2: DATA AKADEMIK KELAS --}}
            <div class="space-y-6 pt-6">
                <div class="flex items-center gap-4 border-b border-slate-200/60 pb-3">
                    <div class="px-4 py-2 bg-emerald-100 text-emerald-800 rounded-xl font-black text-sm uppercase tracking-widest flex items-center gap-2 shadow-sm border border-emerald-200">
                        <span class="text-lg">🏫</span> Berkas Akademik Kelas (Rombel)
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    {{-- Card: Absensi Kelas --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-200/30 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-emerald-400"></div>
                        <div class="pl-2">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-3xl">📝</span>
                                <h4 class="font-black text-slate-900 text-base leading-tight">Daftar Hadir (Absensi) Kelas</h4>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-6 h-8">Cetak lembar rekapitulasi absensi harian kosong untuk guru pengampu.</p>
                            
                            <form action="{{ route('pusat_download.absensi') }}" method="GET" target="_blank" class="space-y-4">
                                @csrf
                                <select name="kelas_id" required class="w-full text-sm font-bold text-slate-700 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 shadow-inner">
                                    <option value="">-- Pilih Ruang Kelas --</option>
                                    @foreach($daftarKelas as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                                <div class="flex gap-2">
                                    <button type="submit" name="format" value="excel" class="flex-1 px-3 py-3 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-colors shadow-md">📊 Excel</button>
                                    <button type="submit" name="format" value="pdf" class="flex-1 px-3 py-3 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-colors shadow-md">📄 PDF</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Card: Jadwal Perkelas --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-200/30 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-emerald-400"></div>
                        <div class="pl-2">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-3xl">📅</span>
                                <h4 class="font-black text-slate-900 text-base leading-tight">Jadwal Pelajaran Kelas</h4>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-6 h-8">Ekstrak jadwal mata pelajaran mingguan khusus untuk ditempel di ruang kelas.</p>
                            
                            <form action="{{ route('pusat_download.jadwal') }}" method="GET" target="_blank" class="space-y-4">
                                @csrf
                                <select name="kelas_id" required class="w-full text-sm font-bold text-slate-700 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 shadow-inner">
                                    <option value="">-- Pilih Ruang Kelas --</option>
                                    @foreach($daftarKelas as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                                <div class="flex gap-2">
                                    <button type="submit" name="format" value="excel" class="flex-1 px-3 py-3 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-colors shadow-md">📊 Excel</button>
                                    <button type="submit" name="format" value="pdf" class="flex-1 px-3 py-3 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-colors shadow-md">📄 PDF</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Card: Daftar Nilai Kelas --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-200/30 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-emerald-500"></div>
                        <div class="pl-2">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-3xl">💯</span>
                                <h4 class="font-black text-slate-900 text-base leading-tight">Format Daftar Nilai Kelas</h4>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-6 h-8">Cetak blangko format lembar penilaian sumatif/formatif harian untuk guru.</p>
                            
                            <form action="{{ route('pusat_download.daftar-nilai') }}" method="GET" target="_blank" class="space-y-4">
                                @csrf
                                <select name="kelas_id" required class="w-full text-sm font-bold text-slate-700 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 shadow-inner">
                                    <option value="">-- Pilih Ruang Kelas --</option>
                                    @foreach($daftarKelas as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-full px-3 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 flex justify-center items-center gap-2">
                                    <span>🖨️</span> Buka Lembar Cetak
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            {{-- KATEGORI 3: EKSKUL & WALI --}}
            <div class="space-y-6 pt-6">
                <div class="flex items-center gap-4 border-b border-slate-200/60 pb-3">
                    <div class="px-4 py-2 bg-teal-100 text-teal-800 rounded-xl font-black text-sm uppercase tracking-widest flex items-center gap-2 shadow-sm border border-teal-200">
                        <span class="text-lg">🎯</span> Bimbingan & Ekstrakurikuler
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- Card: Absensi Kelompok Wali --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between hover:-translate-y-1 hover:shadow-2xl hover:shadow-teal-200/30 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-teal-400"></div>
                        <div class="pl-2">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-3xl">👨‍🏫</span>
                                <h4 class="font-black text-slate-900 text-lg leading-tight">Absensi Kelompok Perwalian</h4>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-6">Unduh format presensi khusus daftar siswa per kelompok bimbingan asuhan wali kelas.</p>
                            
                            <form action="{{ route('pusat_download.data_kelas_wali') }}" method="GET" target="_blank" class="space-y-4">
                                @csrf
                                <select name="kelas_wali_id" required class="w-full text-sm font-bold text-slate-700 rounded-xl border-slate-200 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 bg-slate-50 py-3 shadow-inner">
                                    <option value="">-- Pilih Kelompok Bimbingan Wali --</option>
                                    @foreach($daftarKelasWali as $kw)
                                        <option value="{{ $kw->id }}">{{ $kw->tingkat }} - {{ $kw->nama_kelas }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-full px-4 py-3.5 bg-gradient-to-r from-teal-500 to-teal-400 hover:from-teal-600 hover:to-teal-500 text-white text-[11px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-teal-500/30 hover:-translate-y-0.5 flex justify-center items-center gap-2">
                                    <span>📄</span> Ekstrak PDF Dokumen
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Card: Absensi Ekskul --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between hover:-translate-y-1 hover:shadow-2xl hover:shadow-teal-200/30 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-teal-400"></div>
                        <div class="pl-2">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-3xl">🏅</span>
                                <h4 class="font-black text-slate-900 text-lg leading-tight">Daftar Hadir Ekstrakurikuler</h4>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-6">Berkas formulir pencatatan absensi harian dan mingguan khusus kegiatan pengembangan minat bakat.</p>
                            
                            <form action="{{ route('pusat_download.cetak_absensi_ekskul') }}" method="GET" target="_blank" class="space-y-4">
                                @csrf
                                <select name="ekskul_id" required class="w-full text-sm font-bold text-slate-700 rounded-xl border-slate-200 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 bg-slate-50 py-3 shadow-inner">
                                    <option value="">-- Pilih Program Ekstrakurikuler --</option>
                                    @foreach($daftarEkskul as $ekskul)
                                        <option value="{{ $ekskul->id }}">{{ $ekskul->nama_ekskul }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-full px-4 py-3.5 bg-gradient-to-r from-teal-500 to-teal-400 hover:from-teal-600 hover:to-teal-500 text-white text-[11px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-teal-500/30 hover:-translate-y-0.5 flex justify-center items-center gap-2">
                                    <span>📄</span> Ekstrak PDF Dokumen
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            
            {{-- KATEGORI 4: LAYANAN MASYARAKAT (SKM) --}}
            <div class="space-y-6 pt-6">
                <div class="flex items-center gap-4 border-b border-slate-200/60 pb-3">
                    <div class="px-4 py-2 bg-blue-100 text-blue-800 rounded-xl font-black text-sm uppercase tracking-widest flex items-center gap-2 shadow-sm border border-blue-200">
                        <span class="text-lg">🤝</span> Layanan Publik & Masyarakat
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    {{-- Card: Rekap Hasil Survei SKM --}}
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between hover:-translate-y-1 hover:shadow-2xl hover:shadow-blue-200/30 transition-all duration-300 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-blue-500"></div>
                        <div class="absolute right-0 top-0 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                        <div class="pl-2 relative z-10 flex flex-col h-full">
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-3xl drop-shadow-md">🌟</span>
                                <h4 class="font-black text-slate-900 text-base leading-tight">Rekap Hasil Survei (SKM)</h4>
                            </div>
                            <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-6 flex-1">Unduh data mentah seluruh kuesioner masyarakat untuk penghitungan Indeks Kepuasan Masyarakat.</p>
                            
                            <form action="{{ route('hasil_skm') }}" method="GET" target="_blank" class="mt-auto">
                                <button type="submit" class="w-full px-4 py-3.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 flex justify-center items-center gap-2">
                                    <span>📊</span> Buka Tabel Olah Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>