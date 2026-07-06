<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Ekstrakurikuler') }}
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
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Alerts --}}
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

            {{-- Main Table Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Kegiatan Ekstrakurikuler</h3>
                        <p class="text-xs text-gray-500">Manajemen program ekstrakurikuler sekolah, alokasi pembina, beserta jadwal latihan berkala.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('ekskul.ekstrakurikuler.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ekstrakurikuler..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                                
                                @if(request('search'))
                                    <a href="{{ route('ekskul.ekstrakurikuler.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Clear Search">
                                        &times;
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer shrink-0">
                                🔍 Cari
                            </button>
                        </form>
                        @if(auth()->user()->hasPermission('ekskul.ekstrakurikuler.store'))
                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Tambah Ekskul
                        </button>
                        @endif 
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Ekstrakurikuler</th>
                                <th class="p-4">Pembina</th>
                                <th class="p-4">Jadwal Latihan</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 pr-6 text-center w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($ekskul as $item)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 font-bold text-gray-900 text-sm flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center overflow-hidden shadow-inner shrink-0">
                                            @if($item->logo)
                                                <img src="{{ asset('storage/' . $item->logo) }}" class="object-cover w-full h-full">
                                            @else
                                                <span class="text-base">🏅</span>
                                            @endif
                                        </div>
                                        <span>{{ $item->nama }}</span>
                                    </td>
                                    <td class="p-4 font-medium text-gray-600">
                                        {{ $item->pembina->nama_lengkap ?? 'Belum Ditentukan' }}
                                    </td>
                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">{{ $item->hari_latihan }}</span>
                                        <span class="text-gray-400 block text-[10px]">{{ $item->jam_mulai ? date('H:i', strtotime($item->jam_mulai)) : '--:--' }} - {{ $item->jam_selesai ? date('H:i', strtotime($item->jam_selesai)) : '--:--' }}</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($item->is_aktif)
                                            <span class="px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold uppercase rounded-md shadow-sm">
                                                🟢 Aktif
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-gray-50 border border-gray-200 text-gray-400 text-[10px] font-medium uppercase rounded-md">
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('ekskul.ekstrakurikuler.show', $item->id) }}" class="p-1 text-indigo-600 hover:underline font-semibold">
                                                👁️ Kelola
                                            </a>

                                            <button type="button" @click="initEdit({{ json_encode($item) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                                📝 Edit
                                            </button>

                                            <button type="button" @click="initDelete('{{ route('ekskul.ekstrakurikuler.destroy', $item->id) }}', '{{ addslashes($item->nama) }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
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
                                            Belum ada data ekstrakurikuler terdaftar.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 🟢 POPUP MODAL: CREATE --}}
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Ekstrakurikuler Baru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('ekskul.ekstrakurikuler.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Ekskul *</label>
                        <input type="text" name="nama" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pembina Ekskul</label>
                        <select name="pembina_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Pembina --</option>
                            @foreach($pembina as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hari Latihan *</label>
                            <select name="hari_latihan" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                                    <option value="{{ $hari }}">{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Logo Ekskul</label>
                            <input type="file" name="logo" class="w-full text-[10px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Kegiatan</label>
                        <textarea name="deskripsi" rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🔵 POPUP MODAL: EDIT --}}
        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Data Ekstrakurikuler</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Ekskul *</label>
                        <input type="text" x-model="editNama" name="nama" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pembina Ekskul</label>
                        <select x-model="editPembinaId" name="pembina_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Pembina --</option>
                            @foreach($pembina as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hari Latihan *</label>
                            <select x-model="editHariLatihan" name="hari_latihan" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                                    <option value="{{ $hari }}">{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Ganti Logo</label>
                            <input type="file" name="logo" class="w-full text-[10px] text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Mulai</label>
                            <input type="time" x-model="editJamMulai" name="jam_mulai" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Selesai</label>
                            <input type="time" x-model="editJamSelesai" name="jam_selesai" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Kegiatan</label>
                        <textarea x-model="editDeskripsi" name="deskripsi" rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status Keaktifan *</label>
                        <select x-model="editIsAktif" name="is_aktif" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="0">Non-Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🔴 POPUP MODAL: DELETE --}}
        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Ekstrakurikuler?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus ekstrakurikuler <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Data ini akan dinonaktifkan menggunakan sistem Soft Delete.
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