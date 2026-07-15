<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🏫</span> {{ __('Informasi Sekolah') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 relative z-10">
            
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <div class="mt-1">{{ session('error') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">🚧</span> 
                    <div>
                        <div class="mb-1 text-base font-black text-amber-900">Validasi Data Gagal!</div>
                        <ul class="list-disc list-inside text-xs font-medium text-amber-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- KOLOM KIRI: FORMULIR INPUT --}}
                <div class="lg:col-span-8 bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                    
                    {{-- Efek Latar Belakang Kaca --}}
                    <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                    <div class="p-6 md:p-8 border-b border-slate-100 bg-white/50 backdrop-blur-sm relative z-10">
                        <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                            <span class="text-indigo-500">📝</span> Lembar Penulisan Konten Utama
                        </h3>
                        <p class="text-xs font-medium text-slate-500 mt-2 max-w-2xl leading-relaxed">Tulisan yang memikat dapat meningkatkan pandangan masyarakat terhadap institusi. Gunakan tata bahasa formil untuk sambutan kepala sekolah, atau narasi menginspirasi untuk menjelaskan sejarah institusi.</p>
                    </div>

                    <form action="{{ route('master.tentang.save') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-8 relative z-10">
                        @csrf
                        
                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Judul Sorotan Utama <span class="text-rose-500">*</span></label>
                            <input type="text" name="judul" required value="{{ old('judul', $tentang->judul ?? '') }}" 
                                   placeholder="Cth: Selamat Datang Generasi Emas..." 
                                   class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3.5 px-4 placeholder-slate-300 transition-colors">
                        </div>

                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center justify-between">
                                <span>Deskripsi Narasi Profil <span class="text-rose-500">*</span></span>
                                <span class="text-[10px] text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded font-bold">Paragraf Lengkap</span>
                            </label>
                            <textarea name="deskripsi" rows="8" required 
                                      placeholder="Tuliskan latar belakang, visi, misi, atau kutipan istimewa di sini..." 
                                      class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3.5 px-4 placeholder-slate-300 transition-colors leading-relaxed">{{ old('deskripsi', $tentang->deskripsi ?? '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                            <div class="md:col-span-2">
                                <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-1">🔗 Tindakan Opsional (Call to Action)</h4>
                                <p class="text-[11px] text-slate-500 mb-4">Tambahkan tautan khusus yang mengarahkan pembaca ke halaman pendaftaran atau brosur PDF.</p>
                            </div>
                            
                            <div>
                                <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-2">Teks Tombol Aksi</label>
                                <input type="text" name="tombol_teks" value="{{ old('tombol_teks', $tentang->tombol_teks ?? '') }}" 
                                       placeholder="Cth: Daftar Sekarang" 
                                       class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300 transition-colors">
                            </div>
                            <div>
                                <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-2">Tautan URL Tombol</label>
                                <input type="url" name="tombol_url" value="{{ old('tombol_url', $tentang->tombol_url ?? '') }}" 
                                       placeholder="https://psb.sekolah.sch.id" 
                                       class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300 transition-colors">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Tautan Profil YouTube</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-slate-400">▶️</span>
                                    </div>
                                    <input type="url" name="video_url" value="{{ old('video_url', $tentang->video_url ?? '') }}" 
                                           placeholder="https://youtube.com/watch?v=..." 
                                           class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 py-3.5 pl-10 pr-4 placeholder-slate-300 transition-colors">
                                </div>
                            </div>

                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex justify-between">
                                    <span>Gambar Hero / Banner</span>
                                    <span class="text-[10px] text-slate-400">Maks. 2MB</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="file" name="gambar" accept="image/png, image/jpeg, image/webp" 
                                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 hover:file:text-indigo-700 file:cursor-pointer transition-all border border-slate-200 rounded-xl bg-slate-50 cursor-pointer p-1">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                            @if($tentang)
                                <button type="submit" name="action" formaction="{{ route('master.tentang.reset') }}" onclick="return confirm('Peringatan: Seluruh data profil ini (termasuk gambar) akan dihapus secara permanen. Lanjutkan?')" 
                                        class="text-xs font-bold text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-4 py-2 rounded-lg transition-colors cursor-pointer w-full sm:w-auto text-center border border-transparent hover:border-rose-200">
                                    🧨 Hapus Permanen Seluruh Data
                                </button>
                            @else
                                <div></div>
                            @endif
                            <button type="submit" class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                                <span>💾</span> Simpan Siaran Publik
                            </button>
                        </div>
                    </form>
                </div>

                {{-- KOLOM KANAN: PRATINJAU (PREVIEW) --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white rounded-[2rem] shadow-lg shadow-slate-200/40 border border-slate-100 p-6 md:p-8 relative overflow-hidden sticky top-8">
                        
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 animate-pulse">
                                👁️
                            </div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Pratinjau Antarmuka</h4>
                        </div>
                        
                        @if($tentang)
                            <div class="space-y-5">
                                {{-- Kotak Gambar Pratinjau --}}
                                @if($tentang->gambar)
                                    <div class="relative group rounded-[1.5rem] overflow-hidden shadow-md border border-slate-100">
                                        <img src="{{ asset('storage/' . $tentang->gambar) }}" alt="Preview" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                                    </div>
                                @else
                                    <div class="w-full h-48 bg-slate-50 text-slate-400 text-xs font-bold rounded-[1.5rem] flex flex-col items-center justify-center border-2 border-dashed border-slate-200 gap-2">
                                        <span class="text-3xl">🖼️</span>
                                        Belum Ada Media Visual
                                    </div>
                                @endif

                                {{-- Konten Teks Pratinjau --}}
                                <div>
                                    <h2 class="text-xl font-black text-slate-900 leading-tight mb-2">{{ $tentang->judul }}</h2>
                                    <p class="text-sm text-slate-600 leading-relaxed text-justify whitespace-pre-line line-clamp-5 relative">
                                        {{ $tentang->deskripsi }}
                                        <span class="absolute bottom-0 right-0 bg-gradient-to-l from-white via-white to-transparent pl-4 pr-1 text-indigo-500 font-bold">...baca selengkapnya</span>
                                    </p>
                                </div>

                                {{-- Interaksi Tombol Pratinjau --}}
                                <div class="pt-4 flex flex-wrap gap-2 border-t border-slate-100">
                                    @if($tentang->tombol_teks && $tentang->tombol_url)
                                        <a href="{{ $tentang->tombol_url }}" target="_blank" 
                                           class="px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 text-[11px] font-black uppercase tracking-wider rounded-xl hover:bg-indigo-100 transition-colors shadow-sm flex items-center gap-1.5">
                                            <span>🔗</span> {{ $tentang->tombol_teks }}
                                        </a>
                                    @endif

                                    @if($tentang->video_url)
                                        <a href="{{ $tentang->video_url }}" target="_blank" 
                                           class="px-4 py-2 bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-black uppercase tracking-wider rounded-xl hover:bg-rose-100 transition-colors shadow-sm flex items-center gap-1.5">
                                            <span>▶️</span> Sorotan Video
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="p-8 text-center bg-slate-50 rounded-[1.5rem] border border-dashed border-slate-200 flex flex-col items-center gap-3">
                                <span class="text-4xl">📭</span>
                                <h5 class="text-sm font-black text-slate-700">Data Masih Nihil</h5>
                                <p class="text-xs font-medium text-slate-500 leading-relaxed">
                                    Silakan isi kolom formulir di sebelah kiri terlebih dahulu untuk melihat hasil proyeksi tampilan web perdana Anda di sini.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>