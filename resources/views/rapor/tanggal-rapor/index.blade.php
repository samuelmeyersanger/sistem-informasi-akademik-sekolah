<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Tanggal Rapor') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editTahunAjaranId: '',
        editSemesterId: '',
        editTempatCetak: '',
        editTanggalCetak: '',
        editNamaKepalaSekolah: '',
        editNipKepalaSekolah: '',
        editLabelKepalaSekolah: 'Kepala Sekolah',
        editLabelNipKepalaSekolah: 'NIP.',
        editLabelNipWaliKelas: 'NIP.',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(rapor) {
            this.editActionUrl = `/rapor/tanggal-rapor/${rapor.id}`;
            this.editTahunAjaranId = rapor.tahun_ajaran_id;
            this.editSemesterId = rapor.semester_id;
            this.editTempatCetak = rapor.tempat_cetak;
            // Memotong tanggal menjadi YYYY-MM-DD agar support input type='date'
            this.editTanggalCetak = rapor.tanggal_cetak ? rapor.tanggal_cetak.substring(0, 10) : '';
            this.editNamaKepalaSekolah = rapor.nama_kepala_sekolah;
            this.editNipKepalaSekolah = rapor.nip_kepala_sekolah;
            this.editLabelKepalaSekolah = rapor.label_kepala_sekolah;
            this.editLabelNipKepalaSekolah = rapor.label_nip_kepala_sekolah;
            this.editLabelNipWaliKelas = rapor.label_nip_wali_kelas;
            this.openEdit = true;
        },

        initDelete(actionUrl, targetName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = targetName;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>⚠️</span> {{ session('error') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                
                <!-- HEADER TABEL -->
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Tanggal Cetak Rapor</h3>
                        <p class="text-xs text-gray-500">Atur tanggal, tempat, dan nama Kepala Sekolah yang akan tercetak di lembar rapor.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('rapor.tanggal_rapor.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Tempat / TA..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                                @if(request('search'))
                                    <a href="{{ route('rapor.tanggal_rapor.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm">&times;</a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer shrink-0">
                                🔍 Cari
                            </button>
                        </form>

                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer">
                            ➕ Tambah Pengaturan
                        </button>
                    </div>
                </div>

                <!-- ISI TABEL -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Periode (TA & Semester)</th>
                                <th class="p-4">Tempat & Tanggal Rapor</th>
                                <th class="p-4">TTD Kepala Sekolah</th>
                                <th class="p-4 pr-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($tanggalRapors as $rapor)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="font-bold text-gray-900 text-sm">TA. {{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '-' }}</div>
                                        <div class="text-[10px] uppercase font-bold text-indigo-600 mt-0.5">Semester {{ $rapor->semester->nama_semester ?? '-' }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-800">{{ $rapor->tempat_cetak }}</div>
                                        <div class="text-gray-500">{{ \Carbon\Carbon::parse($rapor->tanggal_cetak)->translatedFormat('d F Y') }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-800">{{ $rapor->nama_kepala_sekolah }}</div>
                                        <div class="text-gray-500">{{ $rapor->label_nip_kepala_sekolah }} {{ $rapor->nip_kepala_sekolah ?? '-' }}</div>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button type="button" @click="initEdit({{ json_encode($rapor) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                                📝 Edit
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('rapor.tanggal_rapor.destroy', $rapor->id) }}', 'TA {{ addslashes($rapor->tahunAjaran->nama_tahun_ajaran ?? '') }} Semester {{ addslashes($rapor->semester->nama_semester ?? '') }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        Belum ada pengaturan tanggal rapor terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tanggalRapors->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $tanggalRapors->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- ============================================== -->
        <!-- MODAL TAMBAH DATA (CREATE)                     -->
        <!-- ============================================== -->
        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Pengaturan Rapor</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('rapor.tanggal_rapor.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- GRID 2 KOLOM -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun Ajaran *</label>
                            <select name="tahun_ajaran_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Semester *</label>
                            <select name="semester_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Pilih Semester --</option>
                                @foreach($semesters as $smt)
                                    <option value="{{ $smt->id }}">{{ $smt->nama_semester }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tempat Cetak (Contoh: Jakarta) *</label>
                            <input type="text" name="tempat_cetak" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Cetak Rapor *</label>
                            <input type="date" name="tanggal_cetak" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kepala Sekolah *</label>
                            <input type="text" name="nama_kepala_sekolah" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">NIP Kepala Sekolah</label>
                            <input type="text" name="nip_kepala_sekolah" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Label TTD Kepala Sekolah *</label>
                            <input type="text" name="label_kepala_sekolah" value="Kepala Sekolah" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Label NIP Kepala Sekolah *</label>
                            <input type="text" name="label_nip_kepala_sekolah" value="NIP." required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <!-- FULL WIDTH -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Label NIP Wali Kelas *</label>
                        <input type="text" name="label_nip_wali_kelas" value="NIP." required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <p class="text-[10px] text-gray-400 mt-1">Isi "NIP." atau "NIY." atau kosongkan sesuai standar sekolah Anda.</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL UBAH DATA (EDIT)                         -->
        <!-- ============================================== -->
        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Pengaturan Rapor</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun Ajaran *</label>
                            <select x-model="editTahunAjaranId" name="tahun_ajaran_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Semester *</label>
                            <select x-model="editSemesterId" name="semester_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">-- Pilih Semester --</option>
                                @foreach($semesters as $smt)
                                    <option value="{{ $smt->id }}">{{ $smt->nama_semester }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tempat Cetak *</label>
                            <input type="text" x-model="editTempatCetak" name="tempat_cetak" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Cetak Rapor *</label>
                            <input type="date" x-model="editTanggalCetak" name="tanggal_cetak" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kepala Sekolah *</label>
                            <input type="text" x-model="editNamaKepalaSekolah" name="nama_kepala_sekolah" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">NIP Kepala Sekolah</label>
                            <input type="text" x-model="editNipKepalaSekolah" name="nip_kepala_sekolah" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Label TTD Kepsek *</label>
                            <input type="text" x-model="editLabelKepalaSekolah" name="label_kepala_sekolah" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Label NIP Kepsek *</label>
                            <input type="text" x-model="editLabelNipKepalaSekolah" name="label_nip_kepala_sekolah" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Label NIP Wali Kelas *</label>
                        <input type="text" x-model="editLabelNipWaliKelas" name="label_nip_wali_kelas" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL HAPUS DATA (DELETE)                      -->
        <!-- ============================================== -->
        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Pengaturan Rapor?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus pengaturan untuk periode <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>?
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer border border-transparent">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">Ya, Hapus</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>