<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen Profil Sekolah (Tentang)') }}
    </x-slot>

    <div class="space-y-6">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-bold text-gray-900">Konfigurasi Konten Profile</h3>
                    <p class="text-xs text-gray-500">Isi data di bawah ini untuk mengubah informasi sambutan atau profil utama di halaman depan web.</p>
                </div>

                <form action="{{ route('admin.tentang.save') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Utama / Sambutan *</label>
                        <input type="text" name="judul" required value="{{ old('judul', $tentang->judul ?? '') }}" placeholder="Contoh: Selamat Datang di SMK Negeri 1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Lengkap / Sejarah Singkat *</label>
                        <textarea name="deskripsi" rows="8" required placeholder="Tuliskan profil atau visi singkat sekolah di sini..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('deskripsi', $tentang->deskripsi ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Teks Tombol Aksi (Opsional)</label>
                            <input type="text" name="tombol_teks" value="{{ old('tombol_teks', $tentang->tombol_teks ?? '') }}" placeholder="Contoh: Selengkapnya" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Link URL Tombol (Opsional)</label>
                            <input type="url" name="tombol_url" value="{{ old('tombol_url', $tentang->tombol_url ?? '') }}" placeholder="Contoh: https://sekolah.sch.id/sejarah" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Link Video Profil Youtube (Opsional)</label>
                        <input type="url" name="video_url" value="{{ old('video_url', $tentang->video_url ?? '') }}" placeholder="Contoh: https://www.youtube.com/watch?v=..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Unggah Gambar Ilustrasi / Foto Sekolah</label>
                        <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maksimal ukuran file 2MB.</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                        @if($tentang)
                            <button type="submit" name="action" formaction="{{ route('admin.tentang.reset') }}" onclick="return confirm('Apakah Anda yakin ingin mereset data profil ini?')" class="text-xs font-medium text-rose-600 hover:underline cursor-pointer">
                                🗑️ Kosongkan Data
                            </button>
                        @else
                            <div></div>
                        @endif
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">
                            💾 Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-gray-100">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Pratinjau Live Konten</h4>
                    
                    @if($tentang)
                        <div class="space-y-4 text-xs">
                            @if($tentang->gambar)
                                <img src="{{ asset('storage/' . $tentang->gambar) }}" alt="Preview" class="w-full h-40 object-cover rounded-xl border border-gray-100 shadow-inner">
                            @else
                                <div class="w-full h-40 bg-gray-100 text-gray-400 font-medium rounded-xl flex items-center justify-center border border-dashed border-gray-200">Belum ada foto profil</div>
                            @endif

                            <div>
                                <h2 class="text-base font-bold text-gray-900">{{ $tentang->judul }}</h2>
                                <p class="text-gray-500 mt-1 leading-relaxed line-clamp-6 text-justify whitespace-pre-line">{{ $tentang->deskripsi }}</p>
                            </div>

                            <div class="pt-2 flex flex-wrap gap-2">
                                @if($tentang->tombol_teks && $tentang->tombol_url)
                                    <a href="{{ $tentang->tombol_url }}" target="_blank" class="px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold rounded-lg hover:bg-indigo-100 transition-colors">
                                        🔗 {{ $tentang->tombol_teks }}
                                    </a >
                                @endif

                                @if($tentang->video_url)
                                    <a href="{{ $tentang->video_url }}" target="_blank" class="px-3 py-1.5 bg-rose-50 border border-rose-200 text-rose-700 font-bold rounded-lg hover:bg-rose-100 transition-colors">
                                        ▶️ Tonton Video
                                    </a >
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-6 text-center text-gray-400 italic bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            Data profil masih kosong. Silakan isi form di sebelah kiri untuk membuat tampilan perdana.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>