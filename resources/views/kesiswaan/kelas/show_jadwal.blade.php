<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('kesiswaan.kelas.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors text-lg">⬅️</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Jadwal Pelajaran Kelas ') }} {{ $kelas->nama_kelas }}
            </h2>
        </div>
    </x-slot>

    <div x-data="{
        openCreate: false,
        
        // Data Master dari Controller (di-parsing ke JSON)
        daftarGuru: {{ json_encode($daftarKodeGuru) }},
        daftarWaktu: {{ json_encode($daftarWaktu) }},
        
        // State Form Input
        selectedGuruId: '',
        selectedMapelId: '',
        selectedWaktuId: '',
        selectedRuanganId: '',
        
        // Array Penampung Mata Pelajaran (Otomatis terisi via Watcher)
        availableMapels: [],

        // Fungsi saat Guru dipilih
        onGuruChange() {
            this.selectedMapelId = ''; // Reset mapel terpilih
            if (!this.selectedGuruId) {
                this.availableMapels = [];
                return;
            }
            
            // Cari data guru yang cocok di dalam array master daftarGuru
            let guru = this.daftarGuru.find(g => g.id == this.selectedGuruId);
            
            // Masukkan list mata pelajarans dari relasi Many-to-Many guru tersebut
            this.availableMapels = guru && guru.mata_pelajarans ? guru.mata_pelajarans : [];
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="text-[10px] bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">
                        Grade {{ $kelas->tingkat }}
                    </span>
                    <h3 class="text-lg font-bold text-gray-900 mt-2">Ruang Kelas {{ $kelas->nama_kelas }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Wali Kelas: <span class="font-semibold text-gray-700">{{ $kelas->waliKelas->nama_lengkap ?? 'Belum ditentukan' }}</span>
                    </p>
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                    <a href="{{ route('kesiswaan.kelas.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors text-center flex-1 md:flex-none">
                        📦 Kembali ke Index
                    </a>
                    <button @click="openCreate = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 flex-1 md:flex-none cursor-pointer">
                        ➕ Tambah Jadwal KBM
                    </button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h4 class="text-sm font-bold text-gray-900">Agenda Mingguan Kegiatan Belajar Mengajar (KBM)</h4>
                    <p class="text-xs text-gray-500">Daftar distribusi jam mengajar guru, mata pelajaran, rincian hari, dan alokasi ruangan ruang kelas.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-32 text-center">Hari</th>
                                <th class="p-4 w-24 text-center">Jam Ke</th>
                                <th class="p-4 w-40 text-center">Waktu (WIB)</th>
                                <th class="p-4">Mata Pelajaran</th>
                                <th class="p-4">Guru Pengampu</th>
                                <th class="p-4 w-32">Ruangan</th>
                                <th class="p-4 pr-6 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            <tr>
                                <td colspan="7" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                    📭 Belum ada jadwal pelajaran yang diatur untuk kelas ini. Klik tombol <span class="font-bold text-emerald-600">Tambah Jadwal KBM</span> di atas untuk memulai pengisian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Jadwal Pelajaran Baru</h3>
                        <p class="text-[10px] text-gray-500 mt-0.5">Plotting jam belajar mengajar khusus untuk kelas {{ $kelas->nama_kelas }}</p>
                    </div>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="#" method="POST" class="space-y-4 text-left">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Guru Pengampu (Kode Guru) *</label>
                        <select name="kode_guru_id" x-model="selectedGuruId" @change="onGuruChange()" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Guru / Kode Pengampu --</option>
                            <template x-for="guru in daftarGuru" :key="guru.id">
                                <option :value="guru.id" x-text="guru.kode + ' - ' + (guru.pegawai ? guru.pegawai.nama_lengkap : '')"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Mata Pelajaran *</label>
                        <select name="mata_pelajaran_id" x-model="selectedMapelId" :disabled="!selectedGuruId" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            <template x-for="mapel in availableMapels" :key="mapel.id">
                                <option :value="mapel.id" x-text="'[' + mapel.singkatan_mapel + '] ' + mapel.nama_mapel"></option>
                            </template>
                        </select>
                        <p x-show="selectedGuruId && availableMapels.length === 0" class="text-[10px] text-rose-500 mt-1 font-medium">
                            ⚠️ Guru ini belum dikaitkan dengan kompetensi mapel apapun di data master.
                        </p>
                    </div>

                    <div class="border-t border-dashed border-gray-100 my-2"></div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Hari & Jam Belajar KBM *</label>
                        <select name="waktu_kbm_id" x-model="selectedWaktuId" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Hari & Jam Ke --</option>
                            @foreach($daftarWaktu as $waktu)
                                <option value="{{ $waktu->id }}">
                                    🗓️ {{ $waktu->hari }} | Jam Ke-{{ $waktu->jam_ke }} ({{ \Carbon\Carbon::parse($waktu->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($waktu->waktu_selesai)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Ruangan Kelas / Lab *</label>
                        <select name="ruangan_id" x-model="selectedRuanganId" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Lokasi Ruangan --</option>
                            @foreach($daftarRuangan as $ruangan)
                                <option value="{{ $ruangan->id }}">🏫 {{ $ruangan->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="!selectedGuruId || !selectedMapelId || !selectedWaktuId || !selectedRuanganId" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>