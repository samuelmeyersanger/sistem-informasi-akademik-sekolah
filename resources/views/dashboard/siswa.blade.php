<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-orange-500 text-3xl">🎒</span> 
            {{ __('Dashboard Ruang Belajar Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Banner: Youthful & Energetic (Sunset Glassmorphism) -->
            <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-pink-500 to-rose-600 rounded-3xl shadow-xl p-10 text-white transform transition-all hover:scale-[1.01] duration-500">
                <!-- Efek Bercahaya di Belakang -->
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-20 rounded-full blur-3xl pointer-events-none animate-pulse" style="animation-duration: 4s;"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-48 h-48 bg-yellow-300 opacity-20 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <p class="text-orange-100 text-sm font-bold tracking-wider uppercase mb-2">Portal Siswa Terpadu</p>
                        <h2 class="text-4xl font-extrabold mb-3 drop-shadow-md">Halo, {{ auth()->user()->name }}! 👋</h2>
                        <p class="text-rose-50 text-base max-w-2xl leading-relaxed font-medium">
                            Siap untuk mendapatkan ilmu baru hari ini? Cek jadwal pelajaranmu, pantau tugas, dan raih prestasi terbaikmu bersama teman-teman!
                        </p>
                    </div>
                    <!-- Ikon Besar Ceria di Kanan -->
                    <div class="hidden md:flex items-center justify-center w-32 h-32 bg-white/20 backdrop-blur-md rounded-2xl border border-white/30 shadow-inner">
                        <span class="text-6xl filter drop-shadow-lg animate-bounce" style="animation-duration: 2s;">🚀</span>
                    </div>
                </div>
            </div>

            <!-- 4 Kotak Statistik Siswa (Prestasi & Kehadiran) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1: Kelas -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-400 to-cyan-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">🏫</div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Ruang Kelas</p>
                            <!-- Ganti variabel ini dengan data kelas siswa dari controller jika ada -->
                            <h4 class="text-3xl font-black text-gray-800 mt-1">Aktif</h4> 
                        </div>
                    </div>
                </div>

                <!-- Card 2: Kehadiran -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-green-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">✅</div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Persentase Hadir</p>
                            <h4 class="text-3xl font-black text-gray-800 mt-1">100<span class="text-lg text-gray-400">%</span></h4>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Ekskul -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-amber-400 to-orange-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">⚽</div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Ekskul Ku</p>
                            <h4 class="text-3xl font-black text-gray-800 mt-1">1 <span class="text-sm font-medium text-gray-400">Kegiatan</span></h4>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Tugas -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-purple-400 to-fuchsia-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-50 to-fuchsia-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">📖</div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Status Belajar</p>
                            <h4 class="text-3xl font-black text-gray-800 mt-1">Aman</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Area Bawah: Jadwal & Informasi -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- KIRI: Jadwal Pelajaran Hari Ini (Lebih Lebar) -->
                <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl">📅</div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Jadwal Pelajaran Hari Ini</h3>
                                <p class="text-sm text-gray-500">Semangat mengikuti kelas!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dummy State: Jika Jadwal Kosong (Bisa Anda integrasikan dengan PHP/Blade nantinya) -->
                    <div class="flex flex-col items-center justify-center py-10 px-4 bg-orange-50/50 rounded-2xl border-2 border-dashed border-orange-200">
                        <div class="w-20 h-20 bg-white rounded-full shadow-sm flex items-center justify-center text-4xl mb-4 animate-bounce">🎮</div>
                        <h3 class="text-gray-800 font-extrabold text-lg mb-1">Wah, Jadwal Belum Terbaca</h3>
                        <p class="text-gray-500 text-sm max-w-sm text-center">
                            Saat ini belum ada jadwal pelajaran yang aktif untuk hari ini, atau kelasmu sedang libur. Waktunya istirahat!
                        </p>
                    </div>

                    <!--
                    Catatan: 
                    Jika Anda punya data jadwal harian dari controller (misal: $jadwal_hari_ini), 
                    Anda bisa meniru kodingan "foreach" seperti pada dashboard guru di sini.
                    -->
                </div>

                <!-- KANAN: Akses Cepat Fitur Siswa -->
                <div class="bg-gradient-to-br from-indigo-800 to-purple-900 rounded-3xl shadow-lg p-8 text-white relative overflow-hidden">
                    <!-- Efek Geometri di Belakang -->
                    <div class="absolute top-0 right-0 opacity-10">
                        <svg class="w-48 h-48 -mt-8 -mr-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.2l7.1 14H4.9L12 6.2z"/></svg>
                    </div>
                    
                    <h3 class="font-bold text-2xl mb-2 relative z-10">Menu Pintas</h3>
                    <p class="text-indigo-200 text-sm mb-6 relative z-10">Akses cepat layanan kesiswaan.</p>
                    
                    <div class="space-y-4 relative z-10">
                        <!-- Cek Nilai / E-Rapor -->
                        <a href="#" class="flex items-center justify-between p-4 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 backdrop-blur-sm transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-orange-500 flex items-center justify-center text-white text-xl shadow-lg group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                                    💯
                                </div>
                                <div>
                                    <div class="font-bold text-sm tracking-wide">E-Rapor & Nilai</div>
                                    <div class="text-[11px] text-indigo-100 mt-0.5">Lihat nilai semester</div>
                                </div>
                            </div>
                            <span class="text-white bg-white/10 p-2 rounded-lg group-hover:bg-white/30 transition-colors">→</span>
                        </a>

                        <!-- Kalender Akademik -->
                        <a href="#" class="flex items-center justify-between p-4 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 backdrop-blur-sm transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-pink-500 flex items-center justify-center text-white text-xl shadow-lg group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                                    🗓️
                                </div>
                                <div>
                                    <div class="font-bold text-sm tracking-wide">Kalender Libur</div>
                                    <div class="text-[11px] text-indigo-100 mt-0.5">Cek hari libur sekolah</div>
                                </div>
                            </div>
                            <span class="text-white bg-white/10 p-2 rounded-lg group-hover:bg-white/30 transition-colors">→</span>
                        </a>
                        
                        <!-- Profil Siswa -->
                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-4 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 backdrop-blur-sm transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-indigo-500 flex items-center justify-center text-white text-xl shadow-lg group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
                                    ⚙️
                                </div>
                                <div>
                                    <div class="font-bold text-sm tracking-wide">Pengaturan Akun</div>
                                    <div class="text-[11px] text-indigo-100 mt-0.5">Ubah kata sandi</div>
                                </div>
                            </div>
                            <span class="text-white bg-white/10 p-2 rounded-lg group-hover:bg-white/30 transition-colors">→</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>