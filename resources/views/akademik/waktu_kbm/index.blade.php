<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">⏰</span> {{ __('Konfigurasi Slot Waktu KBM') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Form States Edit
        editActionUrl: '',
        editHari: 'Senin',
        editJamKe: 0,
        editWaktuMulai: '',
        editWaktuSelesai: '',
        editKegiatan: 'KBM',

        // Form States Hapus
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(w) {
            this.editActionUrl = `/akademik/waktu-kbm/${w.id}`;
            this.editHari = w.hari;
            this.editJamKe = w.jam_ke;
            this.editWaktuMulai = w.waktu_mulai.substring(0, 5);
            this.editWaktuSelesai = w.waktu_selesai.substring(0, 5);
            this.editKegiatan = w.kegiatan;
            this.openEdit = true;
        },

        initDelete(actionUrl, itemName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = itemName;
            this.openDelete = true;
        }
    }" class="py-10 bg-slate-50 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Pesan Sukses -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            <!-- Toolbar Atas: Filter & Tombol Tambah -->
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('akademik.waktu-kbm.index') }}" class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🗓️</span>
                        <select name="hari" class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner py-3 pl-12 pr-10 transition-colors appearance-none font-semibold text-gray-700">
                            <option value="">Semua Hari Aktif</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>Hari {{ $h }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl text-sm transition-transform transform hover:-translate-y-0.5 shadow-md">
                            Filter
                        </button>
                        @if(request('hari'))
                            <a href="{{ route('akademik.waktu-kbm.index') }}" class="w-full sm:w-auto px-5 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-sm text-center rounded-xl font-bold transition-colors">Reset</a>
                        @endif
                    </div>
                </form>

                <button @click="openCreate = true" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <span class="text-lg">➕</span> Tambah Slot Waktu
                </button>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 w-40">Hari KBM</th>
                                <th class="p-5 text-center w-32">Jam Ke</th>
                                <th class="p-5 text-center">Durasi Waktu</th>
                                <th class="p-5 text-center w-48">Jenis Kegiatan</th>
                                <th class="p-5 pr-8 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($waktuKbm as $w)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    
                                    <!-- Kolom Hari -->
                                    <td class="p-5 pl-8 align-middle">
                                        <div class="font-black text-gray-900 text-base flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                            {{ $w->hari }}
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Jam Ke -->
                                    <td class="p-5 text-center align-middle">
                                        <span class="inline-flex items-center justify-center w-12 h-12 bg-white rounded-xl font-black text-indigo-700 text-lg border border-indigo-100 shadow-sm group-hover:border-indigo-300 transition-colors">
                                            {{ $w->jam_ke }}
                                        </span>
                                    </td>
                                    
                                    <!-- Kolom Durasi -->
                                    <td class="p-5 text-center align-middle">
                                        <div class="inline-flex items-center gap-3 px-5 py-2 bg-gray-50 border border-gray-100 rounded-xl">
                                            <span class="font-bold text-gray-800 text-base">{{ \Carbon\Carbon::parse($w->waktu_mulai)->format('H:i') }}</span>
                                            <span class="text-gray-300">━</span>
                                            <span class="font-bold text-gray-800 text-base">{{ \Carbon\Carbon::parse($w->waktu_selesai)->format('H:i') }}</span>
                                            <span class="text-[10px] font-bold text-gray-400 ml-1 uppercase">WIB</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Kolom Kegiatan -->
                                    <td class="p-5 text-center align-middle">
                                        @if($w->kegiatan == 'KBM')
                                            <span class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-black uppercase tracking-wider border border-indigo-200 shadow-sm w-full inline-block">📘 {{ $w->kegiatan }}</span>
                                        @elseif($w->kegiatan == 'Istirahat' || $w->kegiatan == 'MBG')
                                            <span class="px-4 py-2 bg-amber-50 text-amber-700 rounded-lg text-xs font-black uppercase tracking-wider border border-amber-200 shadow-sm w-full inline-block">☕ {{ $w->kegiatan }}</span>
                                        @elseif($w->kegiatan == 'Upacara' || $w->kegiatan == 'G7')
                                            <span class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-black uppercase tracking-wider border border-emerald-200 shadow-sm w-full inline-block">🎌 {{ $w->kegiatan }}</span>
                                        @else
                                            <span class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-black uppercase tracking-wider border border-slate-200 shadow-sm w-full inline-block">⚙️ {{ $w->kegiatan }}</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Kolom Aksi -->
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <div class="flex flex-col gap-2 justify-center">
                                            <button type="button" @click="initEdit({{ json_encode($w) }})" class="w-full px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition-colors border border-emerald-100 shadow-sm">
                                                ✏️ Edit
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('akademik.waktu-kbm.destroy', $w->id) }}', '{{ $w->hari }} Jam Ke-{{ $w->jam_ke }}')" class="w-full px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors border border-rose-100 shadow-sm">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">📭</span>
                                        <p class="text-lg font-bold text-gray-500">Belum ada konfigurasi waktu KBM.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($waktuKbm->hasPages())
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">
                        {{ $waktuKbm->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: TAMBAH SLOT WAKTU -->
        <!-- ========================================== -->
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-gray-100 overflow-hidden" @click.away="openCreate = false">
                <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900">➕ Tambah Slot Waktu</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                <form action="{{ route('akademik.waktu-kbm.store') }}" method="POST">
                    @csrf
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hari Aktif <span class="text-rose-500">*</span></label>
                                <select name="hari" required class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3 font-semibold">
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                        <option value="{{ $h }}">{{ $h }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jam Ke- <span class="text-rose-500">*</span></label>
                                <input type="number" name="jam_ke" required min="0" value="1" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3 font-bold text-center">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100">
                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2 text-center">Mulai ⏱️</label>
                                <input type="time" name="waktu_mulai" required class="w-full text-base rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3 text-center font-bold">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2 text-center">Selesai 🏁</label>
                                <input type="time" name="waktu_selesai" required class="w-full text-base rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3 text-center font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Kegiatan <span class="text-rose-500">*</span></label>
                            <select name="kegiatan" required class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3 font-semibold">
                                @foreach(['KBM', 'Istirahat', 'Upacara', 'G7', 'Korikuler', 'MBG'] as $keg)
                                    <option value="{{ $keg }}">{{ $keg }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl transition-colors shadow-sm">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors">💾 Simpan Slot</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: EDIT SLOT WAKTU -->
        <!-- ========================================== -->
        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-gray-100 overflow-hidden" @click.away="openEdit = false">
                <div class="px-8 py-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900">✏️ Ubah Slot Waktu</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hari Aktif <span class="text-rose-500">*</span></label>
                                <select name="hari" x-model="editHari" required class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3 font-semibold">
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                        <option value="{{ $h }}">{{ $h }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jam Ke- <span class="text-rose-500">*</span></label>
                                <input type="number" name="jam_ke" x-model="editJamKe" required min="0" class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3 font-bold text-center">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100">
                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2 text-center">Mulai ⏱️</label>
                                <input type="time" name="waktu_mulai" x-model="editWaktuMulai" required class="w-full text-base rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3 text-center font-bold">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2 text-center">Selesai 🏁</label>
                                <input type="time" name="waktu_selesai" x-model="editWaktuSelesai" required class="w-full text-base rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3 text-center font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Kegiatan <span class="text-rose-500">*</span></label>
                            <select name="kegiatan" x-model="editKegiatan" required class="w-full text-base rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3 font-semibold">
                                @foreach(['KBM', 'Istirahat', 'Upacara', 'G7', 'Korikuler', 'MBG'] as $keg)
                                    <option value="{{ $keg }}">{{ $keg }}</option>
                                @endforeach
                            </select>
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
                    <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-4" id="modal-title">Hapus Konfigurasi?</h3>
                    <p class="text-sm text-gray-500 mb-8 px-2">
                        Apakah Anda yakin ingin menghapus slot waktu <strong class="text-gray-800" x-text="deleteTargetName"></strong>? Jadwal KBM yang menggunakan slot ini mungkin akan bermasalah (Soft Delete).
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