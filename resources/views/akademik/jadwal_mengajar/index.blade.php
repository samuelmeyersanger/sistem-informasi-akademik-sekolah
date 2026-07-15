<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
                <span class="text-3xl">📅</span> {{ __('Jadwal Mengajar Saya') }}
            </h2>
            
            @if($pegawai && !$jadwalPelajaran->isEmpty())
                <!-- TOMBOL DOWNLOAD PDF DIPINDAH KE HEADER AGAR LEBIH TERLIHAT -->
                <a href="{{ route('akademik.jadwal_mengajar.download') }}" target="_blank" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                    📄 Cetak / Simpan PDF
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(!$pegawai)
                <!-- KONDISI 1: AKUN BELUM TERHUBUNG DENGAN PEGAWAI -->
                <div class="bg-white rounded-3xl p-10 text-center shadow-xl border border-rose-100 max-w-2xl mx-auto mt-10 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-rose-400 to-red-500"></div>
                    <div class="w-24 h-24 mx-auto mb-6 bg-rose-50 rounded-full flex items-center justify-center shadow-inner border-4 border-white ring-4 ring-rose-50">
                        <span class="text-5xl animate-bounce">🔗</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 mb-3">Akses Terbatas!</h3>
                    <p class="text-gray-500 leading-relaxed mb-8">
                        Sistem mendeteksi bahwa akun Anda saat ini belum dikaitkan dengan biodata Pegawai atau Guru manapun. Anda belum bisa melihat jadwal mengajar.
                    </p>
                    <div class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 text-white text-sm font-bold rounded-xl shadow-md">
                        <span>👨‍💻</span> Hubungi Administrator / TU
                    </div>
                </div>

            @elseif($jadwalPelajaran->isEmpty())
                <!-- KONDISI 2: JADWAL KOSONG -->
                <div class="bg-white rounded-3xl p-10 text-center shadow-sm border border-gray-100 max-w-2xl mx-auto mt-10">
                    <div class="w-24 h-24 mx-auto mb-6 bg-indigo-50 rounded-full flex items-center justify-center">
                        <span class="text-5xl opacity-80">📭</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 mb-3">Belum Ada Jadwal</h3>
                    <p class="text-gray-500 leading-relaxed mb-8">
                        Saat ini Anda belum diplot ke dalam jadwal pelajaran manapun. Silakan hubungi <b>Wakasek Kurikulum</b> jika Anda merasa ini adalah sebuah kekeliruan.
                    </p>
                </div>

            @else
                <!-- KONDISI 3: JADWAL TERSEDIA -->
                
                <!-- Banner Identitas -->
                <div class="bg-gradient-to-r from-indigo-900 to-indigo-700 rounded-3xl shadow-lg p-8 text-white flex items-center justify-between mb-8">
                    <div>
                        <p class="text-indigo-200 text-sm font-semibold mb-1 uppercase tracking-wider">Jadwal Aktif Milik</p>
                        <h3 class="text-3xl font-black mb-2">{{ $pegawai->nama_lengkap }}</h3>
                        <p class="text-indigo-100 text-sm max-w-xl leading-relaxed">
                            Pastikan Anda hadir 10 menit sebelum jam pelajaran dimulai. Tetap semangat membagikan ilmu yang bermanfaat bagi siswa-siswi kita!
                        </p>
                    </div>
                    <div class="hidden md:block text-6xl opacity-20">
                        👩‍🏫
                    </div>
                </div>

                <!-- Tabel Jadwal Utama -->
                <div class="bg-white shadow-xl sm:rounded-3xl border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 bg-gray-50 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-5 font-bold border-b border-gray-200">Waktu</th>
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                        <th class="px-6 py-5 font-bold border-b border-gray-200 text-center border-l border-gray-100 w-1/5">{{ $hari }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php
                                    $waktuPerJam = $daftarWaktu->groupBy('jam_ke');
                                @endphp

                                @foreach($waktuPerJam as $jamKe => $waktuList)
                                    @php
                                        $contohWaktu = $waktuList->first();
                                    @endphp
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        
                                        <!-- Kolom Jam -->
                                        <td class="px-6 py-4 whitespace-nowrap bg-gray-50/50 group-hover:bg-indigo-50/50 transition-colors">
                                            <div class="font-black text-gray-900 text-base mb-1">Jam ke-{{ $jamKe }}</div>
                                            <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                                                <span>⏱️</span>
                                                {{ \Carbon\Carbon::parse($contohWaktu->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($contohWaktu->jam_selesai)->format('H:i') }}
                                            </div>
                                        </td>
                                        
                                        <!-- Kolom Hari -->
                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                            @php
                                                $jadwal = $jadwalPelajaran->first(function($item) use ($hari, $jamKe) {
                                                    return $item->waktuKbm->hari === $hari && $item->waktuKbm->jam_ke == $jamKe;
                                                });
                                            @endphp
                                            
                                            <td class="px-4 py-4 text-center align-top border-l border-gray-100 min-w-[140px]">
                                                @if($jadwal)
                                                    <div class="h-full flex flex-col justify-center items-center p-3 bg-white border border-indigo-100 rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-300 transition-all cursor-default">
                                                        <div class="w-full bg-indigo-600 text-white text-[11px] font-bold uppercase tracking-wider py-1.5 px-2 rounded-lg mb-2 truncate">
                                                            {{ $jadwal->mataPelajaran->nama_mapel ?? 'Mapel' }}
                                                        </div>
                                                        <div class="font-black text-gray-800 text-lg mb-0.5">
                                                            {{ $jadwal->kelas->nama_kelas ?? '-' }}
                                                        </div>
                                                        <div class="text-xs font-semibold text-gray-500 bg-gray-50 px-2 py-0.5 rounded-md border border-gray-200 mt-1">
                                                            📍 R. {{ $jadwal->ruangan->nama_ruangan ?? '-' }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="h-full flex items-center justify-center opacity-30">
                                                        <span class="text-gray-300 text-2xl font-light">-</span>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>