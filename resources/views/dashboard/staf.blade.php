<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-teal-600 text-3xl">🏛️</span> 
            {{ __('Dashboard Tenaga Kependidikan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Hero Banner: Modern Glassmorphism -->
            <div class="relative overflow-hidden bg-gradient-to-br from-teal-600 via-emerald-600 to-teal-900 rounded-3xl shadow-xl p-10 text-white transform transition-all hover:scale-[1.01] duration-500">
                <!-- Efek Bercahaya di Belakang -->
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-48 h-48 bg-teal-300 opacity-20 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <p class="text-teal-100 text-sm font-semibold tracking-wider uppercase mb-2">Portal Tata Usaha & Operasional</p>
                        <h2 class="text-4xl font-extrabold mb-3 drop-shadow-md">Halo, {{ auth()->user()->name }}! 👋</h2>
                        <p class="text-emerald-50 text-base max-w-2xl leading-relaxed">
                            Selamat datang di pusat kendali administrasi sekolah. Kelola data induk siswa, manajemen kepegawaian, hingga arsip persuratan dengan lebih terpadu dan efisien hari ini.
                        </p>
                    </div>
                    <!-- Ikon Besar di Kanan -->
                    <div class="hidden md:flex items-center justify-center w-32 h-32 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-inner">
                        <span class="text-6xl filter drop-shadow-lg animate-bounce" style="animation-duration: 3s;">💼</span>
                    </div>
                </div>
            </div>

            <!-- Header Seksi -->
            <div class="mb-4 pt-4 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Modul Operasional Utama</h3>
                    <p class="text-sm text-gray-500">Akses cepat ke berbagai sistem administrasi sekolah</p>
                </div>
            </div>

            <!-- GRID Modul (4 Kolom dengan Efek Hover Garis Warna-Warni) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Card 1: Kesiswaan -->
                <div class="group bg-white rounded-3xl shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <!-- Garis Animasi Hover Biru -->
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-400 to-indigo-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="p-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            🧑‍🤝‍🧑
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Kesiswaan</h3>
                        <p class="text-xs text-gray-500 mb-6 line-clamp-2 h-8">Kelola data induk siswa, mutasi pindah, dan rekapitulasi absensi.</p>
                        
                        <div class="space-y-2">
                            <a href="#" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 text-gray-600 hover:text-blue-700 text-sm font-medium transition-colors">
                                <span class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div> Data Induk Siswa</span>
                                <span>→</span>
                            </a>
                            <a href="#" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50 text-gray-600 hover:text-blue-700 text-sm font-medium transition-colors">
                                <span class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div> Mutasi & Pindah</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Kepegawaian -->
                <div class="group bg-white rounded-3xl shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <!-- Garis Animasi Hover Ungu -->
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-purple-400 to-fuchsia-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="p-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-50 to-fuchsia-50 flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            👨‍🏫
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Kepegawaian</h3>
                        <p class="text-xs text-gray-500 mb-6 line-clamp-2 h-8">Manajemen profil guru, staf, riwayat tugas, dan arsip kepegawaian.</p>
                        
                        <div class="space-y-2">
                            <a href="#" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-purple-50 text-gray-600 hover:text-purple-700 text-sm font-medium transition-colors">
                                <span class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-purple-400"></div> Data Guru & Staf</span>
                                <span>→</span>
                            </a>
                            <a href="#" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-purple-50 text-gray-600 hover:text-purple-700 text-sm font-medium transition-colors">
                                <span class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-purple-400"></div> Riwayat Pangkat</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Persuratan -->
                <div class="group bg-white rounded-3xl shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <!-- Garis Animasi Hover Oren -->
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-amber-400 to-orange-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="p-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            ✉️
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">E-Office Persuratan</h3>
                        <p class="text-xs text-gray-500 mb-6 line-clamp-2 h-8">Pencatatan agenda surat masuk, keluar, dan disposisi pimpinan.</p>
                        
                        <div class="space-y-2">
                            <a href="#" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-amber-50 text-gray-600 hover:text-amber-700 text-sm font-medium transition-colors">
                                <span class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div> Surat Masuk</span>
                                <span>→</span>
                            </a>
                            <a href="#" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-amber-50 text-gray-600 hover:text-amber-700 text-sm font-medium transition-colors">
                                <span class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div> Surat Keluar</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Sarana Prasarana (Bonus) -->
                <div class="group bg-white rounded-3xl shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 ease-out border border-gray-100 overflow-hidden relative">
                    <!-- Garis Animasi Hover Merah -->
                    <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-rose-400 to-red-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                    <div class="p-6">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-50 to-red-50 flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:scale-110 transition-transform duration-300">
                            🏢
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Sarana Prasarana</h3>
                        <p class="text-xs text-gray-500 mb-6 line-clamp-2 h-8">Manajemen aset sekolah, inventaris barang, dan peminjaman.</p>
                        
                        <div class="space-y-2">
                            <a href="#" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-rose-50 text-gray-600 hover:text-rose-700 text-sm font-medium transition-colors">
                                <span class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-rose-400"></div> Inventaris Barang</span>
                                <span>→</span>
                            </a>
                            <a href="#" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-rose-50 text-gray-600 hover:text-rose-700 text-sm font-medium transition-colors">
                                <span class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-rose-400"></div> Peminjaman</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AREA BAWAH: Pengumuman & Akses Cepat -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 pt-4">
                
                <!-- KIRI: Papan Pengumuman Internal -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">📌 Papan Informasi Internal</h3>
                        <span class="text-xs font-bold px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full animate-pulse">Live</span>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Item Pengumuman 1 -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition-colors cursor-default">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 text-teal-500 text-xl">📅</div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Persiapan Validasi Dapodik</h4>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Mohon pastikan seluruh data mutasi siswa dan jadwal guru telah diperbarui sebelum sinkronisasi bulan depan.</p>
                                    <span class="text-[10px] font-bold text-gray-400 mt-2 block">Oleh: Kepala Tata Usaha</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Item Pengumuman 2 -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition-colors cursor-default">
                            <div class="flex items-start gap-4">
                                <div class="mt-1 text-amber-500 text-xl">⚠️</div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">Pembaruan Sistem Persuratan</h4>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Terdapat format baru untuk penomoran surat keluar. Harap merujuk pada pedoman e-office terbaru.</p>
                                    <span class="text-[10px] font-bold text-gray-400 mt-2 block">Oleh: Administrator</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KANAN: Kartu Gelap (Pusat Bantuan / Link Ekstra) -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl shadow-lg p-8 text-white relative overflow-hidden">
                    <!-- Ornamen SVG di Latar Belakang -->
                    <div class="absolute top-0 right-0 opacity-10">
                        <svg class="w-64 h-64 -mt-10 -mr-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.2l7.1 14H4.9L12 6.2z"/></svg>
                    </div>
                    
                    <h3 class="font-bold text-2xl mb-2 relative z-10">Aksi Cepat</h3>
                    <p class="text-slate-400 text-sm mb-8 relative z-10 max-w-sm">Jalan pintas ke fitur-fitur yang paling sering digunakan oleh staf.</p>
                    
                    <div class="space-y-4 relative z-10">
                        <!-- Link ke Pusat Download -->
                        <a href="{{ route('pusat_download.index') }}" class="flex items-center justify-between p-4 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 backdrop-blur-sm transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-teal-500 flex items-center justify-center text-white text-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    🖨️
                                </div>
                                <div>
                                    <div class="font-bold text-sm tracking-wide">Pusat Download Terpadu</div>
                                    <div class="text-xs text-slate-300 mt-0.5">Cetak dokumen, absensi & laporan</div>
                                </div>
                            </div>
                            <span class="text-white bg-white/10 p-2 rounded-lg group-hover:bg-white/30 transition-colors">→</span>
                        </a>
                        
                        <!-- Link ke Profil -->
                        <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-4 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/10 backdrop-blur-sm transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-indigo-500 flex items-center justify-center text-white text-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    ⚙️
                                </div>
                                <div>
                                    <div class="font-bold text-sm tracking-wide">Pengaturan Profil</div>
                                    <div class="text-xs text-slate-300 mt-0.5">Ubah kata sandi & foto profil</div>
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