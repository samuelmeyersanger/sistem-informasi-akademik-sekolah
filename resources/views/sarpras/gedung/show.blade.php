<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('sarpras.gedung.index') }}" class="hover:text-indigo-600 transition-colors">Gedung</a>
            <span>&raquo;</span>
            <span class="text-gray-800 font-medium">Detail Ruangan</span>
        </div>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editNamaRuangan: '',
        editKodeRuangan: '',
        editKapasitas: 0,

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(r) {
            this.editActionUrl = `/sarpras/gedung/ruangan/${r.id}`;
            this.editNamaRuangan = r.nama_ruangan;
            this.editKodeRuangan = r.kode_ruangan;
            this.editKapasitas = r.kapasitas;
            this.openEdit = true;
        },

        initDelete(actionUrl, rName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = rName;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-2xl text-indigo-600 text-2xl">
                        🏢
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-xs uppercase bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded">
                                {{ $gedung->kode_gedung }}
                            </span>
                            <h3 class="text-lg font-bold text-gray-900">{{ $gedung->nama_gedung }}</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $gedung->deskripsi ?? 'Tidak ada deskripsi tambahan mengenai gedung ini.' }}
                        </p>
                        <div class="mt-2 text-[11px] text-slate-500 font-medium">
                            📁 Struktur Bangunan: <span class="text-slate-800">{{ $gedung->jumlah_lantai }} Lantai</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('sarpras.gedung.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors text-center shrink-0">
                    ⬅️ Kembali ke Gedung
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Pemetaan Ruangan Fisik</h4>
                        <p class="text-xs text-gray-500">Daftar ruangan yang berada di dalam bangunan {{ $gedung->nama_gedung }}.</p>
                    </div>
                    <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer shrink-0">
                        ➕ Tambah Ruangan
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Info Ruangan</th>
                                <th class="p-4 text-center">Kapasitas</th>
                                <th class="p-4 text-center">Total Inventaris</th>
                                <th class="p-4 pr-6 text-center w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($gedung->ruangan as $r)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-base">🚪</span>
                                            <span class="font-mono font-bold text-indigo-600 text-sm">[{{ $r->kode_ruangan }}]</span>
                                            <span class="font-bold text-gray-900 text-sm">{{ $r->nama_ruangan }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center font-medium text-gray-800">
                                        {{ $r->kapasitas }} Orang
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded font-bold text-[10px]">
                                            📦 {{ $r->inventaris_count }} Barang
                                        </span>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('sarpras.gedung.showRuangan', $r->id) }}" class="p-1 text-indigo-600 hover:underline font-bold">
                                                👁️ Lihat Inventaris
                                            </a>

                                            <button type="button" @click="initEdit({{ json_encode($r) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                                📝 Edit
                                            </button>

                                            <button type="button" @click="initDelete('{{ route('sarpras.gedung.destroyRuangan', $r->id) }}', '{{ addslashes($r->nama_ruangan) }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        Belum ada data ruangan terdaftar di gedung ini.
                                    </td>
                                endforeach
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Ruangan Baru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('sarpras.gedung.storeRuangan', $gedung->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kode *</label>
                            <input type="text" name="kode_ruangan" required placeholder="R-101" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Ruangan *</label>
                            <input type="text" name="nama_ruangan" required placeholder="Ruang Kelas X-A" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kapasitas (Orang) *</label>
                        <input type="number" name="kapasitas" required min="0" value="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Ruangan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Data Ruangan</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kode *</label>
                            <input type="text" x-model="editKodeRuangan" name="kode_ruangan" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Ruangan *</label>
                            <input type="text" x-model="editNamaRuangan" name="nama_ruangan" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kapasitas (Orang) *</label>
                        <input type="number" x-model="editKapasitas" name="kapasitas" required min="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
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
                    <h4 class="text-sm font-bold text-gray-900">Hapus Data Ruangan?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus ruangan <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Seluruh aset barang inventaris di dalamnya akan diset tanpa ruangan (Soft Delete aktif).
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