<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">📚</span> {{ __('Data Master Mata Pelajaran') }}
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
    }" class="py-10 bg-slate-50 min-h-screen">
        
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Pesan Sukses / Error -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            <!-- Toolbar Atas: Pencarian & Tombol Tambah -->
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('akademik.mata-pelajaran.index') }}" class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:w-80">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau singkatan mapel..." class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner py-3 pl-12 pr-4 transition-colors">
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl text-sm transition-transform transform hover:-translate-y-0.5 shadow-md">
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('akademik.mata-pelajaran.index') }}" class="w-full sm:w-auto px-5 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-sm text-center rounded-xl font-bold transition-colors">Reset</a>
                        @endif
                    </div>
                </form>

                <button @click="openCreate = true" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <span class="text-lg">➕</span> Tambah Mapel
                </button>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 text-center w-24">No. Urut</th>
                                <th class="p-5">Nama Mata Pelajaran</th>
                                <th class="p-5 w-48 text-center">Singkatan</th>
                                <th class="p-5 text-center w-40">Beban Waktu</th>
                                <th class="p-5 pr-8 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($mataPelajaran as $m)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    
                                    <!-- Kolom No Urut -->
                                    <td class="p-5 pl-8 text-center align-middle">
                                        <span class="w-10 h-10 inline-flex items-center justify-center bg-gray-50 rounded-xl font-mono font-black text-gray-500 border border-gray-100 group-hover:bg-white transition-colors">
                                            {{ $m->nomor_urut }}
                                        </span>
                                    </td>
                                    
                                    <!-- Kolom Nama Mapel -->
                                    <td class="p-5 align-middle">
                                        <div class="font-black text-gray-900 text-base">
                                            {{ $m->nama_mapel }}
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Singkatan -->
                                    <td class="p-5 align-middle text-center">
                                        @if($m->singkatan_mapel)
                                            <span class="px-4 py-1.5 bg-slate-100 text-slate-700 rounded-lg font-mono font-bold text-xs uppercase border border-slate-200">
                                                {{ $m->singkatan_mapel }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Kolom Beban Jam -->
                                    <td class="p-5 text-center align-middle">
                                        <div class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl font-bold border border-indigo-100">
                                            <span class="text-lg leading-none">{{ $m->jumlah_jam }}</span>
                                            <span class="text-[10px] uppercase tracking-wider">JP</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Aksi -->
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <div class="flex flex-col gap-2 justify-center">
                                            <button type="button" @click="initEdit({{ json_encode($m) }})" class="w-full px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition-colors border border-emerald-100 shadow-sm">
                                                ✏️ Edit
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('akademik.mata-pelajaran.destroy', $m->id) }}', '{{ addslashes($m->nama_mapel) }}')" class="w-full px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors border border-rose-100 shadow-sm">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">📭</span>
                                        <p class="text-lg font-bold text-gray-500">Belum ada data mata pelajaran yang terekam.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($mataPelajaran->hasPages())
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">
                        {{ $mataPelajaran->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: TAMBAH MATA PELAJARAN -->
        <!-- ========================================== -->
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-gray-100 overflow-hidden" @click.away="openCreate = false">
                <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900">➕ Tambah Mapel</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                <form action="{{ route('akademik.mata-pelajaran.store') }}" method="POST">
                    @csrf
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">No. Urut <span class="text-rose-500">*</span></label>
                                <input type="number" name="nomor_urut" required min="0" value="0" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Beban Waktu (Jam) <span class="text-rose-500">*</span></label>
                                <input type="number" name="jumlah_jam" required min="0" value="0" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Mata Pelajaran <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_mapel" required placeholder="Contoh: Matematika Wajib" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Singkatan / Kode Mapel</label>
                            <input type="text" name="singkatan_mapel" placeholder="Contoh: MTK" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase bg-gray-50 px-4 py-3">
                        </div>
                    </div>
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl transition-colors shadow-sm">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors">💾 Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: EDIT MATA PELAJARAN -->
        <!-- ========================================== -->
        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-gray-100 overflow-hidden" @click.away="openEdit = false">
                <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900">✏️ Ubah Mapel</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">No. Urut <span class="text-rose-500">*</span></label>
                                <input type="number" name="nomor_urut" x-model="editNomorUrut" required min="0" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Beban Waktu (Jam) <span class="text-rose-500">*</span></label>
                                <input type="number" name="jumlah_jam" x-model="editJumlahJam" required min="0" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Mata Pelajaran <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_mapel" x-model="editNamaMapel" required class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Singkatan / Kode Mapel</label>
                            <input type="text" name="singkatan_mapel" x-model="editSingkatanMapel" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase bg-gray-50 px-4 py-3">
                        </div>
                    </div>
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openEdit = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl transition-colors shadow-sm">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-md transition-colors">💾 Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- ⚠️ POPUP MODAL HAPUS (DESAIN MODERN SWEET-ALERT STYLE) -->
        <!-- ========================================================================= -->
        <div x-show="openDelete" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div x-show="openDelete" @click="openDelete = false" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" aria-hidden="true"></div>
                
                <div x-show="openDelete" x-transition.scale.origin.center class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-8 text-center">
                    
                    <!-- Ikon Peringatan -->
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 mb-6 border border-rose-100">
                        <span class="text-4xl">⚠️</span>
                    </div>
                    
                    <!-- Teks -->
                    <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-4" id="modal-title">Arsipkan Mapel?</h3>
                    <p class="text-sm text-gray-500 mb-8 px-2">
                        Apakah Anda yakin ingin menghapus mata pelajaran <strong class="text-gray-800" x-text="deleteTargetName"></strong>? Log pengajaran dan relasi kode guru yang terkait dengan mapel ini akan ikut dinonaktifkan (Soft Delete).
                    </p>
                    
                    <!-- Form dan Tombol -->
                    <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 m-0">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="openDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl transition-colors focus:outline-none">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition-colors shadow-md focus:outline-none">
                            Ya, Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>