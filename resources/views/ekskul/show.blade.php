<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('ekskul.ekstrakurikuler.index') }}" class="p-2 bg-white border border-gray-300 rounded-xl text-gray-500 hover:bg-gray-50 shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Detail Ekskul') }}: {{ $ekskul->nama }}
                </h2>
            </div>
        </div>
    </x-slot>

    {{-- Bungkus Utama Menggunakan Alpine.js untuk Tab dan Modal --}}
    <div class="py-12" x-data="{ currentTab: 'anggota', modalAnggota: false, modalPrestasi: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl text-green-700 shadow-sm flex items-center">
                    <i class="fa-solid fa-circle-check mr-3 text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-700 shadow-sm flex items-center">
                    <i class="fa-solid fa-circle-xmark mr-3 text-lg"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="border-b border-gray-200 mb-6 bg-white p-2 rounded-2xl shadow-sm flex flex-wrap gap-2">
                <button @click="currentTab = 'anggota'" :class="currentTab === 'anggota' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-2.5 font-semibold text-sm rounded-xl transition duration-150">
                    <i class="fa-solid fa-users mr-2"></i> Daftar Anggota
                </button>
                <button @click="currentTab = 'prestasi'" :class="currentTab === 'prestasi' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50'" class="px-5 py-2.5 font-semibold text-sm rounded-xl transition duration-150">
                    <i class="fa-solid fa-trophy mr-2"></i> Rekam Prestasi
                </button>
            </div>

            <div x-show="currentTab === 'anggota'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">Siswa Terdaftar</h3>
                    <button @click="modalAnggota = true" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white font-semibold text-xs rounded-xl uppercase tracking-wider hover:bg-indigo-700 shadow transition">
                        <i class="fa-solid fa-user-plus mr-1.5"></i> Daftarkan Anggota
                    </button>
                </div>
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b border-gray-100">
                                <th class="p-4">Nama Siswa</th>
                                <th class="p-4">Kelas</th>
                                <th class="p-4">No. HP</th>
                                <th class="p-4">Tgl Bergabung</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($ekskul->anggota as $agt)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-4 font-medium text-gray-900">{{ $agt->siswa->nama ?? 'N/A' }}</td>
                                    <td class="p-4">{{ $agt->kelas->nama ?? 'N/A' }}</td>
                                    <td class="p-4 text-gray-500">{{ $agt->nomor_hp ?? '-' }}</td>
                                    <td class="p-4">{{ $agt->tanggal_bergabung->format('d M Y') }}</td>
                                    <td class="p-4 flex justify-center">
                                        <form action="{{ route('ekskul.ekstrakurikuler.anggota.destroy', [$ekskul->id, $agt->id]) }}" method="POST" onsubmit="return confirm('Keluarkan siswa ini dari ekskul?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition" title="Keluarkan Anggota">
                                                <i class="fa-solid fa-user-minus"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400">Belum ada anggota yang terdaftar di ekskul ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="currentTab === 'prestasi'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" style="display: none;">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">Daftar Penghargaan & Juara</h3>
                    <button @click="modalPrestasi = true" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white font-semibold text-xs rounded-xl uppercase tracking-wider hover:bg-indigo-700 shadow transition">
                        <i class="fa-solid fa-medal mr-1.5"></i> Catat Prestasi
                    </button>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase border-b border-gray-100">
                                <th class="p-4">Nama Prestasi</th>
                                <th class="p-4">Tingkat / Juara</th>
                                <th class="p-4">Penyelenggara</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">Arsip</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($ekskul->prestasi as $pres)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-4 font-medium text-gray-900">{{ $pres->nama_prestasi }}</td>
                                    <td class="p-4">
                                        <span class="inline-block px-2 py-0.5 bg-amber-100 text-amber-800 text-xs font-bold rounded-md">Juara {{ $pres->juara }}</span>
                                        <span class="text-xs text-gray-500 block mt-0.5">Tingkat {{ $pres->tingkat }}</span>
                                    </td>
                                    <td class="p-4 text-gray-500">{{ $pres->penyelenggara }}</td>
                                    <td class="p-4">{{ $pres->tanggal_prestasi->format('d M Y') }}</td>
                                    <td class="p-4 space-x-1">
                                        @if($pres->file_sertifikat)
                                            <a href="{{ asset('storage/' . $pres->file_sertifikat) }}" target="_blank" class="inline-flex text-blue-500 hover:underline text-xs"><i class="fa-solid fa-file-pdf mr-1"></i> Sertifikat</a>
                                        @endif
                                        @if($pres->file_dokumentasi)
                                            <a href="{{ asset('storage/' . $pres->file_dokumentasi) }}" target="_blank" class="inline-flex text-green-500 hover:underline text-xs"><i class="fa-solid fa-image mr-1"></i> Foto</a>
                                        @endif
                                    </td>
                                    <td class="p-4 flex justify-center">
                                        <form action="{{ route('ekskul.ekstrakurikuler.prestasi.destroy', [$ekskul->id, $pres->id]) }}" method="POST" onsubmit="return confirm('Hapus data prestasi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-400">Belum ada catatan prestasi untuk ekskul ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="modalAnggota" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm" style="display: none;">
                <div @click.away="modalAnggota = false" class="bg-white rounded-2xl max-w-md w-full shadow-2xl p-6 border-t-4 border-indigo-600 animate__animated animate__fadeInUp">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-bold text-gray-900">Form Anggota Baru</h4>
                        <button @click="modalAnggota = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                    </div>
                    <form action="{{ route('ekskul.ekstrakurikuler.anggota.store', $ekskul->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Pilih Siswa</label>
                            <select name="siswa_id" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                                <option value="">-- Cari Nama Siswa --</option>
                                @foreach($siswaBelumMendaftar as $sw)
                                    <option value="{{ $sw->id }}">{{ $sw->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Kelas Saat Ini</label>
                            <select name="kelas_id" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $kls)
                                    <option value="{{ $kls->id }}">{{ $kls->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">No. HP Wali/Siswa</label>
                            <input type="text" name="nomor_hp" placeholder="Contoh: 081234xxx" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Tanggal Bergabung</label>
                            <input type="date" name="tanggal_bergabung" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Motivasi Bergabung</label>
                            <textarea name="motivasi" rows="2" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white font-bold text-xs rounded-xl uppercase tracking-wider hover:bg-indigo-700 shadow-md transition">Simpan Anggota</button>
                    </form>
                </div>
            </div>

            <div x-show="modalPrestasi" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm" style="display: none;">
                <div @click.away="modalPrestasi = false" class="bg-white rounded-2xl max-w-lg w-full shadow-2xl p-6 border-t-4 border-indigo-600">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-bold text-gray-900">Form Rekam Prestasi Juara</h4>
                        <button @click="modalPrestasi = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                    </div>
                    <form action="{{ route('ekskul.ekstrakurikuler.prestasi.store', $ekskul->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Prestasi / Nama Lomba</label>
                            <input type="text" name="nama_prestasi" placeholder="Misal: Turnamen Futsal Bupati Cup III" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Tingkat Wilayah</label>
                                <select name="tingkat" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                                    <option value="Kabupaten">Kabupaten/Kota</option>
                                    <option value="Provinsi">Provinsi</option>
                                    <option value="Nasional">Nasional</option>
                                    <option value="Sekolah">Internal Sekolah</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Peringkat / Juara</label>
                                <input type="text" name="juara" placeholder="Misal: 1, 2, 3, atau Harapan I" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Instansi Penyelenggara</label>
                                <input type="text" name="penyelenggara" placeholder="Misal: Dispora Jabar" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Tanggal Perolehan</label>
                                <input type="date" name="tanggal_prestasi" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 mb-1 text-blue-600">File Piagam/Sertifikat (PDF/JPG)</label>
                                <input type="file" name="file_sertifikat" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-gray-700 mb-1 text-green-600">Foto Dokumentasi (Gambar)</label>
                                <input type="file" name="file_dokumentasi" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            </div>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white font-bold text-xs rounded-xl uppercase tracking-wider hover:bg-indigo-700 shadow-md transition">Simpan Prestasi</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>