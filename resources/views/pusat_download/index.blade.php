<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Download') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">Download Dokumen Sekolah</h3>
                    <p class="text-xs text-gray-500">Pilih jenis dokumen yang ingin Anda unduh dalam format Excel atau PDF.</p>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- ============================================== -->
                    <!-- KOTAK 1: DOWNLOAD ABSENSI                      -->
                    <!-- ============================================== -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-indigo-50 border-b border-gray-200 p-4">
                            <h4 class="font-bold text-indigo-900 text-sm flex items-center gap-2">
                                📝 Daftar Hadir (Absensi)
                            </h4>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('pusat_download.absensi') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Kelas *</label>
                                    <select name="kelas_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($daftarKelas as $kelas)
                                            <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 pt-2">
                                    <button type="submit" name="format" value="excel" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2">
                                        📊 Excel
                                    </button>
                                    <button type="submit" name="format" value="pdf" class="flex-1 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2">
                                        📄 PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- KOTAK 2: DOWNLOAD JADWAL PELAJARAN             -->
                    <!-- ============================================== -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-blue-50 border-b border-gray-200 p-4">
                            <h4 class="font-bold text-blue-900 text-sm flex items-center gap-2">
                                📅 Jadwal Pelajaran
                            </h4>
                        </div>
                        <div class="p-4">
                            <!-- ACTION form diubah menuju ke route jadwal -->
                            <form action="{{ route('pusat_download.jadwal') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Kelas *</label>
                                    <select name="kelas_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($daftarKelas as $kelas)
                                            <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 pt-2">
                                    <button type="submit" name="format" value="excel" class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2">
                                        📊 Excel
                                    </button>
                                    <button type="submit" name="format" value="pdf" class="flex-1 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2">
                                        📄 PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- KOTAK 3: DOWNLOAD ABSENSI EKSKUL               -->
                    <!-- ============================================== -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-amber-50 border-b border-gray-200 p-4">
                            <h4 class="font-bold text-amber-900 text-sm flex items-center gap-2">
                                🎯 Daftar Hadir (Ekskul)
                            </h4>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('pusat_download.cetak_absensi_ekskul') }}" method="GET" target="_blank" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Ekstrakurikuler *</label>
                                    <select name="ekskul_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm">
                                        <option value="">-- Pilih Ekskul --</option>
                                        @foreach($daftarEkskul as $ekskul)
                                            <option value="{{ $ekskul->id }}">{{ $ekskul->nama_ekskul }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 pt-2">
                                    <button type="submit" class="w-full px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2">
                                        📄 Download PDF (Folio)
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- KOTAK 4: KELOMPOK WALI (BARU)                  -->
                    <!-- ============================================== -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-teal-50 border-b border-gray-200 p-4">
                            <h4 class="font-bold text-teal-900 text-sm flex items-center gap-2">
                                👨‍🏫 Data Kelompok Wali
                            </h4>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('pusat_download.data_kelas_wali') }}" method="POST" target="_blank" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Kelompok Wali *</label>
                                    <select name="kelas_wali_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm">
                                        <option value="">-- Pilih Kelompok --</option>
                                        @foreach($daftarKelasWali as $kw)
                                            <option value="{{ $kw->id }}">Kelompok {{ $kw->nama_kelas }} (Grade {{ $kw->tingkat }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 pt-2">
                                    <button type="submit" class="w-full px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex justify-center items-center gap-2">
                                        📄 Download PDF (Folio)
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>