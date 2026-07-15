<x-guest-layout>
    <div class="min-h-[85vh] flex flex-col justify-center items-center px-4 w-full max-w-md mx-auto relative z-10">
        
        {{-- Kotak Utama: Glassmorphism Tembus Pandang --}}
        <div class="w-full bg-white/80 backdrop-blur-2xl p-8 md:p-10 rounded-[2.5rem] border border-white/60 shadow-2xl shadow-indigo-500/10 transition-all duration-300">
            
            <div class="mb-6 text-center">
                <div class="flex items-center justify-center gap-5 mb-5 relative">
                    {{-- Efek Cahaya di Balik Logo --}}
                    <div class="absolute inset-0 bg-indigo-400/20 blur-2xl rounded-full"></div>
                    
                    @if(!empty($logoSetting->logo_pemda))
                        <img src="{{ asset('storage/' . $logoSetting->logo_pemda) }}" 
                             class="w-16 h-16 object-contain relative z-10 drop-shadow-md" 
                             alt="Logo Pemda">
                    @endif

                    <a href="/" class="transition-transform hover:scale-110 inline-block relative z-10">
                        <img src="{{ !empty($logoSetting->logo_sekolah) ? asset('storage/' . $logoSetting->logo_sekolah) : asset('images/default-logo.png') }}" 
                             class="w-16 h-16 object-contain drop-shadow-md" 
                             alt="Logo Sekolah">
                    </a>
                </div>
                
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">
                    Konfirmasi Keamanan
                </h2>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">
                        Akses Area Sensitif
                    </p>
                </div>
            </div>

            <div class="mb-6 flex items-start gap-3 bg-amber-50/80 backdrop-blur-sm p-4 rounded-2xl border border-amber-200/50 shadow-inner">
                <span class="text-amber-500 text-lg mt-0.5">🛡️</span>
                <p class="text-[11px] text-amber-700 font-medium leading-relaxed">
                    {{ __('Demi menjaga keamanan data SIAS, silakan masukkan kembali kata sandi (password) Anda sebelum melanjutkan tindakan ini.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" x-data="{ showPassword: false }" class="space-y-5">
                @csrf

                <div>
                    <label for="password" class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5">
                        Masukkan Kata Sandi
                    </label>
                    <div class="relative rounded-xl shadow-sm group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <input id="password" 
                               :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••••••"
                               class="block w-full pl-10 pr-10 py-3 text-sm rounded-xl border-slate-200 text-slate-900 placeholder-slate-400 bg-white/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium" />
                        
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-sm text-slate-400 hover:text-indigo-600 focus:outline-none transition-colors"
                                title="Intip Kata Sandi">
                            <i class="fa-regular fa-eye-slash" x-show="!showPassword"></i>
                            <i class="fa-regular fa-eye" x-show="showPassword" style="display: none;"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-[11px] font-bold text-rose-500" />
                </div>

                <div class="pt-3">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer">
                        Konfirmasi Otorisasi <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-5 border-t border-slate-100 text-center">
                <a href="{{ url('/dashboard') }}" class="text-[11px] font-bold text-slate-400 hover:text-indigo-600 transition-colors inline-flex items-center gap-1.5 uppercase tracking-wider group">
                    <i class="fa-solid fa-arrow-left-long group-hover:-translate-x-1 transition-transform"></i> Kembali ke Dashboard
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>