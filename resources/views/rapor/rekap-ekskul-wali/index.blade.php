<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <span class="text-3xl">📊</span> {{ __('Rekap Nilai Ekstrakurikuler') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Toolbar Filter Kelas -->
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('rapor.rekap-ekskul-wali.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-3">
                    
                    <div class="relative w-full sm:w-1/3">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-lg">🏫</span>
                        <select name="kelas_id" class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner py-3 pl-12 pr-10 font-semibold text-gray-700 transition-colors">
                            <option value="">-- Pilih Kelas Anda --</option>
                            @foreach($kelases as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    Kelas {{ $k->tingkat }} - {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl text-sm shadow-md transition-transform transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span>🔍</span> Tampilkan Rekap
                        </button>
                        @if(request('kelas_id'))
                            <a href="{{ route('rapor.rekap-ekskul-wali.index') }}" class="w-full sm:w-auto px-5 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-sm text-center rounded-xl font-bold transition-colors">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tampilan Data / Tabel -->
            @if($kelas_id)
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                    <th class="p-5 pl-8 w-20 text-center">No</th>
                                    <th class="p-5 w-64">Nama Siswa</th>
                                    <th class="p-5 w-56">Ekstrakurikuler</th>
                                    <th class="p-5 text-center w-32">Predikat</th>
                                    <th class="p-5 pr-8">Catatan Pembina Ekskul</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-gray-700">
                                @forelse($siswas as $index => $siswa)
                                    <!-- Jika Siswa tidak ikut ekskul sama sekali -->
                                    @if($siswa->nilaiEkstrakurikuler->isEmpty())
                                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                                            <td class="p-5 pl-8 text-center font-bold text-gray-400">{{ $index + 1 }}</td>
                                            <td class="p-5 font-bold text-gray-900">{{ $siswa->nama_lengkap }}</td>
                                            <td class="p-5" colspan="3">
                                                <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs font-bold border border-gray-200">
                                                    Tidak Mengikuti Ekstrakurikuler
                                                </span>
                                            </td>
                                        </tr>
                                    @else
                                        <!-- Looping jika siswa ikut 1 atau lebih Ekskul (Otomatis menggabung baris nama siswa) -->
                                        @foreach($siswa->nilaiEkstrakurikuler as $i => $nilai)
                                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                                
                                                <!-- Nama Siswa hanya dicetak di baris pertama ekskulnya -->
                                                @if($i === 0)
                                                    <td class="p-5 pl-8 text-center font-bold text-gray-400 align-top border-b border-gray-100" rowspan="{{ $siswa->nilaiEkstrakurikuler->count() }}">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td class="p-5 font-bold text-gray-900 align-top border-b border-gray-100" rowspan="{{ $siswa->nilaiEkstrakurikuler->count() }}">
                                                        {{ $siswa->nama_lengkap }}
                                                    </td>
                                                @endif
                                                
                                                <td class="p-5 align-middle border-b border-gray-50">
                                                    <span class="font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100">
                                                        🏀 {{ $nilai->ekstrakurikuler->nama ?? 'Ekskul Dihapus' }}
                                                    </span>
                                                </td>
                                                
                                                <td class="p-5 text-center align-middle border-b border-gray-50">
                                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl font-black text-lg shadow-sm
                                                        {{ $nilai->predikat == 'A' || $nilai->predikat == 'Sangat Baik' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 
                                                          ($nilai->predikat == 'B' || $nilai->predikat == 'Baik' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 
                                                          ($nilai->predikat == 'C' || $nilai->predikat == 'Cukup' ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-rose-100 text-rose-700 border border-rose-200')) }}">
                                                        {{ $nilai->predikat }}
                                                    </span>
                                                </td>
                                                
                                                <td class="p-5 pr-8 align-middle text-gray-600 font-medium text-xs leading-relaxed border-b border-gray-50">
                                                    {{ $nilai->deskripsi ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-16 text-center text-gray-400 bg-gray-50/30">
                                            <span class="text-5xl block mb-4">📭</span>
                                            <p class="text-lg font-bold text-gray-500">Belum ada data siswa di kelas ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- Halaman Kosong Sebelum Filter Dipilih -->
                <div class="bg-indigo-50/50 border border-indigo-100 rounded-3xl p-16 text-center shadow-inner">
                    <span class="text-6xl block mb-6 drop-shadow-md">🏫</span>
                    <h3 class="text-2xl font-black text-indigo-900 mb-2">Pilih Kelas Anda</h3>
                    <p class="text-indigo-500 font-medium max-w-md mx-auto">Silakan pilih kelas yang Anda ampu pada menu dropdown di atas untuk melihat rekapitulasi nilai ekstrakurikuler siswa secara keseluruhan.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>