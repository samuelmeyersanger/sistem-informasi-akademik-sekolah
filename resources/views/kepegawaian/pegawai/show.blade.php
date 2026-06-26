<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Profil: {{ $pegawai->nama_lengkap }}
            </h2>
            <a href="{{ route('kepegawaian.pegawai.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                ⬅️ Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div x-data="{ 
        activeTab: 'biodata',
        openMutasi: false,
        openPensiun: false,
        openDelete: false,

        // Delete Modal States (Untuk melayani semua hapus riwayat)
        deleteActionUrl: '',
        deleteTargetName: '',
        deleteMessage: '',

        initDelete(actionUrl, targetName, message) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = targetName;
            this.deleteMessage = message;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                    <p class="font-bold text-xs mb-1">⚠️ Terdapat kesalahan input:</p>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl font-bold border border-indigo-100 shadow-inner">
                        {{ strtoupper(substr($pegawai->nama_lengkap, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $pegawai->nama_lengkap }}</h3>
                        <p class="text-xs text-gray-500">NIP: {{ $pegawai->nip ?? '-' }} | Status Keaktifan: 
                            <span class="font-bold uppercase {{ $pegawai->status_keaktifan === 'Aktif' ? 'text-green-600' : ($pegawai->status_keaktifan === 'Mutasi' ? 'text-amber-500' : 'text-rose-600') }}">
                                {{ $pegawai->status_keaktifan }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($pegawai->status_keaktifan === 'Aktif')
                    <div class="flex gap-2">
                        <button @click="openMutasi = true" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-black text-xs font-semibold rounded-lg shadow-sm cursor-pointer">🔄 Proses Mutasi</button>
                        <button @click="openPensiun = true" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-black text-xs font-semibold rounded-lg shadow-sm cursor-pointer">🎓 Pensiunkan</button>
                    </div>
                @endif
            </div>

            <div class="flex border-b border-gray-200 bg-white px-4 rounded-xl shadow-sm overflow-x-auto gap-2">
                <button @click="activeTab = 'biodata'" :class="activeTab === 'biodata' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 px-3 border-b-2 text-xs uppercase tracking-wider cursor-pointer transition-all">📝 Biodata & Edit</button>
                <button @click="activeTab = 'dokumen'" :class="activeTab === 'dokumen' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 px-3 border-b-2 text-xs uppercase tracking-wider cursor-pointer transition-all">📁 Dokumen & Berkas ({{ $pegawai->dokumen->count() }})</button>
                <button @click="activeTab = 'kgb'" :class="activeTab === 'kgb' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 px-3 border-b-2 text-xs uppercase tracking-wider cursor-pointer transition-all">💵 Kenaikan Gaji Berkala (KGB)</button>
                <button @click="activeTab = 'pangkat'" :class="activeTab === 'pangkat' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 px-3 border-b-2 text-xs uppercase tracking-wider cursor-pointer transition-all">🎖️ Riwayat Pangkat</button>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 min-h-[300px]">
                
                <div x-show="activeTab === 'biodata'" x-transition>
                    <h4 class="text-sm font-bold text-gray-900 border-b pb-2 mb-4">Ubah Data Pokok Pegawai</h4>
                    <form action="{{ route('kepegawaian.pegawai.update', $pegawai->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" value="{{ $pegawai->nama_lengkap }}" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                                <input type="email" name="email" value="{{ $pegawai->email }}" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                                    <option value="Laki-Laki" {{ $pegawai->jenis_kelamin == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ $pegawai->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis PTK *</label>
                                <select name="jenis_ptk" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                                    <option value="Guru" {{ $pegawai->jenis_ptk == 'Guru' ? 'selected' : '' }}>Guru</option>
                                    <option value="Tenaga Kependidikan" {{ $pegawai->jenis_ptk == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                                    <option value="Kepala Sekolah" {{ $pegawai->jenis_ptk == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">NIP</label>
                                <input type="text" name="nip" value="{{ $pegawai->nip }}" class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">NUPTK</label>
                                <input type="text" name="nuptk" value="{{ $pegawai->nuptk }}" class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pegawai *</label>
                                <select name="status_pegawai" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                                    <option value="HONORER" {{ $pegawai->status_pegawai == 'HONORER' ? 'selected' : '' }}>HONORER</option>
                                    <option value="PNS" {{ $pegawai->status_pegawai == 'PNS' ? 'selected' : '' }}>PNS</option>
                                    <option value="PPPK" {{ $pegawai->status_pegawai == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Pangkat / Golongan Terkini</label>
                                <input type="text" name="pangkat_golongan" value="{{ $pegawai->pangkat_golongan }}" placeholder="Belum ada pangkat tercatat" class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                        </div>

                        @if($pegawai->status_keaktifan !== 'Aktif')
                            <div class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-600 space-y-1 shadow-inner">
                                <span class="font-bold text-gray-900 uppercase">ℹ️ Informasi Dokumen Status Akhir:</span>
                                @if($pegawai->status_keaktifan === 'Mutasi')
                                    <p>📅 <strong>Tanggal Mutasi:</strong> {{ $pegawai->tanggal_mutasi?->format('d M Y') }}</p>
                                    <p>🏢 <strong>Sekolah Tujuan:</strong> {{ $pegawai->sekolah_tujuan }}</p>
                                    <p>💬 <strong>Alasan:</strong> {{ $pegawai->alasan_mutasi }}</p>
                                    <p>📎 <a href="{{ asset('storage/' . $pegawai->file_surat_mutasi) }}" target="_blank" class="text-blue-600 hover:underline font-bold">Lihat File Surat Mutasi ↗️</a></p>
                                @else
                                    <p>📅 <strong>Tanggal Pensiun:</strong> {{ $pegawai->tanggal_pensiun?->format('d M Y') }}</p>
                                    <p>📎 <a href="{{ asset('storage/' . $pegawai->file_surat_pensiun) }}" target="_blank" class="text-blue-600 hover:underline font-bold">Lihat File Surat SK Pensiun ↗️</a></p>
                                @endif
                            </div>
                        @endif

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan Biodata</button>
                        </div>
                    </form>
                </div>

                <div x-show="activeTab === 'dokumen'" x-transition>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200/60 h-fit space-y-3">
                            <h5 class="text-xs font-bold text-gray-900 uppercase tracking-wide">➕ Unggah Dokumen Baru</h5>
                            <form action="{{ route('kepegawaian.dokumen-pegawai.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-left">
                                @csrf
                                <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Jenis Dokumen *</label>
                                    <input type="text" name="jenis_dokumen" required placeholder="Contoh: Ijazah S1, SK CPNS, Sertifikat" class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Nama Dokumen *</label>
                                    <input type="text" name="nama_dokumen" required placeholder="Nama spesifik file arsip" class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Tahun Dokumen *</label>
                                    <input type="number" name="tahun_dokumen" value="2026" required class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">File Berkas * (PDF/JPG/PNG max 5MB)</label>
                                    <input type="file" name="file_dokumen" required class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-md shadow transition-colors cursor-pointer">Simpan & Upload</button>
                            </form>
                        </div>
                        
                        <div class="lg:col-span-2 overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-600 font-bold border-b border-gray-200">
                                        <th class="p-3">Jenis & Nama Dokumen</th>
                                        <th class="p-3 text-center">Tahun</th>
                                        <th class="p-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($pegawai->dokumen as $dok)
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-3">
                                                <div class="font-bold text-gray-900">📁 {{ $dok->nama_dokumen }}</div>
                                                <div class="text-[10px] text-gray-400 mt-0.5">Kategori: {{ $dok->jenis_dokumen }}</div>
                                            </td>
                                            <td class="p-3 text-center font-medium text-gray-700">{{ $dok->tahun_dokumen }}</td>
                                            <td class="p-3 text-center">
                                                <div class="flex justify-center gap-3">
                                                    <a href="{{ asset('storage/' . $dok->file_dokumen) }}" target="_blank" class="text-indigo-600 font-medium hover:underline">👁️ Lihat File</a>
                                                    
                                                    <button type="button" @click="initDelete('{{ route('kepegawaian.dokumen-pegawai.destroy', $dok->id) }}', '{{ addslashes($dok->nama_dokumen) }}', 'Apakah Anda yakin ingin menghapus arsip berkas dokumen ini? File yang terhapus tidak bisa dikembalikan.')" class="text-rose-600 hover:underline font-medium cursor-pointer">
                                                        🗑️ Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="p-8 text-center text-gray-400 italic">Belum ada file arsip dokumen terunggah.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'kgb'" x-transition>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200/60 h-fit space-y-3">
                            <h5 class="text-xs font-bold text-gray-900 uppercase tracking-wide">➕ Catat Riwayat KGB</h5>
                            <form action="{{ route('kepegawaian.kgb.store') }}" method="POST" class="space-y-3 text-left">
                                @csrf
                                <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Nomor SK KGB *</label>
                                    <input type="text" name="nomor_sk_kgb" required placeholder="Contoh: 821/KGB/2026" class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Tanggal SK *</label>
                                    <input type="date" name="tanggal_sk_kgb" required class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Nominal Gaji Baru (Rp) *</label>
                                    <input type="number" name="nominal_gaji_baru" required placeholder="Contoh: 3500000" class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                                </div>
                                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-md shadow transition-colors cursor-pointer">Catat Riwayat</button>
                            </form>
                        </div>
                        
                        <div class="lg:col-span-2 overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-600 font-bold border-b border-gray-200">
                                        <th class="p-3">Nomor SK</th>
                                        <th class="p-3 text-center">Tanggal SK</th>
                                        <th class="p-3 text-right">Gaji Pokok Baru</th>
                                        <th class="p-3 text-center w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($pegawai->kgb as $k)
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-3 font-medium text-gray-900">📄 {{ $k->nomor_sk_kgb }}</td>
                                            <td class="p-3 text-center text-gray-600">{{ $k->tanggal_sk_kgb?->format('d M Y') }}</td>
                                            <td class="p-3 text-right font-bold text-emerald-700">Rp {{ number_format($k->nominal_gaji_baru, 0, ',', '.') }}</td>
                                            <td class="p-3 text-center">
                                                <button type="button" @click="initDelete('{{ route('kepegawaian.kgb.destroy', $k->id) }}', '{{ addslashes($k->nomor_sk_kgb) }}', 'Apakah Anda yakin ingin menghapus data riwayat Kenaikan Gaji Berkala (KGB) ini?')" class="text-rose-600 hover:underline font-medium cursor-pointer">
                                                    🗑️ Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada rekam jejak Kenaikan Gaji Berkala (KGB).</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'pangkat'" x-transition>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200/60 h-fit space-y-3">
                            <h5 class="text-xs font-bold text-gray-900 uppercase tracking-wide">➕ Input Kenaikan Pangkat</h5>
                            <form action="{{ route('kepegawaian.kenaikan-pangkat.store') }}" method="POST" class="space-y-3 text-left">
                                @csrf
                                <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Nomor SK Kenaikan Pangkat *</label>
                                    <input type="text" name="nomor_sk_kp" required placeholder="Contoh: SK/004/2026" class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Tanggal SK *</label>
                                    <input type="date" name="tanggal_sk_kp" required class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-0.5">Pangkat / Golongan Baru *</label>
                                    <input type="text" name="pangkat_golongan_baru" required placeholder="Contoh: Penata Muda / IIIa" class="w-full text-xs rounded-md border-gray-300 shadow-sm">
                                </div>
                                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-md shadow transition-colors cursor-pointer">Proses Pangkat Baru</button>
                            </form>
                        </div>
                        
                        <div class="lg:col-span-2 overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-600 font-bold border-b border-gray-200">
                                        <th class="p-3">Nomor SK Pangkat</th>
                                        <th class="p-3 text-center">Tanggal SK</th>
                                        <th class="p-3 text-center">Pangkat/Golongan</th>
                                        <th class="p-3 text-center w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($pegawai->kenaikanPangkat as $pkt)
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-3 font-medium text-gray-900">🎖️ {{ $pkt->nomor_sk_kp }}</td>
                                            <td class="p-3 text-center text-gray-600">{{ $pkt->tanggal_sk_kp?->format('d M Y') }}</td>
                                            <td class="p-3 text-center font-bold text-indigo-700">{{ $pkt->pangkat_golongan_baru }}</td>
                                            <td class="p-3 text-center">
                                                <button type="button" @click="initDelete('{{ route('kepegawaian.kenaikan-pangkat.destroy', $pkt->id) }}', '{{ addslashes($pkt->nomor_sk_kp) }}', 'Hapus riwayat pangkat ini? Sistem akan otomatis mengembalikan data pangkat utama pegawai ke riwayat pangkat terbaru sebelumnya.')" class="text-rose-600 hover:underline font-medium cursor-pointer">
                                                    🗑️ Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada data riwayat kenaikan pangkat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div x-show="openMutasi" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl p-6 space-y-4" @click.away="openMutasi = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Formulir Mutasi Keluar</h3>
                    <button type="button" @click="openMutasi = false" class="text-gray-400 font-bold text-lg">&times;</button>
                </div>
                <form action="{{ route('kepegawaian.pegawai.mutasi', $pegawai->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-left">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-0.5">Tanggal Mutasi *</label>
                        <input type="date" name="tanggal_mutasi" required class="w-full text-xs rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-0.5">Sekolah Tujuan *</label>
                        <input type="text" name="sekolah_tujuan" required placeholder="Nama Instansi/Sekolah Baru" class="w-full text-xs rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-0.5">Alasan Mutasi *</label>
                        <textarea name="alasan_mutasi" required rows="2" placeholder="Tuliskan keterangan penugasan..." class="w-full text-xs rounded-md border-gray-300"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-0.5">Upload Surat Mutasi * (PDF/JPG max 2MB)</label>
                        <input type="file" name="file_surat_mutasi" required class="w-full text-xs text-gray-500">
                    </div>
                    <div class="pt-3 border-t flex justify-end gap-2">
                        <button type="button" @click="openMutasi = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-md">Batal</button>
                        <button type="submit" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-md">Eksekusi Mutasi</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openPensiun" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl p-6 space-y-4" @click.away="openPensiun = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Formulir Pensiun Pegawai</h3>
                    <button type="button" @click="openPensiun = false" class="text-gray-400 font-bold text-lg">&times;</button>
                </div>
                <form action="{{ route('kepegawaian.pegawai.pensiun', $pegawai->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-left">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-0.5">Tanggal Resmi Pensiun *</label>
                        <input type="date" name="tanggal_pensiun" required class="w-full text-xs rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-0.5">Upload SK Pensiun * (PDF/JPG max 2MB)</label>
                        <input type="file" name="file_surat_pensiun" required class="w-full text-xs text-gray-500">
                    </div>
                    <div class="pt-3 border-t flex justify-end gap-2">
                        <button type="button" @click="openPensiun = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-md">Batal</button>
                        <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-md">Konfirmasi Pensiun</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Konfirmasi Hapus Data</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Anda akan menghapus: <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>.
                    </p>
                    <p class="text-[11px] text-slate-400 mt-2 bg-slate-50 p-2 rounded-lg" x-text="deleteMessage"></p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>