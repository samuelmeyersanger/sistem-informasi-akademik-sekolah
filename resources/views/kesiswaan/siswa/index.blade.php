<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Data Master Siswa') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        editActionUrl: '',
        
        initEdit(actionUrl, itemData) {
            doInitEdit(this, actionUrl, itemData);
        },

        openDelete: false,
        openGenerateMassal: false,
        currentStep: 1,
        deleteActionUrl: '',
        deleteTargetName: '',

        initDelete(actionUrl, siswaName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = siswaName;
            this.openDelete = true;
        },

        resetWizard() {
            this.openCreate = false;
            this.openEdit = false;
            this.currentStep = 1;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen font-sans">
        
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded-xl">
                    <span class="font-bold block mb-2">❌ DETEKTIF ERROR (KOLOM YANG KOSONG):</span>
                    <ul class="list-disc list-inside space-y-1 bg-white p-3 rounded-lg border border-red-200 font-mono text-xs text-red-600">
                        @foreach ($errors->getMessages() as $namaInput => $pesan)
                            <li>
                                <strong>Nama Input di HTML:</strong> 
                                <span class="bg-yellow-100 px-1.5 py-0.5 rounded text-yellow-800 font-bold border border-yellow-300">
                                    {{ $namaInput }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Daftar Induk Siswa</h3>
                            <p class="text-xs text-gray-500">Gunakan bilah pencarian dan filter di bawah untuk memilah status operasional akademik siswa.</p>
                        </div>
                        
                        <!-- 👇 BUKA GEMBOK 1: Tombol Tambah Siswa -->
                        @if(auth()->user()->hasPermission('kesiswaan.siswa.store'))
                        <button @click="openCreate = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer whitespace-nowrap">
                            ➕ Tambah Siswa Baru
                        </button>
                        @endif
                        
                    </div>
                    <!-- FORM PENCARIAN & FILTER TETAP MUNCUL UNTUK SEMUA ORANG -->
                    <form action="{{ route('kesiswaan.siswa') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NISN, NIPD, NIK..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full bg-white">
                        
                        <select name="tingkat" class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-gray-600 bg-white">
                            <option value="">-- Semua Tingkat --</option>
                            <option value="7" {{ request('tingkat') == '7' ? 'selected' : '' }}>Tingkat 7</option>
                            <option value="8" {{ request('tingkat') == '8' ? 'selected' : '' }}>Tingkat 8</option>
                            <option value="9" {{ request('tingkat') == '9' ? 'selected' : '' }}>Tingkat 9</option>
                        </select>
                        <select name="status" class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-gray-600 bg-white">
                            <option value="">-- Semua Status --</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>🟢 Aktif</option>
                            <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>🔵 Lulus</option>
                            <option value="Mutasi" {{ request('status') == 'Mutasi' ? 'selected' : '' }}>🟡 Mutasi Masuk/Keluar</option>
                            <option value="Keluar" {{ request('status') == 'Keluar' ? 'selected' : '' }}>🔴 Keluar / DO</option>
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer">Filter</button>
                            <a href="{{ route('kesiswaan.siswa') }}" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg flex items-center justify-center transition-colors">Reset</a>
                        </div>
                    </form>
                    <!-- 👇 BUKA GEMBOK 2: Seluruh Fitur Import dan Generate Akun -->
                    @if(auth()->user()->hasPermission('kesiswaan.siswa.store'))
                    <div class="border-t border-gray-200/60 my-2"></div>
                    <div class="bg-gradient-to-br from-white to-slate-50/50 p-4 border border-gray-200/80 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center gap-4 transition-all hover:shadow-md">
                        <div class="flex items-start gap-3 md:max-w-[240px]">
                            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg shadow-inner flex-shrink-0 border border-emerald-100">
                                📊
                            </div>
                            <div class="space-y-0.5">
                                <h5 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Import Kolektif</h5>
                                <p class="text-[10px] text-gray-400 leading-relaxed mb-1">Unggah satu file Excel/CSV untuk entri data masal siswa & wali sekaligus.</p>
                                
                                <a href="{{ route('kesiswaan.siswa.downloadTemplate') }}" class="inline-flex items-center gap-1 text-[10px] text-indigo-600 hover:text-indigo-800 font-bold transition-colors underline decoration-dashed">
                                    📄 Unduh Format Excel (.xlsx)
                                </a>
                            </div>
                        </div>
                        
                        <div class="hidden md:block h-10 w-px bg-gray-200"></div>
                        
                        <form action="{{ route('kesiswaan.siswa.importLengkap') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2 text-xs">
                            @csrf
                            <div class="relative flex-1 flex items-center">
                                <input type="file" name="file_excel" required 
                                    class="w-full text-xs text-gray-500
                                            file:mr-3 file:py-1.5 file:px-3
                                            file:rounded-xl file:border-0
                                            file:text-[11px] file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700
                                            hover:file:bg-indigo-100
                                            file:cursor-pointer cursor-pointer
                                            bg-white border border-gray-200 rounded-xl p-1 shadow-sm focus:outline-none" />
                            </div>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-black font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap">
                                📥 Mulai Import
                            </button>
                        </form>
                    </div>
                    <div class="mb-4 flex justify-start">
                        <button type="button" @click="openGenerateMassal = true" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Generate Akun Massal
                        </button>
                    </div>
                    @endif
                    <!-- 👆 TUTUP GEMBOK 2 -->
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Lengkap / NIPD</th>
                                <th class="p-4">NISN / NIK</th>
                                <th class="p-4">Tingkat & Ruang Kelas</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 pr-6 text-center w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($siswa as $item)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="font-bold text-gray-900 text-sm">{{ $item->nama_lengkap }}</div>
                                        <div class="text-gray-400 font-mono mt-0.5">{{ $item->nipd }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div>{{ $item->nisn ?? '-' }}</div>
                                        <div class="text-gray-400 font-mono mt-0.5">{{ $item->nik }}</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 bg-slate-100 border rounded-md font-medium text-gray-600 text-[10px]">Tgt {{ $item->tingkat }}</span>
                                        <span class="ml-1 text-gray-700 font-bold">{{ $item->kelas->nama_kelas ?? '⚠️ Belum Dipetakan' }}</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->status_siswa == 'Aktif')
                                            <span class="px-2 py-0.5 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold uppercase rounded">🟢 Aktif</span>
                                        @elseif($item->status_siswa == 'Lulus')
                                            <span class="px-2 py-0.5 bg-blue-50 border border-blue-200 text-blue-700 text-[10px] font-bold uppercase rounded">🔵 Lulus</span>
                                        @elseif($item->status_siswa == 'Mutasi')
                                            <span class="px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-bold uppercase rounded">🟡 Mutasi</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-rose-50 border border-rose-200 text-rose-700 text-[10px] font-bold uppercase rounded">🔴 Keluar</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('kesiswaan.siswa.show', $item->id) }}" class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                                                👁️ Profil
                                            </a>

                                            <span class="text-gray-200">|</span>

                                            <button type="button" 
                                                    @click="initEdit('{{ route('kesiswaan.siswa.update', $item->id) }}', {{ json_encode($item) }})" 
                                                    class="inline-flex items-center gap-1 text-xs text-amber-600 hover:text-amber-900 font-medium transition-colors cursor-pointer">
                                                ✏️ Edit
                                            </button>

                                            <span class="text-gray-200">|</span>

                                            @if(!$item->user_id)
                                                <form action="{{ route('kesiswaan.siswa.generateAkun', $item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 bg-amber-500 hover:bg-amber-600 text-black text-[11px] font-semibold rounded transition-colors cursor-pointer" title="Buat akun login siswa ini">
                                                        🔑 Buat Akun
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-medium rounded" title="Siswa sudah punya akun">
                                                    ✅ Aktif
                                                </span>
                                            @endif

                                            <span class="text-gray-200">|</span>

                                            <button type="button" @click="initDelete('{{ route('kesiswaan.siswa.destroy', $item->id) }}', '{{ addslashes($item->nama_lengkap) }}')" class="inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-900 font-medium transition-colors cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        Tidak ada data rekam catatan siswa terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswa->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $siswa->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl border border-gray-100 flex flex-col max-h-[90vh]" @click.away="resetWizard()">
                
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Form Pendaftaran Akun Siswa</h3>
                        <p class="text-[11px] text-gray-400">Harap isi form data diri, domisili, dan silsilah keluarga di bawah.</p>
                    </div>
                    <button type="button" @click="resetWizard()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <div class="px-6 py-2.5 bg-indigo-50/40 border-b border-gray-100 grid grid-cols-3 text-center text-xs font-bold text-gray-400">
                    <div class="pb-1 border-b-2" :class="currentStep === 1 ? 'text-indigo-600 border-indigo-600' : 'border-transparent'">1. Data Personal</div>
                    <div class="pb-1 border-b-2" :class="currentStep === 2 ? 'text-indigo-600 border-indigo-600' : 'border-transparent'">2. Domisili & Academic</div>
                    <div class="pb-1 border-b-2" :class="currentStep === 3 ? 'text-indigo-600 border-indigo-600' : 'border-transparent'">3. Data Orang Tua</div>
                </div>

                <form action="{{ route('kesiswaan.siswa.store') }}" method="POST" class="flex-1 overflow-y-auto p-6 text-xs space-y-4 text-gray-700">
                    @csrf

                    <div x-show="currentStep === 1" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Nama Lengkap Sesuai Dokumen Resmi *</label>
                                <input type="text" name="nama_lengkap" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">NIK (Nomor Induk Kependudukan) *</label>
                                <input type="text" name="nik" required maxlength="16" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Anak Ke *</label>
                                <input type="text" name="anak_ke" required maxlength="2" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">NIPD *</label>
                                <input type="text" name="nipd" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">NISN (Opsional)</label>
                                <input type="text" name="nisn" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tempat Lahir *</label>
                                <input type="text" name="tempat_lahir" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Agama *</label>
                                <select name="agama" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katholik">Katholik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Budha">Budha</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Nomor Handphone / WhatsApp *</label>
                                <input type="text" name="nomor_hp" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Asal Sekolah Dasar (SD/MI) *</label>
                                <input type="text" name="asal_sekolah" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">No Peserta UN *</label>
                                <input type="text" name="no_peserta_un" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div x-show="currentStep === 2" class="space-y-4" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Provinsi *</label>
                                <select id="siswa_provinsi" name="provinsi" data-current="{{ old('provinsi') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kabupaten / Kota *</label>
                                <select id="siswa_kota" name="kota" data-current="{{ old('kota') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Kota/Kabupaten --</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kecamatan *</label>
                                <select id="siswa_kecamatan" name="kecamatan" data-current="{{ old('kecamatan') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kelurahan / Desa *</label>
                                <select id="siswa_kelurahan" name="kelurahan_desa" data-current="{{ old('kelurahan_desa') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Kelurahan/Desa --</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Alamat Jalan / Blok / Kampung *</label>
                                <input type="text" name="alamat_lengkap" required placeholder="Nama jalan, RT/RW, nomor rumah..." class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">RT *</label>
                                <input type="text" name="rt" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">RW *</label>
                                <input type="text" name="rw" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kode Pos *</label>
                                <input type="text" name="kode_pos" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <hr class="border-gray-100 my-2">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tingkat Kelas Masuk *</label>
                                <select name="tingkat" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="7">Tingkat 7</option>
                                    <option value="8">Tingkat 8</option>
                                    <option value="9">Tingkat 9</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Daftar Semester Masuk *</label>
                                <select name="semester_id" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    @foreach($semester_list as $sem)
                                        <option value="{{ $sem->id }}">{{ $sem->nama_semester }} ({{ $sem->tahunAjaran->nama_tahun_ajaran ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tanggal Diterima Masuk *</label>
                                <input type="date" name="diterima_pada_tanggal" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div x-show="currentStep === 3" class="space-y-6 max-h-[60vh] overflow-y-auto pr-2" x-cloak>
                        
                        <!-- ================= AYAH KANDUNG ================= -->
                        <div class="p-5 bg-blue-50/40 border border-blue-100 rounded-2xl space-y-4">
                            <div class="font-bold text-xs uppercase tracking-wider text-blue-700 border-b border-blue-200/60 pb-2 flex items-center gap-1.5">
                                👨 DATA REKAM IDENTITAS AYAH KANDUNG
                                <input type="hidden" name="wali[0][hubungan]" value="Ayah">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Nama Lengkap Ayah *</label>
                                    <input type="text" name="wali[0][nama_lengkap]" required class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">NIK Ayah</label>
                                    <input type="text" name="wali[0][nik]" maxlength="16" placeholder="16 Digit NIK" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Jenis Kelamin</label>
                                    <select name="wali[0][jenis_kelamin]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="Laki-laki" selected>Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Tempat Lahir</label>
                                    <input type="text" name="wali[0][tempat_lahir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="wali[0][tanggal_lahir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Agama</label>
                                    <select name="wali[0][agama]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Agama --</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katholik">Katholik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Pendidikan Terakhir</label>
                                    <select name="wali[0][pendidikan_terakhir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        <option value="Tidak Sekolah">Tidak Sekolah</option>
                                        <option value="Putus SD">Putus SD</option>
                                        <option value="SD / Sederajat">SD / Sederajat</option>
                                        <option value="SMP / Sederajat">SMP / Sederajat</option>
                                        <option value="SMA / Sederajat">SMA / Sederajat</option>
                                        <option value="D1 (Diploma 1)">D1 (Diploma 1)</option>
                                        <option value="D2 (Diploma 2)">D2 (Diploma 2)</option>
                                        <option value="D3 (Diploma 3)">D3 (Diploma 3)</option>
                                        <option value="D4 / S1 (Sarjana)">D4 / S1 (Sarjana)</option>
                                        <option value="S2 (Magister)">S2 (Magister)</option>
                                        <option value="S3 (Doktor)">S3 (Doktor)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Pekerjaan</label>
                                    <select name="wali[0][pekerjaan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Pekerjaan --</option>
                                        <option value="Tidak Bekerja">Tidak Bekerja</option>
                                        <option value="Nelayan">Nelayan</option>
                                        <option value="Petani">Petani</option>
                                        <option value="Peternak">Peternak</option>
                                        <option value="PNS/TNI/Polri">PNS/TNI/Polri</option>
                                        <option value="Karyawan Swasta">Karyawan Swasta</option>
                                        <option value="Pedagang Kecil">Pedagang Kecil</option>
                                        <option value="Pedagang Besar">Pedagang Besar</option>
                                        <option value="Wiraswasta">Wiraswasta</option>
                                        <option value="Buruh">Buruh</option>
                                        <option value="Pensiunan">Pensiunan</option>
                                        <option value="Tenaga Kerja Indonesia (TKI)">Tenaga Kerja Indonesia (TKI)</option>
                                        <option value="Sudah Meninggal Dunia">Sudah Meninggal Dunia</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Penghasilan Bulanan</label>
                                    <input type="number" step="0.01" name="wali[0][penghasilan_bulanan]" placeholder="Contoh: 3500000" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <!-- Alamat Utama & RT/RW -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block font-semibold text-gray-600 mb-1">Alamat Lengkap Rumah Jalan/Kampung *</label>
                                    <input type="text" name="wali[0][alamat_lengkap]" required placeholder="Nama jalan, RT/RW, nomor rumah" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RT</label>
                                    <input type="text" name="wali[0][rt]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RW</label>
                                    <input type="text" name="wali[0][rw]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kode Pos</label>
                                    <input type="text" name="wali[0][kode_pos]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <!-- Dropdown Wilayah Ayah -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Provinsi</label>
                                    <select id="ayah_provinsi" name="wali[0][provinsi]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Provinsi --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kota / Kabupaten</label>
                                    <select id="ayah_kota" name="wali[0][kota]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kecamatan</label>
                                    <select id="ayah_kecamatan" name="wali[0][kecamatan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kelurahan / Desa</label>
                                    <select id="ayah_kelurahan" name="wali[0][kelurahan_desa]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Nomor Handphone / WA</label>
                                    <input type="text" name="wali[0][nomor_hp]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Email Aktif</label>
                                    <input type="email" name="wali[0][email]" placeholder="contoh@gmail.com" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">No. HP Darurat</label>
                                    <input type="text" name="wali[0][nomor_hp_darurat]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Catatan Khusus Mengenai Ayah</label>
                                <textarea name="wali[0][catatan]" rows="1" placeholder="Informasi tambahan jika diperlukan..." class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm"></textarea>
                            </div>
                        </div>

                        <!-- ================= IBU KANDUNG ================= -->
                        <div class="p-5 bg-rose-50/40 border border-rose-100 rounded-2xl space-y-4">
                            <div class="font-bold text-xs uppercase tracking-wider text-rose-700 border-b border-rose-200/60 pb-2 flex items-center gap-1.5">
                                👩 DATA REKAM IDENTITAS IBU KANDUNG
                                <input type="hidden" name="wali[1][hubungan]" value="Ibu">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Nama Lengkap Ibu *</label>
                                    <input type="text" name="wali[1][nama_lengkap]" required class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">NIK Ibu</label>
                                    <input type="text" name="wali[1][nik]" maxlength="16" placeholder="16 Digit NIK" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Jenis Kelamin</label>
                                    <select name="wali[1][jenis_kelamin]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan" selected>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Tempat Lahir</label>
                                    <input type="text" name="wali[1][tempat_lahir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="wali[1][tanggal_lahir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Agama</label>
                                    <select name="wali[1][agama]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Agama --</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katholik">Katholik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Pendidikan Terakhir</label>
                                    <select name="wali[1][pendidikan_terakhir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        <option value="Tidak Sekolah">Tidak Sekolah</option>
                                        <option value="Putus SD">Putus SD</option>
                                        <option value="SD / Sederajat">SD / Sederajat</option>
                                        <option value="SMP / Sederajat">SMP / Sederajat</option>
                                        <option value="SMA / Sederajat">SMA / Sederajat</option>
                                        <option value="D1 (Diploma 1)">D1 (Diploma 1)</option>
                                        <option value="D2 (Diploma 2)">D2 (Diploma 2)</option>
                                        <option value="D3 (Diploma 3)">D3 (Diploma 3)</option>
                                        <option value="D4 / S1 (Sarjana)">D4 / S1 (Sarjana)</option>
                                        <option value="S2 (Magister)">S2 (Magister)</option>
                                        <option value="S3 (Doktor)">S3 (Doktor)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Pekerjaan</label>
                                    <select name="wali[1][pekerjaan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Pekerjaan --</option>
                                        <option value="Tidak Bekerja">Tidak Bekerja</option>
                                        <option value="Nelayan">Nelayan</option>
                                        <option value="Petani">Petani</option>
                                        <option value="Peternak">Peternak</option>
                                        <option value="PNS/TNI/Polri">PNS/TNI/Polri</option>
                                        <option value="Karyawan Swasta">Karyawan Swasta</option>
                                        <option value="Pedagang Kecil">Pedagang Kecil</option>
                                        <option value="Pedagang Besar">Pedagang Besar</option>
                                        <option value="Wiraswasta">Wiraswasta</option>
                                        <option value="Buruh">Buruh</option>
                                        <option value="Pensiunan">Pensiunan</option>
                                        <option value="Tenaga Kerja Indonesia (TKI)">Tenaga Kerja Indonesia (TKI)</option>
                                        <option value="Sudah Meninggal Dunia">Sudah Meninggal Dunia</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Penghasilan Bulanan</label>
                                    <input type="number" step="0.01" name="wali[1][penghasilan_bulanan]" placeholder="0" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <!-- Alamat Utama & RT/RW -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block font-semibold text-gray-600 mb-1">Alamat Lengkap Rumah Jalan/Kampung *</label>
                                    <input type="text" name="wali[1][alamat_lengkap]" required placeholder="Nama jalan, RT/RW, nomor rumah" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RT</label>
                                    <input type="text" name="wali[1][rt]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RW</label>
                                    <input type="text" name="wali[1][rw]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kode Pos</label>
                                    <input type="text" name="wali[1][kode_pos]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <!-- Dropdown Wilayah Ibu -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Provinsi</label>
                                    <select id="ibu_provinsi" name="wali[1][provinsi]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Provinsi --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kota / Kabupaten</label>
                                    <select id="ibu_kota" name="wali[1][kota]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kecamatan</label>
                                    <select id="ibu_kecamatan" name="wali[1][kecamatan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kelurahan / Desa</label>
                                    <select id="ibu_kelurahan" name="wali[1][kelurahan_desa]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Nomor Handphone / WA</label>
                                    <input type="text" name="wali[1][nomor_hp]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Email Aktif</label>
                                    <input type="email" name="wali[1][email]" placeholder="contoh@gmail.com" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">No. HP Darurat</label>
                                    <input type="text" name="wali[1][nomor_hp_darurat]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Catatan Khusus Mengenai Ibu</label>
                                <textarea name="wali[1][catatan]" rows="1" placeholder="Informasi tambahan jika diperlukan..." class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm"></textarea>
                            </div>
                        </div>

                        <!-- ================= WALI (OPSIONAL) ================= -->
                        <div class="p-5 bg-slate-100 border border-slate-200 rounded-2xl space-y-4">
                            <div class="font-bold text-xs uppercase tracking-wider text-slate-700 border-b border-slate-300 pb-2 flex items-center justify-between">
                                <span>👤 DATA REKAM IDENTITAS WALI (OPSIONAL)</span>
                                <input type="hidden" name="wali[2][hubungan]" value="Wali">
                                <span class="text-[10px] text-gray-400 normal-case font-normal">Kosongkan jika siswa terikat langsung dengan Orang Tua Kandung</span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Nama Lengkap Wali</label>
                                    <input type="text" name="wali[2][nama_lengkap]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">NIK Wali</label>
                                    <input type="text" name="wali[2][nik]" maxlength="16" placeholder="16 Digit NIK" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Jenis Kelamin</label>
                                    <select name="wali[2][jenis_kelamin]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Tempat Lahir</label>
                                    <input type="text" name="wali[2][tempat_lahir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="wali[2][tanggal_lahir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Agama</label>
                                    <select name="wali[2][agama]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Agama --</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katholik">Katholik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Budha">Budha</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Pendidikan Terakhir</label>
                                    <select name="wali[2][pendidikan_terakhir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        <option value="Tidak Sekolah">Tidak Sekolah</option>
                                        <option value="Putus SD">Putus SD</option>
                                        <option value="SD / Sederajat">SD / Sederajat</option>
                                        <option value="SMP / Sederajat">SMP / Sederajat</option>
                                        <option value="SMA / Sederajat">SMA / Sederajat</option>
                                        <option value="D1 (Diploma 1)">D1 (Diploma 1)</option>
                                        <option value="D2 (Diploma 2)">D2 (Diploma 2)</option>
                                        <option value="D3 (Diploma 3)">D3 (Diploma 3)</option>
                                        <option value="D4 / S1 (Sarjana)">D4 / S1 (Sarjana)</option>
                                        <option value="S2 (Magister)">S2 (Magister)</option>
                                        <option value="S3 (Doktor)">S3 (Doktor)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Pekerjaan</label>
                                    <select name="wali[2][pekerjaan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Pekerjaan --</option>
                                        <option value="Tidak Bekerja">Tidak Bekerja</option>
                                        <option value="Nelayan">Nelayan</option>
                                        <option value="Petani">Petani</option>
                                        <option value="Peternak">Peternak</option>
                                        <option value="PNS/TNI/Polri">PNS/TNI/Polri</option>
                                        <option value="Karyawan Swasta">Karyawan Swasta</option>
                                        <option value="Pedagang Kecil">Pedagang Kecil</option>
                                        <option value="Pedagang Besar">Pedagang Besar</option>
                                        <option value="Wiraswasta">Wiraswasta</option>
                                        <option value="Buruh">Buruh</option>
                                        <option value="Pensiunan">Pensiunan</option>
                                        <option value="Tenaga Kerja Indonesia (TKI)">Tenaga Kerja Indonesia (TKI)</option>
                                        <option value="Sudah Meninggal Dunia">Sudah Meninggal Dunia</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Penghasilan Bulanan</label>
                                    <input type="number" step="0.01" name="wali[2][penghasilan_bulanan]" placeholder="0" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <!-- Alamat Utama & RT/RW -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block font-semibold text-gray-600 mb-1">Alamat Lengkap Rumah Jalan/Kampung *</label>
                                    <input type="text" name="wali[2][alamat_lengkap]" placeholder="Nama jalan, RT/RW, nomor rumah" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RT</label>
                                    <input type="text" name="wali[2][rt]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RW</label>
                                    <input type="text" name="wali[2][rw]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kode Pos</label>
                                    <input type="text" name="wali[2][kode_pos]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <!-- Dropdown Wilayah Wali -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Provinsi</label>
                                    <select id="wali_provinsi" name="wali[2][provinsi]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Provinsi --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kota / Kabupaten</label>
                                    <select id="wali_kota" name="wali[2][kota]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kecamatan</label>
                                    <select id="wali_kecamatan" name="wali[2][kecamatan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kelurahan / Desa</label>
                                    <select id="wali_kelurahan" name="wali[2][kelurahan_desa]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Nomor Handphone / WA</label>
                                    <input type="text" name="wali[2][nomor_hp]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Email Aktif</label>
                                    <input type="email" name="wali[2][email]" placeholder="contoh@gmail.com" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">No. HP Darurat</label>
                                    <input type="text" name="wali[2][nomor_hp_darurat]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Catatan Khusus Mengenai Wali</label>
                                <textarea name="wali[2][catatan]" rows="1" placeholder="Informasi tambahan jika diperlukan..." class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Navigasi Tombol di bagian bawah -->
                    <div class="pt-4 border-t border-gray-100 flex justify-between bg-white">
                        <button type="button" x-show="currentStep > 1" @click="currentStep--" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                            ⬅️ Kembali
                        </button>
                        <div x-show="currentStep === 1"></div>
                        
                        <button type="button" x-show="currentStep < 3" @click="currentStep++" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                            Lanjut ➡️
                        </button>
                        
                        <button type="submit" x-show="currentStep === 3" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer">
                            💾 Daftarkan Siswa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl border border-gray-100 flex flex-col max-h-[90vh]" @click.away="resetWizard()">
                
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Form Edit Akun Siswa</h3>
                        <p class="text-[11px] text-gray-400">Harap isi form data diri, domisili, dan silsilah keluarga di bawah.</p>
                    </div>
                    <button type="button" @click="resetWizard()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <div class="px-6 py-2.5 bg-indigo-50/40 border-b border-gray-100 grid grid-cols-3 text-center text-xs font-bold text-gray-400">
                    <div class="pb-1 border-b-2" :class="currentStep === 1 ? 'text-indigo-600 border-indigo-600' : 'border-transparent'">1. Data Personal</div>
                    <div class="pb-1 border-b-2" :class="currentStep === 2 ? 'text-indigo-600 border-indigo-600' : 'border-transparent'">2. Domisili & Academic</div>
                    <div class="pb-1 border-b-2" :class="currentStep === 3 ? 'text-indigo-600 border-indigo-600' : 'border-transparent'">3. Data Orang Tua</div>
                </div>

                <form :action="editActionUrl" method="POST" id="formEditSiswa" class="flex-1 overflow-y-auto p-6 text-xs space-y-4 text-gray-700">
                    @csrf

                    <div x-show="currentStep === 1" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Nama Lengkap Sesuai Dokumen Resmi *</label>
                                <input type="text" name="nama_lengkap" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">NIK (Nomor Induk Kependudukan) *</label>
                                <input type="text" name="nik" required maxlength="16" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Anak Ke *</label>
                                <input type="text" name="anak_ke" required maxlength="2" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">NIPD *</label>
                                <input type="text" name="nipd" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">NISN (Opsional)</label>
                                <input type="text" name="nisn" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tempat Lahir *</label>
                                <input type="text" name="tempat_lahir" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Agama *</label>
                                <select name="agama" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katholik">Katholik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Budha">Budha</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Nomor Handphone / WhatsApp *</label>
                                <input type="text" name="nomor_hp" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Asal Sekolah Dasar (SD/MI) *</label>
                                <input type="text" name="asal_sekolah" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">No Peserta UN *</label>
                                <input type="text" name="no_peserta_un" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div x-show="currentStep === 2" class="space-y-4" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Provinsi *</label>
                                <select id="edit_siswa_provinsi" name="provinsi" data-current="{{ old('provinsi') }}" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kabupaten / Kota *</label>
                                <select id="edit_siswa_kota" name="kota" data-current="{{ old('kota') }}" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Kota/Kabupaten --</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kecamatan *</label>
                                <select id="edit_siswa_kecamatan" name="kecamatan" data-current="{{ old('kecamatan') }}" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kelurahan / Desa *</label>
                                <select id="edit_siswa_kelurahan" name="kelurahan_desa" data-current="{{ old('kelurahan_desa') }}" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Kelurahan/Desa --</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Alamat Jalan / Blok / Kampung *</label>
                                <input type="text" name="alamat_lengkap" required placeholder="Nama jalan, RT/RW, nomor rumah..." class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">RT *</label>
                                <input type="text" name="rt" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">RW *</label>
                                <input type="text" name="rw" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Kode Pos *</label>
                                <input type="text" name="kode_pos" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <hr class="border-gray-100 my-2">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tingkat Kelas Masuk *</label>
                                <select name="tingkat" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    <option value="7">Tingkat 7</option>
                                    <option value="8">Tingkat 8</option>
                                    <option value="9">Tingkat 9</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Daftar Semester Masuk *</label>
                                <select name="semester_id" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 shadow-sm">
                                    @foreach($semester_list as $sem)
                                        <option value="{{ $sem->id }}">{{ $sem->nama_semester }} ({{ $sem->tahunAjaran->nama_tahun_ajaran ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Tanggal Diterima Masuk *</label>
                                <input type="date" name="diterima_pada_tanggal" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div x-show="currentStep === 3" class="space-y-6 max-h-[60vh] overflow-y-auto pr-2" x-cloak>
                        
                       @php
                            // 1. Cek apakah $siswa adalah objek paginator (banyak data) atau data tunggal
                            if ($siswa instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                                // Jika paginator, ambil data siswa pertama dari koleksi halaman aktif
                                $siswaTunggal = $siswa->first();
                            } else {
                                // Jika memang sudah data tunggal murni
                                $siswaTunggal = $siswa;
                            }

                            // 2. Ambil relasi wali dari siswa tunggal tersebut secara aman
                            $ayah = null;
                            if ($siswaTunggal && isset($siswaTunggal->wali)) {
                                $ayah = $siswaTunggal->wali->first(function($w) {
                                    return $w->pivot->hubungan === 'Ayah';
                                });
                            }
                        @endphp

                        <div class="p-5 bg-blue-50/40 border border-blue-100 rounded-2xl space-y-4">
                            <div class="font-bold text-xs uppercase tracking-wider text-blue-700 border-b border-blue-200/60 pb-2 flex items-center gap-1.5">
                                👨 DATA REKAM IDENTITAS AYAH KANDUNG
                                <input type="hidden" name="wali[0][hubungan]" value="Ayah">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Nama Lengkap Ayah *</label>
                                    <input type="text" name="wali[0][nama_lengkap]" value="{{ old('wali.0.nama_lengkap', $ayah->nama_lengkap ?? '') }}" required class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">NIK Ayah</label>
                                    <input type="text" name="wali[0][nik]" value="{{ old('wali.0.nik', $ayah->nik ?? '') }}" maxlength="16" placeholder="16 Digit NIK" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Jenis Kelamin</label>
                                    <select name="wali[0][jenis_kelamin]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="Laki-laki" {{ old('wali.0.jenis_kelamin', $ayah->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('wali.0.jenis_kelamin', $ayah->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Tempat Lahir</label>
                                    <input type="text" name="wali[0][tempat_lahir]" value="{{ old('wali.0.tempat_lahir', $ayah->tempat_lahir ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="wali[0][tanggal_lahir]" value="{{ old('wali.0.tanggal_lahir', $ayah->tanggal_lahir ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Agama</label>
                                    <select name="wali[0][agama]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Agama --</option>
                                        @foreach(['Islam', 'Kristen', 'Katholik', 'Hindu', 'Budha'] as $agama)
                                            <option value="{{ $agama }}" {{ old('wali.0.agama', $ayah->agama ?? '') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Pendidikan Terakhir</label>
                                    <select name="wali[0][pendidikan_terakhir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        @foreach(['Tidak Sekolah', 'Putus SD', 'SD / Sederajat', 'SMP / Sederajat', 'SMA / Sederajat', 'D1 (Diploma 1)', 'D2 (Diploma 2)', 'D3 (Diploma 3)', 'D4 / S1 (Sarjana)', 'S2 (Magister)', 'S3 (Doktor)'] as $edu)
                                            <option value="{{ $edu }}" {{ old('wali.0.pendidikan_terakhir', $ayah->pendidikan_terakhir ?? '') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Pekerjaan</label>
                                    <select name="wali[0][pekerjaan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="">-- Pilih Pekerjaan --</option>
                                        @foreach(['Tidak Bekerja', 'Nelayan', 'Petani', 'Peternak', 'PNS/TNI/Polri', 'Karyawan Swasta', 'Pedagang Kecil', 'Pedagang Besar', 'Wiraswasta', 'Buruh', 'Pensiunan', 'Tenaga Kerja Indonesia (TKI)', 'Sudah Meninggal Dunia', 'Lainnya'] as $kerja)
                                            <option value="{{ $kerja }}" {{ old('wali.0.pekerjaan', $ayah->pekerjaan ?? '') == $kerja ? 'selected' : '' }}>{{ $kerja }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Penghasilan Bulanan</label>
                                    <input type="number" step="0.01" name="wali[0][penghasilan_bulanan]" value="{{ old('wali.0.penghasilan_bulanan', $ayah->penghasilan_bulanan ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Alamat Lengkap Rumah Jalan/Kampung *</label>
                                    <input type="text" name="wali[0][alamat_lengkap]" value="{{ old('wali.0.alamat_lengkap', $ayah->alamat_lengkap ?? '') }}" required class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RT</label>
                                    <input type="text" name="wali[0][rt]" value="{{ old('wali.0.rt', $ayah->rt ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">RW</label>
                                    <input type="text" name="wali[0][rw]" value="{{ old('wali.0.rw', $ayah->rw ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kode Pos</label>
                                    <input type="text" name="wali[0][kode_pos]" value="{{ old('wali.0.kode_pos', $ayah->kode_pos ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Provinsi</label>
                                    <select id="edit_ayah_provinsi" name="wali[0][provinsi]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="{{ old('wali.0.provinsi', $ayah->provinsi ?? '') }}">{{ old('wali.0.provinsi', $ayah->provinsi ?? '-- Pilih Provinsi --') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kota / Kabupaten</label>
                                    <select id="edit_ayah_kota" name="wali[0][kota]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="{{ old('wali.0.kota', $ayah->kota ?? '') }}">{{ old('wali.0.kota', $ayah->kota ?? '-- Pilih Kota/Kabupaten --') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kecamatan</label>
                                    <select id="edit_ayah_kecamatan" name="wali[0][kecamatan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="{{ old('wali.0.kecamatan', $ayah->kecamatan ?? '') }}">{{ old('wali.0.kecamatan', $ayah->kecamatan ?? '-- Pilih Kecamatan --') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Kelurahan / Desa</label>
                                    <select id="edit_ayah_kelurahan" name="wali[0][kelurahan_desa]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                        <option value="{{ old('wali.0.kelurahan_desa', $ayah->kelurahan_desa ?? '') }}">{{ old('wali.0.kelurahan_desa', $ayah->kelurahan_desa ?? '-- Pilih Kelurahan/Desa --') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Nomor Handphone / WA</label>
                                    <input type="text" name="wali[0][nomor_hp]" value="{{ old('wali.0.nomor_hp', $ayah->nomor_hp ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Email Aktif</label>
                                    <input type="email" name="wali[0][email]" value="{{ old('wali.0.email', $ayah->email ?? '') }}" placeholder="contoh@gmail.com" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">No. HP Darurat</label>
                                    <input type="text" name="wali[0][nomor_hp_darurat]" value="{{ old('wali.0.nomor_hp_darurat', $ayah->nomor_hp_darurat ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-600 mb-1">Catatan Khusus Mengenai Ayah</label>
                                <textarea name="wali[0][catatan]" rows="1" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">{{ old('wali.0.catatan', $ayah->catatan ?? '') }}</textarea>
                            </div>

                            @php
                                // 1. Ambil data siswa tunggal dari paginator atau objek murni (Sama seperti ayah)
                                if ($siswa instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                                    $siswaTunggal = $siswa->first();
                                } else {
                                    $siswaTunggal = $siswa;
                                }

                                // 2. Kuncinya di sini: Ganti filter pencariannya menjadi 'Ibu'
                                $ibu = null;
                                if ($siswaTunggal && isset($siswaTunggal->wali)) {
                                    $ibu = $siswaTunggal->wali->first(function($w) {
                                        return $w->pivot->hubungan === 'Ibu';
                                    });
                                }
                            @endphp

                            <div class="p-5 bg-rose-50/40 border border-rose-100 rounded-2xl space-y-4">
                                <div class="font-bold text-xs uppercase tracking-wider text-rose-700 border-b border-rose-200/60 pb-2 flex items-center gap-1.5">
                                    👩 DATA REKAM IDENTITAS IBU KANDUNG
                                    <input type="hidden" name="wali[1][hubungan]" value="Ibu">
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Nama Lengkap Ibu *</label>
                                        <input type="text" name="wali[1][nama_lengkap]" value="{{ old('wali.1.nama_lengkap', $ibu->nama_lengkap ?? '') }}" required class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">NIK Ibu</label>
                                        <input type="text" name="wali[1][nik]" value="{{ old('wali.1.nik', $ibu->nik ?? '') }}" maxlength="16" placeholder="16 Digit NIK" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Jenis Kelamin</label>
                                        <select name="wali[1][jenis_kelamin]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                            <option value="Laki-laki" {{ old('wali.1.jenis_kelamin', $ibu->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('wali.1.jenis_kelamin', $ibu->jenis_kelamin ?? 'Perempuan') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Tempat Lahir</label>
                                        <input type="text" name="wali[1][tempat_lahir]" value="{{ old('wali.1.tempat_lahir', $ibu->tempat_lahir ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Tanggal Lahir</label>
                                        <input type="date" name="wali[1][tanggal_lahir]" value="{{ old('wali.1.tanggal_lahir', $ibu->tanggal_lahir ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Agama</label>
                                        <select name="wali[1][agama]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                            <option value="">-- Pilih Agama --</option>
                                            @foreach(['Islam', 'Kristen', 'Katholik', 'Hindu', 'Budha'] as $agama)
                                                <option value="{{ $agama }}" {{ old('wali.1.agama', $ibu->agama ?? '') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Pendidikan Terakhir</label>
                                        <select name="wali[1][pendidikan_terakhir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                            <option value="">-- Pilih Pendidikan --</option>
                                            @foreach(['Tidak Sekolah', 'Putus SD', 'SD / Sederajat', 'SMP / Sederajat', 'SMA / Sederajat', 'D1 (Diploma 1)', 'D2 (Diploma 2)', 'D3 (Diploma 3)', 'D4 / S1 (Sarjana)', 'S2 (Magister)', 'S3 (Doktor)'] as $edu)
                                                <option value="{{ $edu }}" {{ old('wali.1.pendidikan_terakhir', $ibu->pendidikan_terakhir ?? '') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Pekerjaan</label>
                                        <select name="wali[1][pekerjaan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                            <option value="">-- Pilih Pekerjaan --</option>
                                            @foreach(['Tidak Bekerja', 'Nelayan', 'Petani', 'Peternak', 'PNS/TNI/Polri', 'Karyawan Swasta', 'Pedagang Kecil', 'Pedagang Besar', 'Wiraswasta', 'Buruh', 'Pensiunan', 'Tenaga Kerja Indonesia (TKI)', 'Sudah Meninggal Dunia', 'Lainnya'] as $kerja)
                                                <option value="{{ $kerja }}" {{ old('wali.1.pekerjaan', $ibu->pekerjaan ?? '') == $kerja ? 'selected' : '' }}>{{ $kerja }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Penghasilan Bulanan</label>
                                        <input type="number" step="0.01" name="wali[1][penghasilan_bulanan]" value="{{ old('wali.1.penghasilan_bulanan', $ibu->penghasilan_bulanan ?? '') }}" placeholder="0" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    <div class="md:col-span-3">
                                        <label class="block font-semibold text-gray-600 mb-1">Alamat Lengkap Rumah Jalan/Kampung *</label>
                                        <input type="text" name="wali[1][alamat_lengkap]" value="{{ old('wali.1.alamat_lengkap', $ibu->alamat_lengkap ?? '') }}" required placeholder="Nama jalan, RT/RW, nomor rumah" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">RT</label>
                                        <input type="text" name="wali[1][rt]" value="{{ old('wali.1.rt', $ibu->rt ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">RW</label>
                                        <input type="text" name="wali[1][rw]" value="{{ old('wali.1.rw', $ibu->rw ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kode Pos</label>
                                        <input type="text" name="wali[1][kode_pos]" value="{{ old('wali.1.kode_pos', $ibu->kode_pos ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Provinsi</label>
                                        <select id="edit_ibu_provinsi" name="wali[1][provinsi]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                            <option value="{{ old('wali.1.provinsi', $ibu->provinsi ?? '') }}">{{ old('wali.1.provinsi', $ibu->provinsi ?? '-- Pilih Provinsi --') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kota / Kabupaten</label>
                                        <select id="edit_ibu_kota" name="wali[1][kota]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                            <option value="{{ old('wali.1.kota', $ibu->kota ?? '') }}">{{ old('wali.1.kota', $ibu->kota ?? '-- Pilih Kota/Kabupaten --') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kecamatan</label>
                                        <select id="edit_ibu_kecamatan" name="wali[1][kecamatan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                            <option value="{{ old('wali.1.kecamatan', $ibu->kecamatan ?? '') }}">{{ old('wali.1.kecamatan', $ibu->kecamatan ?? '-- Pilih Kecamatan --') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kelurahan / Desa</label>
                                        <select id="edit_ibu_kelurahan" name="wali[1][kelurahan_desa]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                            <option value="{{ old('wali.1.kelurahan_desa', $ibu->kelurahan_desa ?? '') }}">{{ old('wali.1.kelurahan_desa', $ibu->kelurahan_desa ?? '-- Pilih Kelurahan/Desa --') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Nomor Handphone / WA</label>
                                        <input type="text" name="wali[1][nomor_hp]" value="{{ old('wali.1.nomor_hp', $ibu->nomor_hp ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Email Aktif</label>
                                        <input type="email" name="wali[1][email]" value="{{ old('wali.1.email', $ibu->email ?? '') }}" placeholder="contoh@gmail.com" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">No. HP Darurat</label>
                                        <input type="text" name="wali[1][nomor_hp_darurat]" value="{{ old('wali.1.nomor_hp_darurat', $ibu->nomor_hp_darurat ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Catatan Khusus Mengenai Ibu</label>
                                    <textarea name="wali[1][catatan]" rows="1" placeholder="Informasi tambahan jika diperlukan..." class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">{{ old('wali.1.catatan', $ibu->catatan ?? '') }}</textarea>
                                </div>
                            </div>

                            @php
                                // 1. Ambil data siswa tunggal dari paginator atau objek murni
                                if ($siswa instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                                    $siswaTunggal = $siswa->first();
                                } else {
                                    $siswaTunggal = $siswa;
                                }

                                // 2. Filter pencarian pivot khusus untuk hubungan 'Wali'
                                $dataWali = null;
                                if ($siswaTunggal && isset($siswaTunggal->wali)) {
                                    $dataWali = $siswaTunggal->wali->first(function($w) {
                                        return $w->pivot->hubungan === 'Wali';
                                    });
                                }
                            @endphp

                            <div class="p-5 bg-slate-100 border border-slate-200 rounded-2xl space-y-4">
                                <div class="font-bold text-xs uppercase tracking-wider text-slate-700 border-b border-slate-300 pb-2 flex items-center justify-between">
                                    <span>👤 DATA REKAM IDENTITAS WALI (OPSIONAL)</span>
                                    <input type="hidden" name="wali[2][hubungan]" value="Wali">
                                    <span class="text-[10px] text-gray-400 normal-case font-normal">Kosongkan jika siswa terikat langsung dengan Orang Tua Kandung</span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Nama Lengkap Wali</label>
                                        <input type="text" name="wali[2][nama_lengkap]" value="{{ old('wali.2.nama_lengkap', $dataWali->nama_lengkap ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">NIK Wali</label>
                                        <input type="text" name="wali[2][nik]" value="{{ old('wali.2.nik', $dataWali->nik ?? '') }}" maxlength="16" placeholder="16 Digit NIK" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Jenis Kelamin</label>
                                        <select name="wali[2][jenis_kelamin]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <option value="Laki-laki" {{ old('wali.2.jenis_kelamin', $dataWali->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('wali.2.jenis_kelamin', $dataWali->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Tempat Lahir</label>
                                        <input type="text" name="wali[2][tempat_lahir]" value="{{ old('wali.2.tempat_lahir', $dataWali->tempat_lahir ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Tanggal Lahir</label>
                                        <input type="date" name="wali[2][tanggal_lahir]" value="{{ old('wali.2.tanggal_lahir', $dataWali->tanggal_lahir ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Agama</label>
                                        <select name="wali[2][agama]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                            <option value="">-- Pilih Agama --</option>
                                            @foreach(['Islam', 'Kristen', 'Katholik', 'Hindu', 'Budha'] as $agama)
                                                <option value="{{ $agama }}" {{ old('wali.2.agama', $dataWali->agama ?? '') == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Pendidikan Terakhir</label>
                                        <select name="wali[2][pendidikan_terakhir]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                            <option value="">-- Pilih Pendidikan --</option>
                                            @foreach(['Tidak Sekolah', 'Putus SD', 'SD / Sederajat', 'SMP / Sederajat', 'SMA / Sederajat', 'D1 (Diploma 1)', 'D2 (Diploma 2)', 'D3 (Diploma 3)', 'D4 / S1 (Sarjana)', 'S2 (Magister)', 'S3 (Doktor)'] as $edu)
                                                <option value="{{ $edu }}" {{ old('wali.2.pendidikan_terakhir', $dataWali->pendidikan_terakhir ?? '') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Pekerjaan</label>
                                        <select name="wali[2][pekerjaan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                            <option value="">-- Pilih Pekerjaan --</option>
                                            @foreach(['Tidak Bekerja', 'Nelayan', 'Petani', 'Peternak', 'PNS/TNI/Polri', 'Karyawan Swasta', 'Pedagang Kecil', 'Pedagang Besar', 'Wiraswasta', 'Buruh', 'Pensiunan', 'Tenaga Kerja Indonesia (TKI)', 'Sudah Meninggal Dunia', 'Lainnya'] as $kerja)
                                                <option value="{{ $kerja }}" {{ old('wali.2.pekerjaan', $dataWali->pekerjaan ?? '') == $kerja ? 'selected' : '' }}>{{ $kerja }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Penghasilan Bulanan</label>
                                        <input type="number" step="0.01" name="wali[2][penghasilan_bulanan]" value="{{ old('wali.2.penghasilan_bulanan', $dataWali->penghasilan_bulanan ?? '') }}" placeholder="0" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-3">
                                        <label class="block font-semibold text-gray-600 mb-1">Alamat Lengkap Rumah Jalan/Kampung *</label>
                                        <input type="text" name="wali[2][alamat_lengkap]" value="{{ old('wali.2.alamat_lengkap', $dataWali->alamat_lengkap ?? '') }}" placeholder="Nama jalan, RT/RW, nomor rumah" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">RT</label>
                                        <input type="text" name="wali[2][rt]" value="{{ old('wali.2.rt', $dataWali->rt ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">RW</label>
                                        <input type="text" name="wali[2][rw]" value="{{ old('wali.2.rw', $dataWali->rw ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kode Pos</label>
                                        <input type="text" name="wali[2][kode_pos]" value="{{ old('wali.2.kode_pos', $dataWali->kode_pos ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Provinsi</label>
                                        <select id="edit_wali_provinsi" name="wali[2][provinsi]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                            <option value="{{ old('wali.2.provinsi', $dataWali->provinsi ?? '') }}">{{ old('wali.2.provinsi', $dataWali->provinsi ?? '-- Pilih Provinsi --') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kota / Kabupaten</label>
                                        <select id="edit_wali_kota" name="wali[2][kota]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                            <option value="{{ old('wali.2.kota', $dataWali->kota ?? '') }}">{{ old('wali.2.kota', $dataWali->kota ?? '-- Pilih Kota/Kabupaten --') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kecamatan</label>
                                        <select id="edit_wali_kecamatan" name="wali[2][kecamatan]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                            <option value="{{ old('wali.2.kecamatan', $dataWali->kecamatan ?? '') }}">{{ old('wali.2.kecamatan', $dataWali->kecamatan ?? '-- Pilih Kecamatan --') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Kelurahan / Desa</label>
                                        <select id="edit_wali_kelurahan" name="wali[2][kelurahan_desa]" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm focus:ring-indigo-500">
                                            <option value="{{ old('wali.2.kelurahan_desa', $dataWali->kelurahan_desa ?? '') }}">{{ old('wali.2.kelurahan_desa', $dataWali->kelurahan_desa ?? '-- Pilih Kelurahan/Desa --') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Nomor Handphone / WA</label>
                                        <input type="text" name="wali[2][nomor_hp]" value="{{ old('wali.2.nomor_hp', $dataWali->nomor_hp ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">Email Aktif</label>
                                        <input type="email" name="wali[2][email]" value="{{ old('wali.2.email', $dataWali->email ?? '') }}" placeholder="contoh@gmail.com" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-gray-600 mb-1">No. HP Darurat</label>
                                        <input type="text" name="wali[2][nomor_hp_darurat]" value="{{ old('wali.2.nomor_hp_darurat', $dataWali->nomor_hp_darurat ?? '') }}" class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-600 mb-1">Catatan Khusus Mengenai Wali</label>
                                    <textarea name="wali[2][catatan]" rows="1" placeholder="Informasi tambahan jika diperlukan..." class="w-full text-xs rounded-lg border-gray-300 bg-white shadow-sm">{{ old('wali.2.catatan', $dataWali->catatan ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 mt-4 border-t border-gray-100 flex justify-between items-center bg-white">
    
                        <div>
                            <button type="button" 
                                    x-show="currentStep > 1" 
                                    @click="currentStep--" 
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                                ⬅️ Kembali
                            </button>
                        </div>
                        
                        <div>
                            <button type="button" 
                                    x-show="currentStep < 3" 
                                    @click="currentStep++" 
                                    class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                                Lanjut ➡️
                            </button>
                            
                            <form action="{{ url('kesiswaan/siswa/' . ($siswaTunggal?->id ?? '')) }}" method="POST" novalidate>
                                @csrf
                                @method('PUT') 
                                <button type="submit" x-show="currentStep === 3" class="...">
                                    💾 Simpan & Daftarkan Siswa
                                </button>
                            </form>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Data Master Siswa?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus data siswa bernama <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Semua dokumen terunggah & ikatan wali murid akan terputus.
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">Ya, Hapus</button>
                </form>
            </div>
        </div>

        <div x-show="openGenerateMassal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openGenerateMassal = false">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-xl mx-auto border border-indigo-100 animate-pulse">
                    ⚡
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Generate Akun Massal?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin men-generate akun untuk <span class="font-bold text-gray-800">SEMUA siswa</span> yang belum memiliki akun di dalam sistem?
                    </p>
                </div>
                <form action="{{ route('kesiswaan.siswa.generateMassal') }}" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    <button type="button" @click="openGenerateMassal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer border border-transparent">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">
                        Ya, Proses
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        var _regionControllers = {};
        async function fetchJsonRegion(url) {
            try {
                var res = await fetch(url);
                return await res.json();
            } catch(e) {
                console.error('Gagal fetch:', url, e);
                return [];
            }
        }
        function initRegionDropdowns(prefix) {
            var provEl = document.getElementById(prefix + '_provinsi');
            var kotaEl = document.getElementById(prefix + '_kota');
            var kecEl  = document.getElementById(prefix + '_kecamatan');
            var kelEl  = document.getElementById(prefix + '_kelurahan');
            if (!provEl) return;
            if (_regionControllers[prefix]) _regionControllers[prefix].abort();
            _regionControllers[prefix] = new AbortController();
            var sig = _regionControllers[prefix].signal;
            var curProv = String(provEl.getAttribute('data-current') || '');
            var curKota = String(kotaEl ? (kotaEl.getAttribute('data-current') || '') : '');
            var curKec  = String(kecEl  ? (kecEl.getAttribute('data-current')  || '') : '');
            var curKel  = String(kelEl  ? (kelEl.getAttribute('data-current')  || '') : '');
            // Helper: tambah options ke select, return true jika ada yang cocok
                function populateSelect(selectEl, items, nameKey, codeKey, current) {
                selectEl.innerHTML = '';
                var defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = '-- Pilih --';
                selectEl.appendChild(defaultOpt);
                // Jika tidak ada data dari API, stop
                if (!items || !items.length) return false;
                var currentStr  = String(current || '');
                var curLower    = currentStr.toLowerCase();
                var matchedName = null;
                // Cari match HANYA jika ada nilai current (mode Edit)
                if (currentStr) {
                    // Prioritas 1: auto-increment id (data dari import)
                    for (var i = 0; i < items.length; i++) {
                        if (String(items[i].id || '') === currentStr) {
                            matchedName = items[i][nameKey];
                            break;
                        }
                    }
                    // Prioritas 2: BPS code (data dari form manual)
                    if (!matchedName) {
                        for (var i = 0; i < items.length; i++) {
                            if (String(items[i][codeKey] || '') === currentStr) {
                                matchedName = items[i][nameKey];
                                break;
                            }
                        }
                    }
                    // Prioritas 3: nama (case-insensitive)
                    if (!matchedName && curLower) {
                        for (var i = 0; i < items.length; i++) {
                            if ((items[i][nameKey] || '').toLowerCase() === curLower) {
                                matchedName = items[i][nameKey];
                                break;
                            }
                        }
                    }
                }
                // Bangun semua options (selalu dijalankan, termasuk form Create)
                items.forEach(function(item) {
                    var opt = document.createElement('option');
                    opt.value       = item[nameKey];
                    opt.setAttribute('data-id', item[codeKey]);
                    opt.textContent = item[nameKey];
                    // Hanya set selected jika ada match (mode Edit)
                    if (matchedName && item[nameKey] === matchedName) {
                        opt.selected = true;
                    }
                    selectEl.appendChild(opt);
                });
                return matchedName !== null;
            }
            // ── Load Provinsi ─────────────────────────────────────────────────────
            async function loadProvinsi() {
                var data = await fetchJsonRegion('/kesiswaan/api/provinsi');
                var matched = populateSelect(provEl, data, 'name', 'code', curProv);
                if (matched && curProv) {
                    provEl.dispatchEvent(new Event('change'));
                }
            }
            // ── Provinsi → Kota ───────────────────────────────────────────────────
            provEl.addEventListener('change', async function() {
                if (kotaEl) { kotaEl.innerHTML = '<option value="">-- Memuat Kota... --</option>'; }
                if (kecEl)  { kecEl.innerHTML  = '<option value="">-- Pilih Kecamatan --</option>'; }
                if (kelEl)  { kelEl.innerHTML  = '<option value="">-- Pilih Kelurahan/Desa --</option>'; }
                var opt    = this.options[this.selectedIndex];
                var provId = opt ? opt.getAttribute('data-id') : null;
                if (!provId || !kotaEl) {
                    if (kotaEl) kotaEl.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                    return;
                }
                var data    = await fetchJsonRegion('/kesiswaan/api/kota/' + provId);
                var matched = populateSelect(kotaEl, data, 'name', 'code', curKota);
                if (matched && curKota) {
                    kotaEl.dispatchEvent(new Event('change'));
                }
            }, { signal: sig });
            // ── Kota → Kecamatan ──────────────────────────────────────────────────
            if (kotaEl) {
                kotaEl.addEventListener('change', async function() {
                    if (kecEl) { kecEl.innerHTML = '<option value="">-- Memuat Kecamatan... --</option>'; }
                    if (kelEl) { kelEl.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>'; }
                    var opt    = this.options[this.selectedIndex];
                    var kotaId = opt ? opt.getAttribute('data-id') : null;
                    if (!kotaId || !kecEl) {
                        if (kecEl) kecEl.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                        return;
                    }
                    var data    = await fetchJsonRegion('/kesiswaan/api/kecamatan/' + kotaId);
                    var matched = populateSelect(kecEl, data, 'name', 'code', curKec);
                    if (matched && curKec) {
                        kecEl.dispatchEvent(new Event('change'));
                    }
                }, { signal: sig });
            }
            // ── Kecamatan → Kelurahan ─────────────────────────────────────────────
            if (kecEl) {
                kecEl.addEventListener('change', async function() {
                    if (kelEl) { kelEl.innerHTML = '<option value="">-- Memuat Kelurahan... --</option>'; }
                    var opt   = this.options[this.selectedIndex];
                    var kecId = opt ? opt.getAttribute('data-id') : null;
                    if (!kecId || !kelEl) {
                        if (kelEl) kelEl.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
                        return;
                    }
                    var data = await fetchJsonRegion('/kesiswaan/api/kelurahan/' + kecId);
                    populateSelect(kelEl, data, 'name', 'code', curKel);
                    // Kelurahan tidak perlu trigger event lagi (level terakhir)
                }, { signal: sig });
            }
            loadProvinsi();
        }
        // ── doInitEdit ──────────────────────────────────────────────────────────────
        function doInitEdit(alpine, actionUrl, itemData) {
            alpine.editActionUrl = actionUrl;
            alpine.currentStep   = 1;
            alpine.openEdit      = true;
            setTimeout(function() {
                var form = document.getElementById('formEditSiswa');
                if (!form) { console.error('Form tidak ditemukan'); return; }
                function fillField(el, value) {
                    if (!el || value === null || value === undefined || value === '') return;
                    var v = String(value);
                    if (el.tagName === 'SELECT') {
                        el.value = v;
                    } else if (el.type === 'date') {
                        el.value = v.indexOf('T') !== -1 ? v.split('T')[0] : v;
                    } else {
                        el.value = v;
                    }
                }
                // Isi data siswa
                ['nama_lengkap','nik','nipd','nisn','jenis_kelamin','tempat_lahir',
                'tanggal_lahir','agama','nomor_hp','asal_sekolah','no_peserta_un',
                'anak_ke','alamat_lengkap','rt','rw','kode_pos','tingkat',
                'semester_id','diterima_pada_tanggal'
                ].forEach(function(key) {
                    fillField(form.querySelector('[name="' + key + '"]'), itemData[key]);
                });
                // Isi data wali
                var waliKeys = ['nama_lengkap','nik','jenis_kelamin','tempat_lahir','tanggal_lahir',
                    'agama','pendidikan_terakhir','pekerjaan','penghasilan_bulanan',
                    'alamat_lengkap','rt','rw','kode_pos','nomor_hp','email',
                    'nomor_hp_darurat','catatan'];
                [{index:0, data:itemData.ayah_data},
                {index:1, data:itemData.ibu_data},
                {index:2, data:itemData.wali_data}
                ].forEach(function(w) {
                    if (!w.data) return;
                    waliKeys.forEach(function(field) {
                        fillField(
                            form.querySelector('[name="wali[' + w.index + '][' + field + ']"]'),
                            w.data[field]
                        );
                    });
                });
                // Set data-current wilayah (kode Laravolt disimpan langsung)
                function setWilayah(prefix, prov, kota, kec, kel) {
                    var p  = document.getElementById(prefix + '_provinsi');
                    var k  = document.getElementById(prefix + '_kota');
                    var kc = document.getElementById(prefix + '_kecamatan');
                    var kl = document.getElementById(prefix + '_kelurahan');
                    if (p)  p.setAttribute('data-current',  prov || '');
                    if (k)  k.setAttribute('data-current',  kota || '');
                    if (kc) kc.setAttribute('data-current', kec  || '');
                    if (kl) kl.setAttribute('data-current', kel  || '');
                }
                setWilayah('edit_siswa',
                    itemData.provinsi, itemData.kota, itemData.kecamatan, itemData.kelurahan_desa);
                setWilayah('edit_ayah',
                    itemData.ayah_data ? itemData.ayah_data.provinsi : '',
                    itemData.ayah_data ? itemData.ayah_data.kota : '',
                    itemData.ayah_data ? itemData.ayah_data.kecamatan : '',
                    itemData.ayah_data ? itemData.ayah_data.kelurahan_desa : '');
                setWilayah('edit_ibu',
                    itemData.ibu_data ? itemData.ibu_data.provinsi : '',
                    itemData.ibu_data ? itemData.ibu_data.kota : '',
                    itemData.ibu_data ? itemData.ibu_data.kecamatan : '',
                    itemData.ibu_data ? itemData.ibu_data.kelurahan_desa : '');
                setWilayah('edit_wali',
                    itemData.wali_data ? itemData.wali_data.provinsi : '',
                    itemData.wali_data ? itemData.wali_data.kota : '',
                    itemData.wali_data ? itemData.wali_data.kecamatan : '',
                    itemData.wali_data ? itemData.wali_data.kelurahan_desa : '');
                // Init dropdown (otomatis bersihkan listener lama)
                initRegionDropdowns('edit_siswa');
                initRegionDropdowns('edit_ayah');
                initRegionDropdowns('edit_ibu');
                initRegionDropdowns('edit_wali');
            }, 150);
        }
        // ── Init CREATE form saat halaman dimuat ────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            initRegionDropdowns('siswa');
            initRegionDropdowns('ayah');
            initRegionDropdowns('ibu');
            initRegionDropdowns('wali');
        });
    </script>
</x-app-layout>