<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('kepegawaian.pegawai.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-colors" title="Kembali ke Daftar">
                    <span class="text-xl font-bold">←</span>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                        <span class="text-3xl">🪪</span> Profil Lengkap Pegawai
                    </h2>
                    <p class="text-sm font-medium text-gray-500 mt-1">Pengelolaan data biodata, dokumen, dan riwayat karir.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="{ 
        activeTab: 'biodata',
        openMutasi: false,
        openPensiun: false,
        openDelete: false,

        // Delete Modal States (Melayani semua hapus riwayat)
        deleteActionUrl: '',
        deleteTargetName: '',
        deleteMessage: '',

        initDelete(actionUrl, targetName, message) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = targetName;
            this.deleteMessage = message;
            this.openDelete = true;
        }
    }" class="py-10 bg-slate-50 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Alerts / Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-lg mb-2">
                        <span>⚠️</span> Terdapat kesalahan input:
                    </div>
                    <ul class="list-disc pl-6 space-y-1 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Kartu Header Profil --}}
            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-indigo-900/5 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                
                {{-- Aksen Latar --}}
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

                <div class="flex items-center gap-6 relative z-10">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-200 rounded-[2rem] blur-md opacity-50 group-hover:opacity-100 transition-opacity"></div>
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-50 to-white text-indigo-700 rounded-[2rem] flex items-center justify-center text-4xl font-black border-2 border-white shadow-md relative z-10">
                            {{ strtoupper(substr($pegawai->nama_lengkap, 0, 1)) }}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">{{ $pegawai->nama_lengkap }}</h3>
                        <div class="flex flex-wrap items-center gap-3 mt-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 border border-gray-200 text-gray-700 text-xs font-bold rounded-lg shadow-sm">
                                🆔 NIP: {{ $pegawai->nip ?? 'Tidak ada' }}
                            </span>
                            
                            @if($pegawai->status_keaktifan === 'Aktif')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-black uppercase tracking-wider rounded-lg shadow-sm">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Aktif
                                </span>
                            @elseif($pegawai->status_keaktifan === 'Mutasi')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-black uppercase tracking-wider rounded-lg shadow-sm">
                                    🔄 Mutasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-black uppercase tracking-wider rounded-lg shadow-sm">
                                    🎓 Pensiun
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($pegawai->status_keaktifan === 'Aktif')
                    <div class="flex flex-wrap gap-3 relative z-10 w-full md:w-auto">
                        <button @click="openMutasi = true" class="flex-1 md:flex-none px-6 py-3 bg-white border border-gray-200 hover:border-amber-300 hover:bg-amber-50 text-gray-700 hover:text-amber-700 text-sm font-bold rounded-xl shadow-sm transition-colors cursor-pointer flex items-center justify-center gap-2">
                            <span>🔄</span> Pindah/Mutasi
                        </button>
                        <button @click="openPensiun = true" class="flex-1 md:flex-none px-6 py-3 bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-700 hover:to-rose-600 text-white text-sm font-bold rounded-xl shadow-md transition-transform transform hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2">
                            <span>🎓</span> Pensiunkan
                        </button>
                    </div>
                @endif
            </div>

            {{-- 🗂️ Navigasi Tab Premium (Segmented Control) --}}
            <div class="bg-gray-200/60 p-1.5 rounded-2xl shadow-inner flex overflow-x-auto w-full">
                <button @click="activeTab = 'biodata'"
                    :class="activeTab === 'biodata' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 px-4 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">📝</span> Profil Data
                </button>
                <button @click="activeTab = 'dokumen'"
                    :class="activeTab === 'dokumen' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 px-4 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">📁</span> Berkas Arsip ({{ $pegawai->dokumen->count() }})
                </button>
                <button @click="activeTab = 'kgb'"
                    :class="activeTab === 'kgb' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 px-4 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">💵</span> Riwayat KGB
                </button>
                <button @click="activeTab = 'pangkat'"
                    :class="activeTab === 'pangkat' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 px-4 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">🎖️</span> Kepangkatan
                </button>
            </div>

            {{-- Area Konten Tab --}}
            <div class="bg-white p-8 rounded-3xl shadow-xl sm:rounded-[2rem] border border-gray-100 min-h-[400px]">
                
                {{-- TAB 1: BIODATA UTAMA --}}
                <div x-show="activeTab === 'biodata'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-xl">✍️</div>
                        <div>
                            <h4 class="text-lg font-black text-gray-900">Ubah Data Pokok Pegawai</h4>
                            <p class="text-xs text-gray-500 font-medium">Perbarui biodata dan identitas utama pada sistem.</p>
                        </div>
                    </div>

                    <form action="{{ route('kepegawaian.pegawai.update', $pegawai->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap beserta Gelar Akademik <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_lengkap" value="{{ $pegawai->nama_lengkap }}" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Email Akun</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">📧</span>
                                    <input type="email" name="email" value="{{ $pegawai->email }}" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm bg-gray-50 py-3 pl-10 pr-4">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select name="jenis_kelamin" required class="w-full text-sm font-semibold rounded-xl border-gray-300 shadow-sm bg-white px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Laki-Laki" {{ $pegawai->jenis_kelamin == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ $pegawai->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 p-5 bg-indigo-50/30 border border-indigo-100 rounded-2xl grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nomor Induk Pegawai (NIP)</label>
                                    <input type="text" name="nip" value="{{ $pegawai->nip }}" placeholder="Biarkan kosong jika Honorer" class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm bg-white px-4 py-3">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nomor Unik Pendidik (NUPTK)</label>
                                    <input type="text" name="nuptk" value="{{ $pegawai->nuptk }}" placeholder="Biarkan kosong jika belum ada" class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm bg-white px-4 py-3">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Penugasan Pokok (PTK) <span class="text-rose-500">*</span></label>
                                <select name="jenis_ptk" required class="w-full text-sm font-semibold rounded-xl border-gray-300 shadow-sm bg-white px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Guru" {{ $pegawai->jenis_ptk == 'Guru' ? 'selected' : '' }}>Tenaga Pendidik / Guru</option>
                                    <option value="Tenaga Kependidikan" {{ $pegawai->jenis_ptk == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan (Staff/TU)</option>
                                    <option value="Kepala Sekolah" {{ $pegawai->jenis_ptk == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Status Pegawai <span class="text-rose-500">*</span></label>
                                <select name="status_pegawai" required class="w-full text-sm font-semibold rounded-xl border-gray-300 shadow-sm bg-white px-4 py-3 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="HONORER" {{ $pegawai->status_pegawai == 'HONORER' ? 'selected' : '' }}>Pegawai GTY / Honorer</option>
                                    <option value="PNS" {{ $pegawai->status_pegawai == 'PNS' ? 'selected' : '' }}>Pegawai Negeri Sipil (PNS)</option>
                                    <option value="PPPK" {{ $pegawai->status_pegawai == 'PPPK' ? 'selected' : '' }}>ASN - PPPK</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pangkat / Golongan Saat Ini</label>
                                <input type="text" name="pangkat_golongan" value="{{ $pegawai->pangkat_golongan }}" placeholder="Misal: Penata Muda Tk. I / IIIb" class="w-full text-sm font-semibold rounded-xl border-gray-300 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                        </div>

                        {{-- Panel Informasi Tambahan Jika Non-Aktif --}}
                        @if($pegawai->status_keaktifan !== 'Aktif')
                            <div class="mt-8 p-6 bg-amber-50 rounded-2xl border-2 border-amber-100 shadow-inner flex flex-col md:flex-row gap-6 items-start md:items-center">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl shadow-sm border border-amber-200 shrink-0">
                                    {{ $pegawai->status_keaktifan === 'Mutasi' ? '🔄' : '🎓' }}
                                </div>
                                <div class="flex-1 space-y-2">
                                    <h5 class="font-black text-amber-900 text-lg uppercase tracking-wide border-b border-amber-200 pb-2">Informasi Status {{ $pegawai->status_keaktifan }}</h5>
                                    
                                    <div class="text-amber-900 text-sm space-y-1">
                                        @if($pegawai->status_keaktifan === 'Mutasi')
                                            <p><span class="font-bold opacity-75">Tanggal Resmi:</span> {{ $pegawai->tanggal_mutasi?->format('d F Y') }}</p>
                                            <p><span class="font-bold opacity-75">Tujuan Instansi:</span> {{ $pegawai->sekolah_tujuan }}</p>
                                            <p><span class="font-bold opacity-75">Keterangan:</span> {{ $pegawai->alasan_mutasi }}</p>
                                            <div class="pt-2">
                                                <a href="{{ asset('storage/' . $pegawai->file_surat_mutasi) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-amber-100 text-amber-800 font-bold rounded-lg border border-amber-300 shadow-sm transition-colors text-xs">
                                                    📄 Lihat Berkas Keputusan Mutasi ↗️
                                                </a>
                                            </div>
                                        @else
                                            <p><span class="font-bold opacity-75">Tanggal Penetapan:</span> {{ $pegawai->tanggal_pensiun?->format('d F Y') }}</p>
                                            <div class="pt-2">
                                                <a href="{{ asset('storage/' . $pegawai->file_surat_pensiun) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-amber-100 text-amber-800 font-bold rounded-lg border border-amber-300 shadow-sm transition-colors text-xs">
                                                    📄 Lihat Salinan SK Pensiun ↗️
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="pt-6 border-t border-gray-100 flex justify-end">
                            <button type="submit" class="px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 cursor-pointer">
                                💾 Simpan Perubahan Biodata
                            </button>
                        </div>
                    </form>
                </div>

                {{-- TAB 2: DOKUMEN & BERKAS ARSIP --}}
                <div x-show="activeTab === 'dokumen'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex flex-col lg:flex-row gap-8">
                        
                        {{-- Form Upload (Kiri) --}}
                        <div class="lg:w-1/3 bg-gray-50/80 p-6 rounded-3xl border border-gray-100 shadow-inner h-fit">
                            <div class="flex items-center gap-3 mb-5 border-b border-gray-200 pb-3">
                                <span class="text-xl">➕</span>
                                <h5 class="text-sm font-black text-gray-900 uppercase tracking-wide">Unggah Berkas Baru</h5>
                            </div>

                            <form action="{{ route('kepegawaian.dokumen-pegawai.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Klasifikasi Dokumen <span class="text-rose-500">*</span></label>
                                    <input type="text" name="jenis_dokumen" required placeholder="Misal: Ijazah, SK CPNS, KTP" class="w-full text-sm font-medium rounded-xl border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Spesifik File <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nama_dokumen" required placeholder="Misal: Ijazah S1 Pendidikan Matematika" class="w-full text-sm font-medium rounded-xl border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Tahun Penerbitan <span class="text-rose-500">*</span></label>
                                    <input type="number" name="tahun_dokumen" value="{{ date('Y') }}" required class="w-full text-sm font-medium rounded-xl border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Pilih Berkas Lampiran <span class="text-rose-500">*</span></label>
                                    <input type="file" name="file_dokumen" required class="w-full text-xs text-gray-500 bg-white border border-gray-300 rounded-xl cursor-pointer p-1.5 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 shadow-sm">
                                    <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Ekstensi valid: PDF, JPG, PNG (Maks 5MB)</p>
                                </div>
                                <div class="pt-2">
                                    <button type="submit" class="w-full py-3 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-md transition-transform transform hover:-translate-y-0.5 cursor-pointer">
                                        🚀 Simpan & Unggah
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        {{-- Tabel Data (Kanan) --}}
                        <div class="lg:w-2/3 overflow-x-auto bg-white border border-gray-100 rounded-3xl shadow-sm">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                        <th class="p-5 pl-6">Detail Berkas</th>
                                        <th class="p-5 text-center w-24">Tahun</th>
                                        <th class="p-5 pr-6 text-center w-48">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($pegawai->dokumen as $dok)
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="p-5 pl-6 align-middle">
                                                <div class="font-black text-gray-900 text-base flex items-center gap-2">
                                                    📄 {{ $dok->nama_dokumen }}
                                                </div>
                                                <div class="inline-block mt-1.5 px-2.5 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-md border border-gray-200">
                                                    Kategori: {{ $dok->jenis_dokumen }}
                                                </div>
                                            </td>
                                            <td class="p-5 text-center font-bold text-gray-700 align-middle">
                                                {{ $dok->tahun_dokumen }}
                                            </td>
                                            <td class="p-5 pr-6 text-center align-middle">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ asset('storage/' . $dok->file_dokumen) }}" target="_blank" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-100 text-blue-700 font-bold rounded-lg text-[11px] transition-colors shadow-sm cursor-pointer" title="Lihat Berkas Lengkap">
                                                        👁️ Lihat
                                                    </a>
                                                    
                                                    <button type="button" @click="initDelete('{{ route('kepegawaian.dokumen-pegawai.destroy', $dok->id) }}', 'Dokumen {{ addslashes($dok->nama_dokumen) }}', 'Apakah Anda yakin ingin menghapus file arsip berkas dokumen ini secara permanen? Data tidak dapat dipulihkan.')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 font-bold rounded-lg text-[11px] transition-colors shadow-sm cursor-pointer" title="Hapus Permanen">
                                                        🗑️
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                                <span class="text-5xl block mb-4">📭</span>
                                                <p class="text-base font-bold text-gray-500">Koleksi berkas masih kosong, belum ada dokumen terunggah.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: RIWAYAT K.G.B --}}
                <div x-show="activeTab === 'kgb'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex flex-col lg:flex-row gap-8">
                        
                        {{-- Form Kiri --}}
                        <div class="lg:w-1/3 bg-gray-50/80 p-6 rounded-3xl border border-gray-100 shadow-inner h-fit">
                            <div class="flex items-center gap-3 mb-5 border-b border-gray-200 pb-3">
                                <span class="text-xl">💵</span>
                                <h5 class="text-sm font-black text-gray-900 uppercase tracking-wide">Catat Riwayat KGB Baru</h5>
                            </div>

                            <form action="{{ route('kepegawaian.kgb.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Nomor Surat Keputusan (SK) <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nomor_sk_kgb" required placeholder="Contoh: 821/KGB/2026/01" class="w-full text-sm font-medium rounded-xl border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Ditetapkan SK <span class="text-rose-500">*</span></label>
                                    <input type="date" name="tanggal_sk_kgb" required class="w-full text-sm font-medium rounded-xl border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Besaran Gaji Pokok Baru (Rp) <span class="text-rose-500">*</span></label>
                                    <input type="number" name="nominal_gaji_baru" required placeholder="Input nominal (tanpa titik koma)" class="w-full text-sm font-medium rounded-xl border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="pt-2">
                                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-md transition-transform transform hover:-translate-y-0.5 cursor-pointer">
                                        💾 Simpan Riwayat Baru
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        {{-- Tabel Kanan --}}
                        <div class="lg:w-2/3 overflow-x-auto bg-white border border-gray-100 rounded-3xl shadow-sm">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                        <th class="p-5 pl-6">Dasar Nomor SK</th>
                                        <th class="p-5 text-center w-36">Tgl Penetapan</th>
                                        <th class="p-5 text-right w-48">Peningkatan Gaji (Rp)</th>
                                        <th class="p-5 pr-6 text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($pegawai->kgb as $k)
                                        <tr class="hover:bg-emerald-50/30 transition-colors">
                                            <td class="p-5 pl-6 align-middle font-black text-gray-900">
                                                📄 {{ $k->nomor_sk_kgb }}
                                            </td>
                                            <td class="p-5 text-center text-gray-600 font-medium align-middle">
                                                {{ $k->tanggal_sk_kgb?->format('d M Y') }}
                                            </td>
                                            <td class="p-5 text-right align-middle">
                                                <span class="inline-block px-3 py-1.5 bg-emerald-50 text-emerald-700 text-sm font-black rounded-lg border border-emerald-200">
                                                    Rp {{ number_format($k->nominal_gaji_baru, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="p-5 pr-6 text-center align-middle">
                                                <button type="button" @click="initDelete('{{ route('kepegawaian.kgb.destroy', $k->id) }}', 'SK KGB: {{ addslashes($k->nomor_sk_kgb) }}', 'Menghapus riwayat ini akan melenyapkan jejak kenaikan gaji pegawai pada periode ini secara permanen.')" class="p-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 font-bold rounded-xl text-xs transition-colors shadow-sm cursor-pointer" title="Hapus Riwayat">
                                                    🗑️
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                                <span class="text-5xl block mb-4">💳</span>
                                                <p class="text-base font-bold text-gray-500">Belum ada rekam jejak Kenaikan Gaji Berkala (KGB).</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TAB 4: KEPANGKATAN --}}
                <div x-show="activeTab === 'pangkat'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex flex-col lg:flex-row gap-8">
                        
                        {{-- Form Kiri --}}
                        <div class="lg:w-1/3 bg-gray-50/80 p-6 rounded-3xl border border-gray-100 shadow-inner h-fit">
                            <div class="flex items-center gap-3 mb-5 border-b border-gray-200 pb-3">
                                <span class="text-xl">🎖️</span>
                                <h5 class="text-sm font-black text-gray-900 uppercase tracking-wide">Input Mutasi Pangkat</h5>
                            </div>

                            <form action="{{ route('kepegawaian.kenaikan-pangkat.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">No. SK Kenaikan Pangkat <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nomor_sk_kp" required placeholder="Contoh: SK/004/2026/II" class="w-full text-sm font-medium rounded-xl border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal SK Ditetapkan <span class="text-rose-500">*</span></label>
                                    <input type="date" name="tanggal_sk_kp" required class="w-full text-sm font-medium rounded-xl border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Pangkat & Golongan Baru <span class="text-rose-500">*</span></label>
                                    <input type="text" name="pangkat_golongan_baru" required placeholder="Contoh: Penata Muda Tk. I / IIIb" class="w-full text-sm font-medium rounded-xl border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="pt-2">
                                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md transition-transform transform hover:-translate-y-0.5 cursor-pointer">
                                        ⚡ Simpan & Validasi
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        {{-- Tabel Kanan --}}
                        <div class="lg:w-2/3 overflow-x-auto bg-white border border-gray-100 rounded-3xl shadow-sm">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                        <th class="p-5 pl-6">Referensi SK</th>
                                        <th class="p-5 text-center w-36">Tgl Berlaku</th>
                                        <th class="p-5 text-center w-56">Penetapan Pangkat/Golongan</th>
                                        <th class="p-5 pr-6 text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($pegawai->kenaikanPangkat as $pkt)
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="p-5 pl-6 align-middle font-black text-gray-900">
                                                🎖️ {{ $pkt->nomor_sk_kp }}
                                            </td>
                                            <td class="p-5 text-center text-gray-600 font-medium align-middle">
                                                {{ $pkt->tanggal_sk_kp?->format('d M Y') }}
                                            </td>
                                            <td class="p-5 text-center align-middle">
                                                <span class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-800 text-xs font-black rounded-lg border border-indigo-200">
                                                    {{ $pkt->pangkat_golongan_baru }}
                                                </span>
                                            </td>
                                            <td class="p-5 pr-6 text-center align-middle">
                                                <button type="button" @click="initDelete('{{ route('kepegawaian.kenaikan-pangkat.destroy', $pkt->id) }}', 'Pangkat: {{ addslashes($pkt->pangkat_golongan_baru) }}', 'Sistem akan otomatis mengatur ulang (rollback) data kepangkatan utama pegawai pada menu Biodata ke riwayat terbaru sebelumnya.')" class="p-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 font-bold rounded-xl text-xs transition-colors shadow-sm cursor-pointer" title="Hapus Riwayat">
                                                    🗑️
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                                <span class="text-5xl block mb-4">🏆</span>
                                                <p class="text-base font-bold text-gray-500">Arsip riwayat kenaikan pangkat masih kosong.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= MODAL: PROSES MUTASI ================= --}}
        <div x-show="openMutasi" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden" @click.away="openMutasi = false">
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">🔄 Keputusan Mutasi</h3>
                    <button type="button" @click="openMutasi = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold transition-colors cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('kepegawaian.pegawai.mutasi', $pegawai->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-8 space-y-5">
                        
                        <div class="p-4 bg-amber-50/50 border border-amber-100 rounded-xl">
                            <p class="text-xs text-amber-700 font-medium">Data pegawai akan dikunci dari form aktif, namun arsip tetap tersimpan permanen di database.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Eksekusi Mutasi <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_mutasi" value="{{ date('Y-m-d') }}" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:ring-amber-500 focus:border-amber-500 bg-gray-50 px-4 py-3 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tujuan Unit/Instansi Baru <span class="text-rose-500">*</span></label>
                            <input type="text" name="sekolah_tujuan" required placeholder="Misal: SMA Negeri 1 Bandung" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:ring-amber-500 focus:border-amber-500 bg-gray-50 px-4 py-3 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Penjelasan / Alasan Dasar <span class="text-rose-500">*</span></label>
                            <textarea name="alasan_mutasi" required rows="2" placeholder="Tuliskan keterangan SK dari Disdik..." class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:ring-amber-500 focus:border-amber-500 bg-gray-50 px-4 py-3 shadow-sm"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Lampirkan PDF Keputusan <span class="text-rose-500">*</span></label>
                            <input type="file" name="file_surat_mutasi" required class="w-full text-sm text-gray-500 bg-white border border-gray-300 rounded-xl cursor-pointer p-1.5 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 shadow-sm">
                        </div>
                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openMutasi = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-md cursor-pointer">Jalankan Mutasi</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: EKSEKUSI PENSIUN ================= --}}
        <div x-show="openPensiun" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden" @click.away="openPensiun = false">
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">🎓 Formulir Pensiun/Purna Tugas</h3>
                    <button type="button" @click="openPensiun = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold transition-colors cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('kepegawaian.pegawai.pensiun', $pegawai->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-8 space-y-5">
                        
                        <div class="p-4 bg-rose-50/50 border border-rose-100 rounded-xl text-center">
                            <span class="text-3xl block mb-2">🎉</span>
                            <p class="text-xs text-rose-700 font-semibold leading-relaxed">Merupakan sebuah kehormatan mencatat masa purna tugas. Sistem akan menyimpan sejarah kepangkatannya sebagai arsip emas (Gold Archive).</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Terhitung Mulai Purna Tugas <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_pensiun" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:ring-rose-500 focus:border-rose-500 bg-gray-50 px-4 py-3 shadow-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Unggah Salinan SK Pensiun BKN <span class="text-rose-500">*</span></label>
                            <input type="file" name="file_surat_pensiun" required class="w-full text-sm text-gray-500 bg-white border border-gray-300 rounded-xl cursor-pointer p-1.5 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-rose-100 file:text-rose-800 hover:file:bg-rose-200 shadow-sm">
                            <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Ekstensi valid: PDF, JPG. Max 2MB.</p>
                        </div>
                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openPensiun = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer">Sahkan Pensiun</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: KONFIRMASI HAPUS DINAMIS ================= --}}
        <div x-show="openDelete" class="fixed inset-0 z-[200] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl p-8 text-center relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 border border-rose-100 mb-6">
                    <span class="text-4xl">⚠️</span>
                </div>
                
                <div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3">Tindakan Destruktif!</h3>
                    <p class="text-sm text-gray-600 mb-4 px-2">
                        Target Sistem: <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded" x-text="deleteTargetName"></span>
                    </p>
                    <p class="text-[11.5px] font-medium text-rose-600 bg-rose-50/50 border border-rose-100 p-3 rounded-xl mb-8 leading-relaxed" x-text="deleteMessage"></p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none">
                        Urungkan
                    </button>
                    <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none">
                        Ya, Hapus Permanen
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>