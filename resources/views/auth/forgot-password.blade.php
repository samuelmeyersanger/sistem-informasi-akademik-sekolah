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
                    Pemulihan Akun
                </h2>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">
                        SIAS Lupa Kata Sandi
                    </p>
                </div>
            </div>

            <div class="mb-6 flex items-start gap-3 bg-slate-50/80 backdrop-blur-sm p-4 rounded-2xl border border-slate-200/60 shadow-inner">
                <span class="text-slate-500 text-lg mt-0.5">ℹ️</span>
                <p class="text-[11px] text-slate-600 font-medium leading-relaxed">
                    {{ __('Lupa kata sandi Anda? Tidak masalah. Cukup masukkan alamat email Anda yang terdaftar, dan kami akan mengirimkan tautan (link) pemulihan secara otomatis.') }}
                </p>
            </div>

            {{-- Pesan Sukses (Bila Link Berhasil Terkirim) --}}
            @if (session('status'))
                <div class="mb-6 p-4 rounded-2xl text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 shadow-sm flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-200 flex items-center justify-center shrink-0">✅</span> 
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5">
                        Email Resmi Terdaftar
                    </label>
                    <div class="relative rounded-xl shadow-sm group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm group-focus-within:text-indigo-500 transition-colors">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               placeholder="nama@sekolah.sch.id"
                               class="block w-full pl-10 pr-4 py-3 text-sm rounded-xl border-slate-200 text-slate-900 placeholder-slate-400 bg-white/50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-[11px] font-bold text-rose-500" />
                </div>

                <div class="pt-3">
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer">
                        <i class="fa-regular fa-paper-plane mr-2"></i> {{ __('Kirim Link Reset Password') }}
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-5 border-t border-slate-100 flex justify-between items-center px-1">
                <a href="{{ route('login') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 transition-colors inline-flex items-center gap-1.5 uppercase tracking-wider group">
                    <i class="fa-solid fa-arrow-left-long group-hover:-translate-x-1 transition-transform"></i> Login
                </a>
                <a href="/" class="text-[10px] font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">
                    Beranda
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>