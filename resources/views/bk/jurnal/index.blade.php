<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modul BK - Jurnal Harian Bimbingan Konseling') }}
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

        // Trigger pemicu modal edit (Memasukkan data row lama ke dalam Alpine State)
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

            {{-- Blok Navigasi & Pencarian Atas --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Arsip Jurnal Harian Guru BK</h3>
                        <p class="text-xs text-gray-500">Log pencatatan aktivitas layanan bimbingan klasikal, kelompok, maupun individu harian.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('bk.jurnal.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari sasaran, layanan, hasil..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-60">
                            <button type="submit" class="px-3 py-2 bg-gray-800 text-white text-xs font-medium rounded-lg cursor-pointer transition-colors hover:bg-gray-900">Cari</button>
                        </form>

                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm flex items-center justify-center gap-1 cursor-pointer transition-all">
                            ➕ Tambah Jurnal Harian
                        </button>
                    </div>
                </div>

                {{-- Tabel Render Data Jurnal --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Tanggal & Minggu</th>
                                <th class="p-4">Guru BK</th>
                                <th class="p-4">Kelas Sasaran</th>
                                <th class="p-4">Sasaran Kegiatan</th>
                                <th class="p-4">Aktivitas Layanan</th>
                                <th class="p-4">Hasil / Tindak Lanjut</th>
                                <th class="p-4 pr-6 text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($jurnals as $j)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 whitespace-nowrap text-gray-900 font-medium">
                                        {{ \Carbon\Carbon::parse($j->tanggal)->format('d M Y') }}
                                        <div class="text-[10px] text-gray-400 font-normal mt-0.5">Minggu Ke-{{ $j->minggu_ke }}</div>
                                    </td>
                                    <td class="p-4 text-gray-600 whitespace-nowrap font-medium">👨‍🏫 {{ $j->pegawai->nama_lengkap ?? '-' }}</td>
                                    <td class="p-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 border border-slate-200 text-slate-700">
                                            {{ $j->kelas->nama_kelas ?? 'Semua Kelas' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-800 font-medium max-w-xs truncate" title="{{ $j->sasaran_kegiatan }}">{{ $j->sasaran_kegiatan }}</td>
                                    <td class="p-4 text-gray-600 max-w-xs truncate" title="{{ $j->kegiatan_layanan }}">{{ $j->kegiatan_layanan }}</td>
                                    <td class="p-4 text-gray-500 max-w-xs truncate" title="{{ $j->hasil }}">{{ $j->hasil }}</td>
                                    <td class="p-4 pr-6 text-center space-x-2 whitespace-nowrap">
                                        <button type="button" @click="initEdit('{{ route('bk.jurnal.update', $j->id) }}', {{ json_encode($j) }})" class="text-indigo-600 hover:underline font-semibold cursor-pointer">
                                            ✏️ Edit
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        
                                        {{-- 🛠️ PERBAIKAN: Mengganti format escaping karakter tanggal (\'d/m/Y\') dengan format string JavaScript murni tanpa backslash --}}
                                        <button type="button" @click="initDelete('{{ route('bk.jurnal.destroy', $j->id) }}', 'Jurnal Jurnal Tanggal ' + '{{ \Carbon\Carbon::parse($j->tanggal)->format('d-m-Y') }}')" class="text-rose-600 hover:underline font-semibold cursor-pointer">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-12 text-center text-gray-400 italic bg-gray-50/30">Belum ada catatan jurnal harian BK pada lembar arsip ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($jurnals->count() > 0)
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">{{ $jurnals->links() }}</div>
                @endif
            </div>

        </div>

        {{-- ================= MODAL FORM: TAMBAH JURNAL HARIAN ================= --}}
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-start justify-center p-4 pt-10" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl flex flex-col max-h-[calc(100vh-5rem)]" @click.away="openCreate = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 pb-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">📝 Tambah Log Jurnal Harian Baru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('bk.jurnal.store') }}" method="POST" class="overflow-y-auto p-6 pt-2 space-y-4 flex-1">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Kegiatan *</label>
                            <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Minggu Ke *</label>
                            <select name="minggu_ke" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="I">I (Satu)</option>
                                <option value="II">II (Dua)</option>
                                <option value="III">III (Tiga)</option>
                                <option value="IV">IV (Empat)</option>
                                <option value="V">V (Lima)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kelas Sasaran (Opsional)</label>
                            <select name="kelas_id" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($listKelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Guru BK Penanggung Jawab *</label>
                        <select name="pegawai_id" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Aparatur Guru BK --</option>
                            @foreach($listGuruBk as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sasaran Kegiatan *</label>
                        <input type="text" name="sasaran_kegiatan" required placeholder="Contoh: Siswa kelas XI-A yang sering terlambat / Individu Inisial R" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Bentuk Kegiatan Layanan *</label>
                        <textarea name="kegiatan_layanan" rows="3" required placeholder="Contoh: Melakukan konseling pribadi perihal hambatan transportasi rumah..." class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Hasil / Tindak Lanjut Layanan *</label>
                        <textarea name="hasil" rows="3" required placeholder="Contoh: Siswa berjanji mengatur jam alarm tidur dan BK akan memonitor kartu presensi absen..." class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2 bg-white sticky bottom-0">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg cursor-pointer transition-colors hover:bg-gray-200">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg cursor-pointer shadow-md transition-all">Simpan Jurnal</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL FORM: EDIT DATA JURNAL ================= --}}
        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-start justify-center p-4 pt-10" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl flex flex-col max-h-[calc(100vh-5rem)]" @click.away="openEdit = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 pb-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">✏️ Perbarui Lembar Data Jurnal</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
                </div>
                
                <form :action="editActionUrl" method="POST" class="overflow-y-auto p-6 pt-2 space-y-4 flex-1">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Kegiatan *</label>
                            <input type="date" name="tanggal" required x-model="jurnalData.tanggal" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Minggu Ke *</label>
                            <select name="minggu_ke" required x-model="jurnalData.minggu_ke" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="I">I (Satu)</option>
                                <option value="II">II (Dua)</option>
                                <option value="III">III (Tiga)</option>
                                <option value="IV">IV (Empat)</option>
                                <option value="V">V (Lima)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kelas Sasaran (Opsional)</label>
                            <select name="kelas_id" x-model="jurnalData.kelas_id" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($listKelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Guru BK Penanggung Jawab *</label>
                        <select name="pegawai_id" required x-model="jurnalData.pegawai_id" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Aparatur Guru BK --</option>
                            @foreach($listGuruBk as $g)
                                <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sasaran Kegiatan *</label>
                        <input type="text" name="sasaran_kegiatan" required x-model="jurnalData.sasaran_kegiatan" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Bentuk Kegiatan Layanan *</label>
                        <textarea name="kegiatan_layanan" rows="3" required x-model="jurnalData.kegiatan_layanan" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Hasil / Tindak Lanjut Layanan *</label>
                        <textarea name="hasil" rows="3" required x-model="jurnalData.hasil" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2 bg-white sticky bottom-0">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg cursor-pointer transition-colors hover:bg-gray-200">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg cursor-pointer shadow-md transition-all">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: KONFIRMASI HAPUS JURNAL ================= --}}
        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Log Jurnal BK?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus data <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Data yang dihapus akan dipindahkan ke tempat pembuangan sementara (Soft Delete).
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">Ya, Hapus Log</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>