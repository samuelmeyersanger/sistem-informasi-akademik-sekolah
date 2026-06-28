<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kesalahan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-600">
                <div class="p-8 text-center">
                    <div class="mb-4 text-red-600">
                        <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h1 class="text-6xl font-extrabold text-red-600 mb-2">500</h1>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Terjadi Kesalahan Internal</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Maaf, server kami mendeteksi adanya gangguan teknis tak terduga. Tim pengembang sistem sekolah telah dinotifikasi otomatis.
                    </p>
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 shadow-sm transition ease-in-out duration-150">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>