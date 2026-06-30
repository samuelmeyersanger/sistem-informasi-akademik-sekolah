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
                    Konfirmasi Kata Sandi
                </h2>
                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mt-0.5">
                    Area Keamanan Sistem
                </p>
            </div>

            <div class="mb-5 text-xs text-gray-500 text-center bg-amber-50/60 p-3 rounded-xl border border-amber-100/70 leading-relaxed">
                🛡️ {{ __('Ini adalah area aman di dalam sistem sekolah. Silakan masukkan kembali kata sandi Anda sebelum melanjutkan tindakan.') }}
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" x-data TYPE="{ showPassword: false }" class="space-y-4">
                @csrf

                <div>
                    <label for="password" class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Kata Sandi / Password Anda
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">
                            🔑
                        </div>
                        <input id="password" 
                               :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="block w-full pl-9 pr-10 py-2 text-sm rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-150" />
                        
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors"
                                title="Lihat Kata Sandi">
                            <span x-show="!showPassword">👁️</span>
                            <span x-show="showPassword" style="display: none;">🔒</span>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs font-medium text-rose-600" />
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 transform active:scale-[0.99] cursor-pointer">
                        Konfirmasi & Lanjutkan ➔
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <a href="{{ url('/dashboard') }}" class="text-xs font-medium text-gray-400 hover:text-indigo-600 transition-colors inline-flex items-center gap-1">
                    ⬅ Batalkan & Kembali ke Dashboard
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>