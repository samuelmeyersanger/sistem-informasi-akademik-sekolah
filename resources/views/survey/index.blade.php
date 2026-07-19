<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Kepuasan Masyarakat</title>
    <!-- Tailwind CSS (Gunakan Vite bawaan Laravel Anda, atau fallback ini jika belum terhubung) -->
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-indigo-50 min-h-screen font-sans antialiased py-10 px-4">

    <div class="max-w-3xl mx-auto">
        <!-- HEADER -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-600 text-white shadow-lg mb-4">
                <i class="fa-solid fa-ranking-star text-2xl"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Survey Kepuasan Masyarakat</h1>
            <p class="text-slate-500 mt-2 font-medium">Bantu kami meningkatkan kualitas layanan dengan mengisi survei singkat ini.</p>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-xl shadow-sm text-center font-bold">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ session('error') }}
            </div>
        @endif

        <!-- FORM SURVEI -->
        <form action="{{ route('publik.survey.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- KOTAK 1: PROFIL RESPONDEN (GLASSMORPHISM) -->
            <div class="bg-white/80 backdrop-blur-xl shadow-xl shadow-indigo-500/10 rounded-[2rem] border border-white/60 p-8 sm:p-10">
                <h2 class="text-lg font-black text-indigo-900 border-b border-indigo-100 pb-4 mb-6 uppercase tracking-widest"><i class="fa-solid fa-user-astronaut mr-2"></i> Data Responden</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Layanan yang Dinilai (Wajib) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Layanan yang Anda Terima <span class="text-rose-500">*</span></label>
                        <select name="layanan_id" required class="w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 font-medium">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($layanans as $layanan)
                                <option value="{{ $layanan->id }}">{{ $layanan->nama_layanan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Data Opsional (Anonim) -->
                    <div class="md:col-span-2 mt-4 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                        <p class="text-xs font-bold text-indigo-500 mb-4"><i class="fa-solid fa-circle-info"></i> Data di bawah ini bersifat opsional (Boleh dikosongkan jika ingin Anonim)</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Umur (Tahun)</label>
                                <input type="number" name="umur" class="w-full px-4 py-2.5 bg-white border-slate-200 rounded-lg focus:ring-indigo-500 text-sm" placeholder="Contoh: 35">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="w-full px-4 py-2.5 bg-white border-slate-200 rounded-lg focus:ring-indigo-500 text-sm">
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" class="w-full px-4 py-2.5 bg-white border-slate-200 rounded-lg focus:ring-indigo-500 text-sm">
                                    <option value="">-- Pilih --</option>
                                    <option value="SD">SD Kebawah</option><option value="SMP">SMP</option><option value="SMA">SMA/SMK</option>
                                    <option value="D3">Diploma</option><option value="S1">S1</option><option value="S2">S2/S3</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Pekerjaan</label>
                                <select name="pekerjaan" class="w-full px-4 py-2.5 bg-white border-slate-200 rounded-lg focus:ring-indigo-500 text-sm">
                                    <option value="">-- Pilih --</option>
                                    <option value="PNS">PNS / ASN</option><option value="TNI/Polri">TNI / Polri</option>
                                    <option value="Swasta">Pegawai Swasta</option><option value="Wiraswasta">Wiraswasta / Pengusaha</option>
                                    <option value="Pelajar/Mahasiswa">Pelajar / Mahasiswa</option><option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOTAK 2: PERTANYAAN (RATING BINTANG) -->
            <div class="bg-white/80 backdrop-blur-xl shadow-xl shadow-indigo-500/10 rounded-[2rem] border border-white/60 p-8 sm:p-10">
                <h2 class="text-lg font-black text-indigo-900 border-b border-indigo-100 pb-4 mb-6 uppercase tracking-widest"><i class="fa-solid fa-star-half-stroke mr-2"></i> Penilaian Kualitas Layanan</h2>
                <p class="text-sm text-slate-500 font-medium mb-8">Silakan beri penilaian (Bintang) pada setiap unsur pelayanan di bawah ini. (1 Bintang = Buruk, 4 Bintang = Sangat Baik)</p>

                <div class="space-y-8 divide-y divide-slate-100">
                    @forelse($unsurs as $index => $unsur)
                        <div class="pt-6 first:pt-0">
                            <p class="text-base font-bold text-slate-800 mb-3"><span class="text-indigo-500 mr-2">{{ $index + 1 }}.</span> {{ $unsur->pertanyaan }}</p>
                            
                            <!-- LOGIKA BINTANG MENGGUNAKAN ALPINE.JS -->
                            <div x-data="{ rating: 0, hoverRating: 0, 
                                          get text() { 
                                              if(this.rating == 1 || this.hoverRating == 1) return 'Buruk';
                                              if(this.rating == 2 || this.hoverRating == 2) return 'Cukup';
                                              if(this.rating == 3 || this.hoverRating == 3) return 'Baik';
                                              if(this.rating == 4 || this.hoverRating == 4) return 'Sangat Baik';
                                              return 'Pilih Bintang...';
                                          }
                                        }" 
                                 class="flex flex-col sm:flex-row sm:items-center gap-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                                
                                <div class="flex gap-2 justify-center sm:justify-start">
                                    <template x-for="i in 4">
                                        <i class="fa-solid fa-star cursor-pointer text-3xl sm:text-4xl transition-all duration-200 transform hover:scale-110"
                                           :class="i <= (hoverRating || rating) ? 'text-amber-400 drop-shadow-md' : 'text-slate-200'"
                                           @mouseenter="hoverRating = i"
                                           @mouseleave="hoverRating = 0"
                                           @click="rating = i"></i>
                                    </template>
                                </div>
                                
                                <!-- Label Teks Dinamis -->
                                <div class="text-center sm:text-left">
                                    <span class="text-sm font-black tracking-widest uppercase transition-colors"
                                          :class="{'text-rose-500': rating==1, 'text-amber-500': rating==2, 'text-emerald-500': rating==3, 'text-blue-500': rating==4, 'text-slate-400': rating==0}"
                                          x-text="text"></span>
                                </div>

                                <!-- Input Hidden untuk Controller -->
                                <input type="hidden" name="jawaban[{{ $unsur->id }}]" x-model="rating" required>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-slate-400">
                            <i class="fa-solid fa-box-open text-4xl mb-3"></i>
                            <p class="font-medium">Belum ada pertanyaan survei yang tersedia.</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- KOTAK SARAN & MASUKAN -->
                <div class="mt-10 pt-8 border-t border-slate-100">
                    <label class="block text-sm font-bold text-slate-700 mb-3"><i class="fa-regular fa-comment-dots mr-2"></i> Saran & Masukan Tambahan (Opsional)</label>
                    <textarea name="saran_masukan" rows="4" class="w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium resize-none placeholder-slate-300" placeholder="Ketik saran untuk perbaikan layanan kami ke depannya..."></textarea>
                </div>
            </div>

            <!-- TOMBOL SUBMIT -->
            <button type="submit" class="w-full py-5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-indigo-500/30 hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                <span>Kirim Penilaian Saya</span>
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>

        @include('layouts.footer')
    </div>
</body>
</html>