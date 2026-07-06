<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Utama') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-10 flex flex-col items-center justify-center text-center">
                    
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 shadow-inner border border-gray-100">
                        <span class="text-4xl">🔒</span>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Akses Tampilan Terbatas</h2>
                    <p class="text-gray-500 mb-8 max-w-md">
                        Halo <b>{{ auth()->user()->name }}</b>, akun Anda saat ini belum dihubungkan dengan hak akses spesifik (Admin, Guru, atau Siswa). 
                    </p>

                    <div class="bg-amber-50 text-amber-700 px-6 py-4 rounded-xl border border-amber-200 text-sm font-semibold">
                        Mohon hubungi Administrator / Tata Usaha sekolah untuk mengatur Role Anda di menu Manajemen Hak Akses.
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>