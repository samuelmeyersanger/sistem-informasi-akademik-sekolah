<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Kepegawaian') }}
        </h2>
    </x-slot>

    <div x-data="{ 
        openCreateModal: false,
        openEditModal: false,
        openDetailModal: false,
        pegawaiDetail: {},
        statusKeaktifanEdit: 'Aktif'
    }" class="py-12 bg-slate-900/10 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Message -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold">
                    ❌ Gagal menyimpan data. Silahkan periksa kembali inputan Anda.
                </div>
            @endif

            <!-- Main Table Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Pegawai (PTK)</h3>
                        <p class="text-xs text-gray-500">Manajemen data profil Guru, Kepala Sekolah, dan Tenaga Kependidikan.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ route('kepegawaian.pegawai') }}" method="GET" class="flex flex-wrap items-center gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama/NIP/NUPTK..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-48">
                            
                            <select name="jenis_ptk" class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">- Semua PTK -</option>
                                <option value="Kepala Sekolah" {{ request('jenis_ptk') == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                <option value="Guru" {{ request('jenis_ptk') == 'Guru' ? 'selected' : '' }}>Guru</option>
                                <option value="Tenaga Kependidikan" {{ request('jenis_ptk') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                            </select>

                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer">🔍 Cari</button>
                        </form>

                        <button @click="openCreateModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg cursor-pointer flex items-center gap-1 shadow-sm transition-all">
                            ➕ Tambah Pegawai
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-bold text-xs uppercase tracking-wider">
                                <th class="p-4 w-12 text-center">No</th>
                                <th class="p-4">Nama & Identitas</th>
                                <th class="p-4">Jenis PTK</th>
                                <th class="p-4">Status & Golongan</th>
                                <th class="p-4">Kontak / Email</th>
                                <th class="p-4 text-center">Keaktifan</th>
                                <th class="p-4 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($pegawai as $index => $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4 text-center font-mono text-gray-500">
                                        {{ $pegawai->firstItem() + $index }}
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900">{{ $item->nama_lengkap }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">
                                            NIP: {{ $item->nip ?? '-' }} | NUPTK: {{ $item->nuptk ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-100 border border-slate-200 text-slate-700">
                                            {{ $item->jenis_ptk }}
                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-1">Smt: {{ $item->semester->semester_aktif ?? '-' }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-semibold text-gray-800">{{ $item->status_pegawai }}</div>
                                        <div class="text-[10px] text-indigo-600 font-medium">{{ $item->pangkat_golongan ?? 'Tanpa Golongan' }}</div>
                                    </td>
                                    <td class="p-4 font-mono">
                                        <div>✉️ {{ $item->email ?? '-' }}</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">User ID: {{ $item->user_id ?? 'Belum Di-link' }}</div>
                                    </td>
                                    <td class="p-4 text-center">
                                        @php
                                            $keaktifanColors = [
                                                'Aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'Mutasi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'Pensiun' => 'bg-slate-100 text-slate-700 border-slate-300',
                                            ];
                                            $badge = $keaktifanColors[$item->status_keaktifan] ?? 'bg-gray-50 text-gray-700';
                                        @endphp
                                        <span class="px-2 py-0.5 border text-[10px] font-bold rounded-full {{ $badge }}">
                                            {{ $item->status_keaktifan }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center flex items-center justify-center gap-1.5">
                                        <!-- Detail -->
                                        <button type="button" @click="pegawaiDetail = {{ json_encode($item) }}; openDetailModal = true" class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold rounded shadow-sm transition-all cursor-pointer">👁️</button>
                                        
                                        <!-- Edit -->
                                        <button type="button" @click="pegawaiDetail = {{ json_encode($item) }}; statusKeaktifanEdit = pegawaiDetail.status_keaktifan; openEditModal = true" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold rounded shadow-sm transition-all cursor-pointer">✏️</button>
                                        
                                        <!-- Delete -->
                                        <form action="{{ route('kepegawaian.pegawai.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-bold rounded shadow-sm transition-all cursor-pointer">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        Belum ada data rekaman kepegawaian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pegawai->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $pegawai->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ================= MODAL CREATE ================= -->
        <div x-show="openCreateModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden" @click.away="openCreateModal = false">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Tambah Pegawai Baru</h3>
                        <p class="text-[11px] text-gray-500">Pendaftaran Guru atau Tenaga Kependidikan baru.</p>
                    </div>
                    <button @click="openCreateModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl cursor-pointer">&times;</button>
                </div>

                <form action="{{ route('kepegawaian.pegawai.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    <div class="p-6 overflow-y-auto space-y-5 text-xs text-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_lengkap" required class="w-full rounded-lg border-gray-200 text-xs">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select name="jenis_kelamin" required class="w-full rounded-lg border-gray-200 text-xs">
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-Laki">Laki-Laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">NIP (Opsional)</label>
                                <input type="text" name="nip" class="w-full rounded-lg border-gray-200 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">NUPTK (Opsional)</label>
                                <input type="text" name="nuptk" class="w-full rounded-lg border-gray-200 text-xs font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Status Kepegawaian <span class="text-rose-500">*</span></label>
                                <select name="status_pegawai" required class="w-full rounded-lg border-gray-200 text-xs">
                                    <option value="HONORER">HONORER</option>
                                    <option value="PNS">PNS</option>
                                    <option value="PPPK">PPPK</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Pangkat / Golongan</label>
                                <input type="text" name="pangkat_golongan" placeholder="e.g. Pembina / IV-a" class="w-full rounded-lg border-gray-200 text-xs">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Jenis PTK <span class="text-rose-500">*</span></label>
                                <select name="jenis_ptk" required class="w-full rounded-lg border-gray-200 text-xs">
                                    <option value="Guru">Guru</option>
                                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                                    <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" placeholder="nama@sekolah.sch.id" class="w-full rounded-lg border-gray-200 text-xs">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Link Akun User</label>
                                <select name="user_id" class="w-full rounded-lg border-gray-200 text-xs">
                                    <option value="">-- Tanpa Akun Login --</option>
                                    @foreach($user_list as $usr)
                                        <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Semester Input</label>
                                <select name="semester_id" class="w-full rounded-lg border-gray-200 text-xs">
                                    <option value="">-- Pilih Semester --</option>
                                    @foreach($semester_list as $sem)
                                        <option value="{{ $sem->id }}">{{ $sem->semester_aktif }} ({{ $sem->tahun_ajaran }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                        <button type="button" @click="openCreateModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg cursor-pointer">💾 Simpan Pegawai</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL EDIT ================= -->
        <div x-show="openEditModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden" @click.away="openEditModal = false">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Perbarui Data Pegawai</h3>
                        <p class="text-[11px] text-gray-500">Kelola informasi profil kepegawaian termasuk status mutasi/pensiun.</p>
                    </div>
                    <button @click="openEditModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl cursor-pointer">&times;</button>
                </div>

                <form :action="'{{ url('kepegawaian/pegawai/update') }}/' + pegawaiDetail.id" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    @method('PUT')
                    <div class="p-6 overflow-y-auto space-y-5 text-xs text-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" x-model="pegawaiDetail.nama_lengkap" required class="w-full rounded-lg border-gray-200 text-xs">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <select name="jenis_kelamin" x-model="pegawaiDetail.jenis_kelamin" required class="w-full rounded-lg border-gray-200 text-xs">
                                    <option value="Laki-Laki">Laki-Laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">NIP</label>
                                <input type="text" name="nip" x-model="pegawaiDetail.nip" class="w-full rounded-lg border-gray-200 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">NUPTK</label>
                                <input type="text" name="nuptk" x-model="pegawaiDetail.nuptk" class="w-full rounded-lg border-gray-200 text-xs font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Status Pegawai</label>
                                <select name="status_pegawai" x-model="pegawaiDetail.status_pegawai" required class="w-full rounded-lg border-gray-200 text-xs">
                                    <option value="HONORER">HONORER</option>
                                    <option value="PNS">PNS</option>
                                    <option value="PPPK">PPPK</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Pangkat/Golongan</label>
                                <input type="text" name="pangkat_golongan" x-model="pegawaiDetail.pangkat_golongan" class="w-full rounded-lg border-gray-200 text-xs">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Jenis PTK</label>
                                <select name="jenis_ptk" x-model="pegawaiDetail.jenis_ptk" required class="w-full rounded-lg border-gray-200 text-xs">
                                    <option value="Guru">Guru</option>
                                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                                    <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" x-model="pegawaiDetail.email" class="w-full rounded-lg border-gray-200 text-xs">
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Link Akun User</label>
                                <select name="user_id" x-model="pegawaiDetail.user_id" class="w-full rounded-lg border-gray-200 text-xs">
                                    <option value="">-- Tanpa Akun --</option>
                                    @foreach($user_list as $usr)
                                        <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-gray-700 mb-1">Status Keaktifan</label>
                                <select name="status_keaktifan" x-model="statusKeaktifanEdit" required class="w-full rounded-lg border-indigo-300 text-xs bg-indigo-50/50 font-bold text-indigo-900">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Mutasi">Mutasi</option>
                                    <option value="Pensiun">Pensiun</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kondisional Input Mutasi -->
                        <div x-show="statusKeaktifanEdit === 'Mutasi'" class="p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-3" x-transition>
                            <h4 class="font-bold text-amber-800">📋 Berkas & Data Mutasi Keluar</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-medium text-amber-900 mb-1">Tanggal Keluar Mutasi</label>
                                    <input type="date" name="tanggal_mutasi" x-model="pegawaiDetail.tanggal_mutasi" class="w-full rounded-lg border-gray-200 text-xs">
                                </div>
                                <div>
                                    <label class="block font-medium text-amber-900 mb-1">Instansi/Sekolah Tujuan</label>
                                    <input type="text" name="sekolah_tujuan" x-model="pegawaiDetail.sekolah_tujuan" placeholder="Nama sekolah baru" class="w-full rounded-lg border-gray-200 text-xs">
                                </div>
                            </div>
                            <div>
                                <label class="block font-medium text-amber-900 mb-1">Alasan Mutasi</label>
                                <textarea name="alasan_mutasi" x-model="pegawaiDetail.alasan_mutasi" rows="2" class="w-full rounded-lg border-gray-200 text-xs"></textarea>
                            </div>
                            <div>
                                <label class="block font-medium text-amber-900 mb-1">Unggah Dokumen SK Mutasi (PDF/Gambar)</label>
                                <input type="file" name="file_surat_mutasi" class="w-full text-xs">
                            </div>
                        </div>

                        <!-- Kondisional Input Pensiun -->
                        <div x-show="statusKeaktifanEdit === 'Pensiun'" class="p-4 bg-slate-100 border border-slate-300 rounded-xl space-y-3" x-transition>
                            <h4 class="font-bold text-slate-800">👴 Data Pemberhentian / Pensiun</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-medium text-slate-900 mb-1">Tanggal Terhitung Pensiun</label>
                                    <input type="date" name="tanggal_pensiun" x-model="pegawaiDetail.tanggal_pensiun" class="w-full rounded-lg border-gray-200 text-xs">
                                </div>
                                <div>
                                    <label class="block font-medium text-slate-900 mb-1">Unggah Surat Keterangan Pensiun</label>
                                    <input type="file" name="file_surat_pensiun" class="w-full text-xs">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                        <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg cursor-pointer">💾 Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL DETAIL ================= -->
        <div x-show="openDetailModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-xl w-full flex flex-col overflow-hidden" @click.away="openDetailModal = false">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-900">Profil Lengkap Pegawai</h3>
                    <button @click="openDetailModal = false" class="text-gray-400 font-bold text-xl cursor-pointer">&times;</button>
                </div>

                <div class="p-6 space-y-4 text-xs text-gray-700">
                    <div class="border-b border-gray-100 pb-3">
                        <span class="text-gray-400 block font-medium">Nama Lengkap</span>
                        <span class="text-sm font-bold text-gray-900" x-text="pegawaiDetail.nama_lengkap"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-b border-gray-100 pb-3 font-mono">
                        <div>
                            <span class="text-gray-400 block font-sans font-medium">NIP</span>
                            <span class="text-gray-800" x-text="pegawaiDetail.nip || '-'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-sans font-medium">NUPTK</span>
                            <span class="text-gray-800" x-text="pegawaiDetail.nuptk || '-'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-b border-gray-100 pb-3">
                        <div>
                            <span class="text-gray-400 block font-medium">Jenis Kelamin</span>
                            <span class="text-gray-800" x-text="pegawaiDetail.jenis_kelamin"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium">Jenis PTK</span>
                            <span class="text-gray-800 font-bold" x-text="pegawaiDetail.jenis_ptk"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-b border-gray-100 pb-3">
                        <div>
                            <span class="text-gray-400 block font-medium">Status Pegawai</span>
                            <span class="text-gray-800 font-bold" x-text="pegawaiDetail.status_pegawai"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium">Pangkat / Golongan</span>
                            <span class="text-gray-800" x-text="pegawaiDetail.pangkat_golongan || '-'"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-gray-400 block font-medium">Status Keaktifan</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold inline-block mt-1" 
                              :class="pegawaiDetail.status_keaktifan === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : (pegawaiDetail.status_keaktifan === 'Mutasi' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800')"
                              x-text="pegawaiDetail.status_keaktifan"></span>
                    </div>

                    <!-- Detail Tambahan Jika Mutasi -->
                    <div x-show="pegawaiDetail.status_keaktifan === 'Mutasi'" class="p-3 bg-amber-50 rounded-lg border border-amber-100 space-y-1">
                        <div class="font-bold text-amber-900">Detail Mutasi Out:</div>
                        <div>📅 Tanggal: <span class="font-mono text-gray-900" x-text="pegawaiDetail.tanggal_mutasi"></span></div>
                        <div>🏫 Tujuan: <span class="text-gray-900" x-text="pegawaiDetail.sekolah_tujuan"></span></div>
                        <div>📝 Alasan: <span class="text-gray-600 italic" x-text="pegawaiDetail.alasan_mutasi"></span></div>
                    </div>

                    <!-- Detail Tambahan Jika Pensiun -->
                    <div x-show="pegawaiDetail.status_keaktifan === 'Pensiun'" class="p-3 bg-slate-50 rounded-lg border border-slate-200 space-y-1">
                        <div class="font-bold text-slate-800">Detail Pensiun:</div>
                        <div>📅 Tanggal Pensiun: <span class="font-mono text-gray-900" x-text="pegawaiDetail.tanggal_pensiun"></span></div>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button type="button" @click="openDetailModal = false" class="px-5 py-2 bg-gray-800 text-white font-semibold rounded-lg cursor-pointer">Tutup</button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>