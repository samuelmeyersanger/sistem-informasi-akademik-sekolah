<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('kesiswaan.kelas.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-colors" title="Kembali ke Daftar Ruang Kelas">
                <span class="text-xl font-bold">←</span>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🗓️</span> {{ __('Jadwal KBM Kelas') }}
                </h2>
                <p class="text-sm font-medium text-gray-500 mt-1">Pengaturan jadwal mata pelajaran, guru pengampu, dan ruang belajar.</p>
            </div>
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
    }" class="py-10 bg-slate-50 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Kartu Identitas Kelas Premium --}}
            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-indigo-900/5 border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
                
                {{-- Aksen Latar Belakang --}}
                <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

                <div class="flex items-center gap-6 relative z-10">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-200 rounded-[1.5rem] blur-md opacity-50 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-50 to-white text-indigo-700 rounded-[1.5rem] flex flex-col items-center justify-center border-2 border-white shadow-md relative z-10 p-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Grade</span>
                            <span class="text-3xl font-black leading-none">{{ $kelas->tingkat }}</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                            <span>🏫</span> Ruang Kelas {{ $kelas->nama_kelas }}
                        </h3>
                        <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
                            <span class="text-lg">👤</span>
                            <span class="text-xs text-gray-500 font-medium">Wali Kelas:</span>
                            <span class="text-xs font-bold text-gray-800">{{ $kelas->waliKelas->nama_lengkap ?? 'Belum Ditentukan (Kosong)' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 w-full md:w-auto relative z-10">
                    <button @click="openCreate = true" class="flex-1 md:flex-none px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2">
                        <span class="text-lg">➕</span> Tambah Jadwal KBM
                    </button>
                </div>
            </div>

            {{-- Tabel Jadwal Pelajaran --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h4 class="text-lg font-black text-gray-900">Agenda Kegiatan Belajar Mengajar (KBM) Mingguan</h4>
                        <p class="text-sm text-gray-500">Distribusi jam pelajaran, alokasi ruang kelas, dan guru pengampu.</p>
                    </div>
                    <div class="hidden sm:block text-3xl opacity-20">📅</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 w-32 text-center">Hari</th>
                                <th class="p-5 w-24 text-center">Jam Ke</th>
                                <th class="p-5 w-40 text-center">Waktu (WIB)</th>
                                <th class="p-5">Mata Pelajaran</th>
                                <th class="p-5">Guru Pengampu</th>
                                <th class="p-5 w-40">Lokasi / Ruang</th>
                                <th class="p-5 pr-8 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            {{-- Placeholder Saat Ini Belum Ada Loop Data dari Controller --}}
                            <tr>
                                <td colspan="7" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                    <span class="text-5xl block mb-4">📭</span>
                                    <p class="text-base font-bold text-gray-500">Belum ada jadwal pelajaran yang di-plotting untuk kelas ini.</p>
                                    <p class="text-xs font-medium text-gray-400 mt-1">Silakan klik tombol "Tambah Jadwal KBM" untuk memulai.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= MODAL FORM: TAMBAH JADWAL KBM ================= --}}
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden" @click.away="openCreate = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">➕ Plotting Jadwal Pelajaran Baru</h3>
                        <p class="text-xs text-indigo-600 font-bold mt-1 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span> Sinkronisasi Khusus Kelas {{ $kelas->nama_kelas }}</p>
                    </div>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="#" method="POST">
                    @csrf
                    
                    <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        {{-- Dropdown Guru dengan Watcher --}}
                        <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2">Guru Pengampu (Kode Guru) <span class="text-rose-500">*</span></label>
                                <select name="kode_guru_id" x-model="selectedGuruId" @change="onGuruChange()" required class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    <option value="">-- Pilih Guru / Kode Pengampu --</option>
                                    <template x-for="guru in daftarGuru" :key="guru.id">
                                        <option :value="guru.id" x-text="'[' + guru.kode + '] ' + (guru.pegawai ? guru.pegawai.nama_lengkap : 'Tanpa Nama')"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2">Mata Pelajaran <span class="text-rose-500">*</span></label>
                                <select name="mata_pelajaran_id" x-model="selectedMapelId" :disabled="!selectedGuruId" required class="w-full text-sm font-bold rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors">
                                    <option value="">-- Menunggu Guru Dipilih --</option>
                                    <template x-for="mapel in availableMapels" :key="mapel.id">
                                        <option :value="mapel.id" x-text="'[' + (mapel.singkatan_mapel || '-') + '] ' + mapel.nama_mapel"></option>
                                    </template>
                                </select>
                                
                                {{-- Alert jika Guru terpilih tidak punya relasi mapel --}}
                                <div x-show="selectedGuruId && availableMapels.length === 0" class="mt-3 p-3 bg-rose-50 border border-rose-200 rounded-lg flex items-start gap-2" style="display: none;" x-transition>
                                    <span class="text-rose-600 text-sm">⚠️</span>
                                    <p class="text-[11px] text-rose-700 font-bold leading-tight">Guru yang dipilih belum dikaitkan dengan kompetensi mata pelajaran apapun pada Data Master Guru & Mapel.</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-200 my-2"></div>

                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Slot Hari & Waktu KBM <span class="text-rose-500">*</span></label>
                                <select name="waktu_kbm_id" x-model="selectedWaktuId" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                                    <option value="">-- Pilih Jadwal Jam Ke --</option>
                                    @foreach($daftarWaktu as $waktu)
                                        <option value="{{ $waktu->id }}">
                                            🗓️ {{ $waktu->hari }} • Jam Ke-{{ $waktu->jam_ke }} ({{ \Carbon\Carbon::parse($waktu->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($waktu->waktu_selesai)->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi / Ruangan <span class="text-rose-500">*</span></label>
                                <select name="ruangan_id" x-model="selectedRuanganId" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                                    <option value="">-- Pilih Tempat Belajar --</option>
                                    @foreach($daftarRuangan as $ruangan)
                                        <option value="{{ $ruangan->id }}">🏫 Ruang: {{ $ruangan->nama_ruangan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="!selectedGuruId || !selectedMapelId || !selectedWaktuId || !selectedRuanganId" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                            💾 Simpan Jadwal KBM
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <style>
        /* CSS custom scrollbar untuk modal form agar rapi */
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