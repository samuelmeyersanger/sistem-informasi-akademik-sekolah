<x-guest-layout>
    <div class="min-h-screen py-8 flex flex-col justify-center items-center px-4 w-full max-w-md mx-auto">
        
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
                    Pendaftaran Akun
                </h2>
                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mt-0.5">
                    SIAS Pembuatan Akun Baru
                </p>
                <p class="text-xs text-gray-500 mt-1.5 max-w-xs mx-auto leading-relaxed">
                    Silakan isi formulir di bawah ini untuk membuat akun pendaftaran sekolah.
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" x-data="{ showPass: false, showConfirmPass: false }" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nama Lengkap
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">
                            👤
                        </div>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autofocus 
                               autocomplete="name"
                               placeholder="Nama lengkap Anda..."
                               class="block w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-150" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs font-medium text-rose-600" />
                </div>

                <div>
                    <label for="email" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Alamat Email Aktif
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
                               autocomplete="username"
                               placeholder="nama@email.com"
                               class="block w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-150" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs font-medium text-rose-600" />
                </div>

                <div>
                    <label for="password" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Kata Sandi (Minimal 8 Karakter)
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">
                            🔑
                        </div>
                        <input id="password" 
                               :type="showPass ? 'text' : 'password'" 
                               name="password" 
                               required 
                               autocomplete="new-password"
                               placeholder="••••••••"
                               class="block w-full pl-9 pr-10 py-2 text-sm rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-150" />
                        
                        <button type="button" 
                                @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors">
                            <span x-show="!showPass">👁️</span>
                            <span x-show="showPass" style="display: none;">🔒</span>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs font-medium text-rose-600" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Ulangi Kata Sandi
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">
                            🔄
                        </div>
                        <input id="password_confirmation" 
                               :type="showConfirmPass ? 'text' : 'password'" 
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               placeholder="••••••••"
                               class="block w-full pl-9 pr-10 py-2 text-sm rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-150" />
                        
                        <button type="button" 
                                @click="showConfirmPass = !showConfirmPass"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors">
                            <span x-show="!showConfirmPass">👁️</span>
                            <span x-show="showConfirmPass" style="display: none;">🔒</span>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs font-medium text-rose-600" />
                </div>

                <div class="pt-3 space-y-3">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 transform active:scale-[0.99] cursor-pointer">
                        Daftar Sekarang ➔
                    </button>

                    <div class="text-center pt-1">
                        <a class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 hover:underline transition-colors" 
                           href="{{ route('login') }}">
                            Sudah punya akun? Login di sini
                        </a>
                    </div>
                </div>
            </form>

            <div class="mt-5 pt-3 border-t border-gray-100 text-center">
                <a href="/" class="text-xs font-medium text-gray-400 hover:text-indigo-600 transition-colors inline-flex items-center gap-1">
                    ⬅ Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>