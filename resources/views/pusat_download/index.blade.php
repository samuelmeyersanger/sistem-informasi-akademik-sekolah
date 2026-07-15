<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            🗂️ {{ __('Pusat Download Terpadu') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            <!-- Banner Penjelasan -->
            <div class="bg-gradient-to-r from-indigo-900 to-indigo-700 rounded-2xl shadow-lg p-8 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Portal Cetak Dokumen Sekolah</h3>
                    <p class="text-indigo-100 text-sm max-w-2xl leading-relaxed">
                        Selamat datang di Pusat Download. Semua dokumen laporan administratif, jadwal pelajaran, hingga daftar hadir (absensi) dapat Anda unduh secara <em>real-time</em> dalam format PDF maupun Excel.
                    </p>
                </div>
                <div class="hidden md:block text-6xl opacity-80">
                    🖨️
                </div>
            </div>

            <!-- ========================================== -->
            <!-- KATEGORI 1: LAPORAN GLOBAL & MASTER DATA -->
            <!-- ========================================== -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold">1</span>
                    <h3 class="text-xl font-bold text-gray-800">Laporan Global & Master Data</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card: Kode Guru -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-full bg-cyan-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">👩‍🏫</div>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg">Daftar Kode Guru</h4>
                        <p class="text-xs text-gray-500 mt-1 mb-5 h-10">Unduh daftar lengkap seluruh guru beserta kode mengajarnya.</p>
                        <form action="{{ route('pusat_download.kode_guru') }}" method="GET" target="_blank" class="flex gap-2">
                            @csrf
                            <button type="submit" name="format" value="excel" class="flex-1 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 transition-colors">📊 Excel</button>
                            <button type="submit" name="format" value="pdf" class="flex-1 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-200 transition-colors">📄 PDF</button>
                        </form>
                    </div>

                    <!-- Card: Rekap Jumlah Siswa -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📈</div>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg">Rekap Jumlah Siswa</h4>
                        <p class="text-xs text-gray-500 mt-1 mb-5 h-10">Data agregat total siswa per tingkat, jenis kelamin, dan kelas.</p>
                        <form action="{{ route('pusat_download.rekap_siswa') }}" method="GET" target="_blank" class="flex gap-2">
                            @csrf
                            <button type="submit" name="format" value="excel" class="flex-1 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 transition-colors">📊 Excel</button>
                            <button type="submit" name="format" value="pdf" class="flex-1 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-200 transition-colors">📄 PDF</button>
                        </form>
                    </div>

                    <!-- Card: Jadwal Global -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🗓️</div>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg">Jadwal Pelajaran Global</h4>
                        <p class="text-xs text-gray-500 mt-1 mb-5 h-10">Matriks jadwal pelajaran seluruh kelas dalam satu dokumen.</p>
                        <form action="{{ route('pusat_download.jadwal_global') }}" method="GET" target="_blank" class="flex gap-2">
                            @csrf
                            <button type="submit" name="format" value="excel" class="flex-1 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200 transition-colors">📊 Excel</button>
                            <button type="submit" name="format" value="pdf" class="flex-1 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-200 transition-colors">📄 PDF</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- KATEGORI 2: DATA AKADEMIK (PER-KELAS)    -->
            <!-- ========================================== -->
            <div class="border-t border-gray-200 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">2</span>
                    <h3 class="text-xl font-bold text-gray-800">Data Akademik (Berdasarkan Kelas)</h3>
                </div>
                
                <!-- UBAH: grid-cols diubah jadi 3 agar muat Form Daftar Nilai -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Card: Absensi Kelas -->
                    <div class="bg-white border-l-4 border-indigo-500 rounded-r-2xl rounded-l-md p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-2xl">📝</span>
                            <h4 class="font-bold text-gray-900 text-lg">Daftar Hadir (Absensi) Kelas</h4>
                        </div>
                        <p class="text-xs text-gray-500 mb-4 h-8">Lembar absensi kosong untuk digunakan oleh guru mata pelajaran.</p>
                        <form action="{{ route('pusat_download.absensi') }}" method="GET" target="_blank" class="space-y-3">
                            @csrf
                            <select name="kelas_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50">
                                <option value="">-- Pilih Ruang Kelas --</option>
                                @foreach($daftarKelas as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <div class="flex gap-2">
                                <button type="submit" name="format" value="excel" class="flex-1 px-3 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-colors">Unduh Excel</button>
                                <button type="submit" name="format" value="pdf" class="flex-1 px-3 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-colors">Unduh PDF</button>
                            </div>
                        </form>
                    </div>
                    <!-- Card: Jadwal Perkelas -->
                    <div class="bg-white border-l-4 border-indigo-500 rounded-r-2xl rounded-l-md p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-2xl">📅</span>
                            <h4 class="font-bold text-gray-900 text-lg">Jadwal Pelajaran Kelas</h4>
                        </div>
                        <p class="text-xs text-gray-500 mb-4 h-8">Cetak jadwal pelajaran harian khusus untuk ditempel di kelas.</p>
                        <form action="{{ route('pusat_download.jadwal') }}" method="GET" target="_blank" class="space-y-3">
                            @csrf
                            <select name="kelas_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50">
                                <option value="">-- Pilih Ruang Kelas --</option>
                                @foreach($daftarKelas as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <div class="flex gap-2">
                                <button type="submit" name="format" value="excel" class="flex-1 px-3 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-colors">Unduh Excel</button>
                                <button type="submit" name="format" value="pdf" class="flex-1 px-3 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-colors">Unduh PDF</button>
                            </div>
                        </form>
                    </div>
                    <!-- Card BARU: Daftar Nilai Kelas -->
                    <div class="bg-white border-l-4 border-amber-500 rounded-r-2xl rounded-l-md p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-2xl">💯</span>
                            <h4 class="font-bold text-gray-900 text-lg">Daftar Nilai Kelas</h4>
                        </div>
                        <p class="text-xs text-gray-500 mb-4 h-8">Cetak format lembar daftar nilai kosong untuk kelas.</p>
                        <form action="{{ route('pusat_download.daftar-nilai') }}" method="GET" target="_blank" class="space-y-3">
                            <select name="kelas_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-gray-50">
                                <option value="">-- Pilih Ruang Kelas --</option>
                                @foreach($daftarKelas as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <!-- Tombol satu kolom penuh karena hanya ada 1 format cetakan (PDF/HTML) -->
                            <button type="submit" class="w-full px-3 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-colors">
                                🖨️ Unduh PDF / Cetak
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- KATEGORI 3: BIMBINGAN & EKSKUL           -->
            <!-- ========================================== -->
            <div class="border-t border-gray-200 pt-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center font-bold">3</span>
                    <h3 class="text-xl font-bold text-gray-800">Bimbingan Wali & Ekstrakurikuler</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Card: Daftar Hadir Kelas Wali -->
                    <div class="bg-white border-l-4 border-teal-500 rounded-r-2xl rounded-l-md p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-2xl">👨‍🏫</span>
                            <h4 class="font-bold text-gray-900 text-lg">Absensi Kelompok Wali</h4>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Unduh lembar absensi khusus siswa kelompok bimbingan Anda.</p>
                        <form action="{{ route('pusat_download.data_kelas_wali') }}" method="GET" target="_blank" class="space-y-3">
                            @csrf
                            <select name="kelas_wali_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm bg-gray-50">
                                <option value="">-- Pilih Kelompok Wali --</option>
                                @foreach($daftarKelasWali as $kw)
                                    <option value="{{ $kw->id }}">{{ $kw->tingkat }} - {{ $kw->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full px-3 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-colors flex justify-center items-center gap-2">
                                📄 Buka & Unduh PDF
                            </button>
                        </form>
                    </div>

                    <!-- Card: Absensi Ekskul -->
                    <div class="bg-white border-l-4 border-teal-500 rounded-r-2xl rounded-l-md p-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-2xl">🎯</span>
                            <h4 class="font-bold text-gray-900 text-lg">Daftar Hadir Ekstrakurikuler</h4>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Lembar absensi harian untuk kegiatan ekstrakurikuler siswa.</p>
                        <form action="{{ route('pusat_download.cetak_absensi_ekskul') }}" method="GET" target="_blank" class="space-y-3">
                            <select name="ekskul_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm bg-gray-50">
                                <option value="">-- Pilih Ekstrakurikuler --</option>
                                @foreach($daftarEkskul as $ekskul)
                                    <option value="{{ $ekskul->id }}">{{ $ekskul->nama_ekskul }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full px-3 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-colors flex justify-center items-center gap-2">
                                📄 Buka & Unduh PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>