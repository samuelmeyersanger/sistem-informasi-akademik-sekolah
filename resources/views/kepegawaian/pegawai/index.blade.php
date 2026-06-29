<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kepegawaian') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openDelete: false,
        openGeneratePegawai: false, 
        openImport: false, // 🟢 State baru untuk membuka modal import Excel

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initDelete(actionUrl, namaPegawai) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = namaPegawai;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>ℹ️</span> {{ session('info') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>❌</span> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                    <p class="font-bold mb-1 flex items-center gap-1">⚠️ Gagal menyimpan data:</p>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Pegawai & Tenaga Kependidikan</h3>
                        <p class="text-xs text-gray-500">Kelola informasi biodata profil, mutasi, pensiun, berkas arsip, serta kepangkatan pegawai secara terpusat.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('kepegawaian.pegawai.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                                
                                @if(request('search'))
                                    <a href="{{ route('kepegawaian.pegawai.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Clear Search">
                                        &times;
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer shrink-0">
                                🔍 Cari
                            </button>
                        </form>

                        <a href="{{ route('kepegawaian.pegawai.downloadTemplate') }}" class="px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg shadow-sm border border-emerald-200 transition-all flex items-center justify-center gap-1 cursor-pointer">
                            📥 Template Excel
                        </a>

                        <button @click="openImport = true" class="px-3 py-2 bg-teal-600 hover:bg-teal-700 text-black text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer">
                            📤 Import Excel
                        </button>

                        <button @click="openGeneratePegawai = true" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer border border-indigo-200">
                            ⚡ Generate Akun Massal
                        </button>

                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Tambah Pegawai Baru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Lengkap & NIP</th>
                                <th class="p-4">Jenis PTK</th>
                                <th class="p-4">Status & Golongan</th>
                                <th class="p-4 text-center">Keaktifan</th>
                                <th class="p-4 pr-6 text-center w-56">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($pegawai as $p)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="font-bold text-gray-900 text-sm">👤 {{ $p->nama_lengkap }}</div>
                                        <div class="text-gray-400 text-[11px] mt-0.5">NIP: {{ $p->nip ?? '-' }} | NUPTK: {{ $p->nuptk ?? '-' }}</div>
                                        
                                        @if($p->user_id && $p->user)
                                            <div class="text-[10px] text-indigo-600 font-medium mt-1 flex items-center gap-1">
                                                <span>📧 Acc:</span> <span class="bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100">{{ $p->user->email }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[11px] font-medium">
                                            {{ $p->jenis_ptk }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-medium text-gray-800">{{ $p->status_pegawai }}</div>
                                        <div class="text-gray-400 text-[10px]">{{ $p->pangkat_golongan ?? 'Belum ada pangkat' }}</div>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($p->status_keaktifan == 'Aktif')
                                            <span class="px-2 py-0.5 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold rounded">🟢 AKTIF</span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            
                                            @if(!$p->user_id && $p->status_keaktifan == 'Aktif')
                                                <form action="{{ route('kepegawaian.pegawai.generateIndividu', $p->id) }}" method="POST" class="m-0 inline">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-700 font-bold rounded text-[10px] cursor-pointer transition-colors">
                                                        🔑 Buat Akun
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('kepegawaian.pegawai.show', $p->id) }}" class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-semibold rounded text-[10px] border border-indigo-100 transition-colors">
                                                🔍 Detail
                                            </a>
                                            
                                            <button type="button" @click="initDelete('{{ route('kepegawaian.pegawai.destroy', $p->id) }}', '{{ addslashes($p->nama_lengkap) }}')" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold rounded text-[10px] border border-rose-100 cursor-pointer transition-colors">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        @if(request('search'))
                                            ❌ Hasil pencarian "{{ request('search') }}" tidak ditemukan.
                                        @else
                                            Belum ada data pegawai yang terdaftar.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Pegawai Baru</h3>
                        <p class="text-[10px] text-emerald-600 font-medium mt-0.5">ℹ️ Semester akan otomatis diset ke periode Aktif.</p>
                    </div>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('kepegawaian.pegawai.store') }}" method="POST" class="space-y-3 text-left">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" required placeholder="Nama beserta gelar" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Kelamin *</label>
                            <select name="jenis_kelamin" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-Laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis PTK *</label>
                            <select name="jenis_ptk" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Pilih --</option>
                                <option value="Guru">Guru</option>
                                <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                                <option value="Kepala Sekolah">Kepala Sekolah</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">NIP (Opsional)</label>
                            <input type="text" name="nip" placeholder="Nomor Induk Pegawai" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">NUPTK (Opsional)</label>
                            <input type="text" name="nuptk" placeholder="Nomor NUPTK" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Status Kepegawaian *</label>
                            <select name="status_pegawai" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="HONORER">HONORER</option>
                                <option value="PNS">PNS</option>
                                <option value="PPPK">PPPK</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Pangkat / Golongan Awal</label>
                            <input type="text" name="pangkat_golongan" placeholder="Contoh: Penata / IIIc" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" placeholder="alamat@email.com" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Pegawai</button>
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
                    <h4 class="text-sm font-bold text-gray-900">Hapus Data Pegawai?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus data pegawai <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Rekam jejak dokumen & riwayat SK miliknya akan ikut diarsipkan oleh sistem.
                    </p>
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

        <div x-show="openGeneratePegawai" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openGeneratePegawai = false">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-xl mx-auto border border-indigo-100">
                    ⚡
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Generate Akun Pegawai?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin membuatkan akun masuk sistem untuk <span class="font-bold text-gray-800">SEMUA pegawai aktif</span> yang saat ini belum memiliki akun?
                    </p>
                </div>
                <form action="{{ route('kepegawaian.pegawai.generateMassal') }}" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    <button type="button" @click="openGeneratePegawai = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">
                        Ya, Jalankan
                    </button>
                </form>
            </div>
        </div>

        <div x-show="openImport" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openImport = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Import Data Pegawai Massal</h3>
                        <p class="text-[10px] text-gray-500 mt-0.5">Unggah file format .xlsx atau .xls sesuai dengan template.</p>
                    </div>
                    <button type="button" @click="openImport = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('kepegawaian.pegawai.importExcel') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
                    @csrf
                    
                    <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl space-y-2">
                        <span class="text-xs font-bold text-indigo-900 block">💡 Petunjuk Singkat:</span>
                        <ul class="list-disc list-inside text-[11px] text-indigo-700/90 space-y-1">
                            <li>Gunakan tombol <span class="font-semibold">"Template Excel"</span> untuk mengunduh format yang benar.</li>
                            <li>Gunakan dropdown otomatis yang tersedia di Excel untuk kolom <span class="italic">Jenis Kelamin, Status Pegawai</span>, dan <span class="italic">Jenis PTK</span>.</li>
                            <li>Sistem akan meng-update otomatis data jika NIP yang diunggah sudah ada di database.</li>
                        </ul>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Dokumen Excel *</label>
                        <input type="file" name="file_excel" required class="w-full text-xs text-gray-500 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none p-2 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openImport = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-black text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Mulai Unggah & Proses</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>