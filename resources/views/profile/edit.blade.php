<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-indigo-600 text-3xl">⚙️</span> 
            {{ __('Pengaturan Akun') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen font-sans">
        <!-- Memperlebar wadah utama menjadi max-w-5xl agar kolom form bisa memanjang dengan cantik -->
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <!-- Kotak 1: Update Profil -->
            <div class="p-8 sm:p-10 bg-white shadow-xl sm:rounded-3xl border border-gray-100 relative overflow-hidden transform transition-all duration-300 hover:shadow-2xl">
                <!-- Ornamen Garis Biru/Ungu -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                
                <div class="w-full">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Kotak 2: Update Password -->
            <div class="p-8 sm:p-10 bg-white shadow-xl sm:rounded-3xl border border-gray-100 relative overflow-hidden transform transition-all duration-300 hover:shadow-2xl">
                <!-- Ornamen Garis Merah/Pink -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-rose-500 to-pink-500"></div>
                
                <div class="w-full">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>