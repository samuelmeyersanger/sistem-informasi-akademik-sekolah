<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            🎯 {{ __('Tes Gaya Belajar (VAK)') }}
        </h2>
    </x-slot>

    <!-- Menggunakan Alpine.js untuk fitur pencarian dinamis (AJAX) nama siswa berdasarkan Kelas -->
    <div class="py-12 bg-slate-50 min-h-screen" 
         x-data="{ 
            kelas_id: '', 
            siswaList: [], 
            siswa_id: '',
            isLoading: false,
            
            // Fungsi untuk memanggil API daftar siswa tiap kali kelas diubah
            fetchSiswa() {
                if(!this.kelas_id) { this.siswaList = []; return; }
                this.isLoading = true;
                fetch('/api/siswa-by-kelas/' + this.kelas_id)
                    .then(res => res.json())
                    .then(data => {
                        this.siswaList = data;
                        this.isLoading = false;
                    });
            }
         }">
         
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alert Jika Berhasil Mengirim -->
            @if(session('success'))
                <div class="mb-8 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl p-8 shadow-xl text-center text-white transform transition-all hover:scale-105">
                    <span class="text-6xl block mb-4">🎉</span>
                    <h3 class="text-3xl font-black mb-2">Selamat!</h3>
                    <p class="text-lg opacity-90 font-medium">{{ session('success') }}</p>
                    <a href="{{ route('dashboard') }}" class="mt-6 inline-block bg-white text-emerald-600 px-6 py-2 rounded-full font-bold shadow-md hover:bg-gray-50 transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            @else

            <!-- Card Banner -->
            <div class="bg-indigo-900 rounded-t-3xl p-8 text-white text-center shadow-lg relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                <h3 class="text-3xl font-black mb-2 relative z-10">Kenali Potensi Belajarmu! 🚀</h3>
                <p class="text-indigo-200 relative z-10 max-w-lg mx-auto">
                    Kuesioner ini dirancang untuk membantumu mengetahui cara belajar mana yang paling cocok dan efektif untukmu (Visual, Auditory, atau Kinesthetic).
                </p>
            </div>

            <!-- Form Kuesioner -->
            <form action="{{ route('siswa.gaya_belajar.submit') }}" method="POST" class="bg-white p-8 rounded-b-3xl shadow-lg border border-gray-100">
                @csrf
                
                <!-- Identitas Siswa -->
                <div class="bg-indigo-50 rounded-2xl p-6 mb-8 border border-indigo-100">
                    <h4 class="font-bold text-indigo-900 mb-4 flex items-center gap-2">
                        <span class="text-xl">👤</span> Identitas Diri
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Dropdown Kelas -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">1. Kamu dari kelas mana?</label>
                            <select x-model="kelas_id" @change="fetchSiswa()" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white">
                                <option value="">-- Pilih Kelasmu --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown Nama Siswa (Muncul Otomatis via AlpineJS) -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 flex justify-between">
                                <span>2. Siapa namamu?</span>
                                <span x-show="isLoading" class="text-indigo-500 text-xs animate-pulse">Sedang mencari data...</span>
                            </label>
                            <select name="siswa_id" x-model="siswa_id" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white" :disabled="siswaList.length === 0">
                                <option value="">-- Pilih Nama Kamu --</option>
                                <template x-for="siswa in siswaList" :key="siswa.id">
                                    <option :value="siswa.id" x-text="siswa.nama_lengkap"></option>
                                </template>
                            </select>
                            <p x-show="kelas_id && siswaList.length === 0 && !isLoading" class="text-xs text-red-500 mt-2 font-medium">Belum ada siswa di kelas ini.</p>
                        </div>
                    </div>
                </div>

                <!-- Daftar Pertanyaan -->
                @if($soal->count() > 0)
                    <div class="space-y-8" x-show="siswa_id" x-transition.opacity style="display: none;">
                        @foreach($soal as $index => $s)
                            <div class="p-6 border-2 border-gray-100 rounded-2xl hover:border-indigo-200 transition-colors">
                                <div class="font-bold text-gray-800 text-lg mb-4">
                                    <span class="bg-indigo-100 text-indigo-700 w-8 h-8 inline-flex items-center justify-center rounded-full mr-2">{{ $index + 1 }}</span>
                                    {{ $s->pertanyaan }}
                                </div>
                                
                                <div class="space-y-3 pl-11">
                                    <!-- OPSI VISUAL -->
                                    <label class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-200 transition-colors">
                                        <input type="radio" name="jawaban[{{ $s->id }}]" value="V" required class="mt-1 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                                        <span class="text-gray-700">{{ $s->opsi_visual }}</span>
                                    </label>
                                    
                                    <!-- OPSI AUDITORY -->
                                    <label class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-200 transition-colors">
                                        <input type="radio" name="jawaban[{{ $s->id }}]" value="A" required class="mt-1 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                                        <span class="text-gray-700">{{ $s->opsi_auditory }}</span>
                                    </label>
                                    
                                    <!-- OPSI KINESTHETIC -->
                                    <label class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-200 transition-colors">
                                        <input type="radio" name="jawaban[{{ $s->id }}]" value="K" required class="mt-1 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                                        <span class="text-gray-700">{{ $s->opsi_kinesthetic }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-200" x-show="siswa_id" x-transition.opacity style="display: none;">
                        <button type="submit" class="w-full bg-indigo-900 hover:bg-black text-white font-black text-lg py-4 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1">
                            KIRIM JAWABAN SAYA 🚀
                        </button>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                        <span class="text-4xl block mb-3">🛠️</span>
                        Guru BK belum menambahkan pertanyaan kuesioner apapun.<br>Harap lapor kepada Guru BK Anda.
                    </div>
                @endif
            </form>
            @endif

        </div>
    </div>
</x-guest-layout>