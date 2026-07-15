<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">🤝</span> {{ __('Modul BK - Penanganan Kasus Siswa') }}
        </h2>
    </x-slot>

    <div x-data="{
        // Mengatur tab aktif berdasarkan response controller
        activeTab: '{{ $currentTab }}',

        // Kontrol Visibilitas Modal Form
        openCreatePanggilan: false,
        openCreateAlihKasus: false,
        openDelete: false,

        // Memetakan data siswa dan relasi many-to-many walinya ke JSON Alpine.js secara aman
        allSiswa: {{ json_encode($listSiswa->map(function($siswa) {
            return [
                'id' => $siswa->id,
                'nama_lengkap' => $siswa->nama_lengkap,
                'kelas_id' => $siswa->kelas_id ?? null,
                'nama_kelas' => $siswa->kelas->nama_kelas ?? 'Tanpa Kelas',
                'list_wali' => $siswa->wali->map(function($w) {
                    return [
                        'id' => $w->id,
                        'nama_lengkap' => $w->nama_lengkap,
                        'hubungan' => $w->pivot->hubungan ?? 'Wali'
                    ];
                })->toArray()
            ];
        })->toArray()) }},

        // Mengambil daftar unik kelas dari koleksi siswa untuk dropdown filter
        get listKelas() {
            let kelasMap = {};
            this.allSiswa.forEach(s => {
                if(s.kelas_id) {
                    kelasMap[s.kelas_id] = s.nama_kelas;
                }
            });
            return Object.keys(kelasMap).map(id => ({ id: id, nama_kelas: kelasMap[id] }));
        },

        // State filter pilihan bertingkat (Cascading Dropdown)
        selectedKelasPanggilan: '',
        selectedSiswaPanggilan: '',
        selectedKelasAlih: '',

        // Fungsi reaktif mengembalikan daftar wali khusus dari siswa yang sedang dipilih
        get availableWalis() {
            if (!this.selectedSiswaPanggilan) return [];
            let siswa = this.allSiswa.find(s => s.id == this.selectedSiswaPanggilan);
            return siswa ? siswa.list_wali : [];
        },

        // State manajemen penghapusan data (Modal Konfirmasi)
        deleteActionUrl: '',
        deleteTargetName: '',

        initDelete(actionUrl, targetName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = targetName;
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

            {{-- 🗂️ Navigasi Dual-Tab Premium (Segmented Control) --}}
            <div class="bg-gray-200/60 p-1.5 rounded-2xl shadow-inner flex overflow-x-auto w-full max-w-fit mx-auto lg:mx-0">
                <button @click="activeTab = 'panggilan'; window.history.replaceState(null, null, '?tab=panggilan')"
                    :class="activeTab === 'panggilan' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 sm:flex-none px-8 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">👪</span> Pemanggilan Orang Tua
                </button>
                <button @click="activeTab = 'alih'; window.history.replaceState(null, null, '?tab=alih')"
                    :class="activeTab === 'alih' ? 'bg-white text-indigo-700 font-bold shadow-sm border border-gray-100' : 'text-gray-500 hover:text-gray-700 font-semibold'"
                    class="flex-1 sm:flex-none px-8 py-3 text-sm rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 min-w-max">
                    <span class="text-lg">📂</span> Alih Tangan Kasus (Referral)
                </button>
            </div>

            {{-- ================= TAB 1: PEMANGGILAN ORANG TUA ================= --}}
            <div x-show="activeTab === 'panggilan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 mb-1">Log Surat Pemanggilan Wali Murid</h3>
                        <p class="text-sm text-gray-500">Daftar arsip pemanggilan orang tua/wali terkait penanganan kedisiplinan dan kasus siswa.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('bk.penanganan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto relative">
                            <input type="hidden" name="tab" value="panggilan">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                            <input type="text" name="search" value="{{ $currentTab === 'panggilan' ? $search : '' }}" placeholder="Cari nama siswa..." class="text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner w-full sm:w-64 py-3 pl-12 pr-16 transition-colors">
                            <button type="submit" class="absolute inset-y-1.5 right-1.5 px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">Cari</button>
                        </form>

                        <button @click="openCreatePanggilan = true" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
                            <span class="text-lg">➕</span> Terbitkan Panggilan
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 w-36">Tgl Panggilan</th>
                                <th class="p-5 w-48">Identitas Siswa</th>
                                <th class="p-5 w-48">Orang Tua / Wali</th>
                                <th class="p-5 w-56">Alasan Utama</th>
                                <th class="p-5 w-48 text-center">Status Kehadiran</th>
                                <th class="p-5 w-48">Konselor (Guru BK)</th>
                                <th class="p-5 pr-8 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($panggilans as $p)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="p-5 pl-8 text-gray-600 font-medium whitespace-nowrap align-middle">
                                        {{ \Carbon\Carbon::parse($p->tanggal_panggilan)->format('d M Y') }}
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="font-black text-gray-900 text-base mb-1">{{ $p->siswa->nama_lengkap ?? '-' }}</div>
                                        <div class="inline-flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded font-semibold border border-gray-200">
                                            🏫 Kelas {{ $p->siswa->kelas->nama_kelas ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="flex items-center gap-2 font-bold text-gray-800 text-sm">
                                            <span class="w-7 h-7 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0">👪</span>
                                            {{ $p->wali->nama_lengkap ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="text-sm font-semibold text-gray-800 bg-gray-50 p-3 rounded-xl border border-gray-100 line-clamp-2" title="{{ $p->alasan_panggilan }}">
                                            {{ $p->alasan_panggilan }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 text-center align-middle">
                                        <span class="px-3 py-1 text-xs font-black rounded-lg uppercase tracking-wider shadow-sm block w-full
                                            {{ $p->status === 'Terpanggil' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : ($p->status === 'Tidak Hadir' ? 'bg-rose-50 border border-rose-200 text-rose-700' : 'bg-amber-50 border border-amber-200 text-amber-700') }}">
                                            {{ $p->status }}
                                        </span>
                                        @if($p->tanggal_kehadiran)
                                            <div class="text-[11px] font-bold text-gray-400 mt-2 flex items-center justify-center gap-1">
                                                <span>✅</span> Tgl: {{ \Carbon\Carbon::parse($p->tanggal_kehadiran)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="flex items-center gap-2 text-sm text-gray-600 font-semibold">
                                            <span class="w-7 h-7 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0">👨‍🏫</span>
                                            {{ $p->pegawai->nama_lengkap ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <button type="button" @click="initDelete('{{ route('bk.penanganan.destroyPanggilan', $p->id) }}', 'Panggilan {{ addslashes($p->siswa->nama_lengkap ?? '') }}')" class="w-full px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors border border-rose-100 shadow-sm">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">📭</span>
                                        <p class="text-lg font-bold text-gray-500">Tidak ada data pemanggilan orang tua yang tercatat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($currentTab === 'panggilan' && $panggilans->count() > 0)
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">{{ $panggilans->links() }}</div>
                @endif
            </div>

            {{-- ================= TAB 2: ALIH TANGAN KASUS ================= --}}
            <div x-show="activeTab === 'alih'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100" x-cloak>
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 mb-1">Berkas Alih Tangan Kasus (Referral)</h3>
                        <p class="text-sm text-gray-500">Arsip pendelegasian penanganan masalah ke pihak eksternal atau pimpinan sekolah.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('bk.penanganan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto relative">
                            <input type="hidden" name="tab" value="alih">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                            <input type="text" name="search" value="{{ $currentTab === 'alih' ? $search : '' }}" placeholder="Cari dokumen..." class="text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner w-full sm:w-64 py-3 pl-12 pr-16 transition-colors">
                            <button type="submit" class="absolute inset-y-1.5 right-1.5 px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">Cari</button>
                        </form>

                        <button @click="openCreateAlihKasus = true" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-700 hover:to-purple-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
                            <span class="text-lg">➕</span> Buat Alih Kasus
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8 w-36">Tgl Alih Kasus</th>
                                <th class="p-5 w-48">Identitas Siswa</th>
                                <th class="p-5 w-56">Topik Permasalahan</th>
                                <th class="p-5 w-48">Klasifikasi Alih</th>
                                <th class="p-5 w-52">Dialihkan Kepada</th>
                                <th class="p-5 pr-8 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($alihKasusList as $a)
                                <tr class="hover:bg-purple-50/30 transition-colors group">
                                    <td class="p-5 pl-8 text-gray-600 font-medium whitespace-nowrap align-middle">
                                        {{ \Carbon\Carbon::parse($a->tanggal_alih)->format('d M Y') }}
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="font-black text-gray-900 text-base mb-1">{{ $a->siswa->nama_lengkap ?? '-' }}</div>
                                        <div class="inline-flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded font-semibold border border-gray-200">
                                            🏫 Kelas {{ $a->siswa->kelas->nama_kelas ?? '-' }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="text-sm font-semibold text-gray-800 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                            {{ $a->topik_permasalahan }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <span class="px-3 py-1 text-[11px] font-black rounded-lg uppercase tracking-wider bg-purple-50 border border-purple-200 text-purple-700 shadow-sm mb-2 inline-block">
                                            {{ $a->jenis_alih }}
                                        </span>
                                        <div class="text-xs text-gray-500 font-medium">Bimbingan: <span class="text-gray-800 font-bold">{{ $a->bidang_bimbingan }}</span></div>
                                    </td>
                                    
                                    <td class="p-5 align-middle">
                                        <div class="flex items-center gap-2 text-sm text-indigo-700 font-black">
                                            <span class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0 shadow-sm text-lg">💼</span>
                                            {{ $a->kepada_siapa }}
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <button type="button" @click="initDelete('{{ route('bk.penanganan.destroyAlihKasus', $a->id) }}', 'Referral {{ addslashes($a->siswa->nama_lengkap ?? '') }}')" class="w-full px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors border border-rose-100 shadow-sm">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        <span class="text-5xl block mb-4">📭</span>
                                        <p class="text-lg font-bold text-gray-500">Belum ada berkas dokumen referral yang diterbitkan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($currentTab === 'alih' && $alihKasusList->count() > 0)
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">{{ $alihKasusList->links() }}</div>
                @endif
            </div>

        </div>

        {{-- ================= MODAL FORM: TAMBAH PEMANGGILAN ORANG TUA ================= --}}
        <div x-show="openCreatePanggilan" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden" @click.away="openCreatePanggilan = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">👪 Terbitkan Surat Panggilan Orang Tua</h3>
                    <button type="button" @click="openCreatePanggilan = false; selectedKelasPanggilan = ''; selectedSiswaPanggilan = ''" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('bk.penanganan.storePanggilan') }}" method="POST">
                    @csrf
                    <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Panggilan <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_panggilan" required value="{{ date('Y-m-d') }}" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Status Kehadiran <span class="text-rose-500">*</span></label>
                                <select name="status" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                                    <option value="Dijadwalkan Ulang">Dijadwalkan Ulang</option>
                                    <option value="Terpanggil">Terpanggil (Sudah Datang)</option>
                                    <option value="Tidak Hadir">Tidak Hadir</option>
                                </select>
                            </div>
                        </div>

                        {{-- Cascading Filter: Urutan 1 (Kelas) & Urutan 2 (Siswa) --}}
                        <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2">1. Pilih Kelas Siswa <span class="text-rose-500">*</span></label>
                                <select x-model="selectedKelasPanggilan" @change="selectedSiswaPanggilan = ''; document.getElementById('siswa_panggilan_select').value = ''" required class="w-full text-sm rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white px-4 py-3 font-semibold">
                                    <option value="">-- Pilih Kelas --</option>
                                    <template x-for="kls in listKelas">
                                        <option :value="kls.id" x-text="kls.nama_kelas"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2">2. Pilih Siswa Target <span class="text-rose-500">*</span></label>
                                <select name="siswa_id" id="siswa_panggilan_select" x-model="selectedSiswaPanggilan" required class="w-full text-sm rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white px-4 py-3 font-semibold">
                                    <option value="">-- Pilih Siswa --</option>
                                    <template x-for="siswa in allSiswa">
                                        <option x-show="selectedKelasPanggilan == '' || siswa.kelas_id == selectedKelasPanggilan" 
                                                :value="siswa.id" 
                                                x-text="siswa.nama_lengkap">
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        {{-- Cascading Filter: Urutan 3 (Daftar Orang Tua/Wali Otomatis) --}}
                        <div class="p-5 bg-amber-50/50 border border-amber-100 rounded-2xl grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-amber-900 mb-2">3. Orang Tua / Wali Terkait <span class="text-rose-500">*</span></label>
                                <select name="wali_id" required class="w-full text-sm rounded-xl border-amber-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 bg-white px-4 py-3 font-semibold">
                                    <option value="">-- Pilih Orang Tua / Wali --</option>
                                    <template x-for="wali in availableWalis">
                                        <option :value="wali.id" x-text="wali.nama_lengkap + ' (' + wali.hubungan + ')'"></option>
                                    </template>
                                </select>
                                <div class="mt-2">
                                    <p x-show="selectedSiswaPanggilan == ''" class="text-[11px] text-amber-700 font-bold bg-amber-100 px-3 py-1.5 rounded-lg border border-amber-200 inline-block">⚠️ Pilih siswa terlebih dahulu untuk memunculkan daftar wali.</p>
                                    <p x-show="selectedSiswaPanggilan != '' && availableWalis.length == 0" class="text-[11px] text-rose-700 font-bold bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-200 inline-block">❌ Anak ini belum diikat ke data Wali Manapun di Database.</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Kehadiran <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <input type="date" name="tanggal_kehadiran" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white px-4 py-3 font-semibold text-gray-800">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Pemanggilan Kasus <span class="text-rose-500">*</span></label>
                            <textarea name="alasan_panggilan" rows="2" required placeholder="Tulis alasan dikeluarkannya surat panggilan ini..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Hasil Pertemuan Pertanggungjawaban</label>
                            <textarea name="hasil_pertemuan" rows="2" placeholder="Tuliskan butir hasil kesepakatan sidang jika wali murid sudah hadir..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Guru Penanggung Jawab Panggilan <span class="text-rose-500">*</span></label>
                            <select name="pegawai_id" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold">
                                <option value="">-- Pilih Guru BK --</option>
                                @foreach($listPegawai as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreatePanggilan = false; selectedKelasPanggilan = ''; selectedSiswaPanggilan = ''" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">💾 Simpan Rekam Log</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL FORM: TAMBAH ALIH TANGAN KASUS ================= --}}
        <div x-show="openCreateAlihKasus" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            
            <div class="bg-white rounded-3xl max-w-4xl w-full shadow-2xl overflow-hidden" @click.away="openCreateAlihKasus = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <h3 class="text-lg font-black text-gray-900">📂 Penerbitan Berkas Dokumen Alih Kasus (Referral)</h3>
                    <button type="button" @click="openCreateAlihKasus = false; selectedKelasAlih = ''" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('bk.penanganan.storeAlih') }}" method="POST">
                    @csrf
                    
                    <div class="p-8 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                        {{-- Baris 1: Dropdown Filter Kelas dan Siswa Target --}}
                        <div class="p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2">1. Pilih Kelas Siswa <span class="text-rose-500">*</span></label>
                                <select x-model="selectedKelasAlih" @change="document.getElementById('siswa_alih_select').value = ''" required class="w-full text-sm rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white px-4 py-3 font-semibold">
                                    <option value="">-- Pilih Kelas --</option>
                                    <template x-for="kls in listKelas">
                                        <option :value="kls.id" x-text="kls.nama_kelas"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-indigo-900 mb-2">2. Pilih Siswa Target <span class="text-rose-500">*</span></label>
                                <select name="siswa_id" id="siswa_alih_select" required class="w-full text-sm rounded-xl border-indigo-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white px-4 py-3 font-semibold">
                                    <option value="">-- Pilih Siswa --</option>
                                    <template x-for="siswa in allSiswa">
                                        <option x-show="selectedKelasAlih == '' || siswa.kelas_id == selectedKelasAlih" 
                                                :value="siswa.id" 
                                                x-text="siswa.nama_lengkap">
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        {{-- Baris 2: Topik Masalah dan Tanggal Alih --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Topik Utama Permasalahan <span class="text-rose-500">*</span></label>
                                <input type="text" name="topik_permasalahan" required placeholder="Contoh: Indikasi Gangguan Klinis / Bullying Berat" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Alih Tangan <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_alih" required value="{{ date('Y-m-d') }}" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                            </div>
                        </div>

                        {{-- Baris 3: Bidang Bimbingan, Jenis Kegiatan, dan Fungsi Layanan --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Bidang Bimbingan <span class="text-rose-500">*</span></label>
                                <input type="text" name="bidang_bimbingan" required placeholder="Contoh: Pribadi / Sosial / Karir" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Kegiatan <span class="text-rose-500">*</span></label>
                                <input type="text" name="jenis_kegiatan" required placeholder="Contoh: Referral Tenaga Medis Psikolog" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Fungsi Layanan</label>
                                <input type="text" name="fungsi_kegiatan" placeholder="Contoh: Kuratif / Preventif" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                            </div>
                        </div>

                        {{-- Baris 4: Delegasi Alih dan Instansi Penerima --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Delegasi Klasifikasi Alih <span class="text-rose-500">*</span></label>
                                <select name="jenis_alih" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 font-semibold text-gray-800">
                                    <option value="Ke Ahli Lain">Ke Ahli Lain (Psikolog/Dokter)</option>
                                    <option value="Ke Orang Tua">Ke Orang Tua (Penyelesaian Keluarga)</option>
                                    <option value="Ke Kepala Sekolah">Ke Kepala Sekolah (Sanksi Akademik)</option>
                                    <option value="Ke Instansi Lain">Ke Instansi Lain (Polisi/Dinas)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Instansi / Ahli Penerima <span class="text-rose-500">*</span></label>
                                <input type="text" name="kepada_siapa" required placeholder="Contoh: Lembaga Psikologi Bunda / Dr. Rudi Santoso" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                            </div>
                        </div>

                        {{-- Baris 5: Textarea --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tujuan Utama Alih Kasus</label>
                                <textarea name="tujuan_kegiatan" rows="3" placeholder="Tujuan khusus pendelegasian penanganan kepada pihak ahli..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Gambaran Ringkas Masalah <span class="text-rose-500">*</span></label>
                                <textarea name="gambaran_ringkas_masalah" rows="3" required placeholder="Garis besar indikasi gejala atau permasalahan krusial siswa..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Utama Alih Tangan Kasus <span class="text-rose-500">*</span></label>
                                <textarea name="alasan_alih_kasus" rows="3" required placeholder="Mengapa kasus ini diluar batas kewenangan/kemampuan BK dan harus dialihkan..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Hasil Yang Diharapkan Dicapai</label>
                                <textarea name="hasil_yang_dicapai" rows="3" placeholder="Target perkembangan positif yang diharapkan dari penanganan lanjutan ahli..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3 leading-relaxed"></textarea>
                            </div>
                        </div>

                        {{-- Baris 6: Keterangan Pendukung Singkat --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Bahan / Berkas Disertakan</label>
                                <input type="text" name="bahan_disertakan" placeholder="Contoh: Log Poin & Tes IQ" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Keterkaitan Layanan BK Terdahulu</label>
                                <input type="text" name="keterkaitan_layanan_terdahulu" placeholder="Contoh: Telah 3x Konseling Individu" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Rencana Tindak Lanjut</label>
                                <input type="text" name="rencana_penilaian_tindak_lanjut" placeholder="Contoh: Monitoring Evaluasi Berkala" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                            </div>
                        </div>

                        {{-- Baris 7: Catatan Tambahan Full Width --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Tambahan Dokumen</label>
                            <input type="text" name="catatan" placeholder="Keterangan administratif opsional..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 px-4 py-3">
                        </div>
                    </div>

                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreateAlihKasus = false; selectedKelasAlih = ''" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">💾 Terbitkan Dokumen Referral</button>
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
                    <h3 class="text-2xl font-black text-gray-900 mb-4">Hapus Dokumen Penanganan?</h3>
                    <p class="text-sm text-gray-500 mb-8 px-2">
                        Apakah Anda yakin ingin menghapus arsip data <strong class="text-gray-800" x-text="deleteTargetName"></strong>? Data yang dihapus tidak dapat dipulihkan secara langsung (Permanen).
                    </p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none">
                        Ya, Hapus Arsip
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