<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Tenaga Kependidikan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-gradient-to-r from-teal-500 to-teal-700 rounded-2xl shadow-lg p-8 text-white">
                <h2 class="text-3xl font-bold mb-2">Halo, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-teal-100 text-sm">Selamat mengelola data administrasi, kesiswaan, dan kepegawaian hari ini.</p>
            </div>

            <!-- Modul Operasional TU -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kesiswaan -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">🧑‍🤝‍🧑</div>
                        <h3 class="font-bold text-gray-800">Manajemen Kesiswaan</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2 hover:text-teal-600 cursor-pointer">➔ Data Induk Siswa</li>
                        <li class="flex items-center gap-2 hover:text-teal-600 cursor-pointer">➔ Pindah & Mutasi</li>
                        <li class="flex items-center gap-2 hover:text-teal-600 cursor-pointer">➔ Rekap Absensi</li>
                    </ul>
                </div>

                <!-- Kepegawaian -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">💼</div>
                        <h3 class="font-bold text-gray-800">Administrasi Pegawai</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2 hover:text-teal-600 cursor-pointer">➔ Data Guru & Staf</li>
                        <li class="flex items-center gap-2 hover:text-teal-600 cursor-pointer">➔ Riwayat Kepangkatan</li>
                        <li class="flex items-center gap-2 hover:text-teal-600 cursor-pointer">➔ Dokumen & Arsip</li>
                    </ul>
                </div>

                <!-- Persuratan -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">✉️</div>
                        <h3 class="font-bold text-gray-800">Persuratan (E-Office)</h3>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2 hover:text-teal-600 cursor-pointer">➔ Agenda Surat Masuk</li>
                        <li class="flex items-center gap-2 hover:text-teal-600 cursor-pointer">➔ Agenda Surat Keluar</li>
                        <li class="flex items-center gap-2 hover:text-teal-600 cursor-pointer">➔ Klasifikasi Surat</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>