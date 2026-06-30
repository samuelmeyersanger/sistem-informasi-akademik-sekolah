<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modul BK - Penanganan Khusus Kasus Siswa') }}
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
    }" class="py-12 bg-slate-900/10 min-h-screen">
        
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Alert Notifikasi Sistem --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                    <p class="font-bold mb-1 flex items-center gap-1">⚠️ Validasi Gagal:</p>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🗂️ Navigasi Dual-Tab Utama --}}
            <div class="flex border-b border-gray-200 bg-white p-2 rounded-xl shadow-sm gap-2">
                <button @click="activeTab = 'panggilan'; window.history.replaceState(null, null, '?tab=panggilan')"
                    :class="activeTab === 'panggilan' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 sm:flex-none px-6 py-2.5 text-xs font-bold rounded-lg transition-all cursor-pointer flex items-center justify-center gap-2">
                    👪 Pemanggilan Orang Tua / Wali
                </button>
                <button @click="activeTab = 'alih'; window.history.replaceState(null, null, '?tab=alih')"
                    :class="activeTab === 'alih' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 sm:flex-none px-6 py-2.5 text-xs font-bold rounded-lg transition-all cursor-pointer flex items-center justify-center gap-2">
                    📂 Alih Tangan Kasus (Referral)
                </button>
            </div>

            {{-- ================= TAB 1: PEMANGGILAN ORANG TUA ================= --}}
            <div x-show="activeTab === 'panggilan'" class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Log Surat Pemanggilan Wali Murid</h3>
                        <p class="text-xs text-gray-500">Daftar arsip pemanggilan orang tua/wali siswa terkait penanganan masalah kedisiplinan.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('bk.penanganan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <input type="hidden" name="tab" value="panggilan">
                            <input type="text" name="search" value="{{ $currentTab === 'panggilan' ? $search : '' }}" placeholder="Cari nama siswa..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56">
                            <button type="submit" class="px-3 py-2 bg-gray-800 text-white text-xs font-medium rounded-lg cursor-pointer">Cari</button>
                        </form>

                        <button @click="openCreatePanggilan = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Terbitkan Panggilan
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Tgl Panggilan</th>
                                <th class="p-4">Siswa</th>
                                <th class="p-4">Orang Tua / Wali</th>
                                <th class="p-4">Alasan Utama Pemanggilan</th>
                                <th class="p-4">Status & Kehadiran</th>
                                <th class="p-4">Konselor / Guru BK</th>
                                <th class="p-4 pr-6 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($panggilans as $p)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($p->tanggal_panggilan)->format('d M Y') }}</td>
                                    <td class="p-4 font-bold text-gray-900">
                                        {{ $p->siswa->nama_lengkap ?? '-' }}
                                        <div class="text-[10px] text-gray-400 font-normal">{{ $p->siswa->kelas->nama_kelas ?? '' }}</div>
                                    </td>
                                    <td class="p-4 text-gray-600 font-medium">{{ $p->wali->nama_lengkap ?? '-' }}</td>
                                    <td class="p-4 text-gray-800 max-w-xs truncate" title="{{ $p->alasan_panggilan }}">{{ $p->alasan_panggilan }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md uppercase 
                                            {{ $p->status === 'Terpanggil' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : ($p->status === 'Tidak Hadir' ? 'bg-rose-50 border border-rose-200 text-rose-700' : 'bg-amber-50 border border-amber-200 text-amber-700') }}">
                                            {{ $p->status }}
                                        </span>
                                        @if($p->tanggal_kehadiran)
                                            <div class="text-[10px] text-gray-500 mt-1">Hadir: {{ \Carbon\Carbon::parse($p->tanggal_kehadiran)->format('d/m/Y') }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-gray-500">{{ $p->pegawai->nama_lengkap ?? '-' }}</td>
                                    <td class="p-4 pr-6 text-center">
                                        <button type="button" @click="initDelete('{{ route('bk.penanganan.destroyPanggilan', $p->id) }}', 'Panggilan {{ addslashes($p->siswa->nama_lengkap ?? '') }}')" class="text-rose-600 hover:underline font-medium cursor-pointer">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-12 text-center text-gray-400 italic bg-gray-50/30">Tidak ada data pemanggilan orang tua yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($currentTab === 'panggilan' && $panggilans->count() > 0)
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">{{ $panggilans->links() }}</div>
                @endif
            </div>

            {{-- ================= TAB 2: ALIH TANGAN KASUS ================= --}}
            <div x-show="activeTab === 'alih'" class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100" x-cloak>
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Berkas Alih Tangan Kasus (Referral)</h3>
                        <p class="text-xs text-gray-500">Pendelegasian penanganan masalah siswa kepada pihak eksternal atau pimpinan sekolah.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('bk.penanganan.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <input type="hidden" name="tab" value="alih">
                            <input type="text" name="search" value="{{ $currentTab === 'alih' ? $search : '' }}" placeholder="Cari dokumen..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56">
                            <button type="submit" class="px-3 py-2 bg-gray-800 text-white text-xs font-medium rounded-lg cursor-pointer">Cari</button>
                        </form>

                        <button @click="openCreateAlihKasus = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Buat Alih Kasus
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Tanggal Alih</th>
                                <th class="p-4">Siswa</th>
                                <th class="p-4">Topik Permasalahan</th>
                                <th class="p-4">Jenis & Tujuan Alih</th>
                                <th class="p-4">Dialihkan Kepada</th>
                                <th class="p-4 pr-6 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($alihKasusList as $a)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($a->tanggal_alih)->format('d M Y') }}</td>
                                    <td class="p-4 font-bold text-gray-900">
                                        {{ $a->siswa->nama_lengkap ?? '-' }}
                                        <div class="text-[10px] text-gray-400 font-normal">{{ $a->siswa->kelas->nama_kelas ?? '' }}</div>
                                    </td>
                                    <td class="p-4 text-gray-800 font-medium">{{ $a->topik_permasalahan }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md uppercase bg-purple-50 border border-purple-200 text-purple-700">
                                            {{ $a->jenis_alih }}
                                        </span>
                                        <div class="text-[10px] text-gray-500 mt-1">Layanan: {{ $a->bidang_bimbingan }}</div>
                                    </td>
                                    <td class="p-4 text-indigo-700 font-bold">💼 {{ $a->kepada_siapa }}</td>
                                    <td class="p-4 pr-6 text-center">
                                        <button type="button" @click="initDelete('{{ route('bk.penanganan.destroyAlihKasus', $a->id) }}', 'Referral {{ addslashes($a->siswa->nama_lengkap ?? '') }}')" class="text-rose-600 hover:underline font-medium cursor-pointer">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-gray-400 italic bg-gray-50/30">Belum ada berkas dokumen referral (alih tangan kasus) yang terbit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($currentTab === 'alih' && $alihKasusList->count() > 0)
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">{{ $alihKasusList->links() }}</div>
                @endif
            </div>

        </div>

        {{-- ================= MODAL FORM: TAMBAH PEMANGGILAN ORANG TUA ================= --}}
        <div x-show="openCreatePanggilan" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl p-6 space-y-4" @click.away="openCreatePanggilan = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">👪 Terbitkan Surat Pemanggilan Orang Tua</h3>
                    <button type="button" @click="openCreatePanggilan = false; selectedKelasPanggilan = ''; selectedSiswaPanggilan = ''" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('bk.penanganan.storePanggilan') }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Panggilan *</label>
                            <input type="date" name="tanggal_panggilan" required value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Status Kehadiran *</label>
                            <select name="status" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                                <option value="Dijadwalkan Ulang">Dijadwalkan Ulang</option>
                                <option value="Terpanggil">Terpanggil (Sudah Datang)</option>
                                <option value="Tidak Hadir">Tidak Hadir</option>
                            </select>
                        </div>
                    </div>

                    {{-- Cascading Filter: Urutan 1 (Kelas) & Urutan 2 (Siswa) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">1. Pilih Kelas Siswa *</label>
                            <select x-model="selectedKelasPanggilan" @change="selectedSiswaPanggilan = ''; document.getElementById('siswa_panggilan_select').value = ''" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                                <option value="">-- Pilih Kelas --</option>
                                <template x-for="kls in listKelas">
                                    <option :value="kls.id" x-text="kls.nama_kelas"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">2. Pilih Siswa Target *</label>
                            <select name="siswa_id" id="siswa_panggilan_select" x-model="selectedSiswaPanggilan" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
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

                    {{-- Cascading Filter: Urutan 3 (Daftar Orang Tua/Wali Otomatis Berdasarkan Pilihan Anak) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">3. Orang Tua / Wali Terkait *</label>
                            <select name="wali_id" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                                <option value="">-- Pilih Orang Tua / Wali --</option>
                                <template x-for="wali in availableWalis">
                                    <option :value="wali.id" x-text="wali.nama_lengkap + ' (' + wali.hubungan + ')'"></option>
                                </template>
                            </select>
                            <p x-show="selectedSiswaPanggilan == ''" class="text-[10px] text-amber-600 mt-1 font-medium">⚠️ Pilih siswa terlebih dahulu untuk melihat wali.</p>
                            <p x-show="selectedSiswaPanggilan != '' && availableWalis.length == 0" class="text-[10px] text-rose-600 mt-1 font-medium">❌ Anak ini belum diikat ke data Wali Manapun.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Kehadiran (Opsional)</label>
                            <input type="date" name="tanggal_kehadiran" class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Alasan Pemanggilan Kasus *</label>
                        <textarea name="alasan_panggilan" rows="2" required placeholder="Tulis alasan dikeluarkannya surat panggilan ini..." class="w-full text-xs rounded-lg border-gray-300 shadow-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Hasil Pertemuan Pertanggungjawaban</label>
                        <textarea name="hasil_pertemuan" rows="2" placeholder="Tuliskan butir hasil kesepakatan sidang jika wali murid sudah hadir..." class="w-full text-xs rounded-lg border-gray-300 shadow-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Guru Penanggung Jawab Panggilan *</label>
                        <select name="pegawai_id" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm">
                            <option value="">-- Pilih Guru BK --</option>
                            @foreach($listPegawai as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreatePanggilan = false; selectedKelasPanggilan = ''; selectedSiswaPanggilan = ''" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg cursor-pointer">Simpan Rekam Log</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL FORM: TAMBAH ALIH TANGAN KASUS ================= --}}
        <div x-show="openCreateAlihKasus" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-start justify-center p-4 pt-10" style="display: none;" x-transition>
            
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl flex flex-col max-h-[calc(100vh-5rem)]" @click.away="openCreateAlihKasus = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 pb-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">📂 Penerbitan Berkas Dokumen Alih Kasus (Referral)</h3>
                    <button type="button" @click="openCreateAlihKasus = false; selectedKelasAlih = ''" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('bk.penanganan.storeAlih') }}" method="POST" class="overflow-y-auto p-6 pt-2 space-y-4 flex-1">
                    @csrf
                    
                    {{-- Baris 1: Dropdown Filter Kelas dan Siswa Target --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">1. Pilih Kelas Siswa *</label>
                            <select x-model="selectedKelasAlih" @change="document.getElementById('siswa_alih_select').value = ''" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Kelas --</option>
                                <template x-for="kls in listKelas">
                                    <option :value="kls.id" x-text="kls.nama_kelas"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">2. Pilih Siswa Target *</label>
                            <select name="siswa_id" id="siswa_alih_select" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Topik Utama Permasalahan *</label>
                            <input type="text" name="topik_permasalahan" required placeholder="Contoh: Indikasi Gangguan Klinis" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Alih Tangan *</label>
                            <input type="date" name="tanggal_alih" required value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Baris 3: Bidang Bimbingan, Jenis Kegiatan, dan Fungsi Layanan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Bidang Bimbingan *</label>
                            <input type="text" name="bidang_bimbingan" required placeholder="Contoh: Pribadi / Sosial" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Kegiatan *</label>
                            <input type="text" name="jenis_kegiatan" required placeholder="Contoh: Referral Tenaga Medis" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Fungsi Layanan</label>
                            <input type="text" name="fungsi_kegiatan" placeholder="Contoh: Kuratif" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Baris 4: Delegasi Alih dan Instansi Penerima --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Delegasi Klasifikasi Alih *</label>
                            <select name="jenis_alih" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="Ke Ahli Lain">Ke Ahli Lain (Psikolog/Dokter)</option>
                                <option value="Ke Orang Tua">Ke Orang Tua</option>
                                <option value="Ke Kepala Sekolah">Ke Kepala Sekolah</option>
                                <option value="Ke Instansi Lain">Ke Instansi Lain</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Instansi / Ahli Penerima *</label>
                            <input type="text" name="kepada_siapa" required placeholder="Contoh: Lembaga Psikologi / Dr. Rudi" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Baris 5: Textarea --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tujuan Utama Alih Kasus</label>
                            <textarea name="tujuan_kegiatan" rows="3" placeholder="Tujuan khusus penanganan ahli..." class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Gambaran Ringkas Masalah *</label>
                            <textarea name="gambaran_ringkas_masalah" rows="3" required placeholder="Garis besar indikasi gejala siswa..." class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Alasan Utama Alih Tangan Kasus *</label>
                            <textarea name="alasan_alih_kasus" rows="3" required placeholder="Mengapa harus dialihkan..." class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hasil Yang Diharapkan Dicapai</label>
                            <textarea name="hasil_yang_dicapai" rows="3" placeholder="Target perkembangan positif..." class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    {{-- Baris 6: Keterangan Pendukung Singkat --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Bahan Disertakan</label>
                            <input type="text" name="bahan_disertakan" placeholder="Log Poin" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Keterkaitan Layanan</label>
                            <input type="text" name="keterkaitan_layanan_terdahulu" placeholder="Konseling Individu" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Rencana Tindak Lanjut</label>
                            <input type="text" name="rencana_penilaian_tindak_lanjut" placeholder="Monitoring berkala" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Baris 7: Catatan Tambahan Full Width --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan Tambahan Dokumen</label>
                        <input type="text" name="catatan" placeholder="Keterangan opsional..." class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2 bg-white sticky bottom-0">
                        <button type="button" @click="openCreateAlihKasus = false; selectedKelasAlih = ''" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg cursor-pointer transition-colors hover:bg-gray-200">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg cursor-pointer shadow-md transition-all">Terbitkan Dokumen</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: KONFIRMASI HAPUS GABUNGAN ================= --}}
        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Dokumen Penanganan?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus arsip data <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Tindakan ini bersifat permanen.
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">Ya, Hapus</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>