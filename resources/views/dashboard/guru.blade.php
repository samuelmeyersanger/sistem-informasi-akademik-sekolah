<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Tenaga Pendidik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Ucapan Selamat Datang -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl shadow-lg p-6 sm:p-10 text-white flex flex-col sm:flex-row justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-2">Selamat bekerja, {{ $data['pegawai']->nama_lengkap ?? auth()->user()->name }}! 🌟</h2>
                    <p class="text-indigo-100 text-sm">Semoga hari ini menjadi hari yang produktif dan menyenangkan untuk kegiatan belajar mengajar.</p>
                </div>
            </div>

            <!-- 4 Kotak Statistik Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl font-bold">👨‍🎓</div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase">Total Murid</p>
                        <h4 class="text-2xl font-bold text-gray-800">{{ $data['jumlahMuridDiajar'] }} <span class="text-sm font-normal text-gray-400">siswa</span></h4>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold">📚</div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase">Mata Pelajaran</p>
                        <h4 class="text-2xl font-bold text-gray-800">{{ $data['jumlahMapel'] }} <span class="text-sm font-normal text-gray-400">mapel</span></h4>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-2xl font-bold">⏱️</div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase">Jam Mengajar</p>
                        <h4 class="text-2xl font-bold text-gray-800">{{ $data['totalJam'] }} <span class="text-sm font-normal text-gray-400">Jam/Mgg</span></h4>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-2xl font-bold">🏆</div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase">Ekskul Binaan</p>
                        <h4 class="text-2xl font-bold text-gray-800">{{ $data['jumlahEkskul'] }} <span class="text-sm font-normal text-gray-400">ekskul</span></h4>
                    </div>
                </div>
            </div>

            <!-- Jadwal Hari Ini -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Jadwal Mengajar Anda <span class="text-indigo-600">({{ $data['namaHariIni'] }})</span></h3>
                </div>
                @if(isset($data['jadwalHariIni']) && $data['jadwalHariIni']->count() > 0)
                    <!-- Jika ada jadwal hari ini -->
                    <div class="space-y-4">
                        @foreach($data['jadwalHariIni'] as $jadwal)
                        <div class="flex gap-4 items-center p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-indigo-200 hover:shadow-sm transition-all">
                            <div class="text-center w-20 border-r border-gray-200 pr-4 shrink-0">
                                <p class="text-xs font-bold text-gray-400 mb-1">Jam ke-{{ $jadwal->waktuKbm->jam_ke ?? '-' }}</p>
                                <p class="text-sm font-black text-indigo-700">
                                    {{ $jadwal->waktuKbm ? \Carbon\Carbon::parse($jadwal->waktuKbm->jam_mulai)->format('H:i') : '-' }}
                                </p>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 text-base">
                                    {{ $jadwal->kodeGuru->mataPelajaran->nama_mapel ?? ($jadwal->mataPelajaran->nama_mapel ?? 'Mata Pelajaran') }}
                                </h4>
                                <div class="flex items-center flex-wrap gap-3 mt-2">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100">
                                        🏫 Kelas {{ $jadwal->kelas->nama_kelas ?? '-' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-100">
                                        📍 Ruang: {{ $jadwal->ruangan->nama_ruangan ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <!-- Jika HARI INI tidak ada jam mengajar -->
                    <div class="flex flex-col items-center justify-center py-12 px-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <span class="text-5xl mb-4">☕</span>
                        <p class="text-slate-700 font-bold text-lg mb-1">Hari ini Anda tidak ada jadwal mengajar.</p>
                        <p class="text-slate-500 text-sm">Selamat menikmati waktu Anda untuk memeriksa tugas atau administrasi lainnya.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>