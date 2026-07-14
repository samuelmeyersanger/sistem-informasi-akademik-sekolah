<section class="relative">
    <!-- Header Form yang lebih elegan dengan Ikon -->
    <header class="flex items-center gap-4 mb-6 pb-5 border-b border-gray-100">
        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner border border-indigo-100">
            👤
        </div>
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">
                {{ __('Informasi Profil') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 font-medium">
                {{ __("Perbarui nama, alamat email, dan identitas dasar akun Anda di sini.") }}
            </p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Kotak Input Nama -->
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 transition-all duration-300 focus-within:ring-4 focus-within:ring-indigo-500/10 focus-within:border-indigo-300 focus-within:bg-white shadow-sm hover:shadow-md">
            <x-input-label for="name" :value="__('Nama Lengkap')" class="font-bold text-gray-700 text-sm uppercase tracking-wider mb-2" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 font-semibold" :messages="$errors->get('name')" />
        </div>

        <!-- Kotak Input Email -->
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 transition-all duration-300 focus-within:ring-4 focus-within:ring-indigo-500/10 focus-within:border-indigo-300 focus-within:bg-white shadow-sm hover:shadow-md">
            <x-input-label for="email" :value="__('Alamat Email')" class="font-bold text-gray-700 text-sm uppercase tracking-wider mb-2" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 font-semibold" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-5 p-4 bg-amber-50 rounded-xl border border-amber-200 shadow-inner">
                    <p class="text-sm font-semibold text-amber-800 flex items-center gap-2">
                        <span class="text-lg">⚠️</span> {{ __('Alamat email Anda belum diverifikasi.') }}
                    </p>
                    <button form="send-verification" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-800 text-xs font-bold rounded-lg transition-colors border border-amber-300">
                        ✉️ {{ __('Kirim Ulang Email Verifikasi') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-4 font-bold text-sm text-emerald-600 flex items-center gap-2 bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                            <span class="text-lg">✅</span> {{ __('Link verifikasi baru telah dikirim ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Area Tombol Simpan -->
        <div class="flex items-center gap-5 pt-4">
            
            <!-- Tombol Custom Lebih Keren dari Tombol Bawaan -->
            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all duration-300">
                💾 {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <!-- Notifikasi "Tersimpan" dengan Animasi Cantik -->
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
                    <span class="text-lg">✅</span> {{ __('Data Berhasil Disimpan!') }}
                </p>
            @endif
        </div>
    </form>
</section>