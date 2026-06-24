<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil Detail & Dokumen Lampiran Siswa') }}
        </h2>
    </x-slot>

    <div x-data="{
        tabActive: 'profil',
        statusSelected: 'Aktif'
    }" class="py-12 bg-slate-900/10 min-h-screen font-sans">
        
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="mb-2">
                <a href="{{ route('kesiswaan.siswa') }}" class="text-xs font-bold text-indigo-600 hover:underline inline-flex items-center gap-1">
                    ⬅️ Kembali ke Tabel Master Siswa
                </a>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 border border-indigo-100 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                        🎓
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 leading-tight">{{ $siswa->nama_lengkap }}</h1>
                        <div class="flex items-center gap-3 text-xs text-gray-400 font-medium mt-1">
                            <span>NIPD: <strong class="text-gray-600 font-mono">{{ $siswa->nipd }}</strong></span>
                            <span>•</span>
                            <span>NISN: <strong class="text-gray-600 font-mono">{{ $siswa->nisn ?? '-' }}</strong></span>
                        </div>
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <div class="text-[11px] text-gray-400 font-bold uppercase tracking-wide">Plot Ruang Kelas:</div>
                    <div class="text-xs font-bold text-gray-800 bg-gray-100 border px-3 py-1 rounded-lg mt-0.5 inline-block">
                        🏫 {{ $siswa->kelas->nama_kelas ?? 'Belum Dipetakan' }}
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6 space-y-6">
                
                <div class="flex border-b border-gray-100 text-xs font-bold text-gray-400 gap-6">
                    <button @click="tabActive = 'profil'" :class="tabActive === 'profil' ? 'text-indigo-600 border-b-2 border-indigo-600 pb-3' : 'pb-3 hover:text-gray-600'" class="transition-all cursor-pointer">
                        👤 Identitas & Silsilah
                    </button>
                    <button @click="tabActive = 'dokumen'" :class="tabActive === 'dokumen' ? 'text-indigo-600 border-b-2 border-indigo-600 pb-3' : 'pb-3 hover:text-gray-600'" class="transition-all cursor-pointer">
                        📁 Arsip Digital Berkas ({{ $siswa->dokumen->count() }})
                    </button>
                    <button @click="tabActive = 'riwayat'" :class="tabActive === 'riwayat' ? 'text-indigo-600 border-b-2 border-indigo-600 pb-3' : 'pb-3 hover:text-gray-600'" class="transition-all cursor-pointer">
                        📜 Log Akademik & Status
                    </button>
                </div>

                <div x-show="tabActive === 'profil'" class="space-y-6" x-transition>
                    <div class="space-y-4 text-xs">
                        <h3 class="font-bold text-sm text-gray-800 border-b border-gray-50 pb-2">Biodata Diri Personal</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-gray-600">
                            <div><span class="text-gray-400 block mb-0.5">NIK Kependudukan:</span><strong class="text-gray-900 font-mono text-sm">{{ $siswa->nik }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">Tempat, Tanggal Lahir:</span><strong class="text-gray-900">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d F Y') }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">Jenis Kelamin / Agama:</span><strong class="text-gray-900">{{ $siswa->jenis_kelamin }} / {{ $siswa->agama }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">No Handphone Aktif:</span><strong class="text-gray-900">{{ $siswa->nomor_hp }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">Sekolah Asal (SD/MI):</span><strong class="text-gray-900">{{ $siswa->asal_sekolah }}</strong></div>
                            <div><span class="text-gray-400 block mb-0.5">Anak Ke / Kode Pos:</span><strong class="text-gray-900">{{ $siswa->anak_ke }} / {{ $siswa->kode_pos }}</strong></div>
                        </div>
                        <div class="p-3.5 bg-gray-50 border border-gray-100 rounded-xl">
                            <span class="text-gray-400 block mb-1 font-semibold">Alamat Domisili Tetap Sesuai KK:</span>
                            <strong class="text-gray-800">{{ $siswa->alamat_lengkap }}, RT.{{ $siswa->rt }}/RW.{{ $siswa->rw }}, Kel/Desa. {{ $siswa->kelurahan_desa }}, Kec. {{ $siswa->kecamatan }}, {{ $siswa->kota }}, {{ $siswa->provinsi }}</strong>
                        </div>
                    </div>

                    <div class="space-y-4 text-xs">
                        <h3 class="font-bold text-sm text-gray-800 border-b border-gray-50 pb-2">Daftar Relasi Orang Tua / Wali</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @forelse($siswa->wali as $parent)
                                <div class="border border-gray-100 bg-gray-50/50 p-4 rounded-xl space-y-2">
                                    <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold rounded text-[9px] uppercase">{{ $parent->pivot->hubungan }}</span>
                                    <div class="text-gray-700 pt-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ $parent->nama_lengkap }}</div>
                                        <div class="font-mono text-[11px] text-gray-400 mt-0.5">NIK: {{ $parent->nik }}</div>
                                        <div class="text-gray-500 mt-1">📞 No HP: {{ $parent->nomor_hp ?? '-' }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="md:col-span-3 py-4 text-center text-gray-400 italic">Ikatan relasi data penanggung jawab wali siswa belum diisi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div x-show="tabActive === 'dokumen'" 
                    x-data="{ openDeleteDocModal: false, targetFormId: '' }" 
                    class="grid grid-cols-1 md:grid-cols-3 gap-6" 
                    style="display: none;" 
                    x-transition>
                    
                    <div class="bg-gray-50/50 p-4 border border-gray-100 rounded-xl h-fit space-y-3 text-xs">
                        <h4 class="font-bold text-gray-800 border-b border-gray-200 pb-1.5 uppercase">Unggah Berkas Baru</h4>
                        <form action="{{ route('kesiswaan.dokumen.store', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">Jenis Arsip Dokumen</label>
                                <input type="text" name="jenis_dokumen" required placeholder="Ketik jenis dokumen (misal: Ijazah, KIP, dll)" class="w-full text-xs border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white">
                            </div>
                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">Tahun Penerbitan Berkas</label>
                                <input type="number" name="tahun_dokumen" value="{{ date('Y') }}" required class="w-full text-xs border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">File Berkas (PDF/JPG, Max 2MB)</label>
                                <input type="file" name="file_dokumen" required class="w-full text-xs border border-gray-200 bg-white p-1 rounded-lg">
                            </div>
                            <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-sm transition-colors cursor-pointer text-center">
                                Upload File
                            </button>
                        </form>
                    </div>

                    <div class="md:col-span-2 space-y-3 text-xs">
                        <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2 uppercase">Berkas Digital Terverifikasi</h4>
                        <div class="space-y-2">
                            @forelse($siswa->dokumen as $doc)
                                <div class="flex justify-between items-center p-3 border border-gray-100 rounded-xl bg-white hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">📄</span>
                                        <div>
                                            <strong class="text-gray-900 font-bold block">{{ $doc->jenis_dokumen }}</strong>
                                            <span class="text-[11px] text-gray-400">Tahun terbit: {{ $doc->tahun_dokumen }} • Nama: {{ $doc->nama_dokumen }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $doc->file_dokumen) }}" target="_blank" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md font-semibold border border-gray-300">Lihat</a>
                                        
                                        <form id="delete-doc-form-{{ $doc->id }}" action="{{ route('kesiswaan.dokumen.destroy', $doc->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" 
                                                    @click="openDeleteDocModal = true; targetFormId = 'delete-doc-form-{{ $doc->id }}'" 
                                                    class="p-1 text-rose-500 hover:bg-rose-50 rounded font-bold cursor-pointer">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 border border-dashed rounded-xl italic text-gray-400">Belum ada dokumen digital terunggah di sistem.</div>
                            @endforelse
                        </div>
                    </div>

                    <div x-show="openDeleteDocModal" 
                            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                            style="display: none;"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95">
                            
                            <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-gray-100 text-center space-y-5">
                                <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto text-2xl border border-rose-100">
                                    ⚠️
                                </div>
                                
                                <div class="space-y-2">
                                    <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                        Hapus Dokumen Arsip?
                                    </h3>
                                    <p class="text-xs text-gray-500 leading-relaxed px-2">
                                        Apakah Anda yakin ingin menghapus dokumen berkas ini? <strong class="text-rose-600 font-bold">Tindakan ini tidak dapat dibatalkan</strong> dan berkas fisik akan terhapus permanen dari server storage.
                                    </p>
                                </div>
                                
                                <div class="flex items-center justify-center gap-3 pt-2 text-xs font-semibold">
                                    <button type="button" 
                                            @click="openDeleteDocModal = false; targetFormId = ''" 
                                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-all cursor-pointer">
                                        Batal
                                    </button>
                                    
                                    <button type="button" 
                                            @click="document.getElementById(targetFormId).submit(); openDeleteDocModal = false;" 
                                            style="color: #ffffff !important; background-color: #e11d48 !important; display: inline-block !important;"
                                            class="px-5 py-2.5 text-white font-bold rounded-xl shadow-sm hover:bg-rose-700 transition-all cursor-pointer">
                                        Ya, Hapus
                                    </button>
                                </div>
                            </div>
                    </div>

                </div>

                <div x-show="tabActive === 'prestasi'" 
                    x-data="{ openDeletePrestasiModal: false, targetPrestasiFormId: '' }" 
                    class="grid grid-cols-1 md:grid-cols-3 gap-6" 
                    style="display: none;" 
                    x-transition>
                    
                    <div class="bg-gray-50/50 p-4 border border-gray-100 rounded-xl h-fit space-y-3 text-xs">
                        <h4 class="font-bold text-gray-800 border-b border-gray-200 pb-1.5 uppercase">Tambah Prestasi Siswa</h4>
                        
                        <form action="{{ route('kesiswaan.prestasi.store', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <input type="hidden" name="kelas_id" value="{{ $siswa->kelas_aktif_id ?? $siswa->riwayatKelas->last()->kelas_id ?? '' }}">

                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">Jenis Prestasi</label>
                                <select name="jenis_prestasi" required class="w-full text-xs border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Akademik">🏆 Akademik</option>
                                    <option value="Non-Akademik">🎨 Non-Akademik</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">Nama / Judul Prestasi</label>
                                <input type="text" name="nama_prestasi" required placeholder="Contoh: Juara 1 Olimpiade Matematika" class="w-full text-xs border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">Tahun Perolehan</label>
                                <input type="number" name="tahun_prestasi" value="{{ date('Y') }}" required class="w-full text-xs border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-gray-600 font-semibold mb-1">File Sertifikat (PDF/JPG, Max 2MB)</label>
                                <input type="file" name="file_sertifikat" class="w-full text-xs border border-gray-200 bg-white p-1 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-sm transition-colors cursor-pointer text-center">
                                Simpan Prestasi
                            </button>
                        </form>
                    </div>

                    <div class="md:col-span-2 space-y-3 text-xs">
                        <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2 uppercase">Daftar Prestasi Siswa</h4>
                        <div class="space-y-2">
                            @forelse($siswa->prestasi as $pres)
                                <div class="flex justify-between items-center p-3 border border-gray-100 rounded-xl bg-white hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xl">{{ $pres->jenis_prestasi === 'Akademik' ? '🥇' : '🎨' }}</span>
                                        <div>
                                            <strong class="text-gray-900 font-bold block">{{ $pres->nama_prestasi }}</strong>
                                            <span class="text-[11px] text-gray-400">Kategori: {{ $pres->jenis_prestasi }} • Tahun: {{ $pres->tahun_prestasi }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($pres->file_sertifikat)
                                            <a href="{{ asset('storage/' . $pres->file_sertifikat) }}" target="_blank" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md font-semibold border border-gray-300">Lihat Sertifikat</a>
                                        @endif
                                        
                                        <form id="delete-prestasi-form-{{ $pres->id }}" action="{{ route('kesiswaan.prestasi.destroy', $pres->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" 
                                                    @click="openDeletePrestasiModal = true; targetPrestasiFormId = 'delete-prestasi-form-{{ $pres->id }}'" 
                                                    class="p-1 text-rose-500 hover:bg-rose-50 rounded font-bold cursor-pointer">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 border border-dashed rounded-xl italic text-gray-400">Siswa belum memiliki catatan prestasi terunggah.</div>
                            @endforelse
                        </div>
                    </div>

                    <div x-show="openDeletePrestasiModal" 
                        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                        style="display: none;"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95">
                        
                        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-gray-100 text-center space-y-5">
                            <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto text-2xl border border-rose-100">
                                ⚠️
                            </div>
                            
                            <div class="space-y-2">
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">Hapus Data Prestasi Siswa?</h3>
                                <p class="text-xs text-gray-500 leading-relaxed px-2">Apakah Anda yakin ingin menghapus data catatan prestasi ini dari sistem?</p>
                            </div>
                            
                            <div class="flex items-center justify-center gap-3 pt-2 text-xs font-semibold">
                                <button type="button" 
                                        @click="openDeletePrestasiModal = false; targetPrestasiFormId = ''" 
                                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl cursor-pointer">
                                    Batal
                                </button>
                                <button type="button" 
                                        @click="document.getElementById(targetPrestasiFormId).submit(); openDeletePrestasiModal = false;" 
                                        style="color: #ffffff !important; background-color: #e11d48 !important; display: inline-block !important;" 
                                        class="px-5 py-2.5 text-white font-bold rounded-xl shadow-sm hover:bg-rose-700 cursor-pointer">
                                    Ya, Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="tabActive === 'riwayat'" class="grid grid-cols-1 md:grid-cols-2 gap-8" style="display: none;" x-transition>
    
                    <div class="space-y-3 text-xs">
                        <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2 uppercase tracking-wider flex items-center gap-1.5">
                            🏫 Log Histori Ruang Kelas
                        </h4>
                        <div class="relative border-l-2 border-indigo-100 pl-4 space-y-4 ml-2 pt-2">
                            @forelse($siswa->riwayatKelas as $rk)
                                <div class="relative">
                                    <span class="absolute -left-[22px] top-0 w-3 h-3 bg-indigo-600 rounded-full ring-4 ring-indigo-50"></span>
                                    <div class="font-bold text-gray-900 text-sm">Kelas: {{ $rk->kelas->nama_kelas ?? 'Gantung' }}</div>
                                    <div class="text-[11px] text-gray-400 font-medium">Tingkat {{ $rk->tingkat }} • Semester: {{ $rk->semester->nama_semester ?? '-' }}</div>
                                    <div class="mt-1 px-1.5 py-0.5 bg-gray-100 border border-gray-200 inline-block text-[10px] text-gray-600 rounded font-medium italic">{{ $rk->keterangan }}</div>
                                </div>
                            @empty
                                <div class="text-gray-400 italic py-2 pl-2 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                    Belum terekam jejak mutasi plotting kelas siswa.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-6 text-xs">
                        
                        <div class="space-y-3">
                            <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2 uppercase tracking-wider flex items-center gap-1.5">
                                📈 Log Status Operasional
                            </h4>
                            <div class="relative border-l-2 border-emerald-100 pl-4 space-y-4 ml-2 pt-2">
                                @forelse($siswa->riwayatStatus as $rs)
                                    <div class="relative">
                                        <span class="absolute -left-[22px] top-0 w-3 h-3 bg-emerald-600 rounded-full ring-4 ring-emerald-50"></span>
                                        <div class="font-bold text-gray-900">
                                            Status: <span class="px-1.5 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded font-extrabold text-[9px] uppercase">{{ $rs->status }}</span>
                                        </div>
                                        <div class="text-[11px] text-gray-400 font-medium">Log dibuat pada: {{ $rs->created_at->format('d M Y H:i') }}</div>
                                        @if(is_array($rs->metadata))
                                            <div class="mt-1 bg-gray-50 border border-gray-200 p-2 rounded-lg text-[11px] text-gray-600 space-y-0.5 font-medium">
                                                <div>💬 Alasan: {{ $rs->metadata['alasan'] ?? '-' }}</div>
                                                @if(!empty($rs->metadata['sekolah_tujuan'])) <div>🏫 Tujuan: {{ $rs->metadata['sekolah_tujuan'] }}</div> @endif
                                                @if(!empty($rs->metadata['no_ijazah'])) <div>📜 No. Ijazah: {{ $rs->metadata['no_ijazah'] }}</div> @endif
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-gray-400 italic py-2 pl-2 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                        Belum ada perubahan status operasional.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Form Sakelar Perubahan Status -->
                        <div x-data="{ openConfirmModal: false }" class="p-5 bg-rose-50/50 border border-rose-200 rounded-2xl space-y-4 shadow-sm">
                            <h5 class="font-bold text-rose-900 uppercase tracking-wide flex items-center gap-1.5">
                                ⚡ Sakelar Status Akademik
                            </h5>
                            
                            <form id="statusForm" action="{{ route('kesiswaan.siswa.updateStatus', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf @method('PUT')
                                <input type="hidden" name="semester_id" value="{{ $semester_aktif->id ?? '' }}">
                                
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-1">Pilih Status Operasional</label>
                                        <select name="status_siswa" x-model="statusSelected" class="w-full text-xs border-gray-300 rounded-lg shadow-sm text-gray-700 bg-white focus:ring-rose-500 focus:border-rose-500">
                                            <option value="Aktif">🟢 Aktif</option>
                                            <option value="Mutasi">🟡 Mutasi Keluar</option>
                                            <option value="Keluar">🔴 Dikeluarkan (DO)</option>
                                            <option value="Lulus">🔵 Lulus / Alumni</option>
                                        </select>
                                    </div>
                                </div>

                                <div x-show="statusSelected === 'Aktif'" x-transition class="space-y-3">
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-1">Catatan Keterangan</label>
                                        <input type="text" name="alasan_aktif" :required="statusSelected === 'Aktif'" placeholder="Catatan kembali aktif..." class="w-full text-xs border-gray-300 rounded-lg shadow-sm bg-white focus:ring-rose-500 focus:border-rose-500">
                                    </div>
                                </div>

                                <div x-show="statusSelected === 'Mutasi'" style="display: none;" x-transition class="space-y-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-gray-700 font-semibold mb-1">Nama Sekolah Tujuan Mutasi</label>
                                            <input type="text" name="sekolah_tujuan" :required="statusSelected === 'Mutasi'" placeholder="Contoh: SMP Negeri 1 Jakarta" class="w-full text-xs border-gray-300 rounded-lg shadow-sm bg-white focus:ring-rose-500 focus:border-rose-500">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 font-semibold mb-1">Alasan Mutasi</label>
                                            <input type="text" name="alasan_mutasi" :required="statusSelected === 'Mutasi'" placeholder="Alasan pindah sekolah..." class="w-full text-xs border-gray-300 rounded-lg shadow-sm bg-white focus:ring-rose-500 focus:border-rose-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-1">Upload Surat Mutasi <span class="text-rose-500">*</span></label>
                                        <input type="file" name="file_surat_mutasi" :required="statusSelected === 'Mutasi'" class="w-full text-xs border border-gray-300 bg-white p-1 rounded-lg focus:ring-rose-500 focus:border-rose-500">
                                    </div>
                                </div>

                                <div x-show="statusSelected === 'Keluar'" style="display: none;" x-transition class="space-y-3">
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-1">Alasan Keluar</label>
                                        <input type="text" name="alasan_keluar" :required="statusSelected === 'Keluar'" placeholder="Alasan diberhentikan / keluar..." class="w-full text-xs border-gray-300 rounded-lg shadow-sm bg-white focus:ring-rose-500 focus:border-rose-500">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-1">Upload Surat Keluar <span class="text-rose-500">*</span></label>
                                        <input type="file" name="file_surat_keluar" :required="statusSelected === 'Keluar'" class="w-full text-xs border border-gray-300 bg-white p-1 rounded-lg focus:ring-rose-500 focus:border-rose-500">
                                    </div>
                                </div>

                                <div x-show="statusSelected === 'Lulus'" style="display: none;" x-transition class="space-y-4">
                                    <div class="p-3 bg-white border border-gray-200 rounded-xl space-y-2">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-gray-700 font-semibold mb-1">Nomor Surat Kelulusan</label>
                                                <input type="text" name="no_surat_kelulusan" :required="statusSelected === 'Lulus'" placeholder="No. SKL Resmi..." class="w-full text-xs border-gray-300 rounded-lg shadow-sm">
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 font-semibold mb-1">Upload File SKL</label>
                                                <input type="file" name="file_surat_kelulusan" :required="statusSelected === 'Lulus'" class="w-full text-xs border border-gray-300 p-1 rounded-lg bg-gray-50">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-white border border-gray-200 rounded-xl space-y-2">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-gray-700 font-semibold mb-1">Nomor Surat Kelakuan Baik</label>
                                                <input type="text" name="no_skkb" :required="statusSelected === 'Lulus'" placeholder="No. SKKB Sekolah..." class="w-full text-xs border-gray-300 rounded-lg shadow-sm">
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 font-semibold mb-1">Upload File SKKB</label>
                                                <input type="file" name="file_skkb" :required="statusSelected === 'Lulus'" class="w-full text-xs border border-gray-300 p-1 rounded-lg bg-gray-50">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-white border border-gray-200 rounded-xl space-y-2">
                                        <div class="text-[11px] font-bold text-indigo-600 mb-1">📎 Kelengkapan Tambahan (Bisa Menyusul)</div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-gray-600 font-semibold mb-1">Nomor Ijazah</label>
                                                <input type="text" name="no_ijazah" placeholder="Contoh: DN-01/M-SM/..." class="w-full text-xs border-gray-300 rounded-lg shadow-sm">
                                            </div>
                                            <div>
                                                <label class="block text-gray-600 font-semibold mb-1">Upload Berkas Ijazah</label>
                                                <input type="file" name="file_ijazah" class="w-full text-xs border border-gray-300 p-1 rounded-lg bg-gray-50">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-3 bg-white border border-gray-200 rounded-xl space-y-2">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-gray-600 font-semibold mb-1">Nomor Transkrip Nilai</label>
                                                <input type="text" name="no_transkrip" placeholder="No. Transkrip Nilai / SKHUN..." class="w-full text-xs border-gray-300 rounded-lg shadow-sm">
                                            </div>
                                            <div>
                                                <label class="block text-gray-600 font-semibold mb-1">Upload Berkas Transkrip</label>
                                                <input type="file" name="file_transkrip" class="w-full text-xs border border-gray-300 p-1 rounded-lg bg-gray-50">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" 
                                        @click="openConfirmModal = true"
                                        style="color: #ffffff !important; background-color: #e11d48 !important; display: block !important; width: 100% !important;" 
                                        class="w-full block text-center py-3 px-4 text-white font-extrabold rounded-xl text-xs shadow-md hover:bg-rose-700 transition-colors cursor-pointer clear-both">
                                    🔒 Eksekusi Perubahan Status Siswa
                                </button>
                            </form>

                            <div x-show="openConfirmModal" 
                                class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                                style="display: none;"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95">
                                
                                <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-gray-100 text-center space-y-5">
                                    <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto text-2xl border border-rose-100">
                                        ⚠️
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                            Ubah Status Operasional Siswa?
                                        </h3>
                                        <p class="text-xs text-gray-500 leading-relaxed px-2">
                                            Apakah Anda yakin ingin memproses status baru untuk siswa <strong class="text-gray-800 font-bold">{{ $siswa->nama_lengkap }}</strong>? Pastikan berkas dokumen pendukung yang diunggah sudah benar.
                                        </p>
                                    </div>
                                    
                                    <div class="flex items-center justify-center gap-3 pt-2 text-xs font-semibold">
                                        <button type="button" 
                                                @click="openConfirmModal = false" 
                                                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-all cursor-pointer">
                                            Batal
                                        </button>
                                        
                                        <button type="button" 
                                                @click="document.getElementById('statusForm').submit(); openConfirmModal = false;" 
                                                style="color: #ffffff !important; background-color: #e11d48 !important; display: inline-block !important;"
                                                class="px-5 py-2.5 text-white font-bold rounded-xl shadow-sm hover:bg-rose-700 transition-all cursor-pointer">
                                            Ya, Eksekusi
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>