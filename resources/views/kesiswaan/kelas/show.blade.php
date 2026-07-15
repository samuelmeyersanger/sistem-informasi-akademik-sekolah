<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('kesiswaan.kelas.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 shadow-sm transition-colors" title="Kembali ke Daftar Ruang Kelas">
                    <span class="text-xl font-bold">←</span>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                        <span class="text-3xl">👥</span> {{ __('Anggota Kelas') }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500 mt-1">Pemetaan siswa, mutasi kelas, dan status kelulusan anggota.</p>
                </div>
            </div>
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
    }" class="py-10 bg-slate-50 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Alerts / Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-2xl shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-lg mb-2">
                        <span>⚠️</span> Gagal mengeksekusi tindakan:
                    </div>
                    <ul class="list-disc pl-6 space-y-1 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Kartu Info Kelas & Filter Semester --}}
            <div class="bg-white p-8 rounded-[2rem] shadow-xl shadow-indigo-900/5 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                
                {{-- Aksen Latar --}}
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

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
                            <span>🏫</span> Kelas {{ $kelas->nama_kelas }}
                        </h3>
                        <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg shadow-sm">
                            <span class="text-lg">👤</span>
                            <span class="text-xs text-gray-500 font-medium">Wali Kelas:</span>
                            <span class="text-xs font-bold text-gray-800">{{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : 'Belum Ditugaskan' }}</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('kesiswaan.kelas.show', $kelas->id) }}" method="GET" class="relative z-10 w-full md:w-auto p-4 bg-gray-50/80 border border-gray-200 rounded-2xl shadow-inner">
                    <label class="block text-xs font-black uppercase tracking-wider text-gray-500 mb-1.5">Tampilkan Berdasarkan Semester:</label>
                    <select name="semester_id" onchange="this.form.submit()" class="w-full md:w-80 text-sm font-semibold rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white cursor-pointer py-2.5">
                        @foreach($semester_list as $sem)
                            <option value="{{ $sem->id }}" {{ $current_semester_id == $sem->id ? 'selected' : '' }}>
                                Semester {{ $sem->nama }} (Ke-{{ $sem->semester_ke }}) - {{ $sem->tahunAjaran ? $sem->tahunAjaran->tahun_ajaran : 'Tanpa Tahun Ajaran' }} {{ $sem->is_aktif ? ' 🟢 [AKTIF]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Tabel Data Siswa --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-[2rem] border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6 bg-gray-50/50">
                    <div>
                        <h4 class="text-lg font-black text-gray-900 mb-1">Daftar Anggota Terdaftar</h4>
                        <p class="text-sm text-gray-500">Jumlah total peserta didik aktif: <span class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">{{ count($anggota) }} Siswa</span></p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        
                        {{-- Panel Aksi Massal (Muncul jika ada checkbox yg dicentang) --}}
                        <div x-show="selectedSiswa.length > 0" style="display: none;" class="flex items-center gap-3 bg-amber-50 pl-4 pr-1 py-1 rounded-xl border border-amber-200 shadow-sm mr-2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                            <span class="text-xs font-black text-amber-800" x-text="selectedSiswa.length + ' Baris Terpilih'"></span>
                            
                            <div class="flex items-center gap-1">
                                <button @click="openMutasiMassal = true" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer flex items-center gap-1.5">
                                    <span>🔄</span> Proses Mutasi
                                </button>
                                @if($kelas->tingkat == 9)
                                    <button @click="openKelulusanMassal = true" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer flex items-center gap-1.5">
                                        <span>🎓</span> Luluskan Massal
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="h-8 w-px bg-gray-200 mx-1 hidden xl:block"></div>

                        <a href="{{ route('kesiswaan.kelas.anggota.downloadTemplate', ['id' => $kelas->id, 'semester_id' => $current_semester_id]) }}" class="px-4 py-2.5 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-200 text-emerald-700 text-sm font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                            <span>📥</span> Template Excel
                        </a>

                        <button @click="openImport = true" class="px-4 py-2.5 bg-white hover:bg-teal-50 border border-gray-200 hover:border-teal-200 text-teal-700 text-sm font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                            <span>📤</span> Import Excel
                        </button>
                        
                        <button @click="openPlotting = true" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-md transition-transform transform hover:-translate-y-0.5 flex items-center gap-2 cursor-pointer">
                            <span>➕</span> Masukkan Siswa
                        </button>
                    </div>
                </div>

                <form id="massForm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                    <th class="p-5 pl-8 w-14 text-center">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" @change="toggleAll($event.target)" :checked="selectedSiswa.length === {{ count($anggota) }} && {{ count($anggota) }} > 0" class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm cursor-pointer transition-all hover:scale-110">
                                        </div>
                                    </th>
                                    <th class="p-5 w-80">Identitas Peserta Didik</th>
                                    <th class="p-5 text-center w-36">Jenis Kelamin</th>
                                    <th class="p-5 text-center w-48">Status Keanggotaan</th>
                                    <th class="p-5 pr-8 text-center w-36">Aksi Individu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-gray-700">
                                @forelse($anggota as $item)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group" :class="selectedSiswa.includes('{{ $item->siswa_id }}') ? 'bg-indigo-50/50' : ''">
                                        <td class="p-5 pl-8 text-center align-middle">
                                            <div class="flex items-center justify-center">
                                                <input type="checkbox" value="{{ $item->siswa_id }}" x-model="selectedSiswa" class="siswa-checkbox w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm cursor-pointer transition-all hover:scale-110">
                                            </div>
                                        </td>
                                        <td class="p-5 align-middle">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-lg shadow-sm shrink-0">
                                                    {{ $item->siswa->jenis_kelamin == 'Laki-Laki' || $item->siswa->jenis_kelamin == 'Laki-laki' ? '👦' : '👧' }}
                                                </div>
                                                <div>
                                                    <div class="font-black text-gray-900 text-sm mb-0.5">{{ $item->siswa->nama_lengkap }}</div>
                                                    <div class="text-gray-500 text-[11px] font-semibold flex items-center gap-1.5">
                                                        <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600 border border-gray-200 shadow-sm">NISN: {{ $item->siswa->nisn ?? '-' }}</span>
                                                        <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-600 border border-gray-200 shadow-sm">NIK: {{ $item->siswa->nik ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-5 text-center font-bold text-gray-600 align-middle">
                                            {{ $item->siswa->jenis_kelamin == 'Laki-Laki' || $item->siswa->jenis_kelamin == 'Laki-laki' ? 'Laki-Laki' : 'Perempuan' }}
                                        </td>
                                        <td class="p-5 text-center align-middle">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Aktif Belajar
                                            </span>
                                        </td>
                                        <td class="p-5 pr-8 text-center align-middle">
                                            <button type="button" @click="triggerConfirm('{{ route('kesiswaan.kelas.anggota.remove', $item->id) }}', '{{ addslashes($item->siswa->nama_lengkap) }}')" class="px-3 py-1.5 bg-white hover:bg-rose-50 border border-gray-200 hover:border-rose-200 text-rose-600 font-bold rounded-lg text-xs transition-colors shadow-sm cursor-pointer flex items-center justify-center gap-1.5 mx-auto opacity-70 group-hover:opacity-100">
                                                <span>❌</span> Keluarkan
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                            <span class="text-5xl block mb-4">📭</span>
                                            <p class="text-lg font-bold text-gray-500">Belum ada siswa yang di-plotting pada kelas ini.</p>
                                            <p class="text-sm font-medium text-gray-400 mt-1">Gunakan tombol 'Masukkan Siswa' atau 'Import Excel' untuk memulai.</p>
                                        </td>
                                    </tr>
                                @endforelse 
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: MASUKKAN SISWA MANUAL ================= --}}
        <div x-show="openPlotting" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden" @click.away="openPlotting = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">➕ Pilih Siswa Masuk</h3>
                        <p class="text-xs text-indigo-600 font-bold mt-1 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span> Hanya Siswa Tanpa Kelas</p>
                    </div>
                    <button type="button" @click="openPlotting = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('kesiswaan.kelas.anggota.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="semester_id" value="{{ $current_semester_id }}">
                    
                    <div class="p-8 space-y-5">
                        <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                            <label class="block text-sm font-bold text-indigo-900 mb-2">Pilih Satu/Banyak Siswa <span class="text-rose-500">*</span></label>
                            
                            {{-- Hint Multiple Select --}}
                            <p class="text-[10.5px] text-indigo-700 font-medium mb-3 leading-tight bg-white p-2 rounded-lg border border-indigo-50 shadow-sm">
                                💡 Tahan tombol <strong class="bg-indigo-100 px-1 rounded">Ctrl</strong> (Windows) atau <strong class="bg-indigo-100 px-1 rounded">Command ⌘</strong> (Mac) saat mengklik nama siswa untuk memilih lebih dari satu secara bersamaan.
                            </p>

                            <select name="siswa_ids[]" multiple required class="w-full text-sm font-semibold rounded-xl border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-inner bg-white p-3 h-56 custom-scrollbar-select">
                                @forelse($siswa_tanpa_kelas as $s)
                                    <option value="{{ $s->id }}" class="py-2 px-3 hover:bg-indigo-50 rounded-lg cursor-pointer mb-1 border-b border-gray-50 last:border-0 transition-colors">
                                        {{ $s->jenis_kelamin == 'Laki-Laki' || $s->jenis_kelamin == 'Laki-laki' ? '👦' : '👧' }} {{ $s->nama_lengkap }}
                                    </option>
                                @empty
                                    <option disabled class="py-4 text-center text-gray-400 font-bold italic bg-gray-50 rounded-lg">-- Semua siswa aktif sudah mendapatkan kelas --</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openPlotting = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" {{ count($siswa_tanpa_kelas) == 0 ? 'disabled' : '' }} class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <span>📥</span> Masukkan ke Kelas
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: IMPORT EXCEL ================= --}}
        <div x-show="openImport" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden" @click.away="openImport = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">📤 Import Anggota Massal</h3>
                        <p class="text-xs text-teal-600 font-bold mt-1 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 bg-teal-500 rounded-full"></span> Via File Spreadsheet</p>
                    </div>
                    <button type="button" @click="openImport = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('kesiswaan.kelas.anggota.import', $kelas->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="semester_id" value="{{ $current_semester_id }}">
                    
                    <div class="p-8 space-y-6">
                        
                        {{-- Petunjuk Penggunaan --}}
                        <div class="p-4 bg-teal-50/50 border border-teal-100 rounded-2xl relative overflow-hidden">
                            <div class="absolute -right-4 -bottom-4 text-6xl opacity-10">💡</div>
                            <h5 class="text-sm font-black text-teal-900 mb-3 relative z-10">Panduan Import Data:</h5>
                            <ol class="list-decimal list-inside space-y-2 text-xs font-medium text-teal-800 relative z-10">
                                <li>Pastikan sudah <strong>Download Template</strong>.</li>
                                <li>Buka file Excel tanpa merubah nama Sheet 'Template'.</li>
                                <li>Ganti kolom status yang bernilai <strong class="text-rose-600 bg-rose-100 px-1 rounded">N</strong> menjadi <strong class="text-emerald-700 bg-emerald-100 px-1 rounded">Y</strong> pada baris nama siswa tujuan.</li>
                                <li>Simpan <em>(Save)</em> & unggah kembali filenya ke mari.</li>
                            </ol>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Upload Berkas Excel (.xlsx, .xls) <span class="text-rose-500">*</span></label>
                            <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full text-sm text-gray-500 bg-gray-50 border border-gray-300 rounded-xl cursor-pointer p-2 file:mr-4 file:py-2.5 file:px-5 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-teal-100 file:text-teal-800 hover:file:bg-teal-200 shadow-inner focus:outline-none transition-colors">
                        </div>

                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openImport = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer flex items-center gap-2">
                            <span>🚀</span> Mulai Sinkronisasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: MUTASI / NAIK KELAS MASSAL ================= --}}
        <div x-show="openMutasiMassal" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden" @click.away="openMutasiMassal = false">
                
                <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 flex items-center gap-2"><span>🔄</span> Operasi Mutasi Massal</h3>
                        <p class="text-xs text-amber-600 font-bold mt-1 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> Naik Kelas / Tinggal Kelas / Pindah Paralel</p>
                    </div>
                    <button type="button" @click="openMutasiMassal = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>
                
                <form action="{{ route('kesiswaan.kelas.anggota.mutasi') }}" method="POST">
                    @csrf
                    <input type="hidden" name="dari_kelas_id" value="{{ $kelas->id }}">
                    <input type="hidden" name="dari_semester_id" value="{{ $current_semester_id }}">
                    
                    {{-- Loop data terpilih via Alpine Template --}}
                    <template x-for="id in selectedSiswa" :key="id">
                        <input type="hidden" name="siswa_ids[]" :value="id">
                    </template>
                    
                    <div class="p-8 space-y-6">
                        
                        {{-- Ringkasan Pilihan --}}
                        <div class="p-4 bg-gray-900 rounded-2xl flex items-center justify-between text-white shadow-inner">
                            <div>
                                <h5 class="text-xs font-medium text-gray-400 mb-1">Total Siswa Terpilih:</h5>
                                <div class="text-2xl font-black text-amber-400" x-text="selectedSiswa.length + ' Orang'"></div>
                            </div>
                            <span class="text-4xl opacity-50">👥</span>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Keputusan Status <span class="text-rose-500">*</span></label>
                            <select name="status_aksi" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white px-4 py-3">
                                <option value="Naik Kelas">🔼 Naik Kelas (Pindah ke tingkat belajar di atasnya)</option>
                                <option value="Tinggal Kelas">🔽 Tinggal Kelas (Mengulang di tingkat yang sama)</option>
                                <option value="Mutasi Kelas">↔️ Mutasi Kelas (Pindah antar kelas paralel saat ini)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-5 bg-amber-50/50 border border-amber-100 rounded-2xl">
                            <div>
                                <label class="block text-sm font-bold text-amber-900 mb-2">Tujuan Semester Baru <span class="text-rose-500">*</span></label>
                                <select name="ke_semester_id" required class="w-full text-sm font-semibold rounded-xl border-amber-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white px-4 py-3">
                                    <option value="">-- Pilih Periode --</option>
                                    @foreach($semester_list as $sem)
                                        <option value="{{ $sem->id }}">{{ $sem->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-amber-900 mb-2">Tujuan Kelas Baru <span class="text-rose-500">*</span></label>
                                <select name="ke_kelas_id" required class="w-full text-sm font-semibold rounded-xl border-amber-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white px-4 py-3">
                                    <option value="">-- Pilih Ruang --</option>
                                    @foreach($all_kelas as $k)
                                        <option value="{{ $k->id }}">Grade {{ $k->tingkat }} - {{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openMutasiMassal = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer flex items-center gap-2">
                            <span>⚡</span> Terapkan Mutasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL: KELULUSAN MASSAL (GRADE 9 SAJA) ================= --}}
        @if($kelas->tingkat == 9)
            <div x-show="openKelulusanMassal" class="fixed inset-0 z-[100] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
                <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden" @click.away="openKelulusanMassal = false">
                    
                    <div class="flex justify-between items-center border-b border-gray-100 p-6 bg-gray-50">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 flex items-center gap-2"><span>🎓</span> Penetapan Kelulusan</h3>
                            <p class="text-xs text-emerald-600 font-bold mt-1 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Perubahan Status Menjadi Alumni</p>
                        </div>
                        <button type="button" @click="openKelulusanMassal = false" class="text-gray-400 hover:text-rose-500 text-2xl font-bold cursor-pointer transition-colors">&times;</button>
                    </div>
                    
                    <form action="{{ route('kesiswaan.kelas.anggota.kelulusan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="dari_kelas_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="semester_id" value="{{ $current_semester_id }}">
                        
                        <template x-for="id in selectedSiswa" :key="id">
                            <input type="hidden" name="siswa_ids[]" :value="id">
                        </template>
                        
                        <div class="p-8 space-y-6">
                            
                            {{-- Ringkasan Kelulusan --}}
                            <div class="p-5 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl text-white shadow-inner flex items-center gap-4 relative overflow-hidden">
                                <div class="absolute -right-4 -bottom-4 text-6xl opacity-20">🎓</div>
                                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-2xl font-black shrink-0 relative z-10" x-text="selectedSiswa.length"></div>
                                <div class="relative z-10">
                                    <h5 class="text-lg font-black leading-tight">Siswa Siap Lulus</h5>
                                    <p class="text-xs font-medium text-emerald-100 mt-1">Data akan dibekukan (Archive).</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tahun Pelepasan Kelulusan <span class="text-rose-500">*</span></label>
                                <input type="number" name="tahun_lulus" required value="{{ date('Y') }}" class="w-full text-lg font-black text-center rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-gray-50 py-3">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Kelulusan Bersama (Opsional)</label>
                                <textarea name="keterangan" rows="3" placeholder="Misal: Lulus jalur reguler angkatan ke-..." class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-gray-50 px-4 py-3"></textarea>
                            </div>

                        </div>
                        
                        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="openKelulusanMassal = false" class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer">Batal</button>
                            <button type="submit" class="px-6 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-md transition-colors cursor-pointer flex items-center gap-2">
                                <span>🎉</span> Sahkan Kelulusan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ================= MODAL: KONFIRMASI KELUARKAN SISWA (SATUAN) ================= --}}
        <div x-show="openConfirmDelete" class="fixed inset-0 z-[200] overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl p-8 text-center relative overflow-hidden" @click.away="openConfirmDelete = false">
                
                <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-rose-50 border border-rose-100 mb-6">
                    <span class="text-5xl drop-shadow-md">⚠️</span>
                </div>
                
                <div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3">Cabut Keanggotaan?</h3>
                    <p class="text-sm text-gray-600 mb-6 px-2">
                        Anda akan mencabut <strong class="text-gray-900 bg-gray-100 px-2 py-0.5 rounded shadow-sm" x-text="siswaNama"></strong> dari kelas ini. Data nilai dan presensinya pada kelas & semester ini akan menjadi terputus (*Orphan*).
                    </p>
                </div>
                
                <form :action="deleteUrl" method="POST" class="flex justify-center gap-3 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openConfirmDelete = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl cursor-pointer transition-colors focus:outline-none shadow-sm">
                        Urungkan
                    </button>
                    <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md cursor-pointer transition-colors focus:outline-none">
                        Ya, Cabut Sekarang
                    </button>
                </form>
            </div>
        </div>

    </div>

    <style>
        /* CSS custom scrollbar untuk multiple select di form Plotting manual */
        .custom-scrollbar-select::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar-select::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 8px;
        }
        .custom-scrollbar-select::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 8px;
        }
        .custom-scrollbar-select::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
</x-app-layout>