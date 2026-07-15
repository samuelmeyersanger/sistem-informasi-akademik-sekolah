<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">📇</span> {{ __('Pusat Kontak & Sosial Media') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Kelola informasi komunikasi resmi sekolah yang akan tampil ke publik pada website.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6 relative z-10">
            
            {{-- Notifikasi Keberhasilan --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            {{-- Notifikasi Error Global --}}
            @if($errors->any())
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <div>
                        <div class="mb-1 text-base font-black">Data Ditolak oleh Sistem!</div>
                        <p class="text-xs font-medium text-rose-700 leading-relaxed">
                            Terjadi kesalahan format penulisan pada formulir di bawah. Silakan periksa kolom yang bergaris merah.
                        </p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Kartu --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 bg-white/50 backdrop-blur-sm relative z-10">
                    <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                        <span class="text-indigo-500">⚙️</span> Konfigurasi Saluran Bantuan Publik
                    </h3>
                    <p class="text-sm font-medium text-slate-500 mt-2 leading-relaxed max-w-3xl">
                        Data ini secara otomatis terintegrasi ke dalam sistem aplikasi. Mempengaruhi langsung tampilan di bagian footer website, link navigasi, hingga tombol pintas <em>Live Chat WhatsApp</em> pengunjung.
                    </p>
                </div>

                <form action="{{ route('master.setting-kontak.save') }}" method="POST" class="p-6 md:p-8 space-y-10 relative z-10">
                    @csrf

                    {{-- SEKSI 1: KOMUNIKASI UTAMA --}}
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 font-black text-lg shadow-inner">📞</span>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Saluran Komunikasi Prioritas</h4>
                            <div class="flex-grow h-px bg-slate-200 ml-2"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 sm:p-8 rounded-[1.5rem] border border-slate-100 shadow-sm">
                            
                            {{-- Field: Telepon --}}
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center justify-between">
                                    <span>Nomor Telepon Sekolah</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400">☎️</span>
                                    </div>
                                    <input type="text" name="settings[telepon]" value="{{ old('settings.telepon', $settings['telepon'] ?? '') }}" 
                                           placeholder="(021) 789-1234" 
                                           class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3.5 pl-12 pr-4 bg-white placeholder-slate-300 transition-colors @error('settings.telepon') !border-rose-500 !ring-rose-500/20 bg-rose-50/30 text-rose-700 @enderror">
                                </div>
                                @error('settings.telepon')
                                    <p class="text-[11px] font-bold text-rose-600 mt-1.5 flex items-center gap-1">❌ {{ $message }}</p>
                                @enderror
                            </div>
                            
                            {{-- Field: WhatsApp --}}
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center justify-between">
                                    <span>Pusat Pesan WhatsApp</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400">💬</span>
                                    </div>
                                    <input type="text" name="settings[whatsapp]" value="{{ old('settings.whatsapp', $settings['whatsapp'] ?? '') }}" 
                                           placeholder="628123456789" 
                                           class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3.5 pl-12 pr-4 bg-white placeholder-slate-300 transition-colors font-mono @error('settings.whatsapp') !border-rose-500 !ring-rose-500/20 bg-rose-50/30 text-rose-700 @enderror">
                                </div>
                                <p class="text-[10px] font-bold text-amber-600 mt-2 bg-amber-50 p-2 rounded-lg border border-amber-200/50 flex items-center gap-1.5">
                                    💡 <span>Awali nomor dengan kode negara (cth: <strong>62</strong>). Jangan gunakan tanda + atau angka 0.</span>
                                </p>
                                @error('settings.whatsapp')
                                    <p class="text-[11px] font-bold text-rose-600 mt-1.5 flex items-center gap-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Field: Email --}}
                            <div class="md:col-span-2">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Alamat Email Official</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400">📧</span>
                                    </div>
                                    <input type="email" name="settings[email]" value="{{ old('settings.email', $settings['email'] ?? '') }}" 
                                           placeholder="sekolah@namadomain.sch.id" 
                                           class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3.5 pl-12 pr-4 bg-white placeholder-slate-300 transition-colors @error('settings.email') !border-rose-500 !ring-rose-500/20 bg-rose-50/30 text-rose-700 @enderror">
                                </div>
                                @error('settings.email')
                                    <p class="text-[11px] font-bold text-rose-600 mt-1.5 flex items-center gap-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Field: Alamat Fisik --}}
                            <div class="md:col-span-2">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Alamat Surat Fisik & Tata Usaha</label>
                                <textarea name="settings[alamat]" rows="3" 
                                          placeholder="Jl. Pendidikan Nasional No. 1, Kelurahan XYZ, Kec. ABC, Kota 12345..." 
                                          class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3.5 px-4 bg-white placeholder-slate-300 transition-colors leading-relaxed @error('settings.alamat') !border-rose-500 !ring-rose-500/20 bg-rose-50/30 text-rose-700 @enderror">{{ old('settings.alamat', $settings['alamat'] ?? '') }}</textarea>
                                @error('settings.alamat')
                                    <p class="text-[11px] font-bold text-rose-600 mt-1.5 flex items-center gap-1">❌ {{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SEKSI 2: SOSIAL MEDIA --}}
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600 font-black text-lg shadow-inner">🌐</span>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Jejaring Sosial Media Aktif</h4>
                            <div class="flex-grow h-px bg-slate-200 ml-2"></div>
                        </div>

                        <div class="space-y-5">
                            
                            {{-- Field: Facebook --}}
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                    <span class="text-blue-600">🔵</span> Fanspage Facebook
                                </label>
                                <input type="url" name="settings[facebook]" value="{{ old('settings.facebook', $settings['facebook'] ?? '') }}" 
                                       placeholder="https://facebook.com/..." 
                                       class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 py-3.5 px-4 bg-white placeholder-slate-300 transition-colors @error('settings.facebook') !border-rose-500 !ring-rose-500/20 bg-rose-50/30 text-rose-700 @enderror">
                                @error('settings.facebook')
                                    <p class="text-[11px] font-bold text-rose-600 mt-1.5 flex items-center gap-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Field: Instagram --}}
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                    <span class="text-pink-600">🔴</span> Akun Instagram
                                </label>
                                <input type="url" name="settings[instagram]" value="{{ old('settings.instagram', $settings['instagram'] ?? '') }}" 
                                       placeholder="https://instagram.com/..." 
                                       class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-pink-500/10 focus:border-pink-500 py-3.5 px-4 bg-white placeholder-slate-300 transition-colors @error('settings.instagram') !border-rose-500 !ring-rose-500/20 bg-rose-50/30 text-rose-700 @enderror">
                                @error('settings.instagram')
                                    <p class="text-[11px] font-bold text-rose-600 mt-1.5 flex items-center gap-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Field: YouTube --}}
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                    <span class="text-red-600">▶️</span> Channel YouTube
                                </label>
                                <input type="url" name="settings[youtube]" value="{{ old('settings.youtube', $settings['youtube'] ?? '') }}" 
                                       placeholder="https://youtube.com/..." 
                                       class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-red-500/10 focus:border-red-500 py-3.5 px-4 bg-white placeholder-slate-300 transition-colors @error('settings.youtube') !border-rose-500 !ring-rose-500/20 bg-rose-50/30 text-rose-700 @enderror">
                                @error('settings.youtube')
                                    <p class="text-[11px] font-bold text-rose-600 mt-1.5 flex items-center gap-1">❌ {{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2">
                            <span>💾</span> Rekam Konfigurasi Kontak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>