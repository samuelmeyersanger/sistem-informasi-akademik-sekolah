<x-guest-layout>
    <div class="min-h-screen py-8 flex flex-col justify-center items-center px-4 w-full max-w-md mx-auto relative z-10">
        
        {{-- Kotak Utama: Glassmorphism Tembus Pandang --}}
        <div class="w-full bg-white/80 backdrop-blur-2xl p-8 md:p-10 rounded-[2.5rem] border border-white/60 shadow-2xl shadow-indigo-500/10 transition-all duration-300">
            
            <div class="mb-8 text-center">
                <div class="flex items-center justify-center gap-5 mb-5 relative">
                    {{-- Efek Cahaya di Balik Logo --}}
                    <div class="absolute inset-0 bg-indigo-500/20 blur-2xl rounded-full"></div>
                    
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
                    Kata Sandi Baru
                </h2>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">
                        Pemulihan Akses SIAS
                    </p>
                </div>
                
                <p class="text-xs text-slate-500 mt-3 max-w-xs mx-auto leading-relaxed font-medium">
                    Ketikkan kata sandi (password) baru yang kuat dan unik untuk melindungi akun Anda dari akses tidak sah.
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" x-data="{ showNewPass: false, showConfirmNewPass: false }" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Input Email --}}
                <div>
                    <label for="email" class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5">
                        Alamat Email Anda
                    </label>
                    <div class="relative rounded-xl shadow-sm group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email', $request->email) }}" 
                               required 
                               autocomplete="username"
                               placeholder="nama@sekolah.sch.id"
                               class="block w-full pl-10 pr-4 py-3 text-sm rounded-xl border-slate-200 text-slate-900 placeholder-slate-400 bg-white/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-[11px] font-bold text-rose-500" />
                </div>

                {{-- Input Password Baru --}}
                <div>
                    <label for="password" class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5">
                        Kata Sandi Baru
                    </label>
                    <div class="relative rounded-xl shadow-sm group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input id="password" 
                               :type="showNewPass ? 'text' : 'password'" 
                               name="password" 
                               required 
                               autofocus
                               autocomplete="new-password"
                               placeholder="Min. 8 Karakter"
                               class="block w-full pl-10 pr-10 py-3 text-sm rounded-xl border-slate-200 text-slate-900 placeholder-slate-400 bg-white/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium" />
                        
                        <button type="button" 
                                @click="showNewPass = !showNewPass"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-sm text-slate-400 hover:text-indigo-600 focus:outline-none transition-colors"
                                title="Intip Kata Sandi">
                            <i class="fa-regular fa-eye-slash" x-show="!showNewPass"></i>
                            <i class="fa-regular fa-eye" x-show="showNewPass" style="display: none;"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-[11px] font-bold text-rose-500" />
                </div>

                {{-- Input Konfirmasi Password Baru --}}
                <div>
                    <label for="password_confirmation" class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5">
                        Ulangi Kata Sandi Baru
                    </label>
                    <div class="relative rounded-xl shadow-sm group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-lock-open"></i>
                        </div>
                        <input id="password_confirmation" 
                               :type="showConfirmNewPass ? 'text' : 'password'" 
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               placeholder="Ketik ulang password"
                               class="block w-full pl-10 pr-10 py-3 text-sm rounded-xl border-slate-200 text-slate-900 placeholder-slate-400 bg-white/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium" />
                        
                        <button type="button" 
                                @click="showConfirmNewPass = !showConfirmNewPass"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-sm text-slate-400 hover:text-indigo-600 focus:outline-none transition-colors"
                                title="Intip Kata Sandi">
                            <i class="fa-regular fa-eye-slash" x-show="!showConfirmNewPass"></i>
                            <i class="fa-regular fa-eye" x-show="showConfirmNewPass" style="display: none;"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-[11px] font-bold text-rose-500" />
                </div>

                {{-- Tombol Submit --}}
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Perbarui Kata Sandi
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-5 border-t border-slate-100 text-center">
                <a href="{{ route('login') }}" class="text-[11px] font-bold text-slate-400 hover:text-indigo-600 transition-colors inline-flex items-center gap-1.5 uppercase tracking-wider group">
                    <i class="fa-solid fa-arrow-left-long group-hover:-translate-x-1 transition-transform"></i> Batal & Kembali ke Login
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>