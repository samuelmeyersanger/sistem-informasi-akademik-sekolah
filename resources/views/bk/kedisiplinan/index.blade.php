<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modul BK - Kedisiplinan Siswa') }}
        </h2>
    </x-slot>

    <div x-data="{
        // Mengambil tab aktif dari backend laravel agar paginasi tidak meriset tab terpilih
        activeTab: '{{ $currentTab }}',

        // State Modal Kontrol
        openCreatePelanggaran: false,
        openCreateTerlambat: false,
        openDelete: false,

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initDelete(actionUrl, targetName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = targetName;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Alert Notifikasi Sukses/Gagal --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
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

            {{-- 🗂️ Navigasi Tab Modern --}}
            <div class="flex border-b border-gray-200 bg-white p-2 rounded-xl shadow-sm gap-2">
                <button @click="activeTab = 'pelanggaran'; window.history.replaceState(null, null, '?tab=pelanggaran')"
                    :class="activeTab === 'pelanggaran' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 sm:flex-none px-6 py-2.5 text-xs font-bold rounded-lg transition-all cursor-pointer flex items-center justify-center gap-2">
                    🚨 Pelanggaran & Poin Siswa
                </button>
                <button @click="activeTab = 'terlambat'; window.history.replaceState(null, null, '?tab=terlambat')"
                    :class="activeTab === 'terlambat' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 sm:flex-none px-6 py-2.5 text-xs font-bold rounded-lg transition-all cursor-pointer flex items-center justify-center gap-2">
                    ⏰ Presensi Siswa Terlambat
                </button>
            </div>

            {{-- ================= TAB 1: PELANGGARAN SISWA ================= --}}
            <div x-show="activeTab === 'pelanggaran'" class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Catatan Pelanggaran & Poin</h3>
                        <p class="text-xs text-gray-500">Rekapitulasi jenis pelanggaran tata tertib sekolah beserta akumulasi bobot poin pelanggaran siswa.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('bk.kedisiplinan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <input type="hidden" name="tab" value="pelanggaran">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ $currentTab === 'pelanggaran' ? $search : '' }}" placeholder="Cari nama siswa / pelanggaran..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer">🔍 Cari</button>
                        </form>

                        <button @click="openCreatePelanggaran = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Catat Pelanggaran
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Tanggal</th>
                                <th class="p-4">Nama Siswa</th>
                                <th class="p-4">Kelas</th>
                                <th class="p-4">Kategori / Pelanggaran</th>
                                <th class="p-4 text-center">Poin</th>
                                <th class="p-4">Pencatat (Guru)</th>
                                <th class="p-4 pr-6 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($pelanggarans as $p)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 text-gray-500 whitespace-nowrap">{{ $p->tanggal->format('d M Y') }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $p->siswa->nama_lengkap ?? '-' }}</td>
                                    <td class="p-4 text-gray-600 font-medium">{{ $p->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md uppercase 
                                            {{ $p->kategori === 'Berat' ? 'bg-rose-50 border border-rose-200 text-rose-700' : ($p->kategori === 'Sedang' ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-gray-50 border border-gray-200 text-gray-600') }}">
                                            {{ $p->kategori }}
                                        </span>
                                        <div class="font-semibold text-gray-800 mt-1">{{ $p->jenis_pelanggaran }}</div>
                                    </td>
                                    <td class="p-4 text-center font-bold text-rose-600 text-sm">🛑 {{ $p->poin }}</td>
                                    <td class="p-4 text-gray-500">{{ $p->pegawai->nama_lengkap ?? '-' }}</td>
                                    <td class="p-4 pr-6 text-center">
                                        <button type="button" @click="initDelete('{{ route('bk.kedisiplinan.destroyPelanggaran', $p->id) }}', 'Kasus {{ addslashes($p->siswa->nama_lengkap) }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-12 text-center text-gray-400 italic bg-gray-50/30">Belum ada data kasus pelanggaran tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($currentTab === 'pelanggaran' && $pelanggarans->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">{{ $pelanggarans->links() }}</div>
                @endif
            </div>

            {{-- ================= TAB 2: SISWA TERLAMBAT ================= --}}
            <div x-show="activeTab === 'terlambat'" class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100" x-cloak>
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Jurnal Siswa Terlambat</h3>
                        <p class="text-xs text-gray-500">Manajemen pencatatan kehadiran siswa yang terlambat tiba di sekolah untuk kalkulasi kedisiplinan berkala.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('bk.kedisiplinan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <input type="hidden" name="tab" value="terlambat">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ $currentTab === 'terlambat' ? $search : '' }}" placeholder="Cari nama siswa / alasan..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer">🔍 Cari</button>
                        </form>

                        <button @click="openCreateTerlambat = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Catat Keterlambatan
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Tanggal</th>
                                <th class="p-4">Nama Siswa</th>
                                <th class="p-4">Kelas</th>
                                <th class="p-4">Jam Masuk</th>
                                <th class="p-4 text-center">Durasi Telat</th>
                                <th class="p-4">Alasan & Tindak Lanjut</th>
                                <th class="p-4">Guru Piket</th>
                                <th class="p-4 pr-6 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($keterlambatans as $t)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 text-gray-500 whitespace-nowrap">{{ $t->tanggal->format('d M Y') }}</td>
                                    <td class="p-4 font-bold text-gray-900">{{ $t->siswa->nama_lengkap ?? '-' }}</td>
                                    <td class="p-4 text-gray-600 font-medium">{{ $t->kelas->nama_kelas ?? '-' }}</td>
                                    <td class="p-4 font-mono text-indigo-600 text-xs">{{ \Carbon\Carbon::parse($t->jam_masuk)->format('H:i') }} WIB</td>
                                    <td class="p-4 text-center font-bold text-amber-600">⚠️ {{ $t->menit_terlambat }} Menit</td>
                                    <td class="p-4">
                                        <div class="font-semibold text-gray-800">💬 {{ $t->alasan }}</div>
                                        @if($t->tindak_lanjut)
                                            <div class="text-gray-400 mt-0.5 text-[11px]">↳ Tindakan: {{ $t->tindak_lanjut }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-gray-500">{{ $t->pegawai->nama_lengkap ?? '-' }}</td>
                                    <td class="p-4 pr-6 text-center">
                                        <button type="button" @click="initDelete('{{ route('bk.kedisiplinan.destroyTerlambat', $t->id) }}', 'Keterlambatan {{ addslashes($t->siswa->nama_lengkap) }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-gray-400 italic bg-gray-50/30">Belum ada siswa terdata terlambat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($currentTab === 'terlambat' && $keterlambatans->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">{{ $keterlambatans->links() }}</div>
                @endif
            </div>

        </div>

        {{-- ================= MODAL: TAMBAH PELANGGARAN ================= --}}
        <div x-show="openCreatePelanggaran" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreatePelanggaran = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">🚨 Catat Pelanggaran Baru</h3>
                    <button type="button" @click="openCreatePelanggaran = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('bk.kedisiplinan.storePelanggaran') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Kasus *</label>
                            <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori *</label>
                            <select name="kategori" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="Ringan">Ringan</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Berat">Berat</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">1. Pilih Kelas Terlebih Dahulu *</label>
                            <select name="kelas_id" x-model="selectedKelasPelanggaran" @change="document.getElementById('siswa_pelanggaran_select').value = ''" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($listKelas as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Bobot Poin *</label>
                            <input type="number" name="poin" min="1" required placeholder="Poin" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">2. Pilih Siswa *</label>
                        <select name="siswa_id" id="siswa_pelanggaran_select" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Siswa --</option>
                            <template x-for="siswa in allSiswa">
                                <option x-show="selectedKelasPelanggaran == '' || siswa.kelas_id == selectedKelasPelanggaran" 
                                        :value="siswa.id" 
                                        x-text="siswa.nama_lengkap">
                                </option>
                            </template>
                        </select>
                        <p x-show="selectedKelasPelanggaran == ''" class="text-[10px] text-amber-600 mt-1 font-medium">⚠️ Pilihan siswa terkunci sebelum Anda memilih Kelas di atas.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul / Jenis Pelanggaran *</label>
                        <input type="text" name="jenis_pelanggaran" required placeholder="Contoh: Membolos saat jam pelajaran" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kronologi / Deskripsi Kasus *</label>
                        <textarea name="deskripsi" rows="2" required placeholder="Tuliskan detail kejadian..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tindak Lanjut Langsung *</label>
                        <input type="text" name="tindak_lanjut" required placeholder="Contoh: Pembersihan area masjid" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Guru Pelapor / Pencatat *</label>
                        <select name="pegawai_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($listPegawai as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreatePelanggaran = false; selectedKelasPelanggaran = ''" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Kasus</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: TAMBAH TERLAMBAT ================= --}}
        <div x-show="openCreateTerlambat" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreateTerlambat = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">⏰ Catat Keterlambatan Gerbang</h3>
                    <button type="button" @click="openCreateTerlambat = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('bk.kedisiplinan.storeTerlambat') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal *</label>
                            <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Tiba *</label>
                            <input type="time" name="jam_masuk" required value="07:15" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">1. Pilih Kelas Aktif *</label>
                            <select name="kelas_id" x-model="selectedKelasTerlambat" @change="document.getElementById('siswa_terlambat_select').value = ''" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($listKelas as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Durasi (Menit) *</label>
                            <input type="number" name="menit_terlambat" min="1" required placeholder="Menit" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">2. Pilih Siswa *</label>
                        <select name="siswa_id" id="siswa_terlambat_select" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Siswa --</option>
                            <template x-for="siswa in allSiswa">
                                <option x-show="selectedKelasTerlambat == '' || siswa.kelas_id == selectedKelasTerlambat" 
                                        :value="siswa.id" 
                                        x-text="siswa.nama_lengkap">
                                </option>
                            </template>
                        </select>
                        <p x-show="selectedKelasTerlambat == ''" class="text-[10px] text-amber-600 mt-1 font-medium">⚠️ Pilihan siswa terkunci sebelum Anda memilih Kelas di atas.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Alasan Keterlambatan *</label>
                        <input type="text" name="alasan" required placeholder="Contoh: Bangun kesiangan" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tindak Lanjut / Sanksi Piket</label>
                        <input type="text" name="tindak_lanjut" placeholder="Contoh: Berdiri di lapangan 10 menit" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Guru Piket Gerbang *</label>
                        <select name="pegawai_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Guru Piket --</option>
                            @foreach($listPegawai as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreateTerlambat = false; selectedKelasTerlambat = ''" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Presensi</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: KONFIRMASI HAPUS (GABUNGAN) ================= --}}
        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Rekaman Log Kedisiplinan?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus data untuk <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Log rekam jejak ini tidak akan dihitung kembali dalam akumulasi poin penilaian berkala.
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">
                        Ya, Hapus Permanen
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>