<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Konfirmasi Kata Sandi</h2>
        <p class="text-sm text-gray-600 mt-2">
            Ini adalah area aman di dalam sistem sekolah. Silakan masukkan kembali kata sandi Anda sebelum melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="Kata Sandi / Password Anda" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-6">
            <x-primary-button class="w-full justify-center">
                Konfirmasi & Lanjutkan
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>