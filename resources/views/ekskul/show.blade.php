<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('ekskul.ekstrakurikuler.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-colors" title="Kembali ke Daftar Ekskul">
                    <span class="text-xl font-bold">←</span>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                        <span class="text-3xl">🏅</span> {{ $ekskul->nama }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500 mt-1">Manajemen detail anggota dan rekam jejak prestasi ekstrakurikuler.</p>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- Bungkus Utama Menggunakan Alpine.js --}}
    <div class="py-10 bg-slate-50 min-h-screen" 
         x-data="{ 
            currentTab: 'anggota', 
            modalAnggota: false, 
            modalPrestasi: false,
            
            // Delete Modal State
            openDelete: false,
            deleteActionUrl: '',
            deleteTargetName: '',
            
            initDelete(url, name) {
                this.deleteActionUrl = url;
                this.deleteTargetName = name;
                this.openDelete = true;
            }
         }">
         
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Alerts / Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">⚠️</span> {{ session('error') }}
                </div>
            @endif

            {{-- 🗂️ Navigasi Tab Premium (Segmented Control) --}}
            <div class="bg-gray-200/60 p-1.5 rounded-2xl shadow-inner flex overflow-x-auto w-full max-w-fit lg:mx-0">
                <button @click="currentTab = 'anggota'"
                    :class="currentTab === 'anggota' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 sm:flex-none px-8 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">👥</span> Daftar Anggota
                </button>
                <button @click="currentTab = 'prestasi'"
                    :class="currentTab === 'prestasi' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 sm:flex-none px-8 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">🏆</span> Rekam Prestasi
                </button>
            </div>

            {{-- ================= TAB 1: DAFTAR ANGGOTA ================= --}}
            <div x-show="currentTab === 'anggota'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 flex-wrap gap-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Siswa Terdaftar</h3>
                        <p class="text-sm text-gray-500">Daftar siswa aktif yang mengikuti ekstrakurikuler ini.</p>
                    </div>
                    <button @click="modalAnggota = true" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <span class="text-lg">👤</span> Daftarkan Anggota
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8">Nama Siswa</th>
                                <th class="p-5 w-40">Kelas</th>
                                <th class="p-5 w-48">No. HP</th>
                                <th class="p-5 w-48">Tgl Bergabung</th>
                                <th class="p-5 pr-8 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($ekskul->anggota as $agt)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="p-5 pl-8 align-middle">
                                        <div class="font-black text-gray-900 text-base">{{ $agt->siswa->nama_lengkap ?? 'N/A' }}</div>
                                    </td>
                                    <td class="p-5 align-middle">
                                        <span class="inline-flex items-center gap-1.5 text-xs text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg font-bold border border-indigo-100">
                                            🏫 {{ $agt->kelas->nama_kelas ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="p-5 align-middle font-medium text-gray-600">
                                        📞 {{ $agt->nomor_hp ?? '-' }}
                                    </td>
                                    <td class="p-5 align-middle">
                                        <div class="text-sm font-semibold text-gray-700">
                                            {{ $agt->tanggal_bergabung->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <button type="button" @click="initDelete('{{ route('ekskul.ekstrakurikuler.anggota.destroy', [$ekskul->id, $agt->id]) }}', 'Anggota: {{ addslashes($agt->siswa->nama_lengkap ?? '') }}')" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-xl text-xs transition-colors border border-rose-100 shadow-sm inline-flex items-center gap-1.5 cursor-pointer">
                                            🗑️ Keluarkan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">📭</span>
                                        <p class="text-lg font-bold text-gray-500">Belum ada anggota yang terdaftar di ekskul ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================= TAB 2: REKAM PRESTASI ================= --}}
            <div x-show="currentTab === 'prestasi'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50 flex-wrap gap-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Daftar Penghargaan & Juara</h3>
                        <p class="text-sm text-gray-500">Arsip prestasi yang pernah diraih oleh ekstrakurikuler ini.</p>
                    </div>
                    <button @click="modalPrestasi = true" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-600 hover:to-amber-500 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <span class="text-lg">🏆</span> Catat Prestasi
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8">Nama Prestasi Lomba</th>
                                <th class="p-5 w-48">Pencapaian</th>
                                <th class="p-5 w-56">Penyelenggara</th>
                                <th class="p-5 w-40">Tanggal</th>
                                <th class="p-5 w-48">Berkas Arsip</th>
                                <th class="p-5 pr-8 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($ekskul->prestasi as $pres)
                                <tr class="hover:bg-amber-50/30 transition-colors group">
                                    <td class="p-5 pl-8 align-middle">
                                        <div class="font-black text-gray-900 text-base leading-tight">{{ $pres->nama_prestasi }}</div>
                                    </td>
                                    <td class="p-5 align-middle">
                                        <span class="inline-block px-3 py-1 bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 text-xs font-black rounded-lg shadow-sm border border-amber-200 mb-1">
                                            🥇 Juara {{ $pres->juara }}
                                        </span>
                                        <div class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded inline-block">
                                            Tingkat {{ $pres->tingkat }}
                                        </div>
                                    </td>
                                    <td class="p-5 align-middle font-medium text-gray-600">
                                        🏛️ {{ $pres->penyelenggara }}
                                    </td>
                                    <td class="p-5 align-middle font-semibold text-gray-700">
                                        {{ $pres->tanggal_prestasi->format('d M Y') }}
                                    </td>
                                    <td class="p-5 align-middle space-y-1">
                                        @if($pres->file_sertifikat)
                                            <a href="{{ asset('storage/' . $pres->file_sertifikat) }}" target="_blank" class="flex items-center gap-1.5 px-2.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition-colors border border-blue-100 w-max">
                                                <span>📄</span> Sertifikat
                                            </a>
                                        @endif
                                        @if($pres->file_dokumentasi)
                                            <a href="{{ asset('storage/' . $pres->file_dokumentasi) }}" target="_blank" class="flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-bold transition-colors border border-emerald-100 w-max">
                                                <span>🖼️</span> Foto Lomba
                                            </a>
                                        @endif
                                        @if(!$pres->file_sertifikat && !$pres->file_dokumentasi)
                                            <span class="text-xs italic text-gray-400">Tidak ada lampiran</span>
                                        @endif
                                    </td>
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <button type="button" @click="initDelete('{{ route('ekskul.ekstrakurikuler.prestasi.destroy', [$ekskul->id, $pres->id]) }}', 'Prestasi: {{ addslashes($pres->nama_prestasi) }}')" class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-xl transition-colors border border-rose-100 shadow-sm cursor-pointer" title="Hapus Prestasi">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">🏆</span>
                                        <p class="text-lg font-bold text-gray-500">Belum ada catatan prestasi untuk ekskul ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================= MODAL FORM: ANGGOTA BARU ================= --}}
            <div x-show="modalAnggota" 
                 x-data="{ 
                     selectedKelas: '',
                     // Data dari controller diterjemahkan ke format Alpine
                     allSiswa: [
                         @foreach($siswaBelumMendaftar as $sw)
                             { 
                                 id: '{{ $sw->id }}', 
                                 nama: '{{ addslashes($sw->nama ?? $sw->nama_lengkap) }}', 
                                 kelas_id: '{{ $sw->kelas_id ?? '' }}' 
                             },
                         @endforeach
                     ],
                     get filteredSiswa() {
                         if (!this.selectedKelas) return [];
                         return this.allSiswa.filter(s => s.kelas_id == this.selectedKelas);
                     }
                 }" 
                 class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;" x-transition>
                
                <div @click.away="modalAnggota = false" class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden">
                    <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                        <h4 class="text-lg font-black text-gray-900">👥 Daftarkan Anggota Baru</h4>
                        <button type="button" @click="modalAnggota = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                    </div>
                    
                    <form action="{{ route('ekskul.ekstrakurikuler.anggota.store', $ekskul->id) }}" method="POST">
                        @csrf
                        <div class="p-8 space-y-5 max-h-[70vh] overflow-y-auto">
                            
                            {{-- Cascading Filter Kelas -> Siswa --}}
                            <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-indigo-900 mb-2">1. Pilih Kelas <span class="text-rose-500">*</span></label>
                                    <select x-model="selectedKelas" name="kelas_id" required class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $kls)
                                            <option value="{{ $kls->id }}">{{ $kls->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-indigo-900 mb-2">2. Pilih Siswa <span class="text-rose-500">*</span></label>
                                    <select name="siswa_id" required :disabled="!selectedKelas" class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3 disabled:bg-gray-100 disabled:opacity-70 disabled:cursor-not-allowed">
                                        <option value="">-- Cari Nama Siswa --</option>
                                        <template x-for="siswa in filteredSiswa" :key="siswa.id">
                                            <option :value="siswa.id" x-text="siswa.nama"></option>
                                        </template>
                                        <template x-if="selectedKelas && filteredSiswa.length === 0">
                                            <option value="" disabled>Tidak ada siswa mendaftar dari kelas ini</option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">No. HP Wali/Siswa <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <input type="text" name="nomor_hp" placeholder="Contoh: 081234xxx" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Bergabung <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_bergabung" value="{{ date('Y-m-d') }}" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Motivasi Bergabung</label>
                                <textarea name="motivasi" rows="2" placeholder="Tuliskan motivasi pendaftaran (Opsional)..." class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                            </div>
                        </div>

                        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="modalAnggota = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">💾 Simpan Anggota</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================= MODAL FORM: REKAM PRESTASI ================= --}}
            <div x-show="modalPrestasi" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;" x-transition>
                <div @click.away="modalPrestasi = false" class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden">
                    <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                        <h4 class="text-lg font-black text-gray-900">🏆 Form Rekam Prestasi Ekskul</h4>
                        <button type="button" @click="modalPrestasi = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                    </div>
                    
                    <form action="{{ route('ekskul.ekstrakurikuler.prestasi.store', $ekskul->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="p-8 space-y-5 max-h-[70vh] overflow-y-auto">
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lomba / Kejuaraan <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_prestasi" placeholder="Misal: Turnamen Bola Basket Bupati Cup III" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tingkat Wilayah <span class="text-rose-500">*</span></label>
                                    <select name="tingkat" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                        <option value="Kabupaten">Tingkat Kabupaten/Kota</option>
                                        <option value="Provinsi">Tingkat Provinsi</option>
                                        <option value="Nasional">Tingkat Nasional</option>
                                        <option value="Sekolah">Internal Sekolah</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Peringkat / Juara <span class="text-rose-500">*</span></label>
                                    <input type="text" name="juara" placeholder="Misal: 1, 2, atau Harapan I" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Instansi Penyelenggara <span class="text-rose-500">*</span></label>
                                    <input type="text" name="penyelenggara" placeholder="Misal: Dispora Provinsi Jabar" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Perolehan <span class="text-rose-500">*</span></label>
                                    <input type="date" name="tanggal_prestasi" required value="{{ date('Y-m-d') }}" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                                </div>
                            </div>
                            
                            {{-- Upload Berkas --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 bg-indigo-50/30 rounded-2xl border border-indigo-100">
                                <div>
                                    <label class="block text-xs font-bold text-blue-700 mb-2">📄 Scan Piagam/Sertifikat (PDF/JPG)</label>
                                    <input type="file" name="file_sertifikat" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-bold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200 cursor-pointer bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-emerald-700 mb-2">🖼️ Foto Dokumentasi (Gambar)</label>
                                    <input type="file" name="file_dokumentasi" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-bold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="modalPrestasi = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                            <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer text-shadow-sm">💾 Simpan Catatan Prestasi</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================= MODAL: KONFIRMASI HAPUS (GABUNGAN SWEETALERT) ================= --}}
            <div x-show="openDelete" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
                <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl p-8 text-center relative overflow-hidden" @click.away="openDelete = false">
                    
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 border border-rose-100 mb-6">
                        <span class="text-4xl">⚠️</span>
                    </div>
                    
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 mb-4">Lanjutkan Hapus Data?</h3>
                        <p class="text-sm text-gray-500 mb-8 px-2">
                            Apakah Anda yakin ingin menghapus/mengeluarkan <strong class="text-gray-800" x-text="deleteTargetName"></strong>? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    
                    <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 m-0">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="openDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none">
                            Ya, Hapus Data
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>