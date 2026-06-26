<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Data Master Mata Pelajaran
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Form States Edit
        editActionUrl: '',
        editNomorUrut: 0,
        editNamaMapel: '',
        editSingkatanMapel: '',
        editJumlahJam: 0,

        // Form States Hapus
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(m) {
            this.editActionUrl = `/akademik/mata-pelajaran/${m.id}`;
            this.editNomorUrut = m.nomor_urut;
            this.editNamaMapel = m.nama_mapel;
            this.editSingkatanMapel = m.singkatan_mapel ?? '';
            this.editJumlahJam = m.jumlah_jam;
            this.openEdit = true;
        },

        initDelete(actionUrl, itemName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = itemName;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('akademik.mata-pelajaran.index') }}" class="w-full sm:w-auto flex items-center gap-2">
                    <div class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau singkatan mapel..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pl-3 pr-8">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('akademik.mata-pelajaran.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs text-center rounded-lg font-medium">Reset</a>
                    @endif
                </form>

                <button @click="openCreate = true" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    ➕ Tambah Mata Pelajaran
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-20 text-center">No. Urut</th>
                                <th class="p-4">Nama Mata Pelajaran</th>
                                <th class="p-4 w-40">Singkatan</th>
                                <th class="p-4 text-center w-32">Beban Waktu (Jam)</th>
                                <th class="p-4 pr-6 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($mataPelajaran as $m)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-4 pl-6 text-center font-mono font-bold text-gray-400">
                                        {{ $m->nomor_urut }}
                                    </td>
                                    <td class="p-4 font-bold text-gray-900 text-sm">
                                        {{ $m->nama_mapel }}
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-mono font-semibold text-[11px]">
                                            {{ $m->singkatan_mapel ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center font-medium text-gray-900 text-sm">
                                        {{ $m->jumlah_jam }} JP
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-4">
                                            <button type="button" @click="initEdit({{ json_encode($m) }})" class="text-indigo-600 hover:underline font-semibold cursor-pointer">
                                                📝 Edit
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('akademik.mata-pelajaran.destroy', $m->id) }}', '{{ addslashes($m->nama_mapel) }}')" class="text-rose-600 hover:underline font-semibold cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/20">
                                        Belum ada data mata pelajaran yang terekam di dalam sistem.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($mataPelajaran->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $mataPelajaran->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Tambah Mata Pelajaran</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('akademik.mata-pelajaran.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">No. Urut *</label>
                            <input type="number" name="nomor_urut" required min="0" value="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Beban Waktu (Jam) *</label>
                            <input type="number" name="jumlah_jam" required min="0" value="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Mata Pelajaran *</label>
                        <input type="text" name="nama_mapel" required placeholder="Contoh: Matematika Wajib" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Singkatan / Kode Mapel</label>
                        <input type="text" name="singkatan_mapel" placeholder="Contoh: MTK" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase">
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Ubah Mata Pelajaran</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">No. Urut *</label>
                            <input type="number" name="nomor_urut" x-model="editNomorUrut" required min="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Beban Waktu (Jam) *</label>
                            <input type="number" name="jumlah_jam" x-model="editJumlahJam" required min="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Mata Pelajaran *</label>
                        <input type="text" name="nama_mapel" x-model="editNamaMapel" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Singkatan / Kode Mapel</label>
                        <input type="text" name="singkatan_mapel" x-model="editSingkatanMapel" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase">
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
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Arsipkan Mata Pelajaran?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus mata pelajaran <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Log pengajaran dan relasi kode guru yang terkait dengan mapel ini akan dinonaktifkan (Soft Delete).
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">Ya, Hapus</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>