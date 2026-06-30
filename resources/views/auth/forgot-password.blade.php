<x-guest-layout>
    <div class="min-h-[85vh] flex flex-col justify-center items-center px-4 w-full max-w-md mx-auto">
        
        <div class="w-full bg-white p-6 md:p-8 rounded-2xl border border-gray-100 shadow-xl shadow-gray-100/70">
            
            <div class="mb-5 text-center">
                <div class="flex items-center justify-center gap-4 mb-4">
                    @if(!empty($logoSetting->logo_pemda))
                        <img src="{{ asset('storage/' . $logoSetting->logo_pemda) }}" 
                             class="w-14 h-14 object-contain" 
                             alt="Logo Pemda">
                    @endif

                    <a href="/" class="transition-transform hover:scale-105 inline-block">
                        <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                             class="w-16 h-16 object-contain" 
                             alt="Logo Sekolah">
                    </a>
                </div>
                
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">
                    Pemulihan Akun
                </h2>
                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mt-0.5">
                    SIAS Lupa Kata Sandi
                </p>
            </div>

            <div class="mb-5 text-xs text-gray-500 text-center bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed">
                {{ __('Lupa kata sandi Anda? Tidak masalah. Cukup masukkan alamat email Anda yang terdaftar, dan kami akan mengirimkan tautan (link) pemulihan password secara otomatis.') }}
            </div>

            @if (session('status'))
                <div class="mb-4 p-3 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                    ✅ {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Email Resmi Terdaftar
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">
                            📧
                        </div>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               placeholder="nama@sekolah.sch.id"
                               class="block w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-150" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs font-medium text-rose-600" />
                </div>

                <div class="pt-1">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 transform active:scale-[0.99] cursor-pointer">
                        ✉️ {{ __('Kirim Link Reset Password') }}
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center flex justify-between items-center px-1">
                <a href="{{ route('login') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                    ↩ Kembali ke Login
                </a>
                <a href="/" class="text-xs font-medium text-gray-400 hover:text-gray-600 transition-colors">
                    Beranda Utama
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>