<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Semester') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editNama: '',
        editSemesterKe: '',
        editTahunAjaranId: '',
        editIsAktif: '0',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(sem) {
            // Menyesuaikan URL target dengan penamaan route master.semester
            this.editActionUrl = `/master/semester/${sem.id}`;
            this.editNama = sem.nama;
            this.editSemesterKe = sem.semester_ke;
            this.editTahunAjaranId = sem.tahun_ajaran_id;
            this.editIsAktif = sem.is_aktif ? '1' : '0';
            this.openEdit = true;
        },

        initDelete(actionUrl, semName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = semName;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>⚠️</span> {{ session('error') }}
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
                        <h3 class="text-base font-bold text-gray-900">Daftar Pembagian Semester</h3>
                        <p class="text-xs text-gray-500">Manajemen masa berlaku penilaian dan KBM siswa. Pastikan tahun ajaran yang sesuai telah terikat.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('master.semester.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Semester / Tahun Ajaran..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-64 pr-8">
                                
                                @if(request('search'))
                                    <a href="{{ route('master.semester.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Clear Search">
                                        &times;
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer shrink-0">
                                🔍
                            </button>
                        </form>

                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer shrink-0">
                            ➕ Tambah Semester
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama / Periode</th>
                                <th class="p-4 text-center">Semester Ke-</th>
                                <th class="p-4">Tahun Ajaran Induk</th>
                                <th class="p-4 text-center">Status Jalan</th>
                                <th class="p-4 pr-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($semesters as $sem)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 font-medium text-gray-950">
                                        📝 Semester {{ $sem->nama }}
                                    </td>
                                    <td class="p-4 text-center font-mono font-bold text-gray-600">
                                        {{ $sem->semester_ke }}
                                    </td>
                                    <td class="p-4 text-gray-600">
                                        📅 {{ $sem->tahunAjaran->nama_tahun_ajaran ?? 'Tidak terikat Tahun Ajaran' }}
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($sem->is_aktif)
                                            <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold uppercase rounded-md shadow-sm">
                                                🟢 Aktif Saat Ini
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-gray-50 border border-gray-200 text-gray-400 text-[10px] font-medium uppercase rounded-md">
                                                Arsip Lama
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button @click="initEdit({{ json_encode($sem) }})" class="p-1 text-blue-600 hover:text-blue-800 hover:underline font-medium cursor-pointer transition-colors">
                                                📝 Edit
                                            </button>

                                            <button @click="initDelete('{{ route('master.semester.destroy', $sem->id) }}', 'Semester {{ addslashes($sem->nama) }}')" type="button" class="p-1 text-rose-600 hover:text-rose-800 hover:underline font-medium cursor-pointer transition-colors">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        @if(request('search'))
                                            ❌ Hasil pencarian dengan kata kunci "{{ request('search') }}" tidak ditemukan.
                                        |@else
                                            Belum ada data semester terdaftar.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($semesters->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $semesters->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Semester Baru</h3>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-xl cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('master.semester.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Semester *</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Ganjil atau Genap" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan Semester Ke- *</label>
                        <input type="number" name="semester_ke" value="{{ old('semester_ke') }}" required min="1" max="12" placeholder="Misal: 1 atau 2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Hubungkan ke Tahun Ajaran *</label>
                        <select name="tahun_ajaran_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} @if($ta->is_aktif) (Aktif) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status KBM *</label>
                        <select name="is_aktif" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="0" {{ old('is_aktif') == '0' ? 'selected' : '' }}>Non-Aktif (Arsip)</option>
                            <option value="1" {{ old('is_aktif') == '1' ? 'selected' : '' }}>Aktif (Gunakan Sekarang)</option>
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 cursor-pointer transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-indigo-700 cursor-pointer transition-colors">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Data Semester</h3>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-xl cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Semester *</label>
                        <input type="text" x-model="editNama" name="nama" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan Semester Ke- *</label>
                        <input type="number" x-model="editSemesterKe" name="semester_ke" required min="1" max="12" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Hubungkan ke Tahun Ajaran *</label>
                        <select x-model="editTahunAjaranId" name="tahun_ajaran_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status KBM *</label>
                        <select x-model="editIsAktif" name="is_aktif" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="0">Non-Aktif (Arsip)</option>
                            <option value="1">Aktif (Gunakan Sekarang)</option>
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 cursor-pointer transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-indigo-700 cursor-pointer transition-colors">Simpan Perubahan</button>
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
                    <h4 class="text-sm font-bold text-gray-900">Hapus Semester Permanen?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus data <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Seluruh data KBM dan riwayat nilai yang terikat dengan semester ini akan ikut terpengaruh di sistem.
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer border border-transparent">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>