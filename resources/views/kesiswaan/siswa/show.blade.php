<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('kesiswaan.siswa') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-colors" title="Kembali ke Tabel Master">
                    <span class="text-xl font-bold">←</span>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                        <span class="text-3xl">🎓</span> {{ __('Profil & Portofolio Siswa') }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500 mt-1">Kelola data personal, riwayat akademik, hingga arsip digital.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        tabActive: 'profil',
        statusSelected: 'Aktif'
    }" class="py-10 bg-slate-50 min-h-screen font-sans">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Alerts / Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <div>
                        <div class="font-bold text-lg mb-1">Gagal menyimpan data</div>
                        <ul class="list-disc pl-4 space-y-1 font-medium text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Kartu Info Utama Siswa --}}
            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-indigo-900/5 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

                <div class="flex items-center gap-6 relative z-10">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-200 rounded-full blur-md opacity-50 transition-opacity"></div>
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-50 to-white text-indigo-700 rounded-full flex flex-col items-center justify-center border-4 border-white shadow-md relative z-10 p-2">
                            <span class="text-4xl">{{ $siswa->jenis_kelamin == 'Laki-Laki' || $siswa->jenis_kelamin == 'Laki-laki' ? '👦' : '👧' }}</span>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $siswa->nama_lengkap }}</h1>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 font-medium mt-2">
                            <span class="bg-gray-100 border border-gray-200 px-2.5 py-1 rounded-lg shadow-sm">NIPD: <strong class="text-indigo-700 font-mono">{{ $siswa->nipd }}</strong></span>
                            <span class="bg-gray-100 border border-gray-200 px-2.5 py-1 rounded-lg shadow-sm">NISN: <strong class="text-indigo-700 font-mono">{{ $siswa->nisn ?? '-' }}</strong></span>
                            
                            @if($siswa->status_siswa == 'Aktif')
                                <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-black uppercase rounded-lg">🟢 Aktif</span>
                            @elseif($siswa->status_siswa == 'Lulus')
                                <span class="px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-black uppercase rounded-lg">🔵 Lulus</span>
                            @elseif($siswa->status_siswa == 'Mutasi')
                                <span class="px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-black uppercase rounded-lg">🟡 Mutasi</span>
                            @else
                                <span class="px-2.5 py-1 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-black uppercase rounded-lg">🔴 Keluar</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="text-left md:text-right relative z-10 bg-indigo-50/50 p-4 border border-indigo-100 rounded-2xl w-full md:w-auto">
                    <div class="text-[11px] text-indigo-500 font-black uppercase tracking-widest mb-1">Plot Ruang Kelas Pembinaan:</div>
                    <div class="text-lg font-black text-indigo-900 flex items-center gap-2 md:justify-end">
                        <span>🏫</span> {{ $siswa->kelas->nama_kelas ?? 'Belum Dipetakan' }}
                    </div>
                </div>
            </div>

            {{-- Area Konten Tab --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] border border-gray-100 p-8 space-y-8">
                
                {{-- Navigasi Tab --}}
                <div class="flex flex-wrap border-b-2 border-gray-100 text-sm font-black text-gray-400 gap-8">
                    <button @click="tabActive = 'profil'" :class="tabActive === 'profil' ? 'text-indigo-600 border-b-4 border-indigo-600 pb-3' : 'pb-3 hover:text-gray-600 hover:border-b-4 hover:border-gray-300'" class="transition-all cursor-pointer flex items-center gap-2">
                        <span>👤</span> Identitas & Silsilah
                    </button>
                    <button @click="tabActive = 'dokumen'" :class="tabActive === 'dokumen' ? 'text-teal-600 border-b-4 border-teal-600 pb-3' : 'pb-3 hover:text-gray-600 hover:border-b-4 hover:border-gray-300'" class="transition-all cursor-pointer flex items-center gap-2">
                        <span>📁</span> Arsip Digital ({{ $siswa->dokumen->count() }})
                    </button>
                    <button @click="tabActive = 'prestasi'" :class="tabActive === 'prestasi' ? 'text-amber-500 border-b-4 border-amber-500 pb-3' : 'pb-3 hover:text-gray-600 hover:border-b-4 hover:border-gray-300'" class="transition-all cursor-pointer flex items-center gap-2">
                        <span>🏆</span> Data Prestasi ({{ $siswa->prestasi->count() ?? 0 }})
                    </button>
                    <button @click="tabActive = 'riwayat'" :class="tabActive === 'riwayat' ? 'text-rose-600 border-b-4 border-rose-600 pb-3' : 'pb-3 hover:text-gray-600 hover:border-b-4 hover:border-gray-300'" class="transition-all cursor-pointer flex items-center gap-2">
                        <span>📜</span> Log Riwayat Akademik
                    </button>
                </div>

                {{-- ================= TAB 1: PROFIL & SILSILAH ================= --}}
                <div x-show="tabActive === 'profil'" class="space-y-8" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    
                    {{-- Biodata Personal --}}
                    <div class="space-y-5">
                        <h3 class="font-black text-lg text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                            <span>📋</span> Biodata Diri Personal
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-400 block mb-1 uppercase tracking-wider">NIK Kependudukan</span>
                                <strong class="text-gray-900 font-mono text-base">{{ $siswa->nik }}</strong>
                            </div>
                            <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-400 block mb-1 uppercase tracking-wider">Tempat, Tanggal Lahir</span>
                                <strong class="text-gray-900 text-sm">{{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</strong>
                            </div>
                            <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-400 block mb-1 uppercase tracking-wider">Gender / Agama</span>
                                <strong class="text-gray-900 text-sm">{{ $siswa->jenis_kelamin }} / {{ $siswa->agama }}</strong>
                            </div>
                            <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-400 block mb-1 uppercase tracking-wider">No Handphone Aktif</span>
                                <strong class="text-gray-900 text-sm flex items-center gap-1"><span>📱</span> {{ $siswa->nomor_hp ?? '-' }}</strong>
                            </div>
                            <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-400 block mb-1 uppercase tracking-wider">Sekolah Asal (SD/MI)</span>
                                <strong class="text-gray-900 text-sm">{{ $siswa->asal_sekolah }}</strong>
                            </div>
                            <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                <span class="text-xs font-bold text-gray-400 block mb-1 uppercase tracking-wider">Anak Ke / Kode Pos</span>
                                <strong class="text-gray-900 text-sm">Anak ke-{{ $siswa->anak_ke }} / {{ $siswa->kode_pos }}</strong>
                            </div>
                        </div>

                        <div class="p-5 bg-indigo-50/40 border border-indigo-100 rounded-2xl flex gap-4 items-start">
                            <span class="text-2xl mt-1">📍</span>
                            <div>
                                <span class="text-xs font-black text-indigo-400 block mb-1.5 uppercase tracking-wider">Alamat Domisili Tetap Sesuai KK</span>
                                <strong class="text-gray-800 text-sm leading-relaxed block">
                                    {{ $siswa->alamat_lengkap }}, RT.{{ $siswa->rt }}/RW.{{ $siswa->rw }},<br>
                                    Kel/Desa. {{ $siswa->kelurahan_relasi?->name ?? $siswa->kelurahan_desa }}, Kec. {{ $siswa->kecamatan_relasi?->name ?? $siswa->kecamatan }},<br>
                                    {{ $siswa->kota_relasi?->name ?? $siswa->kota }}, Provinsi {{ $siswa->provinsi_relasi?->name ?? $siswa->provinsi }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Relasi Orang Tua / Wali --}}
                    <div class="space-y-5 pt-4">
                        <h3 class="font-black text-lg text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                            <span>👨‍👩‍👧‍👦</span> Daftar Relasi Orang Tua & Wali
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @forelse($siswa->wali as $parent)
                                <div class="bg-white border border-gray-200 p-5 rounded-2xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                                    
                                    {{-- Pita Status Hubungan --}}
                                    <div class="absolute top-0 right-0 px-4 py-1.5 bg-gradient-to-r {{ $parent->pivot->hubungan == 'Ayah' ? 'from-blue-500 to-blue-600' : ($parent->pivot->hubungan == 'Ibu' ? 'from-rose-400 to-rose-500' : 'from-emerald-500 to-emerald-600') }} text-white text-[10px] font-black uppercase tracking-wider rounded-bl-xl shadow-sm">
                                        {{ $parent->pivot->hubungan }}
                                    </div>
                                    
                                    <div class="mt-4 space-y-3">
                                        <div>
                                            <div class="font-black text-gray-900 text-base mb-0.5">{{ $parent->nama_lengkap }}</div>
                                            <div class="font-mono text-[11px] font-bold text-gray-400 bg-gray-50 inline-block px-1.5 rounded">NIK: {{ $parent->nik ?? '-' }}</div>
                                        </div>
                                        
                                        <div class="space-y-1.5 pt-3 border-t border-gray-50 text-xs">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 font-medium">No. Handphone</span>
                                                <strong class="text-gray-800">{{ $parent->nomor_hp ?? '-' }}</strong>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 font-medium">Pekerjaan</span>
                                                <strong class="text-gray-800">{{ $parent->pekerjaan ?? '-' }}</strong>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 font-medium">Pend. Terakhir</span>
                                                <strong class="text-gray-800">{{ $parent->pendidikan_terakhir ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="md:col-span-3 py-12 text-center border border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                                    <span class="text-4xl block mb-3">👨‍👩‍👦</span>
                                    <h4 class="font-bold text-gray-600">Ikatan Relasi Belum Didaftarkan</h4>
                                    <p class="text-xs text-gray-400 mt-1">Data penanggung jawab wali/orang tua siswa belum diisi pada saat pendaftaran.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ================= TAB 2: DOKUMEN ARSIP ================= --}}
                <div x-show="tabActive === 'dokumen'" 
                    x-data="{ openDeleteDocModal: false, targetFormId: '' }" 
                    class="grid grid-cols-1 lg:grid-cols-3 gap-8" 
                    style="display: none;" 
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    
                    {{-- Form Upload Baru --}}
                    <div class="bg-teal-50/40 p-6 border border-teal-100 rounded-2xl h-fit shadow-sm">
                        <h4 class="font-black text-teal-900 border-b border-teal-200/60 pb-3 mb-5 uppercase tracking-wide flex items-center gap-2">
                            <span>📤</span> Unggah Berkas Baru
                        </h4>
                        <form action="{{ route('kesiswaan.dokumen.store', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-sm text-teal-800 font-bold mb-1.5">Jenis Arsip Dokumen <span class="text-rose-500">*</span></label>
                                <input type="text" name="jenis_dokumen" required placeholder="Misal: Ijazah, KIP, KK, dll" class="w-full text-sm font-medium border-teal-200 rounded-xl shadow-sm bg-white focus:ring-teal-500 focus:border-teal-500 px-4 py-2.5">
                            </div>
                            <div>
                                <label class="block text-sm text-teal-800 font-bold mb-1.5">Tahun Penerbitan Berkas <span class="text-rose-500">*</span></label>
                                <input type="number" name="tahun_dokumen" value="{{ date('Y') }}" required class="w-full text-sm font-medium border-teal-200 rounded-xl shadow-sm bg-white focus:ring-teal-500 focus:border-teal-500 px-4 py-2.5 text-center">
                            </div>
                            <div>
                                <label class="block text-sm text-teal-800 font-bold mb-1.5">File Berkas Digital <span class="text-rose-500">*</span></label>
                                <input type="file" name="file_dokumen" required class="w-full text-sm border border-teal-200 bg-white p-2 rounded-xl focus:ring-teal-500 focus:border-teal-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-100 file:text-teal-800 hover:file:bg-teal-200 transition-colors cursor-pointer text-gray-500">
                                <p class="text-[10px] text-teal-600 font-bold mt-2">Format: PDF/JPG/PNG/JPEG. Maksimal 2MB.</p>
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer flex items-center justify-center gap-2">
                                    <span>💾</span> Simpan Dokumen
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- List Dokumen --}}
                    <div class="lg:col-span-2 space-y-4">
                        <h4 class="font-black text-gray-900 border-b border-gray-100 pb-3 uppercase tracking-wide flex items-center gap-2">
                            <span>📁</span> Daftar Berkas Digital Terverifikasi
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            @forelse($siswa->dokumen as $doc)
                                <div class="p-4 border border-gray-200 rounded-2xl bg-white hover:border-teal-300 hover:shadow-md transition-all group flex flex-col justify-between h-full">
                                    <div class="flex items-start gap-4 mb-4">
                                        <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-2xl font-bold shrink-0 shadow-inner">
                                            {{ pathinfo($doc->file_dokumen, PATHINFO_EXTENSION) == 'pdf' ? '📑' : '🖼️' }}
                                        </div>
                                        <div>
                                            <strong class="text-gray-900 font-black block text-sm">{{ $doc->jenis_dokumen }}</strong>
                                            <div class="text-[11px] font-bold text-teal-600 bg-teal-50 inline-block px-1.5 py-0.5 rounded mt-1 mb-1">Tahun Terbit: {{ $doc->tahun_dokumen }}</div>
                                            <div class="text-[10px] text-gray-400 font-medium truncate w-32 md:w-48" title="{{ $doc->nama_dokumen }}">{{ $doc->nama_dokumen }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mt-auto border-t border-gray-50 pt-3">
                                        <a href="{{ asset('storage/' . $doc->file_dokumen) }}" target="_blank" class="flex-1 text-center py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs transition-colors shadow-sm">
                                            👁️ Lihat File
                                        </a>
                                        <form id="delete-doc-form-{{ $doc->id }}" action="{{ route('kesiswaan.dokumen.destroy', $doc->id) }}" method="POST" class="m-0">
                                            @csrf @method('DELETE')
                                            <button type="button" 
                                                    @click="openDeleteDocModal = true; targetFormId = 'delete-doc-form-{{ $doc->id }}'" 
                                                    class="p-2 bg-white hover:bg-rose-50 border border-gray-200 hover:border-rose-200 text-rose-500 rounded-xl font-bold cursor-pointer transition-colors shadow-sm" title="Hapus Dokumen">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="sm:col-span-2 py-16 text-center border border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                                    <span class="text-5xl block mb-4">📭</span>
                                    <h4 class="font-bold text-gray-600 text-lg">Belum Ada Dokumen</h4>
                                    <p class="text-sm text-gray-400 mt-1">Siswa ini belum memiliki arsip berkas digital yang terunggah.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Modal Konfirmasi Hapus Dokumen --}}
                    <div x-show="openDeleteDocModal" 
                            class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                            style="display: none;"
                            x-transition>
                            
                            <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-gray-100 text-center relative overflow-hidden" @click.away="openDeleteDocModal = false; targetFormId = ''">
                                
                                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto text-4xl border border-rose-100 shadow-inner mb-6">
                                    ⚠️
                                </div>
                                
                                <h3 class="text-xl font-black text-gray-900 mb-3">Hapus Dokumen Arsip?</h3>
                                <p class="text-sm text-gray-500 leading-relaxed px-2 mb-8">
                                    Tindakan ini akan menghapus data beserta berkas fisiknya dari server storage dan <strong class="text-rose-600 font-bold bg-rose-50 px-1 rounded">tidak dapat dibatalkan</strong>.
                                </p>
                                
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" 
                                            @click="openDeleteDocModal = false; targetFormId = ''" 
                                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all cursor-pointer">
                                        Urungkan
                                    </button>
                                    
                                    <button type="button" 
                                            @click="document.getElementById(targetFormId).submit(); openDeleteDocModal = false;" 
                                            class="px-6 py-3 bg-rose-600 text-white font-bold rounded-xl shadow-md hover:bg-rose-700 transition-all cursor-pointer flex items-center gap-2">
                                        <span>🗑️</span> Ya, Hapus Permanen
                                    </button>
                                </div>
                            </div>
                    </div>
                </div>

                {{-- ================= TAB 3: PRESTASI ================= --}}
                <div x-show="tabActive === 'prestasi'" 
                    x-data="{ openDeletePrestasiModal: false, targetPrestasiFormId: '' }" 
                    class="grid grid-cols-1 lg:grid-cols-3 gap-8" 
                    style="display: none;" 
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    
                    {{-- Form Tambah Prestasi --}}
                    <div class="bg-amber-50/40 p-6 border border-amber-100 rounded-2xl h-fit shadow-sm">
                        <h4 class="font-black text-amber-900 border-b border-amber-200/60 pb-3 mb-5 uppercase tracking-wide flex items-center gap-2">
                            <span>🏅</span> Tambah Prestasi Siswa
                        </h4>
                        
                        <form action="{{ route('kesiswaan.prestasi.store', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <input type="hidden" name="kelas_id" value="{{ $siswa->kelas_aktif_id ?? $siswa->riwayatKelas->last()->kelas_id ?? '' }}">

                            <div>
                                <label class="block text-sm text-amber-800 font-bold mb-1.5">Kategori / Jenis Prestasi <span class="text-rose-500">*</span></label>
                                <select name="jenis_prestasi" required class="w-full text-sm font-medium border-amber-200 rounded-xl shadow-sm bg-white focus:ring-amber-500 focus:border-amber-500 px-4 py-2.5">
                                    <option value="Akademik">🏆 Akademik</option>
                                    <option value="Non-Akademik">🎨 Non-Akademik</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm text-amber-800 font-bold mb-1.5">Nama / Judul Prestasi <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_prestasi" required placeholder="Contoh: Juara 1 Olimpiade Matematika" class="w-full text-sm font-medium border-amber-200 rounded-xl shadow-sm bg-white focus:ring-amber-500 focus:border-amber-500 px-4 py-2.5">
                            </div>
                            
                            <div>
                                <label class="block text-sm text-amber-800 font-bold mb-1.5">Tahun Perolehan <span class="text-rose-500">*</span></label>
                                <input type="number" name="tahun_prestasi" value="{{ date('Y') }}" required class="w-full text-sm font-medium border-amber-200 rounded-xl shadow-sm bg-white focus:ring-amber-500 focus:border-amber-500 px-4 py-2.5 text-center">
                            </div>
                            
                            <div>
                                <label class="block text-sm text-amber-800 font-bold mb-1.5">Sertifikat / Piagam (Opsional)</label>
                                <input type="file" name="file_sertifikat" class="w-full text-sm border border-amber-200 bg-white p-2 rounded-xl focus:ring-amber-500 focus:border-amber-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 transition-colors cursor-pointer text-gray-500">
                                <p class="text-[10px] text-amber-600 font-bold mt-2">Format: PDF/JPG/PNG. Maks 2MB.</p>
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer flex items-center justify-center gap-2">
                                    <span>🌟</span> Simpan Prestasi
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- List Prestasi --}}
                    <div class="lg:col-span-2 space-y-4">
                        <h4 class="font-black text-gray-900 border-b border-gray-100 pb-3 uppercase tracking-wide flex items-center gap-2">
                            <span>🏆</span> Daftar Prestasi & Pencapaian
                        </h4>
                        
                        <div class="grid grid-cols-1 gap-4 pt-2">
                            @forelse($siswa->prestasi as $pres)
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 border border-gray-200 rounded-2xl bg-white hover:border-amber-300 hover:shadow-md transition-all gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-amber-50 border border-amber-200 rounded-full flex items-center justify-center text-2xl shadow-inner shrink-0">
                                            {{ $pres->jenis_prestasi === 'Akademik' ? '🥇' : '🎨' }}
                                        </div>
                                        <div>
                                            <strong class="text-gray-900 font-black block text-base">{{ $pres->nama_prestasi }}</strong>
                                            <div class="flex items-center gap-2 mt-1.5">
                                                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100">{{ $pres->jenis_prestasi }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="text-xs font-bold text-gray-500">Tahun: {{ $pres->tahun_prestasi }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 w-full sm:w-auto">
                                        @if($pres->file_sertifikat)
                                            <a href="{{ asset('storage/' . $pres->file_sertifikat) }}" target="_blank" class="flex-1 sm:flex-none text-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl font-bold text-xs transition-colors shadow-sm flex items-center justify-center gap-1.5">
                                                <span>📜</span> Piagam
                                            </a>
                                        @else
                                            <span class="flex-1 sm:flex-none text-center px-4 py-2 bg-gray-50 text-gray-400 border border-dashed border-gray-200 rounded-xl font-bold text-xs">
                                                🚫 Tanpa Bukti
                                            </span>
                                        @endif
                                        
                                        <form id="delete-prestasi-form-{{ $pres->id }}" action="{{ route('kesiswaan.prestasi.destroy', $pres->id) }}" method="POST" class="m-0 shrink-0">
                                            @csrf @method('DELETE')
                                            <button type="button" 
                                                    @click="openDeletePrestasiModal = true; targetPrestasiFormId = 'delete-prestasi-form-{{ $pres->id }}'" 
                                                    class="p-2 bg-white hover:bg-rose-50 border border-gray-200 hover:border-rose-200 text-rose-500 rounded-xl font-bold cursor-pointer transition-colors shadow-sm" title="Hapus Prestasi">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="py-16 text-center border border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                                    <span class="text-5xl block mb-4">🤷‍♂️</span>
                                    <h4 class="font-bold text-gray-600 text-lg">Belum Ada Prestasi Terdata</h4>
                                    <p class="text-sm text-gray-400 mt-1">Yuk tambahkan pencapaian membanggakan dari siswa ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Modal Konfirmasi Hapus Prestasi --}}
                    <div x-show="openDeletePrestasiModal" 
                        class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                        style="display: none;"
                        x-transition>
                        
                        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-gray-100 text-center relative overflow-hidden" @click.away="openDeletePrestasiModal = false; targetPrestasiFormId = ''">
                            
                            <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto text-4xl border border-rose-100 shadow-inner mb-6">
                                ⚠️
                            </div>
                            
                            <h3 class="text-xl font-black text-gray-900 mb-3">Hapus Data Prestasi?</h3>
                            <p class="text-sm text-gray-500 leading-relaxed px-2 mb-8">
                                Apakah Anda yakin ingin menghapus catatan prestasi ini beserta bukti sertifikat pendukungnya (jika ada)?
                            </p>
                            
                            <div class="flex items-center justify-center gap-3">
                                <button type="button" 
                                        @click="openDeletePrestasiModal = false; targetPrestasiFormId = ''" 
                                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl cursor-pointer transition-colors">
                                    Urungkan
                                </button>
                                <button type="button" 
                                        @click="document.getElementById(targetPrestasiFormId).submit(); openDeletePrestasiModal = false;" 
                                        class="px-6 py-3 bg-rose-600 text-white font-bold rounded-xl shadow-md hover:bg-rose-700 cursor-pointer transition-colors flex items-center gap-2">
                                    <span>🗑️</span> Ya, Hapus Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= TAB 4: RIWAYAT & STATUS ================= --}}
                <div x-show="tabActive === 'riwayat'" class="grid grid-cols-1 lg:grid-cols-2 gap-10" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
    
                    {{-- Timeline Ruang Kelas --}}
                    <div class="space-y-5">
                        <h4 class="font-black text-gray-900 border-b border-gray-100 pb-3 uppercase tracking-wide flex items-center gap-2 text-lg">
                            <span>🏫</span> Histori Plotting Ruang Kelas
                        </h4>
                        
                        <div class="relative border-l-4 border-indigo-100 pl-6 space-y-6 ml-3 pt-4 pb-4">
                            @forelse($siswa->riwayatKelas as $rk)
                                <div class="relative">
                                    <span class="absolute -left-[35px] top-1 w-5 h-5 bg-indigo-600 rounded-full ring-4 ring-white shadow-sm"></span>
                                    <div class="bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:shadow-md transition-shadow">
                                        <div class="font-black text-gray-900 text-lg mb-1">{{ $rk->kelas->nama_kelas ?? 'Gantung/Tanpa Kelas' }}</div>
                                        <div class="flex flex-wrap items-center gap-2 mb-2.5">
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold text-[11px] rounded uppercase">Grade {{ $rk->tingkat }}</span>
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 font-bold text-[11px] rounded uppercase">Semester: {{ $rk->semester->nama_semester ?? '-' }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 font-medium leading-relaxed bg-gray-50 p-2 rounded-lg italic">
                                            "{{ $rk->keterangan }}"
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-gray-500 font-bold italic py-8 px-4 text-center border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50">
                                    Masa lalu bersih. Belum ada jejak mutasi / plotting kelas.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Timeline Status & Form Sakelar --}}
                    <div class="space-y-8">
                        
                        <div class="space-y-5">
                            <h4 class="font-black text-gray-900 border-b border-gray-100 pb-3 uppercase tracking-wide flex items-center gap-2 text-lg">
                                <span>📈</span> Log Operasional Akademik
                            </h4>
                            <div class="relative border-l-4 border-emerald-100 pl-6 space-y-6 ml-3 pt-4 pb-4">
                                @forelse($siswa->riwayatStatus as $rs)
                                    <div class="relative">
                                        <span class="absolute -left-[35px] top-1 w-5 h-5 bg-emerald-500 rounded-full ring-4 ring-white shadow-sm"></span>
                                        <div class="bg-white border border-gray-100 shadow-sm p-4 rounded-2xl hover:shadow-md transition-shadow">
                                            <div class="flex justify-between items-start mb-2">
                                                <span class="px-2.5 py-1 
                                                    {{ $rs->status == 'Aktif' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 
                                                      ($rs->status == 'Lulus' ? 'bg-blue-50 border-blue-200 text-blue-700' : 
                                                      ($rs->status == 'Mutasi' ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-rose-50 border-rose-200 text-rose-700')) }} 
                                                    border rounded-lg font-black text-[10px] uppercase tracking-wider">
                                                    {{ $rs->status }}
                                                </span>
                                                <span class="text-[10px] text-gray-400 font-bold">{{ $rs->created_at->translatedFormat('d M Y, H:i') }}</span>
                                            </div>
                                            
                                            @if(is_array($rs->metadata) && count($rs->metadata) > 0)
                                                <div class="mt-3 bg-gray-50 border border-gray-200 p-3 rounded-xl text-xs text-gray-700 font-medium space-y-1.5">
                                                    @if(isset($rs->metadata['alasan'])) 
                                                        <div class="flex items-start gap-1.5"><span>💬</span> <span>{{ $rs->metadata['alasan'] }}</span></div>
                                                    @endif
                                                    @if(isset($rs->metadata['sekolah_tujuan'])) 
                                                        <div class="flex items-start gap-1.5"><span>🏫</span> <span>Ke: {{ $rs->metadata['sekolah_tujuan'] }}</span></div>
                                                    @endif
                                                    @if(isset($rs->metadata['no_ijazah'])) 
                                                        <div class="flex items-start gap-1.5"><span>📜</span> <span>Ijazah: {{ $rs->metadata['no_ijazah'] }}</span></div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-gray-500 font-bold italic py-8 px-4 text-center border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50">
                                        Status siswa belum pernah diubah sejak didaftarkan.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Panel Eksekusi Perubahan Status -->
                        <div x-data="{ openConfirmModal: false }" class="p-6 md:p-8 bg-gradient-to-br from-rose-50 to-white border border-rose-100 rounded-[2rem] shadow-sm relative overflow-hidden">
                            
                            <div class="absolute -right-10 -top-10 text-8xl opacity-10 pointer-events-none">⚡</div>
                            
                            <h5 class="font-black text-rose-900 text-lg uppercase tracking-wide flex items-center gap-2 mb-6 relative z-10 border-b border-rose-200 pb-3">
                                <span>⚙️</span> Control Panel Status Akademik
                            </h5>
                            
                            <form id="statusForm" action="{{ route('kesiswaan.siswa.updateStatus', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 relative z-10">
                                @csrf @method('PUT')
                                <input type="hidden" name="semester_id" value="{{ $semester_aktif->id ?? '' }}">
                                
                                <div>
                                    <label class="block text-sm text-gray-800 font-black mb-2">Ganti Status Operasional Siswa Menjadi:</label>
                                    <select name="status_siswa" x-model="statusSelected" class="w-full text-base font-bold border-rose-200 rounded-xl shadow-sm text-gray-800 bg-white focus:ring-rose-500 focus:border-rose-500 py-3">
                                        <option value="Aktif">🟢 Aktif (Dikembalikan ke Proses KBM)</option>
                                        <option value="Mutasi">🟡 Mutasi Keluar Pindah Sekolah</option>
                                        <option value="Keluar">🔴 Dikeluarkan (Drop Out)</option>
                                        <option value="Lulus">🔵 Penetapan Kelulusan Alumni</option>
                                    </select>
                                </div>

                                {{-- Field Dinamis Berdasarkan Pilihan --}}
                                <div class="bg-white p-5 rounded-2xl border border-rose-100 shadow-sm space-y-5 transition-all">
                                    
                                    {{-- Aktif --}}
                                    <div x-show="statusSelected === 'Aktif'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="space-y-3">
                                        <label class="block text-xs text-gray-600 font-bold mb-1">Berikan Catatan Pengaktifan (Opsional)</label>
                                        <input type="text" name="alasan_aktif" :required="statusSelected === 'Aktif'" placeholder="Ketik alasan mengapa diaktifkan kembali..." class="w-full text-sm font-medium border-gray-300 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:ring-rose-500 focus:border-rose-500 py-2.5">
                                    </div>

                                    {{-- Mutasi --}}
                                    <div x-show="statusSelected === 'Mutasi'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs text-gray-600 font-bold mb-1.5">Nama Sekolah Tujuan <span class="text-rose-500">*</span></label>
                                                <input type="text" name="sekolah_tujuan" :required="statusSelected === 'Mutasi'" placeholder="Contoh: SMPN 1 Jakarta" class="w-full text-sm font-medium border-gray-300 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:ring-rose-500 focus:border-rose-500 py-2.5">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 font-bold mb-1.5">Alasan Utama Mutasi <span class="text-rose-500">*</span></label>
                                                <input type="text" name="alasan_mutasi" :required="statusSelected === 'Mutasi'" placeholder="Contoh: Ikut orang tua dinas" class="w-full text-sm font-medium border-gray-300 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:ring-rose-500 focus:border-rose-500 py-2.5">
                                            </div>
                                        </div>
                                        <div class="p-4 bg-amber-50/50 border border-amber-100 rounded-xl">
                                            <label class="block text-xs text-amber-900 font-black mb-1.5 flex items-center gap-1"><span>📄</span> Upload Bukti Surat Mutasi Resmi <span class="text-rose-500">*</span></label>
                                            <input type="file" name="file_surat_mutasi" :required="statusSelected === 'Mutasi'" class="w-full text-xs font-bold border border-amber-200 bg-white p-2 rounded-xl focus:ring-amber-500 focus:border-amber-500 cursor-pointer file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-amber-100 file:text-amber-800">
                                        </div>
                                    </div>

                                    {{-- Keluar --}}
                                    <div x-show="statusSelected === 'Keluar'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="space-y-4">
                                        <div>
                                            <label class="block text-xs text-gray-600 font-bold mb-1.5">Alasan/Pelanggaran (Penyebab DO) <span class="text-rose-500">*</span></label>
                                            <input type="text" name="alasan_keluar" :required="statusSelected === 'Keluar'" placeholder="Contoh: Melanggar aturan berat" class="w-full text-sm font-medium border-gray-300 rounded-xl shadow-sm bg-gray-50 focus:bg-white focus:ring-rose-500 focus:border-rose-500 py-2.5">
                                        </div>
                                        <div class="p-4 bg-rose-50/50 border border-rose-100 rounded-xl">
                                            <label class="block text-xs text-rose-900 font-black mb-1.5 flex items-center gap-1"><span>📄</span> Upload Surat Keputusan Keluar (DO) <span class="text-rose-500">*</span></label>
                                            <input type="file" name="file_surat_keluar" :required="statusSelected === 'Keluar'" class="w-full text-xs font-bold border border-rose-200 bg-white p-2 rounded-xl focus:ring-rose-500 focus:border-rose-500 cursor-pointer file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-rose-100 file:text-rose-800">
                                        </div>
                                    </div>

                                    {{-- Lulus --}}
                                    <div x-show="statusSelected === 'Lulus'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" class="space-y-4">
                                        
                                        <div class="p-4 bg-blue-50/30 border border-blue-100 rounded-xl space-y-4">
                                            <h6 class="text-xs font-black text-blue-800 uppercase tracking-widest border-b border-blue-200/50 pb-2">Dokumen Wajib Kelulusan</h6>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-[11px] text-gray-600 font-bold mb-1">Nomor SKL Resmi <span class="text-rose-500">*</span></label>
                                                    <input type="text" name="no_surat_kelulusan" :required="statusSelected === 'Lulus'" placeholder="Input Nomor SKL" class="w-full text-xs font-medium border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] text-gray-600 font-bold mb-1">File Berkas SKL <span class="text-rose-500">*</span></label>
                                                    <input type="file" name="file_surat_kelulusan" :required="statusSelected === 'Lulus'" class="w-full text-[10px] font-bold border border-gray-300 p-1.5 rounded-lg bg-white cursor-pointer file:rounded file:border-0 file:bg-blue-100 file:text-blue-700">
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-[11px] text-gray-600 font-bold mb-1">Nomor SKKB <span class="text-rose-500">*</span></label>
                                                    <input type="text" name="no_skkb" :required="statusSelected === 'Lulus'" placeholder="Input Nomor SKKB" class="w-full text-xs font-medium border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] text-gray-600 font-bold mb-1">File Berkas SKKB <span class="text-rose-500">*</span></label>
                                                    <input type="file" name="file_skkb" :required="statusSelected === 'Lulus'" class="w-full text-[10px] font-bold border border-gray-300 p-1.5 rounded-lg bg-white cursor-pointer file:rounded file:border-0 file:bg-blue-100 file:text-blue-700">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-4">
                                            <h6 class="text-xs font-black text-gray-500 uppercase tracking-widest border-b border-gray-200/80 pb-2 flex items-center gap-1.5">
                                                <span>⏳</span> Dokumen Menyusul (Opsional)
                                            </h6>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-[11px] text-gray-600 font-bold mb-1">Nomor Blangko Ijazah</label>
                                                    <input type="text" name="no_ijazah" placeholder="DN-01/M-SM/..." class="w-full text-xs font-medium border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] text-gray-600 font-bold mb-1">Scan Ijazah Asli</label>
                                                    <input type="file" name="file_ijazah" class="w-full text-[10px] font-bold border border-gray-300 p-1.5 rounded-lg bg-white cursor-pointer file:rounded file:border-0 file:bg-gray-200 file:text-gray-700">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-[11px] text-gray-600 font-bold mb-1">No. Transkrip Nilai/SKHUN</label>
                                                    <input type="text" name="no_transkrip" placeholder="Input Nomor Transkrip..." class="w-full text-xs font-medium border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] text-gray-600 font-bold mb-1">Scan Transkrip Nilai</label>
                                                    <input type="file" name="file_transkrip" class="w-full text-[10px] font-bold border border-gray-300 p-1.5 rounded-lg bg-white cursor-pointer file:rounded file:border-0 file:bg-gray-200 file:text-gray-700">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" 
                                        @click="openConfirmModal = true"
                                        class="w-full flex items-center justify-center gap-2 py-4 px-6 bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-700 hover:to-rose-600 text-white font-black text-sm rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 cursor-pointer">
                                    <span>⚙️</span> Eksekusi Status Pembinaan Sekarang
                                </button>
                            </form>

                            {{-- Modal Konfirmasi Eksekusi Status --}}
                            <div x-show="openConfirmModal" 
                                class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                                style="display: none;"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95">
                                
                                <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-gray-100 text-center relative overflow-hidden" @click.away="openConfirmModal = false">
                                    
                                    <div class="w-24 h-24 bg-rose-50 rounded-full flex items-center justify-center mx-auto text-5xl border border-rose-100 shadow-inner mb-6">
                                        ⚙️
                                    </div>
                                    
                                    <h3 class="text-xl font-black text-gray-900 mb-3 tracking-tight">Konfirmasi Perubahan</h3>
                                    
                                    <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl mb-6">
                                        <p class="text-sm text-gray-600 leading-relaxed font-medium">
                                            Status <strong class="text-gray-900">{{ $siswa->nama_lengkap }}</strong> akan diubah secara radikal menjadi <strong class="text-rose-600 uppercase tracking-widest text-lg block mt-2 mb-2" x-text="statusSelected"></strong>
                                        </p>
                                        <p class="text-xs text-rose-500 italic font-bold">Pastikan semua data dan lampiran dokumen penunjang telah diisi dengan benar sebelum melanjutkan.</p>
                                    </div>
                                    
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                        <button type="button" 
                                                @click="openConfirmModal = false" 
                                                class="w-full sm:w-auto px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all cursor-pointer">
                                            Tinjau Kembali
                                        </button>
                                        
                                        <button type="button" 
                                                @click="document.getElementById('statusForm').submit(); openConfirmModal = false;" 
                                                class="w-full sm:w-auto px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center gap-2">
                                            <span>⚡</span> Setujui & Eksekusi
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