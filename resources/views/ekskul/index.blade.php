<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">🏅</span> {{ __('Manajemen Ekstrakurikuler') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editNama: '',
        editPembinaId: '',
        editHariLatihan: '',
        editJamMulai: '',
        editJamSelesai: '',
        editDeskripsi: '',
        editIsAktif: '1',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(ekskul) {
            this.editActionUrl = `/ekskul/ekstrakurikuler/${ekskul.id}`;
            this.editNama = ekskul.nama;
            this.editPembinaId = ekskul.pembina_id ?? '';
            this.editHariLatihan = ekskul.hari_latihan;
            this.editJamMulai = ekskul.jam_mulai ? ekskul.jam_mulai.substring(0, 5) : '';
            this.editJamSelesai = ekskul.jam_selesai ? ekskul.jam_selesai.substring(0, 5) : '';
            this.editDeskripsi = ekskul.deskripsi ?? '';
            this.editIsAktif = ekskul.is_aktif ? '1' : '0';
            this.openEdit = true;
        },

        initDelete(actionUrl, ekskulName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = ekskulName;
            this.openDelete = true;
        }
    }" class="py-10 bg-slate-50 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Alerts / Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">⚠️</span> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-lg mb-2">
                        <span>⚠️</span> Gagal menyimpan data:
                    </div>
                    <ul class="list-disc pl-6 space-y-1 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Main Table Card --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 mb-1">Daftar Kegiatan Ekstrakurikuler</h3>
                        <p class="text-sm text-gray-500">Manajemen program ekstrakurikuler sekolah, alokasi pembina, beserta jadwal latihan berkala.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('ekskul.ekstrakurikuler.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ekstrakurikuler..." class="text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner w-full sm:w-64 py-3 pl-12 pr-12 transition-colors">
                            
                            @if(request('search'))
                                <a href="{{ route('ekskul.ekstrakurikuler.index') }}" class="absolute inset-y-0 right-16 flex items-center pr-2 text-gray-400 hover:text-rose-500 font-bold text-lg transition-colors cursor-pointer" title="Bersihkan Pencarian">
                                    &times;
                                </a>
                            @endif
                            <button type="submit" class="absolute inset-y-1.5 right-1.5 px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">Cari</button>
                        </form>

                        @if(auth()->user()->hasPermission('ekskul.ekstrakurikuler.store'))
                        <button @click="openCreate = true" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
                            <span class="text-lg">➕</span> Tambah Ekskul
                        </button>
                        @endif 
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8">Nama Ekstrakurikuler</th>
                                <th class="p-5 w-56">Pembina</th>
                                <th class="p-5 w-48">Jadwal Latihan</th>
                                <th class="p-5 text-center w-36">Status</th>
                                <th class="p-5 pr-8 text-center w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($ekskul as $item)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="p-5 pl-8 align-middle">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden shadow-sm shrink-0">
                                                @if($item->logo)
                                                    <img src="{{ asset('storage/' . $item->logo) }}" class="object-cover w-full h-full">
                                                @else
                                                    <span class="text-2xl">🏅</span>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-black text-gray-900 text-base">{{ $item->nama }}</h4>
                                                @if($item->deskripsi)
                                                    <p class="text-[11px] text-gray-500 truncate max-w-[200px]" title="{{ $item->deskripsi }}">{{ $item->deskripsi }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5 align-middle">
                                        <div class="flex items-center gap-2 font-bold text-gray-800 text-sm">
                                            <span class="w-7 h-7 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100 text-xs">👤</span>
                                            {{ $item->pembina->nama_lengkap ?? 'Belum Ditentukan' }}
                                        </div>
                                    </td>
                                    <td class="p-5 align-middle">
                                        <div class="text-sm font-black text-gray-800 mb-1 flex items-center gap-1.5">
                                            <span>📅</span> {{ $item->hari_latihan }}
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded font-semibold border border-gray-200">
                                            ⏱️ {{ $item->jam_mulai ? date('H:i', strtotime($item->jam_mulai)) : '--:--' }} - {{ $item->jam_selesai ? date('H:i', strtotime($item->jam_selesai)) : '--:--' }}
                                        </div>
                                    </td>
                                    <td class="p-5 text-center align-middle">
                                        @if($item->is_aktif)
                                            <span class="px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-black uppercase tracking-wider rounded-lg shadow-sm inline-block w-full">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 bg-gray-100 border border-gray-200 text-gray-500 text-xs font-black uppercase tracking-wider rounded-lg inline-block w-full">
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('ekskul.ekstrakurikuler.show', $item->id) }}" class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors border border-blue-100 shadow-sm" title="Lihat Detail & Anggota">
                                                👁️
                                            </a>

                                            <button type="button" @click="initEdit({{ json_encode($item) }})" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors border border-amber-100 shadow-sm cursor-pointer" title="Edit Data">
                                                📝
                                            </button>

                                            <button type="button" @click="initDelete('{{ route('ekskul.ekstrakurikuler.destroy', $item->id) }}', '{{ addslashes($item->nama) }}')" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition-colors border border-rose-100 shadow-sm cursor-pointer" title="Hapus">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        @if(request('search'))
                                            <span class="text-5xl block mb-4">🔍</span>
                                            <p class="text-lg font-bold text-gray-500">Hasil pencarian "{{ request('search') }}" tidak ditemukan.</p>
                                        @else
                                            <span class="text-5xl block mb-4">📭</span>
                                            <p class="text-lg font-bold text-gray-500">Belum ada data ekstrakurikuler terdaftar.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($ekskul instanceof \Illuminate\Pagination\LengthAwarePaginator && $ekskul->count() > 0)
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">{{ $ekskul->links() }}</div>
                @endif
            </div>
        </div>

        {{-- ================= MODAL FORM: TAMBAH EKSKUL ================= --}}
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl overflow-hidden" @click.away="openCreate = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">➕ Tambah Ekstrakurikuler Baru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('ekskul.ekstrakurikuler.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-8 space-y-5 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Ekskul <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama" required placeholder="Contoh: Pramuka / Paskibra" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pembina Ekskul (Guru)</label>
                                <select name="pembina_id" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    <option value="">-- Pilih Pembina (Opsional) --</option>
                                    @foreach($pembina as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hari Latihan Rutin <span class="text-rose-500">*</span></label>
                                <select name="hari_latihan" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                                        <option value="{{ $hari }}">{{ $hari }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Logo / Icon Ekskul <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <div class="w-full flex items-center justify-center p-2 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 sm:col-span-2 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                                <div>
                                    <label class="block text-xs font-bold text-indigo-900 mb-2">Jam Mulai</label>
                                    <input type="time" name="jam_mulai" class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-indigo-900 mb-2">Jam Selesai</label>
                                    <input type="time" name="jam_selesai" class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat Kegiatan</label>
                                <textarea name="deskripsi" rows="3" placeholder="Jelaskan secara singkat visi atau aktivitas dari ekskul ini..." class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                            </div>
                        </div>

                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">💾 Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL FORM: EDIT EKSKUL ================= --}}
        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl overflow-hidden" @click.away="openEdit = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">📝 Edit Data Ekstrakurikuler</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form :action="editActionUrl" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-8 space-y-5 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Ekskul <span class="text-rose-500">*</span></label>
                                <input type="text" x-model="editNama" name="nama" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pembina Ekskul</label>
                                <select x-model="editPembinaId" name="pembina_id" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    <option value="">-- Pilih Pembina (Kosongkan jika tidak ada) --</option>
                                    @foreach($pembina as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hari Latihan <span class="text-rose-500">*</span></label>
                                <select x-model="editHariLatihan" name="hari_latihan" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                                        <option value="{{ $hari }}">{{ $hari }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ganti Logo <span class="text-gray-400 font-normal">(Abaikan jika sama)</span></label>
                                <div class="w-full flex items-center justify-center p-2 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 sm:col-span-2 p-4 bg-amber-50/50 rounded-2xl border border-amber-100">
                                <div>
                                    <label class="block text-xs font-bold text-amber-900 mb-2">Jam Mulai</label>
                                    <input type="time" x-model="editJamMulai" name="jam_mulai" class="w-full text-sm font-semibold rounded-xl border-amber-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white px-4 py-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-amber-900 mb-2">Jam Selesai</label>
                                    <input type="time" x-model="editJamSelesai" name="jam_selesai" class="w-full text-sm font-semibold rounded-xl border-amber-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white px-4 py-3">
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Kegiatan</label>
                                <textarea x-model="editDeskripsi" name="deskripsi" rows="3" class="w-full text-sm rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                            </div>

                            <div class="sm:col-span-2 p-5 bg-gray-50 border border-gray-200 rounded-2xl">
                                <label class="block text-sm font-bold text-gray-900 mb-3">Status Keaktifan Ekstrakurikuler <span class="text-rose-500">*</span></label>
                                <div class="flex gap-4">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" x-model="editIsAktif" name="is_aktif" value="1" class="peer sr-only">
                                        <div class="text-center p-3 rounded-xl border border-gray-200 bg-white text-gray-500 font-bold transition-all peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 peer-checked:shadow-sm">
                                            🟢 Aktif Berjalan
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" x-model="editIsAktif" name="is_aktif" value="0" class="peer sr-only">
                                        <div class="text-center p-3 rounded-xl border border-gray-200 bg-white text-gray-500 font-bold transition-all peer-checked:bg-rose-50 peer-checked:border-rose-500 peer-checked:text-rose-700 peer-checked:shadow-sm">
                                            🔴 Non-Aktif
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openEdit = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">🔄 Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: KONFIRMASI HAPUS (GABUNGAN SWEETALERT) ================= --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl p-8 text-center relative overflow-hidden" @click.away="openDelete = false">
                
                <!-- Ikon Peringatan -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 border border-rose-100 mb-6">
                    <span class="text-4xl">⚠️</span>
                </div>
                
                <div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">Hapus Ekstrakurikuler?</h3>
                    <p class="text-sm text-gray-500 mb-8 px-2">
                        Apakah Anda yakin ingin menghapus ekstrakurikuler <strong class="text-gray-800" x-text="deleteTargetName"></strong>? Data ini akan dinonaktifkan dari sistem (Soft Delete).
                    </p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>

    </div>

    <style>
        /* CSS Untuk scrollbar custom di dalam modal */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
</x-app-layout>