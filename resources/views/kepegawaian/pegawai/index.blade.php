<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">👨‍💼</span> {{ __('Manajemen Kepegawaian') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openDelete: false,
        openGeneratePegawai: false, 
        openImport: false, // State membuka modal import Excel

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initDelete(actionUrl, namaPegawai) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = namaPegawai;
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

            @if(session('info'))
                <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">ℹ️</span> {{ session('info') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">❌</span> {{ session('error') }}
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

            {{-- Main Table Card --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 mb-1">Direktori Tenaga Pendidik & Kependidikan</h3>
                        <p class="text-sm text-gray-500">Kelola informasi biodata profil, mutasi, pensiun, serta kepangkatan pegawai secara terpusat.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <!-- Form Pencarian -->
                        <form action="{{ route('kepegawaian.pegawai.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🔍</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP..." class="text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner w-full sm:w-64 py-3 pl-12 pr-12 transition-colors">
                            
                            @if(request('search'))
                                <a href="{{ route('kepegawaian.pegawai.index') }}" class="absolute inset-y-0 right-16 flex items-center pr-2 text-gray-400 hover:text-rose-500 font-bold text-lg transition-colors cursor-pointer" title="Bersihkan Pencarian">
                                    &times;
                                </a>
                            @endif
                            <button type="submit" class="absolute inset-y-1.5 right-1.5 px-3 py-1.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors cursor-pointer">Cari</button>
                        </form>

                        <!-- Tombol Aksi Kunci (Berdasarkan Permission) -->
                        @if(auth()->user()->hasPermission('kepegawaian.pegawai.store'))
                            <div class="flex flex-wrap items-center gap-2 bg-gray-50/50 p-1 rounded-xl border border-gray-200/60 shadow-sm shrink-0">
                                <a href="{{ route('kepegawaian.pegawai.downloadTemplate') }}" class="px-4 py-2 bg-white hover:bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg shadow-sm border border-emerald-100 transition-colors flex items-center gap-1.5 cursor-pointer">
                                    <span class="text-base">📥</span> Template Excel
                                </a>
                                <button @click="openImport = true" class="px-4 py-2 bg-white hover:bg-teal-50 text-teal-700 text-xs font-bold rounded-lg shadow-sm border border-teal-100 transition-colors flex items-center gap-1.5 cursor-pointer">
                                    <span class="text-base">📤</span> Import Data
                                </button>
                                <button @click="openGeneratePegawai = true" class="px-4 py-2 bg-white hover:bg-amber-50 text-amber-700 text-xs font-bold rounded-lg shadow-sm border border-amber-100 transition-colors flex items-center gap-1.5 cursor-pointer" title="Buat akun untuk semua yang belum punya">
                                    <span class="text-base">⚡</span> Mass Generate
                                </button>
                            </div>
                            
                            <button @click="openCreate = true" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold rounded-xl shadow-lg transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2 shrink-0">
                                <span class="text-lg">➕</span> Pegawai Baru
                            </button>
                        @endif 
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <th class="p-5 pl-8">Identitas Pegawai</th>
                                <th class="p-5 w-48">Tugas Pokok</th>
                                <th class="p-5 w-56">Status & Golongan</th>
                                <th class="p-5 text-center w-36">Keaktifan</th>
                                <th class="p-5 pr-8 text-center w-64">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-gray-700">
                            @forelse($pegawai as $p)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="p-5 pl-8 align-middle">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-lg shadow-sm shrink-0">
                                                {{ $p->jenis_kelamin === 'Perempuan' ? '👩‍🏫' : '👨‍🏫' }}
                                            </div>
                                            <div>
                                                <div class="font-black text-gray-900 text-base leading-tight mb-1">{{ $p->nama_lengkap }}</div>
                                                <div class="text-gray-500 text-[11px] font-semibold flex flex-wrap gap-2">
                                                    <span>NIP: <span class="text-gray-800">{{ $p->nip ?? '-' }}</span></span>
                                                    <span class="text-gray-300">|</span>
                                                    <span>NUPTK: <span class="text-gray-800">{{ $p->nuptk ?? '-' }}</span></span>
                                                </div>
                                                @if($p->user_id && $p->user)
                                                    <div class="mt-2 inline-flex items-center gap-1.5 text-[10px] text-indigo-700 bg-indigo-50/50 px-2 py-0.5 rounded border border-indigo-100 font-bold">
                                                        <span>📧</span> {{ $p->user->email }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5 align-middle">
                                        <span class="inline-block px-3 py-1.5 bg-slate-50 border border-slate-200 text-slate-700 font-black text-xs uppercase tracking-wider rounded-lg shadow-sm">
                                            {{ $p->jenis_ptk }}
                                        </span>
                                    </td>
                                    <td class="p-5 align-middle">
                                        <div class="font-black text-gray-800 text-sm mb-1">{{ $p->status_pegawai }}</div>
                                        <div class="inline-flex items-center gap-1.5 text-[10px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded font-bold border border-gray-200">
                                            🏅 {{ $p->pangkat_golongan ?? 'Belum ada kepangkatan' }}
                                        </div>
                                    </td>
                                    <td class="p-5 text-center align-middle">
                                        @if($p->status_keaktifan == 'Aktif')
                                            <span class="px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm inline-flex items-center gap-1.5 w-full justify-center">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Aktif
                                            </span>
                                        @else
                                            <span class="px-3 py-1.5 bg-gray-100 border border-gray-200 text-gray-500 text-[10px] font-black uppercase tracking-wider rounded-lg inline-flex items-center w-full justify-center">
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-5 pr-8 text-center align-middle">
                                        <div class="flex items-center justify-center gap-2">
                                            
                                            @if(!$p->user_id && $p->status_keaktifan == 'Aktif')
                                                <form action="{{ route('kepegawaian.pegawai.generateIndividu', $p->id) }}" method="POST" class="m-0 inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-700 font-bold rounded-xl text-[10px] cursor-pointer transition-colors shadow-sm" title="Buat Akun Sistem">
                                                        🔑 Buat Akun
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('kepegawaian.pegawai.show', $p->id) }}" class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 text-indigo-700 font-bold rounded-xl text-[10px] transition-colors shadow-sm" title="Lihat Profil">
                                                🔍 Profil
                                            </a>
                                            
                                            @if(auth()->user()->hasPermission('kepegawaian.pegawai.store'))
                                                <button type="button" @click="initDelete('{{ route('kepegawaian.pegawai.destroy', $p->id) }}', '{{ addslashes($p->nama_lengkap) }}')" class="p-2 bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 font-bold rounded-xl text-xs cursor-pointer transition-colors shadow-sm" title="Hapus Data">
                                                    🗑️
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                        @if(request('search'))
                                            <span class="text-5xl block mb-4">🔍</span>
                                            <p class="text-lg font-bold text-gray-500">Hasil pencarian "{{ request('search') }}" tidak ditemukan.</p>
                                        @else
                                            <span class="text-5xl block mb-4">📭</span>
                                            <p class="text-lg font-bold text-gray-500">Belum ada data pegawai yang terdaftar.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Area Paginasi (Bila ada) --}}
                @if($pegawai instanceof \Illuminate\Pagination\LengthAwarePaginator && $pegawai->count() > 0)
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">{{ $pegawai->links() }}</div>
                @endif
            </div>
        </div>

        {{-- ================= MODAL FORM: TAMBAH PEGAWAI ================= --}}
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden" @click.away="openCreate = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">➕ Registrasi Pegawai Baru</h3>
                        <p class="text-xs text-emerald-600 font-bold mt-1 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Status keaktifan otomatis di-set ke Aktif.</p>
                    </div>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('kepegawaian.pegawai.store') }}" method="POST">
                    @csrf
                    <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_lengkap" required placeholder="Contoh: Dr. Budi Santoso, M.Pd." class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select name="jenis_kelamin" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    <option value="">-- Pilih Gender --</option>
                                    <option value="Laki-Laki">Laki-Laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jenis PTK (Tugas Utama) <span class="text-rose-500">*</span></label>
                                <select name="jenis_ptk" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    <option value="">-- Klasifikasi Tugas --</option>
                                    <option value="Guru">Guru / Pendidik</option>
                                    <option value="Tenaga Kependidikan">Tenaga Kependidikan (TU/Staff)</option>
                                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2 p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-indigo-900 mb-2">Nomor Induk Pegawai (NIP)</label>
                                    <input type="text" name="nip" placeholder="Opsional jika Honorer" class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-indigo-900 mb-2">NUPTK</label>
                                    <input type="text" name="nuptk" placeholder="Opsional jika belum ada" class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Status Kepegawaian Dasar <span class="text-rose-500">*</span></label>
                                <select name="status_pegawai" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white px-4 py-3">
                                    <option value="HONORER">GTY / HONORER</option>
                                    <option value="PNS">Pegawai Negeri Sipil (PNS)</option>
                                    <option value="PPPK">ASN - PPPK</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pangkat / Golongan</label>
                                <input type="text" name="pangkat_golongan" placeholder="Misal: Penata Muda Tk. I / IIIb" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 px-4 py-3">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email Sistem <span class="text-gray-400 font-normal">(Digunakan untuk Akun Login)</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">📧</span>
                                    <input type="email" name="email" placeholder="nama@sekolah.sch.id" class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50 py-3 pl-10 pr-4">
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">💾 Simpan Pegawai</button>
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
                    <p class="text-sm text-gray-500 mb-8 px-2">
                        Anda akan menghapus data profil milik <strong class="text-gray-800" x-text="deleteTargetName"></strong>. Semua rekam jejak, riwayat pangkat, dan akun sistemnya akan ikut dihapus.
                    </p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: GENERATE MASSAL AKUN ================= --}}
        <div x-show="openGeneratePegawai" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl p-8 text-center relative overflow-hidden" @click.away="openGeneratePegawai = false">
                
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-indigo-50 border border-indigo-100 mb-6 relative">
                    <div class="absolute inset-0 bg-indigo-100 rounded-full animate-ping opacity-50"></div>
                    <span class="text-4xl relative z-10">⚡</span>
                </div>
                
                <div>
                    <h3 class="text-2xl font-black text-gray-900 mb-4">Otomasi Akun Massal</h3>
                    <p class="text-sm text-gray-500 mb-8 px-2">
                        Sistem akan melakukan pemindaian dan <strong class="text-gray-800">menciptakan akun login</strong> (Email & Password default) untuk seluruh pegawai berstatus aktif yang saat ini belum memiliki akses.
                    </p>
                </div>
                
                <form action="{{ route('kepegawaian.pegawai.generateMassal') }}" method="POST" class="flex flex-col gap-3 m-0">
                    @csrf
                    <button type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none w-full text-base">
                        Mulai Proses Otomasi
                    </button>
                    <button type="button" @click="openGeneratePegawai = false" class="px-6 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none w-full">
                        Batalkan
                    </button>
                </form>
            </div>
        </div>

        {{-- ================= MODAL FORM: IMPORT EXCEL ================= --}}
        <div x-show="openImport" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden" @click.away="openImport = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">📤 Import Data Pegawai</h3>
                        <p class="text-xs text-gray-500 font-medium mt-1">Unggah berkas Excel berformat .xlsx atau .xls.</p>
                    </div>
                    <button type="button" @click="openImport = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('kepegawaian.pegawai.importExcel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="p-8 space-y-6">
                        
                        <div class="p-5 bg-blue-50/80 border border-blue-100 rounded-2xl">
                            <span class="text-sm font-black text-blue-900 block mb-3 flex items-center gap-2"><span class="text-xl">💡</span> Petunjuk Import:</span>
                            <ul class="list-disc list-inside text-xs text-blue-800 font-medium space-y-2">
                                <li>Pastikan menggunakan format tabel dari tombol <strong class="text-blue-900">"Template Excel"</strong>.</li>
                                <li>Gunakan validasi dropdown bawaan template untuk kolom <em>Jenis Kelamin</em> dan <em>Status</em>.</li>
                                <li>Sistem akan <strong class="text-blue-900">meng-update otomatis</strong> data pegawai jika NIP yang diunggah cocok dengan data di database.</li>
                            </ul>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Dokumen Spreadsheets <span class="text-rose-500">*</span></label>
                            
                            <div class="w-full flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:bg-gray-50 hover:border-teal-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <span class="text-4xl">📊</span>
                                    <div class="flex text-sm text-gray-600 mt-2 font-medium">
                                        <input type="file" name="file_excel" required accept=".xlsx,.xls" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer focus:outline-none">
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">Maksimal ukuran file 5MB</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openImport = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer">🚀 Unggah & Eksekusi</button>
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