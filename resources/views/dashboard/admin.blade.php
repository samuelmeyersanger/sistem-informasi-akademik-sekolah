<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pimpinan & Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl shadow-xl p-8 text-white flex flex-col sm:flex-row justify-between items-center relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold mb-2">Pusat Kendali Sistem Sekolah 🚀</h2>
                    <p class="text-slate-300 text-sm">Pantau aktivitas akademik, kepegawaian, dan kesiswaan secara real-time.</p>
                </div>
                <!-- Dekorasi Background -->
                <div class="absolute right-0 top-0 opacity-10">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13.5h-13L12 6.5z"/></svg>
                </div>
            </div>

            <!-- 4 Kotak Data dari Controller -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card Siswa -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl shadow-inner border border-blue-100">🎓</div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Siswa Aktif</p>
                        <h4 class="text-3xl font-black text-slate-800">{{ $data['totalSiswa'] ?? 0 }}</h4>
                    </div>
                </div>

                <!-- Card Pegawai -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl shadow-inner border border-emerald-100">👔</div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pegawai</p>
                        <h4 class="text-3xl font-black text-slate-800">{{ $data['totalPegawai'] ?? 0 }}</h4>
                    </div>
                </div>

                <!-- Card Kelas -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-3xl shadow-inner border border-amber-100">🏫</div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rombongan Belajar</p>
                        <h4 class="text-3xl font-black text-slate-800">{{ $data['totalKelas'] ?? 0 }}</h4>
                    </div>
                </div>

                <!-- Card Ekskul -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-3xl shadow-inner border border-purple-100">🏆</div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ekstrakurikuler</p>
                        <h4 class="text-3xl font-black text-slate-800">{{ $data['totalEkskul'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>

            <!-- Fitur Jalan Pintas Admin -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Akses Cepat Administrator</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="#" class="p-4 rounded-xl border border-dashed border-slate-200 hover:bg-slate-50 transition-colors text-center text-sm font-semibold text-slate-600 flex flex-col items-center gap-2">
                        <span class="text-2xl">📝</span> Kelola Pengguna
                    </a>
                    <a href="#" class="p-4 rounded-xl border border-dashed border-slate-200 hover:bg-slate-50 transition-colors text-center text-sm font-semibold text-slate-600 flex flex-col items-center gap-2">
                        <span class="text-2xl">🛡️</span> Hak Akses
                    </a>
                    <a href="#" class="p-4 rounded-xl border border-dashed border-slate-200 hover:bg-slate-50 transition-colors text-center text-sm font-semibold text-slate-600 flex flex-col items-center gap-2">
                        <span class="text-2xl">⚙️</span> Pengaturan Web
                    </a>
                    <a href="#" class="p-4 rounded-xl border border-dashed border-slate-200 hover:bg-slate-50 transition-colors text-center text-sm font-semibold text-slate-600 flex flex-col items-center gap-2">
                        <span class="text-2xl">📊</span> Laporan Akademik
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>