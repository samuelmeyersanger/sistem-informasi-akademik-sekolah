<section class="relative">
    <!-- Header Form yang lebih elegan dengan Ikon Kunci -->
    <header class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
        <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner border border-rose-100">
            🔐
        </div>
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">
                {{ __('Ubah Kata Sandi') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 font-medium">
                {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman dari peretas.') }}
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <!-- Kotak Input Password Saat Ini (Atas - Full Width) -->
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 transition-all duration-300 focus-within:ring-4 focus-within:ring-rose-500/10 focus-within:border-rose-300 focus-within:bg-white shadow-sm hover:shadow-md">
            <x-input-label for="update_password_current_password" :value="__('Kata Sandi Saat Ini')" class="font-bold text-gray-700 text-sm uppercase tracking-wider mb-2" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition-all" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 font-semibold" />
        </div>

        <!-- Layout 2 Kolom untuk Password Baru & Konfirmasi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Kotak Input Password Baru -->
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 transition-all duration-300 focus-within:ring-4 focus-within:ring-rose-500/10 focus-within:border-rose-300 focus-within:bg-white shadow-sm hover:shadow-md">
                <x-input-label for="update_password_password" :value="__('Kata Sandi Baru')" class="font-bold text-gray-700 text-sm uppercase tracking-wider mb-2" />
                <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition-all" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 font-semibold" />
            </div>

            <!-- Kotak Konfirmasi Password Baru -->
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 transition-all duration-300 focus-within:ring-4 focus-within:ring-rose-500/10 focus-within:border-rose-300 focus-within:bg-white shadow-sm hover:shadow-md">
                <x-input-label for="update_password_password_confirmation" :value="__('Ketik Ulang Sandi Baru')" class="font-bold text-gray-700 text-sm uppercase tracking-wider mb-2" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition-all" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 font-semibold" />
            </div>
            
        </div>

        <!-- Area Tombol Simpan -->
        <div class="flex items-center gap-5 pt-4">
            
            <!-- Tombol Custom Gelap untuk Otoritas Keamanan -->
            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300">
                🔑 {{ __('Perbarui Kata Sandi') }}
            </button>

            @if (session('status') === 'password-updated')
                <!-- Notifikasi "Tersimpan" dengan Animasi -->
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-8"
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-bold text-emerald-700 flex items-center gap-2 bg-emerald-50 px-5 py-2.5 rounded-xl border border-emerald-200 shadow-sm"
                >
                    <span class="text-lg">✅</span> {{ __('Sandi Anda Berhasil Diubah!') }}
                </p>
            @endif
        </div>
    </form>
</section>