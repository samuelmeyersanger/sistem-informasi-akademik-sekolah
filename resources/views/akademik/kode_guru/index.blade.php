<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">👨‍🏫</span> {{ __('Manajemen Kode Guru & Penugasan') }}
        </h2>
    </x-slot>

    <!-- Pustaka Tom Select (Dipertahankan) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Form States Edit
        editActionUrl: '',
        editKode: '',
        editPegawaiId: '',

        // Form States Hapus
        deleteActionUrl: '',
        deleteTargetName: '',

        // Holder untuk instance Tom Select
        selectCreateInstance: null,
        selectEditInstance: null,

        init() {
            this.selectCreateInstance = new TomSelect('#create-mapel-select', {
                plugins: ['remove_button'],
                placeholder: 'Ketik atau pilih mapel...',
                create: false
            });

            this.selectEditInstance = new TomSelect('#edit-mapel-select', {
                plugins: ['remove_button'],
                placeholder: 'Ketik atau pilih mapel...',
                create: false
            });
        },

        initEdit(k) {
            this.editActionUrl = `/akademik/kode-guru/${k.id}`;
            this.editKode = k.kode;
            this.editPegawaiId = k.pegawai_id ?? '';
            
            let selectedMapelIds = k.mata_pelajarans.map(item => item.id);
            if (this.selectEditInstance) {
                this.selectEditInstance.setValue(selectedMapelIds);
            }
            this.openEdit = true;
        },

        initDelete(actionUrl, itemName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = itemName;
            this.openDelete = true;
        }
    }" class="py-10 bg-slate-50 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Pesan Sukses / Error -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-lg mb-2">
                        <span>⚠️</span> Gagal menyimpan data:
                    </div>
                    <ul class="list-disc pl-6 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Toolbar Atas: Pencarian & Tombol Tambah -->
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('akademik.kode-guru.index') }}" class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:w-96">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama guru, atau mapel..." class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner py-3 pl-12 pr-4 transition-colors">
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl text-sm transition-transform transform hover:-translate-y-0.5 shadow-md">
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('akademik.kode-guru.index') }}" class="w-full sm:w-auto px-5 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-sm text-center rounded-xl font-bold transition-colors">Reset</a>
                        @endif
                    </div>
                </form>

                <button @click="openCreate = true; if(selectCreateInstance) selectCreateInstance.clear();" class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <span class="text-lg">➕</span> Tambah Penugasan
                </button>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 text-center w-28">Kode</th>
                                <th class="p-5 w-72">Identitas Guru</th>
                                <th class="p-5">Beban Mata Pelajaran</th>
                                <th class="p-5 text-center w-36">Total JP</th>
                                <th class="p-5 pr-8 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($kodeGuru as $k)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    
                                    <!-- Kolom Kode -->
                                    <td class="p-5 pl-8 text-center align-middle">
                                        <span class="px-4 py-2 bg-indigo-100 text-indigo-800 rounded-xl font-mono font-black text-sm uppercase shadow-sm border border-indigo-200 block mx-auto w-max">
                                            {{ $k->kode }}
                                        </span>
                                    </td>
                                    
                                    <!-- Kolom Identitas -->
                                    <td class="p-5 align-middle">
                                        <div class="font-black text-gray-900 text-base mb-1">
                                            {{ $k->pegawai->nama_lengkap ?? 'Belum Ditentukan' }}
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md font-semibold">
                                            <span>💳</span> NIP/ID: {{ $k->pegawai->nip ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Mata Pelajaran -->
                                    <td class="p-5 align-middle">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($k->mataPelajarans as $mp)
                                                <div class="inline-flex items-center bg-white border border-gray-200 text-gray-700 rounded-lg text-xs font-bold shadow-sm overflow-hidden group-hover:border-indigo-200 transition-colors">
                                                    <span class="px-3 py-1.5 border-r border-gray-200 bg-gray-50">📚 {{ $mp->nama_mapel }}</span>
                                                    <span class="px-2 py-1.5 bg-indigo-50 text-indigo-700 font-black">{{ $mp->pivot->jam_mengajar_porsi }} JP</span>
                                                </div>
                                            @empty
                                                <span class="text-gray-400 italic text-sm font-medium bg-gray-50 px-3 py-1.5 rounded-lg">🚫 Belum ada mata pelajaran</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Total Beban -->
                                    <td class="p-5 text-center align-middle">
                                        <div class="inline-flex flex-col items-center justify-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner group-hover:bg-white group-hover:border-indigo-100 transition-colors">
                                            <span class="font-black text-gray-900 text-xl leading-none mb-1">{{ $k->total_jam_mengajar }}</span>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jam</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Aksi -->
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <div class="flex flex-col gap-2 justify-center">
                                            <button type="button" @click="initEdit({{ json_encode($k) }})" class="w-full px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition-colors border border-emerald-100 shadow-sm">
                                                ✏️ Edit
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('akademik.kode-guru.destroy', $k->id) }}', '{{ addslashes($k->kode) }} - {{ addslashes($k->pegawai->nama_lengkap ?? '') }}')" class="w-full px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors border border-rose-100 shadow-sm">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">📭</span>
                                        <p class="text-lg font-bold text-gray-500">Data penugasan guru belum tersedia.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kodeGuru->hasPages())
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">
                        {{ $kodeGuru->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: TAMBAH PENUGASAN -->
        <!-- ========================================== -->
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-show="openCreate" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-gray-100 overflow-hidden" @click.away="openCreate = false">
                <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900">➕ Tambah Penugasan Guru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                <form action="{{ route('akademik.kode-guru.store') }}" method="POST">
                    @csrf
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kode Guru <span class="text-rose-500">*</span></label>
                            <input type="text" name="kode" required placeholder="Contoh: G01" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase font-mono font-bold bg-gray-50 px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Pegawai <span class="text-rose-500">*</span></label>
                            <select name="pegawai_id" required class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                                <option value="">-- Cari / Pilih Pegawai --</option>
                                @foreach($daftarPegawai as $peg)
                                    <option value="{{ $peg->id }}">{{ $peg->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Mata Pelajaran (Bisa Lebih dari 1) <span class="text-rose-500">*</span></label>
                            <div class="bg-gray-50 rounded-xl border border-gray-300 p-1">
                                <select name="mata_pelajaran_ids[]" id="create-mapel-select" multiple required class="w-full">
                                    @foreach($daftarMapel as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->jumlah_jam }} JP)</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="text-xs font-semibold text-gray-500 mt-2 flex items-center gap-1">
                                <span>💡</span> Beban jam akan diakumulasi otomatis.
                            </span>
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
        <!-- MODAL: EDIT PENUGASAN -->
        <!-- ========================================== -->
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-show="openEdit" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-gray-100 overflow-hidden" @click.away="openEdit = false">
                <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900">✏️ Ubah Penugasan</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kode Guru <span class="text-rose-500">*</span></label>
                            <input type="text" name="kode" x-model="editKode" required class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase font-mono font-bold bg-gray-50 px-4 py-3">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Pegawai <span class="text-rose-500">*</span></label>
                            <select name="pegawai_id" x-model="editPegawaiId" required class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                                <option value="">-- Cari / Pilih Pegawai --</option>
                                @foreach($daftarPegawai as $peg)
                                    <option value="{{ $peg->id }}">{{ $peg->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Mata Pelajaran <span class="text-rose-500">*</span></label>
                            <div class="bg-gray-50 rounded-xl border border-gray-300 p-1">
                                <select name="mata_pelajaran_ids[]" id="edit-mapel-select" multiple required class="w-full">
                                    @foreach($daftarMapel as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->jumlah_jam }} JP)</option>
                                    @endforeach
                                </select>
                            </div>
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
                    <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-4" id="modal-title">Arsipkan Data Penugasan?</h3>
                    <p class="text-sm text-gray-500 mb-8 px-2">
                        Apakah Anda yakin ingin menghapus data penugasan untuk <strong class="text-gray-800" x-text="deleteTargetName"></strong>? Rekam jejak penugasan mengajarnya akan ikut terputus (Soft Delete).
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