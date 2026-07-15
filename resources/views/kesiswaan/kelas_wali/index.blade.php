<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">👥</span> {{ __('Manajemen Kelompok Wali') }}
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
    }" class="py-10 bg-slate-50 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Alerts / Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
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

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 mb-1">Daftar Kelompok Wali & Pembimbing</h3>
                        <p class="text-sm text-gray-500">Kelola master data kelompok pembimbingan, penentuan tingkat, dan penugasan Guru Pembimbing.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-4">
                        <form action="{{ route('kesiswaan.kelas_wali.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kelompok..." class="text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner w-full sm:w-72 py-3 pl-12 pr-12 transition-colors">
                            
                            @if(request('search'))
                                <a href="{{ route('kesiswaan.kelas_wali.index') }}" class="absolute inset-y-0 right-16 flex items-center pr-2 text-gray-400 hover:text-rose-500 font-bold text-lg transition-colors cursor-pointer" title="Hapus Pencarian">
                                    &times;
                                </a>
                            @endif
                            <button type="submit" class="absolute inset-y-1.5 right-1.5 px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">Cari</button>
                        </form>
                        
                        @if(auth()->user()->hasPermission('kesiswaan.kelas_wali.store'))
                            <button @click="openCreate = true" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
                                <span class="text-lg">➕</span> Kelompok Baru
                            </button>
                        @endif
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 text-center w-28">Tingkat</th>
                                <th class="p-5 w-64">Nama Kelompok</th>
                                <th class="p-5 w-80">Wali / Pembimbing</th>
                                <th class="p-5 pr-8 text-center w-64">Aksi / Navigasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($kelasWali as $item)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="p-5 pl-8 text-center align-middle">
                                        <div class="inline-flex flex-col items-center justify-center p-2 rounded-xl bg-gradient-to-b from-indigo-50 to-white border border-indigo-100 shadow-sm w-16 h-16">
                                            <span class="text-[10px] font-bold text-indigo-400 tracking-widest uppercase mb-0.5">Grade</span>
                                            <span class="text-2xl font-black text-indigo-700 leading-none">{{ $item->tingkat }}</span>
                                        </div>
                                    </td>
                                    <td class="p-5 align-middle">
                                        <div class="font-black text-gray-900 text-lg flex items-center gap-2">
                                            <span>🏫</span> {{ $item->nama_kelas }}
                                        </div>
                                    </td>
                                    <td class="p-5 align-middle">
                                        @if($item->waliKelas)
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-lg shadow-sm shrink-0">
                                                    {{ $item->waliKelas->jenis_kelamin === 'Perempuan' ? '👩‍🏫' : '👨‍🏫' }}
                                                </div>
                                                <div>
                                                    <div class="font-black text-gray-800 text-sm mb-0.5">{{ $item->waliKelas->nama_lengkap }}</div>
                                                    <div class="text-gray-500 text-[11px] font-semibold flex items-center gap-2">
                                                        <span>NIP: {{ $item->waliKelas->nip ?? '-' }}</span>
                                                        <span class="text-gray-300">|</span>
                                                        <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $item->waliKelas->jenis_ptk }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-black rounded-lg shadow-sm">
                                                <span class="w-2 h-2 bg-amber-500 rounded-full animate-ping"></span> BELUM DI-PLOTTING
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            
                                            <a href="{{ route('kesiswaan.kelas_wali.show', $item->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-50 hover:bg-teal-100 border border-teal-100 text-teal-700 font-bold rounded-xl text-xs transition-colors shadow-sm" title="Anggota Siswa Kelompok">
                                                <span>🔍</span> Detail
                                            </a>
                                            
                                            @if(auth()->user()->hasPermission('kesiswaan.kelas_wali.update'))
                                                <div class="h-6 w-px bg-gray-200 mx-1"></div>
                                                <button type="button" @click="initEdit('{{ route('kesiswaan.kelas_wali.update', $item->id) }}', '{{ $item->tingkat }}', '{{ $item->nama_kelas }}', '{{ $item->wali_kelas_id }}')" class="p-2.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 text-indigo-600 font-bold rounded-xl text-xs cursor-pointer transition-colors shadow-sm" title="Edit Data">
                                                    ✏️
                                                </button>
                                            @endif
                                            
                                            @if(auth()->user()->hasPermission('kesiswaan.kelas_wali.destroy'))
                                                <button type="button" @click="initDelete('{{ route('kesiswaan.kelas_wali.destroy', $item->id) }}', 'Kelompok {{ addslashes($item->nama_kelas) }}')" class="p-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 font-bold rounded-xl text-xs cursor-pointer transition-colors shadow-sm" title="Hapus Data">
                                                    🗑️
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        @if(request('search'))
                                            <span class="text-5xl block mb-4">🔍</span>
                                            <p class="text-lg font-bold text-gray-500">Hasil pencarian "{{ request('search') }}" tidak ditemukan.</p>
                                        @else
                                            <span class="text-5xl block mb-4">📭</span>
                                            <p class="text-lg font-bold text-gray-500">Sistem belum memiliki referensi kelompok wali.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($kelasWali->hasPages())
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">
                        {{ $kelasWali->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ================= MODAL FORM: TAMBAH KELOMPOK ================= --}}
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden" @click.away="openCreate = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">➕ Registrasi Kelompok</h3>
                        <p class="text-xs text-emerald-600 font-bold mt-1 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Terikat dengan Semester Aktif</p>
                    </div>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('kesiswaan.kelas_wali.store') }}" method="POST">
                    @csrf
                    <div class="p-8 space-y-6">
                        
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tingkat / Grade <span class="text-rose-500">*</span></label>
                                <select name="tingkat" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    <option value="">-- Pilih --</option>
                                    <option value="7">Grade 7</option>
                                    <option value="8">Grade 8</option>
                                    <option value="9">Grade 9</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Identitas Kelompok <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_kelas" required placeholder="Misal: Asrama A" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                        </div>

                        <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-2xl">
                            <label class="block text-sm font-bold text-blue-900 mb-2">Guru Pembimbing (Opsional)</label>
                            <select name="wali_kelas_id" class="w-full text-sm font-semibold rounded-xl border-blue-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                <option value="">-- Belum ada plotting --</option>
                                @foreach($guru_list as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }} ({{ $guru->jenis_ptk }})</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-blue-600/80 font-bold mt-2">Daftar dropdown hanya memuat Pegawai Aktif.</p>
                        </div>

                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer flex items-center gap-2">
                            <span>💾</span> Simpan Kelompok
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL FORM: UBAH KELOMPOK ================= --}}
        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden" @click.away="openEdit = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">✏️ Modifikasi Kelompok</h3>
                        <p class="text-xs text-indigo-600 font-bold mt-1 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span> Sinkronisasi Hak Akses Otomatis</p>
                    </div>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form :action="editActionUrl" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-8 space-y-6">
                        
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tingkat / Grade <span class="text-rose-500">*</span></label>
                                <select name="tingkat" x-model="editTingkat" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    <option value="7">Grade 7</option>
                                    <option value="8">Grade 8</option>
                                    <option value="9">Grade 9</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Identitas Kelompok <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_kelas" x-model="editNamaKelas" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>
                        </div>

                        <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-2xl">
                            <label class="block text-sm font-bold text-blue-900 mb-2">Wali / Guru Pembimbing</label>
                            <select name="wali_kelas_id" x-model="editWaliKelasId" class="w-full text-sm font-semibold rounded-xl border-blue-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                <option value="">-- Hapus Jabatan / Tanpa Wali --</option>
                                @foreach($guru_list as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }} ({{ $guru->jenis_ptk }})</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openEdit = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer flex items-center gap-2">
                            <span>💾</span> Terapkan Ubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: KONFIRMASI HAPUS ================= --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl p-8 text-center relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 border border-rose-100 mb-6">
                    <span class="text-4xl">⚠️</span>
                </div>
                
                <div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">Hapus Permanen?</h3>
                    <p class="text-sm text-gray-500 mb-5 px-2">
                        Anda akan menghapus data <strong class="text-gray-800 bg-gray-100 px-2 py-0.5 rounded" x-text="deleteTargetName"></strong>.
                    </p>
                    <div class="p-4 bg-rose-50/50 border border-rose-100 rounded-xl mb-8">
                        <p class="text-xs font-bold text-rose-700 leading-relaxed text-left">
                            🚨 <strong>Peringatan Penghapusan Berseri (Cascade):</strong><br>Seluruh pemetaan keanggotaan siswa aktif yang terkait dengan kelompok wali ini akan ikut dilenyapkan.
                        </p>
                    </div>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none">
                        Urungkan
                    </button>
                    <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none flex items-center gap-2">
                        <span>🗑️</span> Ya, Hapus Kelompok
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>