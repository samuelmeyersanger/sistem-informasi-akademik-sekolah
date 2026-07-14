<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-gray-500 text-3xl">🛡️</span> 
            {{ __('Dashboard Utama') }}
        </h2>
    </x-slot>

    <!-- Menambahkan flex agar box persis di tengah layar -->
    <div class="py-12 bg-slate-50 min-h-screen font-sans flex flex-col pt-20">
        <div class="max-w-2xl w-full mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100 relative group">
                <!-- Efek Garis Atas (Nuansa Abu-abu netral) -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-gray-300 via-slate-400 to-gray-300"></div>
                
                <div class="p-12 flex flex-col items-center justify-center text-center">
                    
                    <!-- Ikon Gembok Beranimasi Halus -->
                    <div class="relative w-28 h-28 bg-gradient-to-br from-gray-50 to-gray-100 rounded-full flex items-center justify-center mb-8 shadow-inner border border-gray-200 group-hover:shadow-md transition-all duration-300">
                        <!-- Efek gelombang/denyut di belakang gembok -->
                        <div class="absolute inset-0 rounded-full bg-gray-200 opacity-30 animate-ping" style="animation-duration: 3s;"></div>
                        <span class="text-5xl drop-shadow-sm group-hover:scale-110 transition-transform duration-300 relative z-10">🔒</span>
                    </div>

                    <h2 class="text-3xl font-extrabold text-gray-800 mb-3 tracking-tight">Akses Tampilan Terbatas</h2>
                    <p class="text-gray-500 mb-8 max-w-md leading-relaxed text-sm">
                        Halo <b class="text-gray-800">{{ auth()->user()->name }}</b>, sepertinya akun Anda saat ini belum diberikan wewenang hak akses yang spesifik (sebagai Admin, Guru, Staf, atau Siswa). 
                    </p>

                    <!-- Alert Box Peringatan -->
                    <div class="bg-amber-50 w-full text-amber-700 px-6 py-5 rounded-2xl border border-amber-200 shadow-sm flex items-start gap-4 text-left mb-8">
                        <div class="text-2xl mt-0.5">⚠️</div>
                        <div>
                            <h4 class="font-bold text-amber-800 mb-1">Tindakan Diperlukan</h4>
                            <p class="text-xs font-medium text-amber-700/80 leading-relaxed">
                                Mohon segera hubungi Administrator atau staf Tata Usaha sekolah untuk mengatur <i>Role / Hak Akses</i> Anda pada sistem ini agar menu fungsional dapat terbuka.
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Jalan Keluar (Edit Profil) -->
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        <span>⚙️</span> Menuju Pengaturan Akun
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>