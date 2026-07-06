<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kelas & Wali Kelas') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editTingkat: '',
        editNamaKelas: '',
        editWaliKelasId: '',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(actionUrl, tingkat, namaKelas, waliKelasId) {
            this.editActionUrl = actionUrl;
            this.editTingkat = tingkat;
            this.editNamaKelas = namaKelas;
            this.editWaliKelasId = waliKelasId ? waliKelasId : '';
            this.openEdit = true;
        },

        initDelete(actionUrl, namaKelas) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = namaKelas;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
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
                        <h3 class="text-base font-bold text-gray-900">Daftar Ruang Kelas & Plotting Wali Kelas</h3>
                        <p class="text-xs text-gray-500">Kelola master data ruang kelas, penentuan tingkat belajar, penugasan Wali Kelas, serta manajemen jadwal KBM.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('kesiswaan.kelas.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tingkat atau kelas..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                                
                                @if(request('search'))
                                    <a href="{{ route('kesiswaan.kelas.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Clear Search">
                                        &times;
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer shrink-0">
                                🔍 Cari
                            </button>
                        </form>
                        @if(auth()->user()->hasPermission('kesiswaan.kelas.store'))
                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Tambah Kelas Baru
                        </button>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 text-center w-28">Tingkat</th>
                                <th class="p-4">Nama Ruang Kelas</th>
                                <th class="p-4">Wali Kelas Utama</th>
                                <th class="p-4 pr-6 text-center w-72">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($kelas as $item)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 text-center">
                                        <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold rounded-lg text-[11px] shadow-inner">
                                            GRADE {{ $item->tingkat }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900 text-sm">🏫 Kelas {{ $item->nama_kelas }}</div>
                                    </td>
                                    <td class="p-4">
                                        @if($item->waliKelas)
                                            <div class="font-bold text-gray-800 text-xs">👤 {{ $item->waliKelas->nama_lengkap }}</div>
                                            <div class="text-gray-400 text-[10px] mt-0.5">NIP: {{ $item->waliKelas->nip ?? '-' }} | {{ $item->waliKelas->jenis_ptk }}</div>
                                        @else
                                            <span class="px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-bold rounded">⚠️ BELUM DI-PLOTTING</span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('kesiswaan.kelas.show', $item->id) }}" class="p-1 text-teal-600 hover:underline font-semibold flex items-center gap-0.5">
                                                🔍 Detail Kelas
                                            </a>
                                            
                                            <a href="{{ route('kesiswaan.kelas.jadwal', $item->id) }}" class="p-1 text-emerald-600 hover:underline font-semibold flex items-center gap-0.5">
                                                📅 Lihat Jadwal
                                            </a>

                                            <button type="button" @click="initEdit('{{ route('kesiswaan.kelas.update', $item->id) }}', '{{ $item->tingkat }}', '{{ $item->nama_kelas }}', '{{ $item->wali_kelas_id }}')" class="p-1 text-indigo-600 hover:underline font-semibold cursor-pointer">
                                                ✏️ Edit
                                            </button>
                                            @if(auth()->user()->hasPermission('kesiswaan.kelas.destroy'))
                                            <button type="button" @click="initDelete('{{ route('kesiswaan.kelas.destroy', $item->id) }}', 'Kelas {{ addslashes($item->nama_kelas) }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        @if(request('search'))
                                            ❌ Hasil pencarian "{{ request('search') }}" tidak ditemukan.
                                        @else
                                            Belum ada master data kelas yang terdaftar.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($kelas->hasPages())
                <div class="mt-4">
                    {{ $kelas->links() }}
                </div>
            @endif
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Ruang Kelas Baru</h3>
                        <p class="text-[10px] text-emerald-600 font-medium mt-0.5">ℹ️ Kelas akan dikunci otomatis pada periode Semester Aktif.</p>
                    </div>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('kesiswaan.kelas.store') }}" method="POST" class="space-y-3 text-left">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tingkat Kelas *</label>
                        <select name="tingkat" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="7">7 (Tujuh)</option>
                            <option value="8">8 (Delapan)</option>
                            <option value="9">9 (Sembilan)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Ruang Kelas *</label>
                        <input type="text" name="nama_kelas" required placeholder="Contoh: 7-A, 8B, IX-C" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Wali Kelas (Opsional)</label>
                        <select name="wali_kelas_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Tanpa Wali Kelas Dahulu --</option>
                            @foreach($guru_list as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }} ({{ $guru->jenis_ptk }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">Hanya menampilkan daftar pegawai aktif berjenis tugas Guru/Kepala Sekolah.</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Kelas</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Ubah Detail Ruang Kelas</h3>
                        <p class="text-[10px] text-indigo-600 font-medium mt-0.5">ℹ️ Perubahan plotting wali kelas akan langsung memperbarui hak akses presensi kelas.</p>
                    </div>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                
                <form :action="editActionUrl" method="POST" class="space-y-3 text-left">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tingkat Kelas *</label>
                        <select name="tingkat" x-model="editTingkat" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="7">7 (Tujuh)</option>
                            <option value="8">8 (Delapan)</option>
                            <option value="9">9 (Sembilan)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Ruang Kelas *</label>
                        <input type="text" name="nama_kelas" x-model="editNamaKelas" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Wali Kelas</label>
                        <select name="wali_kelas_id" x-model="editWaliKelasId" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Tanpa Wali Kelas --</option>
                            @foreach($guru_list as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }} ({{ $guru->jenis_ptk }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
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
                    <h4 class="text-sm font-bold text-gray-900">Hapus Master Ruang Kelas?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus data <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>?
                    </p>
                    <p class="text-[10px] text-rose-600 mt-2 bg-rose-50/70 p-2.5 rounded-lg leading-relaxed border border-rose-100">
                        🚨 <strong>Peringatan Cascade Hapus:</strong> Seluruh data rekam jejak pemetaan siswa (anggota aktif) di dalam kelas ini akan ikut terhapus otomatis dari basis data.
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">
                        Ya, Hapus Kelas
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>