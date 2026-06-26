<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sarpras - Manajemen Gedung') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editNamaGedung: '',
        editKodeGedung: '',
        editJumlahLantai: 1,
        editDeskripsi: '',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(g) {
            this.editActionUrl = `/sarpras/gedung/${g.id}`;
            this.editNamaGedung = g.nama_gedung;
            this.editKodeGedung = g.kode_gedung;
            this.editJumlahLantai = g.jumlah_lantai;
            this.editDeskripsi = g.deskripsi ?? '';
            this.openEdit = true;
        },

        initDelete(actionUrl, gName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = gName;
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
                        <h3 class="text-base font-bold text-gray-900">Daftar Gedung Sekolah</h3>
                        <p class="text-xs text-gray-500">Kelola bangunan fisik sekolah sebelum memetakan ruangan dan mengorganisasi barang inventaris.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('sarpras.gedung.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari gedung atau kode..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                                
                                @if(request('search'))
                                    <a href="{{ route('sarpras.gedung.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Clear Search">
                                        &times;
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer shrink-0">
                                🔍 Cari
                            </button>
                        </form>

                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Tambah Gedung
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Info Gedung</th>
                                <th class="p-4 text-center">Fasilitas</th>
                                <th class="p-4 pr-6 text-center w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($gedung as $g)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-base">🏢</span>
                                            <span class="font-mono font-bold text-indigo-600 text-sm">[{{ $g->kode_gedung }}]</span>
                                            <span class="font-bold text-gray-900 text-sm">{{ $g->nama_gedung }}</span>
                                        </div>
                                        @if($g->deskripsi)
                                            <p class="text-gray-400 text-[11px] mt-0.5 pl-5">{{ $g->deskripsi }}</p>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex flex-col sm:flex-row items-center justify-center gap-1.5">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[10px] font-medium border border-slate-200">
                                                📶 {{ $g->jumlah_lantai }} Lantai
                                            </span>
                                            <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] font-bold rounded-md shadow-sm">
                                                🚪 {{ $g->ruangan_count }} Ruangan
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('sarpras.gedung.show', $g->id) }}" class="p-1 text-indigo-600 hover:underline font-bold">
                                                🔍 Detail
                                            </a>

                                            <button type="button" @click="initEdit({{ json_encode($g) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                                📝 Edit
                                            </button>

                                            <button type="button" @click="initDelete('{{ route('sarpras.gedung.destroy', $g->id) }}', '{{ addslashes($g->nama_gedung) }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        @if(request('search'))
                                            ❌ Hasil pencarian "{{ request('search') }}" tidak ditemukan.
                                        @else
                                            Belum ada data gedung terdaftar.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($gedung->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $gedung->links() }}
                    </div>
                @endif
            </div>

        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Gedung Baru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('sarpras.gedung.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kode *</label>
                            <input type="text" name="kode_gedung" required placeholder="GD-A" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Gedung *</label>
                            <input type="text" name="nama_gedung" required placeholder="Gedung Utama" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Lantai *</label>
                        <input type="number" name="jumlah_lantai" required min="1" value="1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi / Keterangan</label>
                        <textarea name="deskripsi" placeholder="Keterangan peruntukan gedung..." rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
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
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Data Gedung</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kode *</label>
                            <input type="text" x-model="editKodeGedung" name="kode_gedung" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Gedung *</label>
                            <input type="text" x-model="editNamaGedung" name="nama_gedung" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Lantai *</label>
                        <input type="number" x-model="editJumlahLantai" name="jumlah_lantai" required min="1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi / Keterangan</label>
                        <textarea x-model="editDeskripsi" name="deskripsi" rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
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
                    <h4 class="text-sm font-bold text-gray-900">Hapus Data Gedung?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus gedung <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Seluruh ruangan di dalam gedung ini akan diset kosong (unlinked), namun data fisik ruangan tidak terhapus (Soft Delete aktif).
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