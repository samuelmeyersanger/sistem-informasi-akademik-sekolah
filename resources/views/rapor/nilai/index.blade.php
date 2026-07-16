<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Nilai Sumatif & Ujian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3 w-max">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Filter Area --}}
            <div class="bg-white/80 backdrop-blur-2xl shadow-xl shadow-indigo-500/5 rounded-[2rem] border border-white/60 p-8">
                <form action="{{ route('rapor.nilai.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-slate-700 text-sm mb-2">Pilih Kelas</label>
                        <select name="kelas_id" onchange="this.form.submit()" class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-indigo-700 rounded-xl focus:border-indigo-500 shadow-inner">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelases as $kelas)
                                <option value="{{ $kelas->id }}" {{ (isset($kelas_id) && $kelas_id == $kelas->id) ? 'selected' : '' }}>
                                    {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-sm mb-2">Pilih Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" onchange="this.form.submit()" class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-indigo-700 rounded-xl focus:border-indigo-500 shadow-inner" {{ empty($kelas_id) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach ($mapels as $mapel)
                                <option value="{{ $mapel->id }}" {{ (isset($mata_pelajaran_id) && $mata_pelajaran_id == $mapel->id) ? 'selected' : '' }}>
                                    {{ $mapel->nama_mata_pelajaran }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- Grid Matriks Area --}}
            @if (!empty($kelas_id) && !empty($mata_pelajaran_id))
                <div class="bg-white/80 backdrop-blur-2xl shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60 overflow-hidden">
                    
                    <div class="bg-indigo-50 px-8 py-4 border-b border-indigo-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-calculator text-indigo-600 text-lg"></i>
                            <span class="text-xs font-bold text-indigo-800 uppercase tracking-widest">Matriks Nilai Akademik</span>
                        </div>
                        <span class="text-[10px] font-bold text-indigo-500 bg-indigo-100 px-3 py-1 rounded-full"><i class="fa-solid fa-robot mr-1"></i> Nilai Rapor dihitung Otomatis</span>
                    </div>

                    <form action="{{ route('rapor.nilai.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">
                        <input type="hidden" name="mata_pelajaran_id" value="{{ $mata_pelajaran_id }}">

                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full whitespace-nowrap table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white text-[10px] uppercase tracking-widest font-black">
                                        <th rowspan="2" class="px-4 py-3 border-r border-white/20 sticky left-0 z-20 bg-indigo-600">No</th>
                                        <th rowspan="2" class="px-6 py-3 border-r border-white/20 text-left sticky left-12 z-20 bg-indigo-600">Nama Siswa</th>
                                        
                                        @if($tujuanPembelajarans->count() > 0)
                                            <th colspan="{{ $tujuanPembelajarans->count() }}" class="px-4 py-2 text-center border-r border-white/20">
                                                Nilai Sumatif per TP
                                            </th>
                                        @else
                                            <th rowspan="2" class="px-6 py-3 text-center bg-rose-500/80">BELUM ADA DATA TP DIBUAT</th>
                                        @endif
                                        
                                        <th rowspan="2" class="px-4 py-3 text-center border-l border-white/20 bg-blue-600" title="Penilaian Sumatif Tengah Semester">PSTS</th>
                                        <th rowspan="2" class="px-4 py-3 text-center bg-blue-700" title="Penilaian Sumatif Akhir Semester">PSAS</th>
                                    </tr>
                                    @if($tujuanPembelajarans->count() > 0)
                                        <tr class="bg-indigo-700/80 text-white text-[9px] uppercase tracking-wider font-bold">
                                            @foreach ($tujuanPembelajarans as $tp)
                                                <th class="px-3 py-2 text-center border-r border-white/10" title="{{ $tp->deskripsi_tujuan ?? $tp->deskripsi }}">TP {{ $tp->nomor_tujuan }}</th>
                                            @endforeach
                                        </tr>
                                    @endif
                                </thead>
                                
                                <tbody class="text-sm font-medium text-slate-700 bg-white">
                                    @forelse ($siswas as $index => $siswa)
                                        @php 
                                            $nData = $nilaiData[$siswa->id] ?? null; 
                                            // Extract JSON array to PHP array safely
                                            $sumatifArr = $nData && is_array($nData->nilai_sumatif) ? $nData->nilai_sumatif : json_decode($nData->nilai_sumatif ?? '{}', true);
                                        @endphp
                                        <tr class="hover:bg-indigo-50/50 transition-colors border-b border-slate-100">
                                            <td class="px-4 py-3 text-center font-bold text-slate-400 sticky left-0 z-10 bg-white border-r border-slate-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">{{ $index + 1 }}</td>
                                            <td class="px-6 py-3 sticky left-12 z-10 bg-white border-r border-slate-100 shadow-[4px_0_8px_-2px_rgba(0,0,0,0.05)]">
                                                <div class="font-bold text-slate-800">{{ $siswa->nama }}</div>
                                            </td>
                                            
                                            {{-- Input Kolom Sumatif TP --}}
                                            @foreach ($tujuanPembelajarans as $tp)
                                                <td class="px-2 py-3 text-center border-r border-slate-50 bg-indigo-50/10">
                                                    <input type="number" step="0.01" min="0" max="100" 
                                                           name="nilai[{{ $siswa->id }}][sumatif][{{ $tp->id }}]" 
                                                           value="{{ $sumatifArr[$tp->id] ?? '' }}" 
                                                           class="w-16 px-2 py-1.5 text-center text-sm font-bold bg-white border-slate-200 rounded-md focus:border-indigo-500 focus:ring-indigo-500 shadow-inner placeholder-slate-300"
                                                           placeholder="-">
                                                </td>
                                            @endforeach

                                            {{-- Input Kolom PSTS & PSAS --}}
                                            <td class="px-2 py-3 text-center border-l border-slate-200 bg-blue-50/30">
                                                <input type="number" step="0.01" min="0" max="100" 
                                                       name="nilai[{{ $siswa->id }}][psts]" 
                                                       value="{{ $nData->psts ?? '' }}" 
                                                       class="w-20 px-2 py-1.5 text-center text-sm font-black text-blue-700 bg-white border-blue-200 rounded-md focus:border-blue-500 shadow-inner placeholder-blue-300" placeholder="-">
                                            </td>
                                            <td class="px-2 py-3 text-center bg-blue-100/30">
                                                <input type="number" step="0.01" min="0" max="100" 
                                                       name="nilai[{{ $siswa->id }}][psas]" 
                                                       value="{{ $nData->psas ?? '' }}" 
                                                       class="w-20 px-2 py-1.5 text-center text-sm font-black text-blue-800 bg-white border-blue-300 rounded-md focus:border-blue-600 shadow-inner placeholder-blue-300" placeholder="-">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 4 + $tujuanPembelajarans->count() }}" class="px-6 py-12 text-center text-slate-400">Belum ada siswa di kelas ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-6 bg-white border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 text-white font-black text-[12px] uppercase tracking-widest rounded-xl shadow-lg">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Hitung & Simpan Nilai
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { height: 10px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</x-app-layout>