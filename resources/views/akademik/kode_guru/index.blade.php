<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Kode Guru & Penugasan
        </h2>
    </x-slot>

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
            // Inisialisasi Multiselect pada form Tambah Data
            this.selectCreateInstance = new TomSelect('#create-mapel-select', {
                plugins: ['remove_button'],
                placeholder: 'Pilih satu atau beberapa mapel...',
                create: false
            });

            // Inisialisasi Multiselect pada form Edit Data
            this.selectEditInstance = new TomSelect('#edit-mapel-select', {
                plugins: ['remove_button'],
                placeholder: 'Pilih satu atau beberapa mapel...',
                create: false
            });
        },

        initEdit(k) {
            this.editActionUrl = `/akademik/kode-guru/${k.id}`;
            this.editKode = k.kode;
            this.editPegawaiId = k.pegawai_id ?? '';
            
            // Ambil array ID mata pelajaran dari relasi pivot Many-to-Many
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
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl shadow-sm">
                    <p class="font-bold mb-1">⚠️ Gagal menyimpan data:</p>
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('akademik.kode-guru.index') }}" class="w-full md:w-auto flex items-center gap-2">
                    <div class="relative w-full md:w-80">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama guru, atau mapel..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pl-3 pr-8">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('akademik.kode-guru.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs text-center rounded-lg font-medium">Reset</a>
                    @endif
                </form>

                <button @click="openCreate = true; if(selectCreateInstance) selectCreateInstance.clear();" class="w-full md:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    ➕ Tambah Penugasan Kode Guru
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-32 text-center">Kode</th>
                                <th class="p-4 w-64">Identitas Guru</th>
                                <th class="p-4">Mata Pelajaran Diampu</th>
                                <th class="p-4 text-center w-32">Total Beban</th>
                                <th class="p-4 pr-6 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($kodeGuru as $k)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-4 pl-6 text-center">
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full font-mono font-bold text-xs uppercase">
                                            {{ $k->kode }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900 text-sm">
                                            {{ $k->pegawai->nama_lengkap ?? 'Belum Ditentukan' }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">
                                            NIP/ID: {{ $k->pegawai->nip ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse($k->mataPelajarans as $mp)
                                                <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-md text-[11px] font-semibold border border-indigo-100 shadow-sm">
                                                    📚 {{ $mp->nama_mapel }} 
                                                    <span class="ml-1 px-1 bg-indigo-200 text-indigo-800 rounded text-[9px] font-bold">
                                                        {{ $mp->pivot->jam_mengajar_porsi }} JP
                                                    </span>
                                                </span>
                                            @empty
                                                <span class="text-gray-400 italic text-[11px]">Belum mendaftarkan mata pelajaran.</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-4 text-center font-bold text-gray-950 text-sm bg-gray-50/30">
                                        {{ $k->total_jam_mengajar }} <span class="text-[10px] text-gray-400 font-normal">JP</span>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-4">
                                            <button type="button" @click="initEdit({{ json_encode($k) }})" class="text-indigo-600 hover:underline font-semibold cursor-pointer">
                                                📝 Edit
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('akademik.kode-guru.destroy', $k->id) }}', '{{ addslashes($k->kode) }} - {{ addslashes($k->pegawai->nama_lengkap ?? '') }}')" class="text-rose-600 hover:underline font-semibold cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/20">
                                        Data penugasan guru belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kodeGuru->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $kodeGuru->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-show="openCreate" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Penugasan Guru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('akademik.kode-guru.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Guru *</label>
                        <input type="text" name="kode" required placeholder="Cth: G01" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase font-mono font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Guru / Pegawai *</label>
                        <select name="pegawai_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($daftarPegawai as $peg)
                                <option value="{{ $peg->id }}">{{ $peg->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Mata Pelajaran (Bisa Lebih dari 1) *</label>
                        <select name="mata_pelajaran_ids[]" id="create-mapel-select" multiple required class="w-full text-xs">
                            @foreach($daftarMapel as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->jumlah_jam }} JP)</option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-gray-400 mt-1 block">💡 Beban jam mengajar akan dihitung akumulatif berdasarkan mapel yang dipilih.</span>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" x-show="openEdit" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Ubah Data Penugasan</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Guru *</label>
                        <input type="text" name="kode" x-model="editKode" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase font-mono font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Guru / Pegawai *</label>
                        <select name="pegawai_id" x-model="editPegawaiId" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($daftarPegawai as $peg)
                                <option value="{{ $peg->id }}">{{ $peg->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Mata Pelajaran (Bisa Lebih dari 1) *</label>
                        <select name="mata_pelajaran_ids[]" id="edit-mapel-select" multiple required class="w-full text-xs">
                            @foreach($daftarMapel as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->jumlah_jam }} JP)</option>
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

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-show="openDelete" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Arsipkan Kode Guru?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus kode guru <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Penugasan mengajar pada jadwal pelajaran terkait akan ikut terputus (Soft Delete).
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