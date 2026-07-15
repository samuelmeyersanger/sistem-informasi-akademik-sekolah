<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">⚙️</span> {{ __('Konfigurasi Matriks Piket') }}
                </h2>
                <div class="flex flex-wrap items-center gap-3 mt-2 text-xs font-bold">
                    <span class="bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg border border-indigo-100 flex items-center gap-1.5 shadow-sm">
                        <span>🗓️</span> TA: {{ $tahunAktif?->nama_tahun_ajaran ?? 'Belum Diatur' }}
                    </span>
                    <span class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg border border-emerald-100 flex items-center gap-1.5 shadow-sm">
                        <span>🏷️</span> Sem: {{ $semesterAktif?->nama ?? 'Belum Diatur' }}
                    </span>
                </div>
            </div>
            
            <a href="{{ route('piket.dashboard') }}" class="px-5 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-black rounded-xl shadow-lg shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2 text-center w-full md:w-auto justify-center">
                <span>📓</span> Buka Dashboard Harian
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen relative font-sans" x-data="{ 
        openCreate: false,
        openEdit: false,
        
        // State Form Edit Dinamis
        editAction: '',
        editHari: '',
        editPjId: '',
        editAnggotaIds: []
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 relative z-10">

            {{-- PANEL INFORMASI & TOMBOL TAMBAH --}}
            <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 relative overflow-hidden group">
                <div class="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-indigo-500 to-indigo-600"></div>
                <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>
                
                <div class="space-y-2 relative z-10">
                    <h3 class="text-base font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                        <span>📅</span> Plotting Jadwal Rutin Mingguan
                    </h3>
                    <p class="text-xs font-medium text-slate-500 leading-relaxed max-w-3xl">
                        Susunan personel di bawah ini adalah data acuan dasar (Master Data). Sistem Jurnal Piket Harian akan membaca nama-nama koordinator dan regu secara otomatis dengan menyesuaikan hari yang sedang berjalan secara *real-time*.
                    </p>
                </div>
                
                <button @click="openCreate = true" class="relative z-10 w-full lg:w-auto px-6 py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white text-sm font-black rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 shrink-0 border border-emerald-400">
                    <span>➕</span> Distribusi Hari Baru
                </button>
            </div>

            {{-- TABEL MATRIKS JADWAL --}}
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                <div class="p-6 border-b border-slate-100 bg-white/50 backdrop-blur-sm relative z-10">
                    <h3 class="text-sm font-black text-slate-800 tracking-tight">Katalog Siklus Piket Pegawai</h3>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                <th class="p-5 pl-8 w-32">Hari Kerja</th>
                                <th class="p-5 w-64">Ketua (Penanggung Jawab)</th>
                                <th class="p-5">Daftar Anggota Regu Piket</th>
                                <th class="p-5 pr-8 text-center w-36">Manajemen Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($jadwalPiket as $jadwal)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8">
                                        <div class="inline-flex items-center px-4 py-2 font-black rounded-xl text-xs bg-white text-indigo-700 border border-indigo-100 shadow-sm group-hover:bg-indigo-50 transition-colors">
                                            {{ $jadwal->hari }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-black text-slate-900 text-sm">
                                            {{ $jadwal->penanggungJawab->nama_lengkap ?? '⚠️ Belum ditentukan' }}
                                        </div>
                                        <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-wider">
                                            NIP: {{ $jadwal->penanggungJawab->nip ?? 'Tidak ada' }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex flex-wrap gap-2">
                                            @if(!empty($jadwal->anggota_piket) && count($jadwal->objek_anggota_piket) > 0)
                                                @foreach($jadwal->objek_anggota_piket as $anggota)
                                                    <span class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-700 border border-slate-200 font-bold text-[11px] px-3 py-1.5 rounded-lg shadow-sm">
                                                        <span class="text-slate-400">👤</span> {{ $anggota->nama_lengkap }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 border border-rose-200 text-rose-700 font-bold text-[10px] uppercase tracking-wider rounded-lg shadow-sm">
                                                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span> Kosong
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" 
                                                @click="
                                                    editAction = '{{ route('piket.petugas.update', $jadwal->id) }}';
                                                    editHari = '{{ $jadwal->hari }}';
                                                    editPjId = '{{ $jadwal->penanggung_jawab_id }}';
                                                    editAnggotaIds = {{ json_encode($jadwal->anggota_piket ?? []) }};
                                                    openEdit = true;
                                                "
                                                class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Ubah Plotting">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <form action="{{ route('piket.petugas.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Peringatan: Plotting hari ini akan hilang permanen. Yakin?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Jadwal Hari Ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="text-5xl mb-4 opacity-50">🗓️</div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Matriks Kosong</h4>
                                            <span class="text-sm font-medium">Klik tombol "Distribusi Hari Baru" di atas untuk menyusun regu piket sekolah.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL CREATE JADWAL --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-emerald-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-emerald-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        ✨ Tambah Hari Operasional
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('piket.petugas.store') }}" method="POST" class="p-6 md:p-8 space-y-5 bg-white relative z-10">
                    @csrf
                    
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Pilih Hari Kerja <span class="text-rose-500">*</span></label>
                        <select name="hari" required class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 shadow-sm py-3 px-4">
                            <option value="">-- Sentuh untuk Memilih --</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>

                    <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                        <label class="block font-black text-indigo-900 text-[10px] uppercase tracking-widest mb-1.5">👑 Koordinator (Ketua)</label>
                        <select name="penanggung_jawab_id" class="w-full text-sm font-bold rounded-xl border-indigo-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm py-2.5">
                            <option value="">-- Biarkan kosong bila tidak ada --</option>
                            @foreach($daftarPegawai as $pegawai)
                                <option value="{{ $pegawai->id }}">{{ $pegawai->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5 flex items-center justify-between">
                            <span>👥 Plotting Anggota Regu <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="relative bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-inner">
                            <select name="anggota_piket[]" required multiple size="6" class="w-full text-xs font-bold bg-transparent focus:ring-0 border-none p-3 space-y-1">
                                @foreach($daftarPegawai as $pegawai)
                                    <option value="{{ $pegawai->id }}" class="p-2 rounded-lg hover:bg-slate-200 transition-colors">
                                        {{ $pegawai->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mt-2 p-2.5 bg-amber-50 border border-amber-200 rounded-lg flex gap-2">
                            <span class="text-base mt-0.5">💡</span>
                            <p class="text-[10px] text-amber-800 font-bold leading-relaxed">
                                Tahan tombol <kbd class="bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded shadow-sm mx-0.5">Ctrl</kbd> (Windows) atau <kbd class="bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded shadow-sm mx-0.5">Cmd</kbd> (Mac) saat meng-klik nama untuk memblok lebih dari satu pegawai sekaligus.
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row justify-end gap-3 border-t border-slate-100 mt-6">
                        <button type="button" @click="openCreate = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-black rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>💾</span> Validasi Matriks
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT JADWAL --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/50 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <div>
                        <h3 class="text-base font-black text-amber-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                            📝 Revisi Personel Piket
                        </h3>
                        <p class="text-[10px] font-bold text-amber-700 mt-1 uppercase tracking-widest relative z-10">Hari Operasional: <span x-text="editHari" class="font-black px-2 py-0.5 bg-amber-200/50 rounded-md"></span></p>
                    </div>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form method="POST" x-bind:action="editAction" class="p-6 md:p-8 space-y-5 bg-white relative z-10">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-4 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                        <label class="block font-black text-indigo-900 text-[10px] uppercase tracking-widest mb-1.5">👑 Koordinator (Ketua)</label>
                        <select name="penanggung_jawab_id" x-model="editPjId" class="w-full text-sm font-bold rounded-xl border-indigo-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm py-2.5">
                            <option value="">-- Kosongkan (N/A) --</option>
                            @foreach($daftarPegawai as $pegawai)
                                <option value="{{ $pegawai->id }}">{{ $pegawai->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5 flex items-center justify-between">
                            <span>👥 Plotting Anggota Regu <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="relative bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-inner">
                            <select name="anggota_piket[]" required multiple size="6" x-model="editAnggotaIds" class="w-full text-xs font-bold bg-transparent focus:ring-0 border-none p-3 space-y-1">
                                @foreach($daftarPegawai as $pegawai)
                                    <option value="{{ $pegawai->id }}" class="p-2 rounded-lg hover:bg-slate-200 transition-colors">
                                        {{ $pegawai->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mt-2 p-2.5 bg-amber-50 border border-amber-200 rounded-lg flex gap-2">
                            <span class="text-base mt-0.5">💡</span>
                            <p class="text-[10px] text-amber-800 font-bold leading-relaxed">
                                Tahan tombol <kbd class="bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded shadow-sm mx-0.5">Ctrl</kbd> atau <kbd class="bg-amber-100 border border-amber-300 px-1.5 py-0.5 rounded shadow-sm mx-0.5">Cmd</kbd> untuk mencopot (un-select) atau menambah nama pegawai yang sedang di-highlight.
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row justify-end gap-3 border-t border-slate-100 mt-6">
                        <button type="button" @click="openEdit = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Tutup Form</button>
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>🔄</span> Terapkan Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>