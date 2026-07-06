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
                        <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50">
                            Anda belum memiliki jadwal mengajar aktif di sistem.
                        </div>
                    @else
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-700">Jadwal Mengajar: {{ $pegawai->nama_lengkap }}</h3>
                            <p class="text-sm text-gray-500">Berikut adalah jadwal Anda yang tersebar di berbagai kelas.</p>
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