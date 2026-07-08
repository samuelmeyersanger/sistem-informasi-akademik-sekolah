<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Kelas & Anggota Siswa') }}
            </h2>
            <a href="{{ route('kesiswaan.kelas.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                ⬅️ Kembali ke Daftar Kelas
            </a>
        </div>
    </x-slot>

    <div x-data="{
        openPlotting: false,
        openMutasiMassal: false,
        openKelulusanMassal: false,
        openImport: false,
        openConfirmDelete: false,
        deleteUrl: '',
        siswaNama: '',
        
        // Checkbox massal states
        selectedSiswa: [],
        
        toggleAll(el) {
            if (el.checked) {
                this.selectedSiswa = Array.from(document.querySelectorAll('.siswa-checkbox')).map(cb => cb.value);
            } else {
                this.selectedSiswa = [];
            }
        },

        triggerConfirm(url, nama) {
            this.deleteUrl = url;
            this.siswaNama = nama;
            this.openConfirmDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                    <p class="font-bold mb-1 flex items-center gap-1">⚠️ Gagal mengeksekusi tindakan:</p>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-2xl flex items-center justify-center text-2xl font-bold shadow-inner">
                        {{ $kelas->tingkat }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Ruang Kelas {{ $kelas->nama_kelas }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Wali Kelas: 
                            <span class="font-semibold text-gray-800">
                                {{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : 'Belum Ditugaskan' }}
                            </span>
                        </p>
                    </div>
                </div>

                <form action="{{ route('kesiswaan.kelas.show', $kelas->id) }}" method="GET" class="flex items-center gap-2 bg-gray-50 p-2 rounded-xl border border-gray-100">
                    <label class="text-xs font-bold text-gray-600 pl-1">Periode Anggota:</label>
                    <select name="semester_id" onchange="this.form.submit()" class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pr-8 py-1">
                        @foreach($semester_list as $sem)
                            <option value="{{ $sem->id }}" {{ $current_semester_id == $sem->id ? 'selected' : '' }}>
                                Semester {{ $sem->nama }} (Ke-{{ $sem->semester_ke }}) - {{ $sem->tahunAjaran ? $sem->tahunAjaran->tahun_ajaran : 'Tanpa Tahun Ajaran' }} {{ $sem->is_aktif ? '[AKTIF]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h4 class="text-base font-bold text-gray-900">Daftar Siswa Terdaftar</h4>
                        <p class="text-xs text-gray-500">Jumlah total peserta didik aktif pada kelas ini: <strong>{{ count($anggota) }} Siswa</strong>.</p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-2">
                        <button @click="openPlotting = true" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1 cursor-pointer">
                            ➕ Masukkan Siswa
                        </button>

                        <a href="{{ route('kesiswaan.kelas.anggota.downloadTemplate', ['id' => $kelas->id, 'semester_id' => $current_semester_id]) }}" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-black text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1">
                            📥 Download Template
                        </a>

                        <button @click="openImport = true" class="px-3 py-2 bg-teal-600 hover:bg-teal-700 text-black text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1 cursor-pointer">
                            📤 Import Excel Anggota
                        </button>
                        
                        <div x-show="selectedSiswa.length > 0" style="display: none;" class="flex items-center gap-2 bg-amber-50 p-1 rounded-lg border border-amber-200" x-transition>
                            <span class="text-[10px] font-bold text-amber-800 px-2" x-text="selectedSiswa.length + ' dipilih:'"></span>
                            <button @click="openMutasiMassal = true" class="px-2 py-1 bg-amber-600 hover:bg-amber-700 text-black text-[11px] font-medium rounded cursor-pointer shadow-sm">
                                🔄 Mutasi/Naik Kelas
                            </button>
                            @if($kelas->tingkat == 9)
                                <button @click="openKelulusanMassal = true" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-black text-[11px] font-medium rounded cursor-pointer shadow-sm">
                                    🎓 Luluskan Massal
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <form id="massForm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                    <th class="p-4 pl-6 w-10 text-center">
                                        <input type="checkbox" @change="toggleAll($event.target)" :checked="selectedSiswa.length === {{ count($anggota) }} && {{ count($anggota) }} > 0" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </th>
                                    <th class="p-4">Nama Lengkap & NISN</th>
                                    <th class="p-4 text-center">Jenis Kelamin</th>
                                    <th class="p-4 text-center">Status Keanggotaan</th>
                                    <th class="p-4 pr-6 text-center w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                                '@forelse($anggota as $item)'
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="p-4 pl-6 text-center">
                                            <input type="checkbox" value="{{ $item->siswa_id }}" x-model="selectedSiswa" class="siswa-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </td>
                                        <td class="p-4">
                                            <div class="font-bold text-gray-900 text-sm">👤 {{ $item->siswa->nama_lengkap }}</div>
                                            <div class="text-gray-400 text-[11px] mt-0.5">NISN: {{ $item->siswa->nisn ?? '-' }} | NIK: {{ $item->siswa->nik ?? '-' }}</div>
                                        </td>
                                        <td class="p-4 text-center font-medium">
                                            {{ $item->siswa->jenis_kelamin == 'Laki-Laki' || $item->siswa->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="px-2 py-0.5 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold rounded">🟢 AKTIF BELAJAR</span>
                                        </td>
                                        <td class="p-4 pr-6 text-center">
                                            <button type="button" @click="triggerConfirm('{{ route('kesiswaan.kelas.anggota.remove', $item->id) }}', '{{ addslashes($item->siswa->nama_lengkap) }}')" class="p-1 text-rose-600 hover:text-rose-800 hover:underline font-medium cursor-pointer">
                                                ❌ Keluarkan
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                            Belum ada siswa yang di-plotting masuk ke dalam kelas ini pada semester terpilih.
                                        </td>
                                    </tr>
                                @endforelse 
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openPlotting" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openPlotting = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Plotting Siswa Masuk Kelas</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Hanya menampilkan siswa berstatus aktif yang belum memiliki kelas di semester terpilih.</p>
                    </div>
                    <button type="button" @click="openPlotting = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('kesiswaan.kelas.anggota.store') }}" method="POST" class="space-y-3 text-left">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="semester_id" value="{{ $current_semester_id }}">
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Satu/Banyak Siswa *</label>
                        <select name="siswa_ids[]" multiple required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm h-48">
                            @forelse($siswa_tanpa_kelas as $s)
                                <option value="{{ $s->id }}">👤 {{ $s->nama_lengkap }} ({{ $s->jenis_kelamin == 'Laki-Laki' || $s->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }})</option>
                            @empty
                                <option disabled>-- Tidak ada siswa kosong/tanpa kelas saat ini --</option>
                            @endforelse
                        </select>
                        <p class="text-[10px] text-indigo-500 mt-1">💡 Tahan tombol <strong>Ctrl (Windows)</strong> atau <strong>Command (Mac)</strong> untuk memilih lebih dari 1 siswa sekaligus.</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openPlotting = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" {{ count($siswa_tanpa_kelas) == 0 ? 'disabled' : '' }} class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer disabled:opacity-50">
                            Masukkan Ke Kelas
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openImport" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openImport = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Import Anggota Kelas Massal</h3>
                        <p class="text-[10px] text-gray-400 mt-0.5">Unggah file template Excel yang telah diisi tanda 'Y'.</p>
                    </div>
                    <button type="button" @click="openImport = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('kesiswaan.kelas.anggota.import', $kelas->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
                    @csrf
                    <input type="hidden" name="semester_id" value="{{ $current_semester_id }}">
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih File Excel *</label>
                        <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-gray-200 rounded-lg p-1 shadow-sm focus:outline-none">
                    </div>

                    <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl text-[11px] text-blue-800 leading-relaxed space-y-1">
                        <p class="font-bold">💡 Cara Menggunakan Fitur Import:</p>
                        <ol class="list-decimal list-inside space-y-0.5 pl-0.5">
                            <li>Klik tombol <strong>Download Template</strong> terlebih dahulu.</li>
                            <li>Buka file Excel tersebut di Microsoft Excel atau Google Sheets (Jangan ubah nama Sheet 'Template').</li>
                            <li>Ganti huruf <strong class="text-rose-600">N</strong> menjadi <strong class="text-emerald-600">Y</strong> pada kolom paling kanan untuk siswa yang ingin dimasukkan ke kelas ini.</li>
                            <li>Simpan kembali dalam format Excel (.xlsx), lalu upload filenya ke mari.</li>
                        </ol>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openImport = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-black text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Mulai Unggah & Proses</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openMutasiMassal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openMutasiMassal = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase">Mutasi / Proses Kenaikan Kelas Massal</h3>
                        <p class="text-[10px] text-amber-600 font-medium mt-0.5">Memproses data siswa yang dicentang di halaman utama.</p>
                    </div>
                    <button type="button" @click="openMutasiMassal = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('kesiswaan.kelas.anggota.mutasi') }}" method="POST" class="space-y-4 text-left">
                    @csrf
                    <input type="hidden" name="dari_kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="dari_semester_id" value="{{ $current_semester_id }}">
                    
                    <template x-for="id in selectedSiswa" :key="id">
                        <input type="hidden" name="siswa_ids[]" :value="id">
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Aksi Kependidikan *</label>
                        <select name="status_aksi" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="Naik Kelas">Naik Kelas (Pindah tingkat & kelas baru)</option>
                            <option value="Tinggal Kelas">Tinggal Kelas (Tetap di tingkat sama pada semester baru)</option>
                            <option value="Mutasi Kelas">Mutasi Kelas (Pindah paralel ruang kelas lain berjalan)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Target Semester Baru *</label>
                            <select name="ke_semester_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                @foreach($semester_list as $sem)
                                    <option value="{{ $sem->id }}">{{ $sem->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Target Kelas Baru *</label>
                            <select name="ke_kelas_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                @foreach($all_kelas as $k)
                                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }} (Grade {{ $k->tingkat }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openMutasiMassal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-black text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Eksekusi Mutasi</button>
                    </div>
                </form>
            </div>
        </div>

        @if($kelas->tingkat == 9)
            <div x-show="openKelulusanMassal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
                <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openKelulusanMassal = false">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase">🎓 Proses Kelulusan Alumni</h3>
                            <p class="text-[10px] text-emerald-600 font-medium mt-0.5">Siswa terpilih akan dilepas status keaktifannya menjadi Alumni.</p>
                        </div>
                        <button type="button" @click="openKelulusanMassal = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                    </div>
                    
                    <form action="{{ route('kesiswaan.kelas.anggota.kelulusan') }}" method="POST" class="space-y-3 text-left">
                        @csrf
                        <input type="hidden" name="dari_kelas_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="semester_id" value="{{ $current_semester_id }}">
                        
                        <template x-for="id in selectedSiswa" :key="id">
                            <input type="hidden" name="siswa_ids[]" :value="id">
                        </template>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun Kelulusan Resmi *</label>
                            <input type="number" name="tahun_lulus" required value="2026" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan Tambahan Catatan</label>
                            <textarea name="keterangan" rows="2" placeholder="Contoh: Lulus utama reguler angkatan lama." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                            <button type="button" @click="openKelulusanMassal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Luluskan Siswa</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div x-show="openConfirmDelete" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 text-center space-y-4" @click.away="openConfirmDelete = false">
                
                <div class="w-16 h-16 bg-rose-50 border border-rose-100 text-rose-600 rounded-full flex items-center justify-center text-2xl mx-auto shadow-inner">
                    ⚠️
                </div>

                <div>
                    <h3 class="text-base font-bold text-gray-900">Keluarkan Anggota Kelas?</h3>
                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                        Apakah Anda yakin ingin mengeluarkan <span class="font-bold text-gray-800" x-text="siswaNama"></span> dari kelas aktif ini? Tindakan ini akan mengosongkan penempatan kelas siswa pada semester terpilih.
                    </p>
                </div>

                <form :action="deleteUrl" method="POST" class="flex items-center justify-center gap-2 pt-2 border-t border-gray-100">
                    @csrf
                    @method('DELETE')
                    
                    <button type="button" @click="openConfirmDelete = false" class="w-1/2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="w-1/2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-black text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                        Ya, Keluarkan
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>