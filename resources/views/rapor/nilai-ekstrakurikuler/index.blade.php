<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Nilai Ekstrakurikuler') }}
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

            {{-- KOTAK 1: Filter Ganda (Ekskul & Kelas) --}}
            <div class="bg-white/80 backdrop-blur-2xl shadow-xl shadow-indigo-500/5 rounded-[2rem] border border-white/60 p-8">
                <h3 class="text-lg font-black text-slate-800 tracking-tight mb-6 uppercase border-b border-slate-100 pb-4">Pilih Parameter Ekstrakurikuler</h3>
                
                <form action="{{ route('rapor.nilai-ekstrakurikuler.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold text-slate-700 text-sm mb-2">1. Pilih Ekstrakurikuler</label>
                        <select name="ekstrakurikuler_id" onchange="this.form.submit()" 
                                class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-indigo-700 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-inner">
                            <option value="">-- Pilih Ekstrakurikuler --</option>
                            @foreach ($ekstrakurikulers ?? [] as $ekskul)
                                <option value="{{ $ekskul->id }}" {{ (isset($ekstrakurikuler_id) && $ekstrakurikuler_id == $ekskul->id) ? 'selected' : '' }}>
                                    {{ $ekskul->nama_ekstrakurikuler }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-sm mb-2">2. Pilih Kelas</label>
                        <select name="kelas_id" onchange="this.form.submit()" 
                                class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-indigo-700 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-inner"
                                {{ empty($ekstrakurikuler_id) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelases ?? [] as $kelas)
                                <option value="{{ $kelas->id }}" {{ (isset($kelas_id) && $kelas_id == $kelas->id) ? 'selected' : '' }}>
                                    {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- KOTAK 2: Tabel Input Massal --}}
            @if (!empty($kelas_id) && !empty($ekstrakurikuler_id))
                <div class="bg-white/80 backdrop-blur-2xl shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60 overflow-hidden">
                    
                    <form action="{{ route('rapor.nilai-ekstrakurikuler.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">
                        <input type="hidden" name="ekstrakurikuler_id" value="{{ $ekstrakurikuler_id }}">

                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-[11px] uppercase tracking-widest font-black">
                                        <th class="px-6 py-5 text-center w-16">No</th>
                                        <th class="px-6 py-5 text-left w-64">Nama Siswa</th>
                                        <th class="px-6 py-5 text-center border-l border-white/20">Predikat</th>
                                        <th class="px-6 py-5 text-left">Deskripsi (Opsional)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700 bg-slate-50/50">
                                    @forelse ($siswas as $index => $siswa)
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="px-6 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-800">{{ $siswa->nama }}</div>
                                            </td>
                                            
                                            {{-- Dropdown Predikat --}}
                                            <td class="px-6 py-4 border-l border-slate-100 text-center">
                                                @php $predikatSaatIni = $nilaiData[$siswa->id]->predikat ?? ''; @endphp
                                                <select name="nilai[{{ $siswa->id }}][predikat]" 
                                                        class="w-full px-3 py-2 bg-white border-slate-200 text-sm font-bold text-slate-700 rounded-lg focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="Sangat Baik" {{ $predikatSaatIni == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                                    <option value="Baik" {{ $predikatSaatIni == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                    <option value="Cukup" {{ $predikatSaatIni == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                                    <option value="Kurang" {{ $predikatSaatIni == 'Kurang' ? 'selected' : '' }}>Kurang</option>
                                                </select>
                                            </td>

                                            {{-- Input Deskripsi --}}
                                            <td class="px-6 py-4">
                                                <input type="text" name="nilai[{{ $siswa->id }}][deskripsi]" 
                                                       value="{{ $nilaiData[$siswa->id]->deskripsi ?? '' }}"
                                                       class="w-full px-3 py-2 bg-white border-slate-200 text-sm rounded-lg focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                                                       placeholder="Deskripsi kemampuan...">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">Belum ada data siswa.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-6 bg-white border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 text-white font-black text-[12px] uppercase tracking-widest rounded-xl shadow-lg">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Nilai Ekskul
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>