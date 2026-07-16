<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Nilai Kokurikuler (P5)') }}
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

            {{-- KOTAK 1: Filter 3 Lapis (Kelas -> Kegiatan -> Profil) --}}
            <div class="bg-white/80 backdrop-blur-2xl shadow-xl shadow-indigo-500/5 rounded-[2rem] border border-white/60 p-8">
                <h3 class="text-lg font-black text-slate-800 tracking-tight mb-6 uppercase border-b border-slate-100 pb-4">Parameter Penilaian P5</h3>
                
                <form action="{{ route('rapor.nilai-kokurikuler.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Filter 1: Kelas --}}
                    <div>
                        <label class="block font-bold text-slate-700 text-sm mb-2">1. Pilih Kelas</label>
                        <select name="kelas_id" onchange="this.form.submit()" 
                                class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-indigo-700 rounded-xl focus:border-indigo-500 shadow-inner">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelases as $kelas)
                                <option value="{{ $kelas->id }}" {{ (isset($kelas_id) && $kelas_id == $kelas->id) ? 'selected' : '' }}>
                                    Tingkat {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter 2: Kegiatan (Hanya terbuka jika kelas sudah dipilih) --}}
                    <div>
                        <label class="block font-bold text-slate-700 text-sm mb-2">2. Pilih Kegiatan P5</label>
                        <select name="kegiatan_kokurikuler_id" onchange="this.form.submit()" 
                                class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-indigo-700 rounded-xl focus:border-indigo-500 shadow-inner"
                                {{ empty($kelas_id) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Kegiatan --</option>
                            @foreach ($kegiatans as $keg)
                                <option value="{{ $keg->id }}" {{ (isset($kegiatan_kokurikuler_id) && $kegiatan_kokurikuler_id == $keg->id) ? 'selected' : '' }}>
                                    {{ Str::limit($keg->nama_kegiatan, 40) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter 3: Profil Lulusan --}}
                    <div>
                        <label class="block font-bold text-slate-700 text-sm mb-2">3. Profil Lulusan Dituju</label>
                        <select name="profil_lulusan_id" onchange="this.form.submit()" 
                                class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-[12px] font-bold text-indigo-700 rounded-xl focus:border-indigo-500 shadow-inner"
                                {{ empty($kegiatan_kokurikuler_id) ? 'disabled' : '' }}>
                            <option value="">-- Pilih Profil --</option>
                            @foreach ($profils as $pro)
                                <option value="{{ $pro->id }}" {{ (isset($profil_lulusan_id) && $profil_lulusan_id == $pro->id) ? 'selected' : '' }}>
                                    {{ $pro->dimensi_profil_lulusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- KOTAK 2: Tabel Input --}}
            @if (!empty($kelas_id) && !empty($kegiatan_kokurikuler_id) && !empty($profil_lulusan_id))
                <div class="bg-white/80 backdrop-blur-2xl shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60 overflow-hidden">
                    
                    {{-- Petunjuk Alur Perkembangan --}}
                    <div class="bg-sky-50 px-8 py-4 border-b border-sky-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-sky-800 uppercase tracking-widest"><i class="fa-solid fa-circle-info mr-2"></i> Predikat Alur Perkembangan</span>
                    </div>

                    <form action="{{ route('rapor.nilai-kokurikuler.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">
                        <input type="hidden" name="kegiatan_kokurikuler_id" value="{{ $kegiatan_kokurikuler_id }}">
                        <input type="hidden" name="profil_lulusan_id" value="{{ $profil_lulusan_id }}">

                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="bg-gradient-to-r from-sky-500 to-sky-600 text-white text-[11px] uppercase tracking-widest font-black">
                                        <th class="px-6 py-5 text-center w-16">No</th>
                                        <th class="px-6 py-5 text-left w-64">Nama Siswa</th>
                                        <th class="px-6 py-5 text-center border-l border-white/20">Capaian Subdimensi Profil Lulusan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700 bg-slate-50/50">
                                    @forelse ($siswas as $index => $siswa)
                                        <tr class="hover:bg-sky-50/30 transition-colors">
                                            <td class="px-6 py-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-800">{{ $siswa->nama }}</div>
                                            </td>
                                            
                                            {{-- Dropdown Predikat --}}
                                            <td class="px-6 py-4 border-l border-slate-100 text-center">
                                                @php $predikatSaatIni = $nilaiData[$siswa->id]->predikat ?? ''; @endphp
                                                <select name="nilai[{{ $siswa->id }}][predikat]" 
                                                        class="w-full max-w-sm px-4 py-3 bg-white border-slate-200 text-sm font-bold text-slate-700 rounded-xl focus:border-sky-500 focus:ring-sky-500 shadow-sm mx-auto block">
                                                    <option value="">-- Pilih Capaian --</option>
                                                    <option value="Berkembang" {{ $predikatSaatIni == 'Berkembang' ? 'selected' : '' }}>Berkembang</option>
                                                    <option value="Cakap" {{ $predikatSaatIni == 'Cakap' ? 'selected' : '' }}>Cakap</option>
                                                    <option value="Mahir" {{ $predikatSaatIni == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-12 text-center text-slate-400 font-medium">Belum ada data siswa.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-6 bg-white border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-sky-600 to-sky-500 hover:from-sky-700 text-white font-black text-[12px] uppercase tracking-widest rounded-xl shadow-lg">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Capaian P5
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>