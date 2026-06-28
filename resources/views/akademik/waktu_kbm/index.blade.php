<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Konfigurasi Slot Waktu KBM
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
            // Memastikan format waktu hanya H:i untuk input type='time'
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
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('akademik.waktu-kbm.index') }}" class="w-full md:w-auto flex items-center gap-2">
                    <select name="hari" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">Semua Hari</option>
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                            <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer">
                        Filter Hari
                    </button>
                    @if(request('hari'))
                        <a href="{{ route('akademik.waktu-kbm.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs text-center rounded-lg font-medium">Reset</a>
                    @endif
                </form>

                <button @click="openCreate = true" class="w-full md:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    ➕ Tambah Slot Waktu
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-32">Hari</th>
                                <th class="p-4 text-center w-24">Jam Ke</th>
                                <th class="p-4 text-center">Durasi Waktu</th>
                                <th class="p-4 text-center">Jenis Kegiatan</th>
                                <th class="p-4 pr-6 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($waktuKbm as $w)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-4 pl-6">
                                        <span class="font-bold text-gray-900">{{ $w->hari }}</span>
                                    </td>
                                    <td class="p-4 text-center font-mono font-bold text-indigo-600 text-sm">
                                        {{ $w->jam_ke }}
                                    </td>
                                    <td class="p-4 text-center font-medium text-gray-800">
                                        {{ \Carbon\Carbon::parse($w->waktu_mulai)->format('H:i') }} 
                                        <span class="text-gray-400 mx-1">-</span> 
                                        {{ \Carbon\Carbon::parse($w->waktu_selesai)->format('H:i') }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md shadow-sm border
                                            @if($w->kegiatan == 'KBM') bg-indigo-50 border-indigo-200 text-indigo-700
                                            @elseif($w->kegiatan == 'Istirahat' || $w->kegiatan == 'MBG') bg-amber-50 border-amber-200 text-amber-700
                                            @elseif($w->kegiatan == 'Upacara' || $w->kegiatan == 'G7') bg-emerald-50 border-emerald-200 text-emerald-700
                                            @else bg-slate-50 border-slate-200 text-slate-700 @endif">
                                            {{ $w->kegiatan }}
                                        </span>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-4">
                                            <button type="button" @click="initEdit({{ json_encode($w) }})" class="text-indigo-600 hover:underline font-semibold cursor-pointer">
                                                📝 Edit
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('akademik.waktu-kbm.destroy', $w->id) }}', '{{ $w->hari }} Jam Ke-{{ $w->jam_ke }}')" class="text-rose-600 hover:underline font-semibold cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/20">
                                        Belum ada konfigurasi waktu KBM yang dibuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($waktuKbm->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $waktuKbm->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Slot Waktu</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('akademik.waktu-kbm.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hari *</label>
                            <select name="hari" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Ke- *</label>
                            <input type="text" name="jam_ke" required min="0" value="1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Waktu Mulai *</label>
                            <input type="time" name="waktu_mulai" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Waktu Selesai *</label>
                            <input type="time" name="waktu_selesai" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Kegiatan *</label>
                        <select name="kegiatan" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @foreach(['KBM', 'Istirahat', 'Upacara', 'G7', 'Korikuler', 'MBG'] as $keg)
                                <option value="{{ $keg }}">{{ $keg }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Slot</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Ubah Slot Waktu</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hari *</label>
                            <select name="hari" x-model="editHari" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Ke- *</label>
                            <input type="number" name="jam_ke" x-model="editJamKe" required min="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Waktu Mulai *</label>
                            <input type="time" name="waktu_mulai" x-model="editWaktuMulai" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Waktu Selesai *</label>
                            <input type="time" name="waktu_selesai" x-model="editWaktuSelesai" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Kegiatan *</label>
                        <select name="kegiatan" x-model="editKegiatan" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @foreach(['KBM', 'Istirahat', 'Upacara', 'G7', 'Korikuler', 'MBG'] as $keg)
                                <option value="{{ $keg }}">{{ $keg }}</option>
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

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Konfigurasi Waktu?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Menghapus slot waktu <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>. Jadwal pelajaran yang menggunakan slot ini akan mengalami ketidakkonsistenan data (Soft Delete).
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