<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Kehadiran Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Alert Notifikasi --}}
            @if (session('success'))
                <div class="bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            {{-- KOTAK 1: Filter Pemilihan Kelas --}}
            <div class="bg-white/80 backdrop-blur-2xl shadow-xl shadow-indigo-500/5 rounded-[2rem] border border-white/60 p-8">
                <h3 class="text-lg font-black text-slate-800 tracking-tight mb-6 uppercase border-b border-slate-100 pb-4">Input Absensi Siswa</h3>
                
                <form action="{{ route('rapor.kehadiran.index') }}" method="GET" class="flex items-center gap-6">
                    <label class="font-bold text-slate-700 text-sm whitespace-nowrap">Pilih Kelas</label>
                    {{-- onChange otomatis mensubmit form tanpa harus klik tombol cari --}}
                    <select name="kelas_id" onchange="this.form.submit()" 
                            class="w-full max-w-2xl px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-indigo-700 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-inner transition-colors">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelases as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelas_id == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- KOTAK 2: Tabel Input Massal (Muncul Jika Kelas Dipilih) --}}
            @if ($kelas_id)
                <div class="bg-white/80 backdrop-blur-2xl shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60 overflow-hidden">
                    
                    <form action="{{ route('rapor.kehadiran.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">

                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-[11px] uppercase tracking-widest font-black">
                                        <th class="px-6 py-5 text-center w-16">No</th>
                                        <th class="px-6 py-5 text-left">Nama Siswa</th>
                                        <th class="px-6 py-5 text-center">NISN</th>
                                        <th class="px-6 py-5 text-center">NIPD</th>
                                        {{-- Header Bertingkat untuk Ketidakhadiran --}}
                                        <th colspan="3" class="px-6 py-2 text-center border-l border-white/20">
                                            <div class="border-b border-white/30 pb-2 mb-2 w-full">Jml Ketidakhadiran</div>
                                            <div class="grid grid-cols-3 gap-2">
                                                <span>Sakit</span>
                                                <span>Izin</span>
                                                <span>Tanpa Keterangan</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700 bg-slate-50/50">
                                    @forelse ($siswas as $index => $siswa)
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="px-6 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-800">{{ $siswa->nama_lengkap }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center text-slate-500">{{ $siswa->nisn }}</td>
                                            <td class="px-6 py-4 text-center text-slate-500">{{ $siswa->nipd }}</td>
                                            
                                            {{-- Input Absensi (Menggunakan Array berdasarkan ID Siswa) --}}
                                            <td colspan="3" class="px-6 py-4 border-l border-slate-100">
                                                <div class="grid grid-cols-3 gap-4">
                                                    {{-- Sakit --}}
                                                    <input type="number" min="0" name="kehadiran[{{ $siswa->id }}][sakit]" 
                                                           value="{{ $siswa->kehadiran->sakit ?? '' }}" 
                                                           class="w-full px-3 py-2 text-center text-sm font-bold bg-white border-slate-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 placeholder-slate-300"
                                                           placeholder="0">
                                                    {{-- Izin --}}
                                                    <input type="number" min="0" name="kehadiran[{{ $siswa->id }}][izin]" 
                                                           value="{{ $siswa->kehadiran->izin ?? '' }}" 
                                                           class="w-full px-3 py-2 text-center text-sm font-bold bg-white border-slate-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 placeholder-slate-300"
                                                           placeholder="0">
                                                    {{-- Alpa --}}
                                                    <input type="number" min="0" name="kehadiran[{{ $siswa->id }}][tanpa_keterangan]" 
                                                           value="{{ $siswa->kehadiran->tanpa_keterangan ?? '' }}" 
                                                           class="w-full px-3 py-2 text-center text-sm font-bold bg-white border-slate-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 placeholder-slate-300"
                                                           placeholder="0">
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">
                                                Belum ada data siswa di kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Tombol Simpan Fix di Bawah --}}
                        <div class="p-6 bg-white border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-black text-[12px] uppercase tracking-widest rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Data Absensi
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="text-center py-12 bg-white/50 backdrop-blur-sm rounded-[2rem] border border-dashed border-slate-300">
                    <i class="fa-solid fa-hand-pointer text-4xl text-slate-300 mb-3 block"></i>
                    <p class="text-slate-500 font-medium">Silakan pilih Kelas pada form di atas untuk menampilkan daftar siswa.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>