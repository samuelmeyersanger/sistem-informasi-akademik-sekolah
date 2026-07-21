<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">👮</span> {{ __('Modul BK - Kedisiplinan Siswa') }}
        </h2>
    </x-slot>

    <div x-data="{
        // Mengambil tab aktif dari backend laravel
        activeTab: '{{ $currentTab }}',

        // Data Master untuk Modal
        allSiswa: {{ json_encode($listSiswa) }},
        selectedKelasPelanggaran: '',
        selectedKelasTerlambat: '',

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
    }" class="py-10 bg-slate-50 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Alert Notifikasi Sukses/Gagal --}}
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
                    <ul class="list-disc pl-6 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🗂️ Navigasi Tab Premium (Modern Segmented Control) --}}
            <div class="bg-gray-200/60 p-1.5 rounded-2xl shadow-inner flex overflow-x-auto w-full max-w-fit mx-auto lg:mx-0">
                <button @click="activeTab = 'pelanggaran'; window.history.replaceState(null, null, '?tab=pelanggaran')"
                    :class="activeTab === 'pelanggaran' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 sm:flex-none px-8 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">🚨</span> Catatan Pelanggaran
                </button>
                <button @click="activeTab = 'terlambat'; window.history.replaceState(null, null, '?tab=terlambat')"
                    :class="activeTab === 'terlambat' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 sm:flex-none px-8 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">⏰</span> Presensi Terlambat
                </button>
            </div>

            {{-- ================= TAB 1: PELANGGARAN SISWA ================= --}}
            <div x-show="activeTab === 'pelanggaran'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 mb-1">Catatan Pelanggaran & Poin</h3>
                        <p class="text-sm text-gray-500">Rekapitulasi jenis pelanggaran tata tertib beserta akumulasi poin siswa.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('bk.kedisiplinan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto relative">
                            <input type="hidden" name="tab" value="pelanggaran">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                            <input type="text" name="search" value="{{ $currentTab === 'pelanggaran' ? $search : '' }}" placeholder="Cari nama siswa / jenis..." class="text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner w-full sm:w-64 py-3 pl-12 pr-16 transition-colors">
                            <button type="submit" class="absolute inset-y-1.5 right-1.5 px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">Cari</button>
                        </form>

                        <button @click="openCreatePelanggaran = true" class="px-6 py-3 bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-700 hover:to-rose-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
                            <span class="text-lg">➕</span> Catat Pelanggaran
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 w-36">Tanggal</th>
                                <th class="p-5 w-52">Identitas Siswa</th>
                                <th class="p-5 w-60">Kategori & Pelanggaran</th>
                                <th class="p-5 text-center w-32">Poin</th>
                                <th class="p-5 w-48">Pencatat (Guru)</th>
                                <th class="p-5 pr-8 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($pelanggarans as $p)
                                <tr class="hover:bg-rose-50/30 transition-colors group">
                                    <td class="p-5 pl-8 text-gray-600 font-medium whitespace-nowrap align-middle">
                                        {{ $p->tanggal->format('d M Y') }}
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="font-black text-gray-900 text-base mb-1">{{ $p->siswa->nama_lengkap ?? '-' }}</div>
                                        <div class="inline-flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded font-semibold border border-gray-200">
                                            🏫 Kelas {{ $p->kelas->nama_kelas ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="px-3 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider
                                                {{ $p->kategori === 'Berat' ? 'bg-rose-100 border border-rose-200 text-rose-800' : ($p->kategori === 'Sedang' ? 'bg-amber-100 border border-amber-200 text-amber-800' : 'bg-gray-100 border border-gray-200 text-gray-700') }}">
                                                Kat: {{ $p->kategori }}
                                            </span>
                                        </div>
                                        <div class="font-bold text-gray-800 leading-tight">{{ $p->jenis_pelanggaran }}</div>
                                    </td>
                                    
                                    <td class="p-5 text-center align-middle">
                                        <div class="inline-flex items-center justify-center flex-col w-14 h-14 bg-rose-50 rounded-2xl border border-rose-100 shadow-inner group-hover:bg-white transition-colors">
                                            <span class="font-black text-rose-600 text-xl leading-none mb-0.5">{{ $p->poin }}</span>
                                            <span class="text-[9px] font-bold text-rose-400 uppercase tracking-widest">Poin</span>
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="flex items-center gap-2 text-sm text-gray-600 font-semibold">
                                            <span class="w-7 h-7 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0">👨‍🏫</span>
                                            {{ $p->pegawai->nama_lengkap ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <button type="button" @click="initDelete('{{ route('bk.kedisiplinan.destroyPelanggaran', $p->id) }}', 'Kasus {{ addslashes($p->siswa->nama_lengkap) }}')" class="w-full px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors border border-rose-100 shadow-sm">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">📭</span>
                                        <p class="text-lg font-bold text-gray-500">Belum ada data kasus pelanggaran tercatat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($currentTab === 'pelanggaran' && $pelanggarans->hasPages())
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">{{ $pelanggarans->links() }}</div>
                @endif
            </div>

            {{-- ================= TAB 2: SISWA TERLAMBAT ================= --}}
            <div x-show="activeTab === 'terlambat'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100" x-cloak>
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 mb-1">Jurnal Siswa Terlambat</h3>
                        <p class="text-sm text-gray-500">Rekapitulasi kehadiran siswa yang terlambat tiba di gerbang sekolah.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('bk.kedisiplinan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto relative">
                            <input type="hidden" name="tab" value="terlambat">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                            <input type="text" name="search" value="{{ $currentTab === 'terlambat' ? $search : '' }}" placeholder="Cari nama siswa / alasan..." class="text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner w-full sm:w-64 py-3 pl-12 pr-16 transition-colors">
                            <button type="submit" class="absolute inset-y-1.5 right-1.5 px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">Cari</button>
                        </form>

                        <button @click="openCreateTerlambat = true" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
                            <span class="text-lg">➕</span> Catat Keterlambatan
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 w-32">Tanggal</th>
                                <th class="p-5 w-48">Identitas Siswa</th>
                                <th class="p-5 text-center w-36">Jam Tiba</th>
                                <th class="p-5 text-center w-36">Durasi Telat</th>
                                <th class="p-5">Alasan & Tindakan</th>
                                <th class="p-5 w-40">Guru Piket</th>
                                <th class="p-5 pr-8 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($keterlambatans as $t)
                                <tr class="hover:bg-amber-50/30 transition-colors group">
                                    <td class="p-5 pl-8 text-gray-600 font-medium whitespace-nowrap align-middle">
                                        {{ $t->tanggal->format('d M Y') }}
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="font-black text-gray-900 text-base mb-1">{{ $t->siswa->nama_lengkap ?? '-' }}</div>
                                        <div class="inline-flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded font-semibold border border-gray-200">
                                            🏫 Kelas {{ $t->kelas->nama_kelas ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 text-center align-middle">
                                        <span class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg font-mono font-bold text-sm shadow-sm">
                                            {{ \Carbon\Carbon::parse($t->jam_masuk)->format('H:i') }} WIB
                                        </span>
                                    </td>
                                    
                                    <td class="p-5 text-center align-middle">
                                        <div class="inline-flex flex-col items-center justify-center w-full max-w-20 mx-auto py-2 bg-amber-50 rounded-xl border border-amber-100 shadow-inner group-hover:bg-white transition-colors">
                                            <span class="font-black text-amber-600 text-lg leading-none mb-0.5">+{{ $t->menit_terlambat }}</span>
                                            <span class="text-[9px] font-bold text-amber-500 uppercase tracking-widest">Menit</span>
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 align-middle space-y-2">
                                        <div class="font-semibold text-gray-800 text-sm bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                            💬 {{ $t->alasan }}
                                        </div>
                                        @if($t->tindak_lanjut)
                                            <div class="text-xs font-medium text-emerald-700 bg-emerald-50/50 p-2.5 rounded-lg border border-emerald-100">
                                                🛠️ Tindakan: {{ $t->tindak_lanjut }}
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="flex items-center gap-2 text-xs text-gray-600 font-semibold">
                                            <span class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0">👮</span>
                                            {{ $t->pegawai->nama_lengkap ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <button type="button" @click="initDelete('{{ route('bk.kedisiplinan.destroyTerlambat', $t->id) }}', 'Keterlambatan {{ addslashes($t->siswa->nama_lengkap) }}')" class="w-full px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors border border-rose-100 shadow-sm">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">📭</span>
                                        <p class="text-lg font-bold text-gray-500">Belum ada siswa terdata terlambat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($currentTab === 'terlambat' && $keterlambatans->hasPages())
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">{{ $keterlambatans->links() }}</div>
                @endif
            </div>

        </div>

        {{-- ================= MODAL: TAMBAH PELANGGARAN ================= --}}
        <div x-show="openCreatePelanggaran" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden" @click.away="openCreatePelanggaran = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">🚨 Catat Pelanggaran Baru</h3>
                    <button type="button" @click="openCreatePelanggaran = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('bk.kedisiplinan.storePelanggaran') }}" method="POST">
                    @csrf
                    <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Kasus <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="text-rose-500">*</span></label>
                                <select name="kategori" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                                    <option value="Ringan">Ringan</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Berat">Berat</option>
                                </select>
                            </div>
                        </div>

                        <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-indigo-900 mb-2">1. Pilih Kelas Target <span class="text-rose-500">*</span></label>
                                <select name="kelas_id" x-model="selectedKelasPelanggaran" @change="document.getElementById('siswa_pelanggaran_select').value = ''" required class="w-full text-sm rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white px-4 py-3 font-semibold">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($listKelas as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-rose-700 mb-2">Bobot Poin <span class="text-rose-500">*</span></label>
                                <input type="number" name="poin" min="1" required placeholder="Cth: 10" class="w-full text-sm rounded-xl border-rose-200 shadow-sm focus:border-rose-500 focus:ring-rose-500 bg-white px-4 py-3 font-black text-rose-600 text-center">
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-bold text-indigo-900 mb-2">2. Identitas Siswa Pelanggar <span class="text-rose-500">*</span></label>
                                <select name="siswa_id" id="siswa_pelanggaran_select" required class="w-full text-sm rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white px-4 py-3 font-semibold">
                                    <option value="">-- Pilih Siswa --</option>
                                    <template x-for="siswa in allSiswa">
                                        <option x-show="selectedKelasPelanggaran == '' || siswa.kelas_id == selectedKelasPelanggaran" 
                                                :value="siswa.id" 
                                                x-text="siswa.nama_lengkap">
                                        </option>
                                    </template>
                                </select>
                                <p x-show="selectedKelasPelanggaran == ''" class="text-[11px] text-amber-600 mt-2 font-bold bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-100 inline-block">⚠️ Form Siswa terkunci. Silakan pilih kelas terlebih dahulu.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Pelanggaran <span class="text-rose-500">*</span></label>
                            <input type="text" name="jenis_pelanggaran" required placeholder="Contoh: Membolos saat jam pelajaran" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kronologi Kasus <span class="text-rose-500">*</span></label>
                            <textarea name="deskripsi" rows="3" required placeholder="Tuliskan detail kejadian..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tindak Lanjut / Hukuman <span class="text-rose-500">*</span></label>
                            <input type="text" name="tindak_lanjut" required placeholder="Contoh: Pembersihan area masjid dan teguran lisan" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Guru Pencatat <span class="text-rose-500">*</span></label>
                            <select name="pegawai_id" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($listPegawai as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreatePelanggaran = false; selectedKelasPelanggaran = ''" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">💾 Simpan Kasus</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: TAMBAH TERLAMBAT ================= --}}
        <div x-show="openCreateTerlambat" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden" @click.away="openCreateTerlambat = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">⏰ Catat Keterlambatan Gerbang</h3>
                    <button type="button" @click="openCreateTerlambat = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('bk.kedisiplinan.storeTerlambat') }}" method="POST">
                    @csrf
                    <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Masuk <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jam Kedatangan <span class="text-rose-500">*</span></label>
                                <input type="time" name="jam_masuk" required value="07:15" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-black text-center text-indigo-700">
                            </div>
                        </div>

                        <div class="p-5 bg-amber-50/50 border border-amber-100 rounded-2xl grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-amber-900 mb-2">1. Pilih Kelas Siswa <span class="text-rose-500">*</span></label>
                                <select name="kelas_id" x-model="selectedKelasTerlambat" @change="document.getElementById('siswa_terlambat_select').value = ''" required class="w-full text-sm rounded-xl border-amber-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white px-4 py-3 font-semibold">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($listKelas as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-rose-700 mb-2">Durasi (Menit) <span class="text-rose-500">*</span></label>
                                <input type="number" name="menit_terlambat" min="1" required placeholder="Cth: 15" class="w-full text-sm rounded-xl border-rose-200 shadow-sm focus:border-rose-500 focus:ring-rose-500 bg-white px-4 py-3 font-black text-rose-600 text-center">
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-bold text-amber-900 mb-2">2. Identitas Siswa Terlambat <span class="text-rose-500">*</span></label>
                                <select name="siswa_id" id="siswa_terlambat_select" required class="w-full text-sm rounded-xl border-amber-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white px-4 py-3 font-semibold">
                                    <option value="">-- Pilih Siswa --</option>
                                    <template x-for="siswa in allSiswa">
                                        <option x-show="selectedKelasTerlambat == '' || siswa.kelas_id == selectedKelasTerlambat" 
                                                :value="siswa.id" 
                                                x-text="siswa.nama_lengkap">
                                        </option>
                                    </template>
                                </select>
                                <p x-show="selectedKelasTerlambat == ''" class="text-[11px] text-amber-700 mt-2 font-bold bg-amber-100 px-3 py-1.5 rounded-lg border border-amber-200 inline-block">⚠️ Form Siswa terkunci. Silakan pilih kelas terlebih dahulu.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Keterlambatan <span class="text-rose-500">*</span></label>
                            <input type="text" name="alasan" required placeholder="Contoh: Bangun kesiangan / Ban bocor" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tindak Lanjut / Sanksi Piket</label>
                            <input type="text" name="tindak_lanjut" placeholder="Contoh: Berdiri di lapangan 10 menit" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Guru Piket Penjaga <span class="text-rose-500">*</span></label>
                            <select name="pegawai_id" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold">
                                <option value="">-- Pilih Guru Piket --</option>
                                @foreach($listPegawai as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreateTerlambat = false; selectedKelasTerlambat = ''" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">💾 Simpan Presensi</button>
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
                    <h3 class="text-2xl font-black text-gray-900 mb-4">Hapus Rekam Jejak?</h3>
                    <p class="text-sm text-gray-500 mb-8 px-2">
                        Apakah Anda yakin ingin menghapus data <strong class="text-gray-800" x-text="deleteTargetName"></strong>? Log kedisiplinan ini tidak akan dihitung kembali dalam akumulasi poin siswa (Soft Delete).
                    </p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none">
                        Ya, Hapus Permanen
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