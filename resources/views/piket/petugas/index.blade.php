<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    ⚙️ Pengaturan Jadwal Petugas Piket
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Tahun Ajaran: <span class="font-semibold text-indigo-600">{{ $tahunAktif?->nama_tahun_ajaran ?? 'Belum Diatur' }}</span> 
                    | Semester: <span class="font-semibold text-indigo-600">{{ $semesterAktif?->nama ?? 'Belum Diatur' }}</span>
                </p>
            </div>
            <a href="{{ route('piket.dashboard') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                📓 Buka Dashboard Operasional Harian
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100/50 min-h-screen" x-data="{ 
        openCreate: false,
        openEdit: false,
        
        // State Form Edit Dinamis
        editAction: '',
        editHari: '',
        editPjId: '',
        editAnggotaIds: []
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="space-y-1">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Plotting Jadwal Piket Mingguan</h3>
                    <p class="text-xs text-gray-500">Konfigurasi ini bertindak sebagai master jadwal statis mingguan. Sistem operasional harian akan otomatis merujuk pada nama yang Anda tentukan di bawah ini sesuai dengan harinya.</p>
                </div>
                <button @click="openCreate = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer flex items-center gap-1">
                    ➕ Buat Jadwal Hari Baru
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-xs text-gray-700 uppercase">
                    📋 Matriks Distribusi Guru Piket (Senin - Sabtu)
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-100/50 text-gray-600 font-semibold border-b border-gray-100 uppercase tracking-wider">
                                <th class="p-4 pl-6 w-32">Hari Kerja</th>
                                <th class="p-4 w-64">Penanggung Jawab (Pj)</th>
                                <th class="p-4">Anggota Tim Piket Bertugas</th>
                                <th class="p-4 pr-6 text-center w-40">Aksi Manajemen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($jadwalPiket as $jadwal)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 pl-6">
                                        <span class="px-3 py-1 font-bold rounded-lg text-xs bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $jadwal->hari }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $jadwal->penanggungJawab->nama_lengkap ?? '⚠️ Belum ditentukan' }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">
                                            NIP: {{ $jadwal->penanggungJawab->nip ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            @if(!empty($jadwal->anggota_piket) && count($jadwal->objek_anggota_piket) > 0)
                                                @foreach($jadwal->objek_anggota_piket as $anggota)
                                                    <span class="bg-slate-100 text-slate-800 border border-slate-200 font-medium px-2 py-0.5 rounded-md">
                                                        👤 {{ $anggota->nama_lengkap }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-rose-500 italic font-medium">Belum ada anggota tim terdaftar.</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" 
                                                @click="
                                                    editAction = '{{ route('piket.petugas.update', $jadwal->id) }}';
                                                    editHari = '{{ $jadwal->hari }}';
                                                    editPjId = '{{ $jadwal->penanggung_jawab_id }}';
                                                    editAnggotaIds = {{ json_encode($jadwal->anggota_piket ?? []) }};
                                                    openEdit = true;
                                                "
                                                class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-md font-medium transition-colors cursor-pointer text-[11px]">
                                                📝 Edit Regu
                                            </button>

                                            <form action="{{ route('piket.petugas.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data jadwal piket hari ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-md font-medium transition-colors cursor-pointer text-[11px]">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        📭 Matriks jadwal masih kosong. Silakan klik tombol <span class="font-bold text-emerald-600">Buat Jadwal Hari Baru</span> untuk mengisi plotting piket sekolah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="font-bold text-xs uppercase text-gray-900">Tambah Jadwal Piket Mingguan</h3>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('piket.petugas.store') }}" method="POST" class="space-y-3 text-left text-xs">
                    @csrf
                    
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Hari Kerja *</label>
                        <select name="hari" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Hari Kerja --</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Guru Penanggung Jawab (Ketua Piket)</label>
                        <select name="penanggung_jawab_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Guru Penanggung Jawab --</option>
                            @foreach($daftarPegawai as $pegawai)
                                <option value="{{ $pegawai->id }}">{{ $pegawai->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Anggota Tim Piket * <span class="text-[10px] text-gray-400 font-normal">(Bisa pilih lebih dari satu)</span></label>
                        <select name="anggota_piket[]" required multiple size="6" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm p-2 bg-white">
                            @foreach($daftarPegawai as $pegawai)
                                <option value="{{ $pegawai->id }}">👥 {{ $pegawai->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">💡 Tahan tombol <kbd class="bg-gray-100 border px-1 rounded">Ctrl</kbd> (Windows) atau <kbd class="bg-gray-100 border px-1 rounded">Cmd</kbd> (Mac) untuk memilih multi-pegawai sekaligus.</p>
                    </div>

                    <div class="pt-3 border-t flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg cursor-pointer">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <h3 class="font-bold text-xs uppercase text-amber-700">Edit Susunan Regu Piket</h3>
                        <p class="text-[10px] text-gray-400 font-medium mt-0.5">Hari: <span x-text="editHari" class="font-bold text-gray-700"></span></p>
                    </div>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer">&times;</button>
                </div>
                
                <form method="POST" x-bind:action="editAction" class="space-y-3 text-left text-xs">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Guru Penanggung Jawab (Ketua Piket)</label>
                        <select name="penanggung_jawab_id" x-model="editPjId" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Guru Penanggung Jawab --</option>
                            @foreach($daftarPegawai as $pegawai)
                                <option value="{{ $pegawai->id }}">{{ $pegawai->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Anggota Tim Piket * <span class="text-[10px] text-gray-400 font-normal">(Bisa pilih lebih dari satu)</span></label>
                        <select name="anggota_piket[]" required multiple size="6" x-model="editAnggotaIds" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm p-2 bg-white">
                            @foreach($daftarPegawai as $pegawai)
                                <option value="{{ $pegawai->id }}">👥 {{ $pegawai->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">💡 Tahan tombol <kbd class="bg-gray-100 border px-1 rounded">Ctrl</kbd> atau <kbd class="bg-gray-100 border px-1 rounded">Cmd</kbd> untuk mengubah opsi terpilih.</p>
                    </div>

                    <div class="pt-3 border-t flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg cursor-pointer">Perbarui Regu</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>