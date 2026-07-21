<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            🏅 {{ __('Pendaftaran Ekstrakurikuler') }}
        </h2>
    </x-slot>

    <!-- Menggunakan Alpine.js untuk fitur pencarian dinamis (AJAX) nama siswa -->
    <div class="py-12 bg-slate-50 min-h-screen" 
         x-data="{ 
            ekskul_id: '',
            kelas_id: '', 
            siswaList: [], 
            siswa_id: '',
            isLoading: false,
            
            // Fungsi untuk memanggil API daftar siswa 
            fetchSiswa() {
                // Hanya jalan jika Ekskul dan Kelas sudah dipilih
                if(!this.ekskul_id || !this.kelas_id) { 
                    this.siswaList = []; 
                    this.siswa_id = '';
                    return; 
                }
                
                this.isLoading = true;
                fetch('/api/siswa-ekskul/' + this.ekskul_id + '/' + this.kelas_id)
                    .then(res => res.json())
                    .then(data => {
                        this.siswaList = data;
                        this.isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.isLoading = false;
                    });
            }
         }">
         
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alert Jika Berhasil Mendaftar -->
            @if(session('success'))
                <div class="mb-8 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl p-8 shadow-xl text-center text-white transform transition-all hover:scale-105">
                    <span class="text-6xl block mb-4">🎉</span>
                    <h3 class="text-3xl font-black mb-2">Pendaftaran Berhasil!</h3>
                    <p class="text-lg opacity-90 font-medium">{{ session('success') }}</p>
                    <a href="{{ url('/') }}" class="mt-6 inline-block bg-white text-emerald-600 px-6 py-2 rounded-full font-bold shadow-md hover:bg-gray-50 transition-colors">
                        Kembali ke Halaman Utama
                    </a>
                </div>
            @endif

            <!-- Alert Jika Terjadi Kesalahan (Misal: Sudah Terdaftar) -->
            @if(session('error'))
                <div class="mb-8 bg-gradient-to-r from-red-500 to-pink-500 rounded-3xl p-8 shadow-xl text-center text-white transform transition-all hover:scale-105">
                    <span class="text-6xl block mb-4">⚠️</span>
                    <h3 class="text-3xl font-black mb-2">Oops!</h3>
                    <p class="text-lg opacity-90 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if(!session('success'))
            <!-- Card Banner -->
            <div class="bg-indigo-900 rounded-t-3xl p-8 text-white text-center shadow-lg relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                <h3 class="text-3xl font-black mb-2 relative z-10">Kembangkan Bakat & Minatmu! ⚽🎨</h3>
                <p class="text-indigo-200 relative z-10 max-w-lg mx-auto">
                    Pilih ekstrakurikuler yang sesuai dengan *passion* kamu, dan jadilah versi terbaik dari dirimu di luar jam pelajaran kelas!
                </p>
            </div>

            <!-- Form Pendaftaran -->
            <form action="{{ route('ekskul.pendaftaran.store') }}" method="POST" class="bg-white p-8 rounded-b-3xl shadow-lg border border-gray-100">
                @csrf
                
                <!-- STEP 1 & 2: Identitas Diri -->
                <div class="bg-indigo-50 rounded-2xl p-6 mb-8 border border-indigo-100">
                    <h4 class="font-bold text-indigo-900 mb-4 flex items-center gap-2">
                        <span class="text-xl">👤</span> Identitas & Pilihan
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Dropdown Ekskul -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">1. Pilih Ekstrakurikuler</label>
                            <select name="ekstrakurikuler_id" x-model="ekskul_id" @change="fetchSiswa()" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white">
                                <option value="">-- Pilih Ekstrakurikuler --</option>
                                @foreach($ekskulList as $eks)
                                    <option value="{{ $eks->id }}">{{ $eks->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown Kelas -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">2. Kamu dari kelas mana?</label>
                            <select name="kelas_id" x-model="kelas_id" @change="fetchSiswa()" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white" :disabled="!ekskul_id" :class="{ 'bg-gray-100 opacity-75': !ekskul_id }">
                                <option value="">-- Pilih Kelasmu --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->tingkat }} - {{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <p x-show="!ekskul_id" class="text-xs text-indigo-500 mt-2 font-medium">Pilih ekstrakurikuler terlebih dahulu</p>
                        </div>
                    </div>

                    <!-- Dropdown Nama Siswa (Muncul Otomatis via AlpineJS) -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 flex justify-between">
                            <span>3. Siapa namamu?</span>
                            <span x-show="isLoading" class="text-indigo-500 text-xs animate-pulse">Sedang mencari data...</span>
                        </label>
                        <select name="siswa_id" x-model="siswa_id" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white" :disabled="siswaList.length === 0" :class="{ 'bg-gray-100 opacity-75': siswaList.length === 0 }">
                            <option value="">-- Pilih Nama Kamu --</option>
                            <template x-for="siswa in siswaList" :key="siswa.id">
                                <option :value="siswa.id" x-text="siswa.nama_lengkap"></option>
                            </template>
                        </select>
                        
                        <p x-show="kelas_id && ekskul_id && siswaList.length === 0 && !isLoading" class="text-xs text-red-500 mt-2 font-medium">
                            Semua siswa di kelas ini sudah mendaftar di ekstrakurikuler tersebut.
                        </p>
                    </div>
                </div>

                <!-- STEP 3: Data Pendukung (Hanya Muncul Jika Nama Sudah Dipilih) -->
                <div class="space-y-6" x-show="siswa_id" x-transition.opacity style="display: none;">
                    
                    <div class="p-6 border-2 border-gray-100 rounded-2xl hover:border-indigo-200 transition-colors">
                        <h4 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                            <span class="text-xl">📱</span> Informasi Kontak
                        </h4>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nomor WhatsApp (Aktif)</label>
                            <input type="text" name="nomor_hp" required placeholder="Contoh: 081234567890" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white p-3">
                            <p class="text-xs text-gray-500 mt-2">Pastikan nomor aktif agar pembina mudah menghubungi.</p>
                        </div>
                    </div>

                    <div class="p-6 border-2 border-gray-100 rounded-2xl hover:border-indigo-200 transition-colors">
                        <h4 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                            <span class="text-xl">🔥</span> Motivasi Bergabung
                        </h4>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kenapa kamu ingin ikut ekstrakurikuler ini?</label>
                            <textarea name="motivasi" rows="3" required placeholder="Tuliskan alasan dan tujuanmu..." class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white p-3"></textarea>
                        </div>
                    </div>

                    <!-- Tombol Kirim -->
                    <div class="mt-8 pt-4 border-t border-gray-200">
                        <button type="submit" class="w-full bg-indigo-900 hover:bg-black text-white font-black text-lg py-4 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1">
                            KIRIM PENDAFTARAN SAYA 🚀
                        </button>
                    </div>
                </div>
            </form>
            @endif

        </div>
    </div>
</x-guest-layout>