<x-guest-layout>
    <div class="min-h-[85vh] flex flex-col justify-center items-center px-4 w-full max-w-md mx-auto relative z-10">
        
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
                    Verifikasi Akun
                </h2>
                <div class="flex items-center justify-center gap-2 mt-1">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">
                        SIAS Validasi Alamat Email
                    </p>
                </div>
            </div>

            <div class="mb-6 flex items-start gap-3 bg-indigo-50/80 backdrop-blur-sm p-4 rounded-2xl border border-indigo-200/60 shadow-inner">
                <span class="text-indigo-500 text-lg mt-0.5">📬</span>
                <p class="text-[11px] text-slate-600 font-medium leading-relaxed">
                    <span class="font-black text-slate-700">Pendaftaran Berhasil!</span> Sebelum bisa mengakses fitur, silakan klik tautan verifikasi yang baru saja dikirim ke kotak masuk email Anda. (Periksa folder Spam jika tidak ada).
                </p>
            </div>

            {{-- Pesan Jika Klik Kirim Ulang Email --}}
            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 rounded-2xl text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 shadow-sm flex items-start gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-200 flex items-center justify-center shrink-0 mt-0.5">🎉</span> 
                    <span>Tautan verifikasi baru telah berhasil dikirimkan ulang ke alamat email Anda.</span>
                </div>
            @endif

            <div class="space-y-6">
                {{-- Form 1: Kirim Ulang --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer">
                        <i class="fa-regular fa-paper-plane mr-2"></i> Kirim Ulang Tautan Verifikasi
                    </button>
                </form>

                {{-- Form 2: Logout Bawah --}}
                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf
                    <button type="submit" 
                            class="text-[11px] font-bold text-slate-400 hover:text-rose-500 border-b border-dashed border-slate-300 hover:border-rose-400 pb-0.5 transition-colors focus:outline-none uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Keluar / Gunakan Akun Lain
                    </button>
                </form>
            </div>

            <div class="mt-8 pt-5 border-t border-slate-100 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Pusat Bantuan SIAS
                </p>
                <p class="text-[11px] font-medium text-slate-500 mt-1">
                    Hubungi Sekretariat Utama Sekolah jika Anda mengalami kendala.
                </p>
            </div>

        </div>
    </div>
</x-guest-layout>