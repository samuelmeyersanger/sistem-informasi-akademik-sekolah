<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">📝</span> {{ __('Modul BK - Jurnal Harian') }}
        </h2>
    </x-slot>

    <div x-data="{
        // Kontrol modal view
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // State data untuk form edit & delete
        editActionUrl: '',
        deleteActionUrl: '',
        deleteTargetName: '',

        // Data temporary pengisian form edit
        jurnalData: {
            tanggal: '',
            pegawai_id: '',
            kelas_id: '',
            minggu_ke: '',
            sasaran_kegiatan: '',
            kegiatan_layanan: '',
            hasil: ''
        },

        // Trigger pemicu modal edit
        initEdit(actionUrl, data) {
            this.editActionUrl = actionUrl;
            this.jurnalData = {
                tanggal: data.tanggal,
                pegawai_id: data.pegawai_id,
                kelas_id: data.kelas_id || '',
                minggu_ke: data.minggu_ke,
                sasaran_kegiatan: data.sasaran_kegiatan,
                kegiatan_layanan: data.kegiatan_layanan,
                hasil: data.hasil
            };
            this.openEdit = true;
        },

        // Trigger pemicu konfirmasi hapus
        initDelete(actionUrl, label) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = label;
            this.openDelete = true;
        }
    }" class="py-10 bg-slate-50 min-h-screen">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Alert Notifikasi Sistem --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-lg mb-2">
                        <span>⚠️</span> Validasi Gagal:
                    </div>
                    <ul class="list-disc pl-6 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Blok Navigasi & Pencarian Atas --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h3 class="text-lg font-black text-gray-900 mb-1">Arsip Jurnal Harian Guru BK</h3>
                    <p class="text-sm text-gray-500">Log pencatatan aktivitas layanan bimbingan klasikal, kelompok, maupun individu.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                    <form action="{{ route('bk.jurnal.index') }}" method="GET" class="w-full sm:w-auto relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari sasaran, layanan, hasil..." class="w-full sm:w-72 text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner py-3 pl-12 pr-20 transition-colors">
                        <button type="submit" class="absolute inset-y-1.5 right-1.5 px-4 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors">Cari</button>
                    </form>

                    <button @click="openCreate = true" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
                        <span class="text-lg">➕</span> Tambah Jurnal
                    </button>
                </div>
            </div>

            {{-- Tabel Render Data Jurnal --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 w-40">Tanggal & Minggu</th>
                                <th class="p-5 w-48">Guru BK</th>
                                <th class="p-5 w-32">Kelas</th>
                                <th class="p-5 w-48">Sasaran Kegiatan</th>
                                <th class="p-5">Aktivitas & Hasil Layanan</th>
                                <th class="p-5 pr-8 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($jurnals as $j)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    
                                    <!-- Kolom Tanggal -->
                                    <td class="p-5 pl-8 align-top">
                                        <div class="font-black text-gray-900 text-base mb-1 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($j->tanggal)->format('d M Y') }}
                                        </div>
                                        <div class="inline-flex items-center gap-1 text-[10px] text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded font-bold uppercase tracking-wider border border-indigo-100">
                                            Minggu Ke-{{ $j->minggu_ke }}
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Guru -->
                                    <td class="p-5 align-top">
                                        <div class="flex items-center gap-2 font-bold text-gray-800">
                                            <span class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-sm shrink-0">👨‍🏫</span>
                                            {{ $j->pegawai->nama_lengkap ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Kelas -->
                                    <td class="p-5 align-top">
                                        @if($j->kelas_id)
                                            <span class="px-3 py-1.5 text-xs font-black rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-center block shadow-sm">
                                                {{ $j->kelas->nama_kelas }}
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 text-[10px] font-bold rounded-lg bg-slate-100 border border-slate-200 text-slate-500 uppercase tracking-wider text-center block">
                                                Semua Kelas
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- Kolom Sasaran -->
                                    <td class="p-5 align-top">
                                        <div class="text-sm font-semibold text-gray-800 bg-gray-50 p-3 rounded-xl border border-gray-100" title="{{ $j->sasaran_kegiatan }}">
                                            <span class="text-indigo-500 mr-1">🎯</span> {{ Str::limit($j->sasaran_kegiatan, 40) }}
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Aktivitas & Hasil -->
                                    <td class="p-5 align-top space-y-2">
                                        <div>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Kegiatan Layanan:</span>
                                            <p class="text-xs text-gray-700 font-medium leading-relaxed bg-white border border-gray-100 p-2.5 rounded-lg shadow-sm" title="{{ $j->kegiatan_layanan }}">
                                                {{ Str::limit($j->kegiatan_layanan, 80) }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Hasil / Tindak Lanjut:</span>
                                            <p class="text-xs text-emerald-800 font-medium leading-relaxed bg-emerald-50/50 border border-emerald-100 p-2.5 rounded-lg" title="{{ $j->hasil }}">
                                                {{ Str::limit($j->hasil, 80) }}
                                            </p>
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Aksi -->
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <div class="flex flex-col gap-2 justify-center">
                                            <button type="button" @click="initEdit('{{ route('bk.jurnal.update', $j->id) }}', {{ json_encode($j) }})" class="w-full px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition-colors border border-emerald-100 shadow-sm">
                                                ✏️ Edit
                                            </button>
                                            
                                            <button type="button" @click="initDelete('{{ route('bk.jurnal.destroy', $j->id) }}', 'Jurnal Tanggal ' + '{{ \Carbon\Carbon::parse($j->tanggal)->format('d-m-Y') }}')" class="w-full px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors border border-rose-100 shadow-sm">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">📭</span>
                                        <p class="text-lg font-bold text-gray-500">Belum ada catatan jurnal harian BK pada lembar arsip ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($jurnals->hasPages())
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">
                        {{ $jurnals->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- ================= MODAL FORM: TAMBAH JURNAL HARIAN ================= --}}
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl flex flex-col max-h-[90vh] overflow-hidden" @click.away="openCreate = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">📝 Tambah Log Jurnal Baru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('bk.jurnal.store') }}" method="POST" class="overflow-y-auto p-8 space-y-6 flex-1 custom-scrollbar">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Kegiatan <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Minggu Ke <span class="text-rose-500">*</span></label>
                            <select name="minggu_ke" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                                <option value="I">I (Satu)</option>
                                <option value="II">II (Dua)</option>
                                <option value="III">III (Tiga)</option>
                                <option value="IV">IV (Empat)</option>
                                <option value="V">V (Lima)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kelas (Opsional)</label>
                            <select name="kelas_id" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($listKelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                        <label class="block text-sm font-bold text-indigo-900 mb-2">Guru BK Penanggung Jawab <span class="text-rose-500">*</span></label>
                        <select name="pegawai_id" required class="w-full text-sm rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white px-4 py-3 font-semibold">
                            <option value="">-- Pilih Aparatur Guru BK --</option>
                            @foreach($listGuruBk as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sasaran Kegiatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="sasaran_kegiatan" required placeholder="Cth: Siswa XI-A yang sering terlambat / Individu Inisial R" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Bentuk Kegiatan Layanan <span class="text-rose-500">*</span></label>
                        <textarea name="kegiatan_layanan" rows="3" required placeholder="Cth: Melakukan konseling pribadi perihal hambatan transportasi..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Hasil / Tindak Lanjut Layanan <span class="text-rose-500">*</span></label>
                        <textarea name="hasil" rows="3" required placeholder="Cth: Siswa berjanji mengatur jam tidur dan BK akan memonitor..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                    </div>
                    
                    <!-- Spacer for scrolling -->
                    <div class="h-4"></div>
                </form>

                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 shrink-0">
                    <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                    <button onclick="this.closest('div').previousElementSibling.submit()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">💾 Simpan Jurnal</button>
                </div>
            </div>
        </div>

        {{-- ================= MODAL FORM: EDIT DATA JURNAL ================= --}}
        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl flex flex-col max-h-[90vh] overflow-hidden" @click.away="openEdit = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">✏️ Perbarui Lembar Jurnal</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form :action="editActionUrl" method="POST" class="overflow-y-auto p-8 space-y-6 flex-1 custom-scrollbar">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Kegiatan <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal" required x-model="jurnalData.tanggal" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Minggu Ke <span class="text-rose-500">*</span></label>
                            <select name="minggu_ke" required x-model="jurnalData.minggu_ke" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                                <option value="I">I (Satu)</option>
                                <option value="II">II (Dua)</option>
                                <option value="III">III (Tiga)</option>
                                <option value="IV">IV (Empat)</option>
                                <option value="V">V (Lima)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kelas (Opsional)</label>
                            <select name="kelas_id" x-model="jurnalData.kelas_id" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($listKelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                        <label class="block text-sm font-bold text-indigo-900 mb-2">Guru BK Penanggung Jawab <span class="text-rose-500">*</span></label>
                        <select name="pegawai_id" required x-model="jurnalData.pegawai_id" class="w-full text-sm rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white px-4 py-3 font-semibold">
                            <option value="">-- Pilih Aparatur Guru BK --</option>
                            @foreach($listGuruBk as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sasaran Kegiatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="sasaran_kegiatan" required x-model="jurnalData.sasaran_kegiatan" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Bentuk Kegiatan Layanan <span class="text-rose-500">*</span></label>
                        <textarea name="kegiatan_layanan" rows="3" required x-model="jurnalData.kegiatan_layanan" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Hasil / Tindak Lanjut Layanan <span class="text-rose-500">*</span></label>
                        <textarea name="hasil" rows="3" required x-model="jurnalData.hasil" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                    </div>
                    
                    <!-- Spacer for scrolling -->
                    <div class="h-4"></div>
                </form>

                <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 shrink-0">
                    <button type="button" @click="openEdit = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                    <button onclick="this.closest('div').previousElementSibling.submit()" class="px-6 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">💾 Perbarui Data</button>
                </div>
            </div>
        </div>

        {{-- ================= MODAL: KONFIRMASI HAPUS (MODERN SWEETALERT) ================= --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl p-8 text-center relative overflow-hidden" @click.away="openDelete = false">
                
                <!-- Ikon Peringatan -->
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 border border-rose-100 mb-6">
                    <span class="text-4xl">⚠️</span>
                </div>
                
                <div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">Hapus Jurnal BK?</h3>
                    <p class="text-sm text-gray-500 mb-8 px-2">
                        Apakah Anda yakin ingin menghapus <strong class="text-gray-800" x-text="deleteTargetName"></strong>? Data catatan jurnal ini akan diarsipkan (Soft Delete).
                    </p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none">
                        Ya, Hapus Log
                    </button>
                </form>
            </div>
        </div>

    </div>

    <style>
        /* CSS Untuk scrollbar custom di dalam modal yang lebih rapi */
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