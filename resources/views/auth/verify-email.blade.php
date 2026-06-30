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
                    Verifikasi Email
                </h2>
                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mt-0.5">
                    SIAS Validasi Alamat Akun
                </p>
            </div>

            <div class="mb-5 text-xs text-gray-500 text-center bg-indigo-50/50 p-4 rounded-xl border border-indigo-100/40 leading-relaxed">
                📬 <span class="font-medium text-gray-700">Terima kasih telah mendaftar!</span> Sebelum memulai, silakan klik tautan verifikasi yang baru saja kami kirimkan ke email Anda. Jika tidak menerima email tersebut, kami dengan senang hati akan mengirimkannya ulang.
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-5 p-3 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 leading-normal text-center">
                    🎉 Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda gunakan saat mendaftar.
                </div>
            @endif

            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 transform active:scale-[0.99] cursor-pointer">
                        ✉️ Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="text-center pt-2">
                    @csrf
                    <button type="submit" 
                            class="text-xs font-semibold text-gray-400 hover:text-rose-600 border-b border-dashed border-gray-300 hover:border-rose-500 pb-0.5 transition-colors focus:outline-none">
                        🚪 Keluar / Ganti Akun
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <p class="text-[11px] text-gray-400">
                    Butuh bantuan? Hubungi Sekretariat Utama Sekolah.
                </p>
            </div>

        </div>
    </div>
</x-guest-layout>