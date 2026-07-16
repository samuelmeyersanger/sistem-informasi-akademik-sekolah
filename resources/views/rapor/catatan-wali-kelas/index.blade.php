<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Catatan Wali Kelas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            {{-- KOTAK 1: Filter Kelas --}}
            <div class="bg-white/80 backdrop-blur-2xl shadow-xl shadow-indigo-500/5 rounded-[2rem] border border-white/60 p-8">
                <h3 class="text-lg font-black text-slate-800 tracking-tight mb-6 uppercase border-b border-slate-100 pb-4">Input Catatan Wali Kelas</h3>
                
                <form action="{{ route('rapor.catatan-wali-kelas.index') }}" method="GET" class="flex items-center gap-6">
                    <label class="font-bold text-slate-700 text-sm whitespace-nowrap">Pilih Kelas</label>
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

            {{-- KOTAK 2: Tabel Input Massal --}}
            @if ($kelas_id)
                <div class="bg-white/80 backdrop-blur-2xl shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60 overflow-hidden">
                    
                    <form action="{{ route('rapor.catatan-wali-kelas.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">

                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-[11px] uppercase tracking-widest font-black">
                                        <th class="px-6 py-5 text-center w-16">No</th>
                                        <th class="px-6 py-5 text-left w-64">Nama Siswa</th>
                                        <th class="px-6 py-5 text-center">NISN</th>
                                        <th class="px-6 py-5 text-center">NIS</th>
                                        <th class="px-6 py-5 text-left border-l border-white/20">Catatan Wali Kelas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700 bg-slate-50/50">
                                    @forelse ($siswas as $index => $siswa)
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="px-6 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-800">{{ $siswa->nama }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center text-slate-500">{{ $siswa->nisn }}</td>
                                            <td class="px-6 py-4 text-center text-slate-500">{{ $siswa->nis }}</td>
                                            
                                            {{-- Input Teks Area --}}
                                            <td class="px-6 py-4 border-l border-slate-100">
                                                <textarea name="data_catatan[{{ $siswa->id }}][catatan]" rows="2"
                                                          class="w-full px-4 py-3 bg-white border-slate-200 text-sm font-medium text-slate-800 rounded-xl focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors resize-none"
                                                          placeholder="Tulis pesan motivasi atau evaluasi untuk siswa ini...">{{ $siswa->catatanWaliKelas->catatan ?? '' }}</textarea>
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

                        <div class="p-6 bg-white border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-black text-[12px] uppercase tracking-widest rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Catatan Wali Kelas
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="text-center py-12 bg-white/50 backdrop-blur-sm rounded-[2rem] border border-dashed border-slate-300">
                    <i class="fa-solid fa-hand-pointer text-4xl text-slate-300 mb-3 block"></i>
                    <p class="text-slate-500 font-medium">Silakan pilih Kelas pada form di atas.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>