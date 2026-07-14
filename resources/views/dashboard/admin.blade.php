<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-slate-800 text-3xl">🛡️</span> 
            {{ __('Dashboard Pimpinan & Admin') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Banner: Wibawa Eksekutif (Dark Slate Glassmorphism) -->
            <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-700 to-slate-900 rounded-3xl shadow-xl p-10 text-white transform transition-all hover:scale-[1.01] duration-500">
                <!-- Efek Bercahaya di Belakang -->
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-48 h-48 bg-blue-300 opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <p class="text-slate-300 text-sm font-semibold tracking-wider uppercase mb-2">Pusat Kendali Sistem Sekolah</p>
                        <h2 class="text-4xl font-extrabold mb-3 drop-shadow-md">Selamat Datang, {{ auth()->user()->name }}! 🚀</h2>
                        <p class="text-slate-100 text-base max-w-2xl leading-relaxed">
                            Pantau dan kelola seluruh aktivitas akademik, kepegawaian, dan kesiswaan secara *real-time*. Keputusan strategis dan pengawasan sistem sekolah Anda bermula dari sini.
                        </p>
                    </div>
                    <!-- Ikon Besar Eksekutif di Kanan -->
                    <div class="hidden md:flex items-center justify-center w-32 h-32 bg-white/10 backdrop-blur-md rounded-2xl border border-white/10 shadow-inner">
                        <span class="text-6xl filter drop-shadow-lg animate-pulse" style="animation-duration: 3.5s;">👑</span>
                    </div>
                </div>
            </div>

            <!-- 4 Kotak Statistik Utama dengan Hover Animasi Merayap -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1: Siswa -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-slate-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-400 to-cyan-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform duration-300">🎓</div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Siswa Aktif</p>
                            <h4 class="text-3xl font-black text-slate-800 mt-1">{{ $data['totalSiswa'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pegawai -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-slate-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-green-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform duration-300">👔</div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Total Pegawai</p>
                            <h4 class="text-3xl font-black text-slate-800 mt-1">{{ $data['totalPegawai'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Kelas -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-slate-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-amber-400 to-orange-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform duration-300">🏫</div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Rombel Kelas</p>
                            <h4 class="text-3xl font-black text-slate-800 mt-1">{{ $data['totalKelas'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Ekskul -->
                <div class="group bg-white rounded-3xl p-6 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-slate-100 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-purple-400 to-fuchsia-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-50 to-fuchsia-50 flex items-center justify-center text-3xl shadow-inner group-hover:scale-110 transition-transform duration-300">🏆</div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide">Ekskul Aktif</p>
                            <h4 class="text-3xl font-black text-slate-800 mt-1">{{ $data['totalEkskul'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Area Bawah: Fitur Jalan Pintas Admin (Dirombak Menjadi Interaktif) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 text-white flex items-center justify-center text-xl">⚡</div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Akses Cepat Administrator</h3>
                            <p class="text-sm text-slate-500 font-medium">Jalan pintas ke modul-modul pengaturan master yang kritikal.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    
                    <!-- Shortcut 1: Pengguna -->
                    <a href="{{ route('master.user.index') }}" class="group flex flex-col items-center gap-3 p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-800 hover:border-slate-800 hover:shadow-lg transition-all duration-300">
                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">📝</div>
                        <span class="text-sm font-extrabold text-slate-700 group-hover:text-white transition-colors">Kelola Pengguna</span>
                    </a>
                    
                    <!-- Shortcut 2: Hak Akses -->
                    <a href="{{ route('master.permission.index') }}" class="group flex flex-col items-center gap-3 p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-800 hover:border-slate-800 hover:shadow-lg transition-all duration-300">
                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">🛡️</div>
                        <span class="text-sm font-extrabold text-slate-700 group-hover:text-white transition-colors">Hak Akses</span>
                    </a>
                    
                    <!-- Shortcut 3: Profil Sekolah -->
                    <a href="{{ route('master.profil-sekolah.index') }}" class="group flex flex-col items-center gap-3 p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-800 hover:border-slate-800 hover:shadow-lg transition-all duration-300">
                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">⚙️</div>
                        <span class="text-sm font-extrabold text-slate-700 group-hover:text-white transition-colors">Profil Sekolah</span>
                    </a>
                    
                    <!-- Shortcut 4: Data Siswa -->
                    <a href="{{ route('kesiswaan.siswa') }}" class="group flex flex-col items-center gap-3 p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-800 hover:border-slate-800 hover:shadow-lg transition-all duration-300">
                        <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">🎓</div>
                        <span class="text-sm font-extrabold text-slate-700 group-hover:text-white transition-colors">Data Siswa Induk</span>
                    </a>
                    
                </div>
            </div>

        </div>
    </div>
</x-app-layout>