<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jadwal Mengajar Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(!$pegawai)
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">
                            Akun Anda belum terhubung dengan data Pegawai/Guru.
                        </div>
                    @elseif($jadwalPelajaran->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 px-4 bg-gradient-to-b from-slate-50 to-white rounded-2xl border-2 border-slate-100 border-dashed my-6 text-center shadow-sm">
                            <!-- Ikon Kalender Lingkaran -->
                            <div class="w-20 h-20 mb-5 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-500 shadow-inner ring-4 ring-white">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12h.01M12 16h.01M8 12h.01M8 16h.01M16 12h.01"></path>
                                </svg>
                            </div>
                            
                            <!-- Teks -->
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Belum Ada Jadwal Mengajar</h3>
                            <p class="text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">
                                Saat ini Anda belum diplot ke dalam jadwal pelajaran manapun. Silakan hubungi <b>Wakasek Kurikulum</b> jika Anda merasa ini adalah sebuah kekeliruan.
                            </p>
                        </div>
                    @else
                        <!-- Baris Judul & Tombol (Flexbox) -->
                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-700">Jadwal Mengajar: {{ $pegawai->nama_lengkap }}</h3>
                                <p class="text-sm text-gray-500">Berikut adalah jadwal Anda yang tersebar di berbagai kelas.</p>
                            </div>
                            
                            <!-- TOMBOL DOWNLOAD PDF -->
                            <a href="{{ route('akademik.jadwal_mengajar.download') }}" target="_blank" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2 shrink-0">
                                📄 Cetak / Simpan PDF
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 border border-gray-300">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-300">
                                    <tr>
                                        <th class="px-4 py-3 border-r border-gray-300">Waktu</th>
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                            <th class="px-4 py-3 border-r border-gray-300 text-center">{{ $hari }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Mengelompokkan berdasarkan Jam Ke (Contoh: Jam 1, Jam 2, dst)
                                        $waktuPerJam = $daftarWaktu->groupBy('jam_ke');
                                    @endphp

                                    @foreach($waktuPerJam as $jamKe => $waktuList)
                                        @php
                                            $contohWaktu = $waktuList->first();
                                        @endphp
                                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">
                                                <div class="font-bold text-gray-800">Jam ke-{{ $jamKe }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ \Carbon\Carbon::parse($contohWaktu->jam_mulai)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($contohWaktu->jam_selesai)->format('H:i') }}
                                                </div>
                                            </td>
                                            
                                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                                @php
                                                    // Cocokkan jadwal dengan HARI dan JAM KE
                                                    $jadwal = $jadwalPelajaran->first(function($item) use ($hari, $jamKe) {
                                                        // Sesuaikan nama relasi waktuKbm Anda di sini jika perlu
                                                        return $item->waktuKbm->hari === $hari && $item->waktuKbm->jam_ke == $jamKe;
                                                    });
                                                @endphp
                                                
                                                <td class="px-4 py-3 border-r border-gray-200 text-center align-top min-w-[120px]">
                                                    @if($jadwal)
                                                        <div class="bg-indigo-100 text-indigo-800 p-2 rounded text-xs font-bold mb-1 shadow-sm border border-indigo-200">
                                                            {{ $jadwal->mataPelajaran->nama_mapel ?? 'Mapel' }}
                                                        </div>
                                                        <div class="text-xs font-semibold text-gray-700">
                                                            Kelas: {{ $jadwal->kelas->nama_kelas ?? '-' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            R. {{ $jadwal->ruangan->nama_ruangan ?? '-' }}
                                                        </div>
                                                    @else
                                                        <span class="text-gray-300">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>