<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-indigo-600 text-3xl">👨‍🏫</span> 
            {{ __('Dashboard Tenaga Pendidik') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Banner: Modern Glassmorphism -->
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-blue-600 to-indigo-900 rounded-3xl shadow-xl p-10 text-white transform transition-all hover:scale-[1.01] duration-500">
                <!-- Efek Bercahaya di Belakang -->
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-48 h-48 bg-indigo-300 opacity-20 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <p class="text-indigo-200 text-sm font-semibold tracking-wider uppercase mb-2">Portal Guru & Pengajar</p>
                        <h2 class="text-4xl font-extrabold mb-3 drop-shadow-md">Selamat Bekerja, {{ $data['pegawai']->nama_lengkap ?? auth()->user()->name }}! 🌟</h2>
                        <p class="text-blue-50 text-base max-w-2xl leading-relaxed">
                            Semoga hari ini menjadi hari yang produktif dan menyenangkan. Mari bersama-sama ciptakan generasi penerus yang cerdas dan berkarakter unggul!
                        </p>
                    </div>
                    <!-- Ikon Besar di Kanan -->
                    <div class="hidden md:flex items-center justify-center w-32 h-32 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-inner">
                        <span class="text-6xl filter drop-shadow-lg animate-pulse" style="animation-duration: 3s;">🎓</span>
                    </div>
                </div>
            </div>

            <!-- 4 Kotak Statistik Utama dengan Hover Animasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-400 to-cyan-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform duration-300">👨‍🎓</div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Total Murid</p>
                            <h4 class="text-3xl font-black text-gray-800 mt-1">{{ $data['jumlahMuridDiajar'] }} <span class="text-sm font-medium text-gray-400">Siswa</span></h4>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-green-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform duration-300">📚</div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Mata Pelajaran</p>
                            <h4 class="text-3xl font-black text-gray-800 mt-1">{{ $data['jumlahMapel'] }} <span class="text-sm font-medium text-gray-400">Mapel</span></h4>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-amber-400 to-orange-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform duration-300">⏱️</div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Jam Mengajar</p>
                            <h4 class="text-3xl font-black text-gray-800 mt-1">{{ $data['totalJam'] }} <span class="text-sm font-medium text-gray-400">Jam/Mgg</span></h4>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-purple-400 to-fuchsia-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-50 to-fuchsia-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform duration-300">🏆</div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Ekskul Binaan</p>
                            <h4 class="text-3xl font-black text-gray-800 mt-1">{{ $data['jumlahEkskul'] }} <span class="text-sm font-medium text-gray-400">Ekskul</span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Area Bawah: Jadwal Hari Ini (Dirombak Total) -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">📅</div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Agenda Mengajar Anda</h3>
                            <p class="text-sm text-gray-500 font-medium">Hari ini: <span class="text-indigo-600 font-bold uppercase">{{ $data['namaHariIni'] }}</span></p>
                        </div>
                    </div>
                </div>

                @if(isset($data['jadwalHariIni']) && $data['jadwalHariIni']->count() > 0)
                    <!-- Jika HARI INI ada jam mengajar -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($data['jadwalHariIni'] as $jadwal)
                        <div class="group flex gap-5 p-5 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-white hover:border-indigo-300 hover:shadow-xl transition-all duration-300">
                            <!-- Bagian Waktu & Jam Ke- -->
                            <div class="text-center w-24 border-r-2 border-slate-200 group-hover:border-indigo-400 pr-5 shrink-0 transition-colors flex flex-col justify-center">
                                <p class="text-xs font-extrabold text-slate-400 tracking-wider uppercase mb-1">Jam Ke-{{ $jadwal->waktuKbm->jam_ke ?? '-' }}</p>
                                <p class="text-2xl font-black text-indigo-700 bg-indigo-50 py-1.5 rounded-lg border border-indigo-100 shadow-inner">
                                    {{ $jadwal->waktuKbm ? \Carbon\Carbon::parse($jadwal->waktuKbm->jam_mulai)->format('H:i') : '-' }}
                                </p>
                            </div>
                            <!-- Bagian Detail Mapel & Kelas -->
                            <div class="flex-1 flex flex-col justify-center">
                                <h4 class="font-bold text-gray-900 text-lg group-hover:text-indigo-700 transition-colors">
                                    {{ $jadwal->kodeGuru->mataPelajaran->nama_mapel ?? ($jadwal->mataPelajaran->nama_mapel ?? 'Mata Pelajaran') }}
                                </h4>
                                <div class="flex items-center flex-wrap gap-2 mt-3">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100/80 px-3 py-1.5 rounded-lg border border-emerald-200">
                                        🏫 Kelas {{ $jadwal->kelas->nama_kelas ?? '-' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-100/80 px-3 py-1.5 rounded-lg border border-amber-200">
                                        📍 Ruang: {{ $jadwal->ruangan->nama_ruangan ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <!-- Jika HARI INI TIDAK ADA jam mengajar (Waktu Luang) -->
                    <div class="flex flex-col items-center justify-center py-16 px-4 bg-gradient-to-br from-slate-50 to-gray-50 rounded-3xl border-2 border-dashed border-slate-200">
                        <!-- Animasi Cangkir Kopi Melompat -->
                        <div class="w-24 h-24 bg-white rounded-full shadow-sm flex items-center justify-center text-5xl mb-6 animate-bounce" style="animation-duration: 2.5s;">☕</div>
                        <h3 class="text-slate-800 font-extrabold text-2xl mb-2">Tidak Ada Jadwal Hari Ini</h3>
                        <p class="text-slate-500 text-sm max-w-md text-center leading-relaxed">
                            Selamat! Anda tidak memiliki jam mengajar hari ini di sekolah. Silakan gunakan waktu luang ini untuk memeriksa tugas siswa atau bersantai sejenak.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>