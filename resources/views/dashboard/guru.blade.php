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

            <!-- Tampilan Kosong untuk Jadwal (Bisa disesuaikan nanti) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Jadwal Hari Ini</h3>
                <div class="flex flex-col items-center justify-center py-12 px-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-gray-500 text-sm">Modul Jadwal Hari Ini akan segera ditampilkan di sini.</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>