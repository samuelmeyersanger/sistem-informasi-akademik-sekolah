<div class="max-w-xl mx-auto my-12 p-6 bg-white rounded-xl shadow-md border border-gray-100"
     x-data="{
        loading: false,
        successMessage: '',
        errorMessage: '',
        
        async submitForm(e) {
            this.loading = true;
            this.successMessage = '';
            this.errorMessage = '';

            let formData = new FormData(e.target);

            try {
                let response = await fetch('{{ route('publik.kontak.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                let result = await response.json();

                if (response.ok) {
                    this.successMessage = result.message;
                    e.target.reset(); // Kosongkan form setelah sukses
                } else {
                    // Tangkap pesan error validasi dari Laravel jika ada
                    this.errorMessage = result.message || 'Terjadi kesalahan, silakan periksa kembali isian Anda.';
                }
            } catch (error) {
                this.errorMessage = 'Gagal terhubung ke server. Silakan coba beberapa saat lagi.';
            } finally {
                this.loading = false;
            }
        }
     }">

    <h2 class="text-xl font-bold text-gray-800 mb-2">Hubungi Kami</h2>
    <p class="text-xs text-gray-500 mb-6">Punya pertanyaan, kritik, atau saran? Kirimkan pesan Anda melalui formulir di bawah ini.</p>

    <div x-show="successMessage" x-transition class="p-4 mb-4 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-lg border border-emerald-200">
        ✅ <span x-text="successMessage"></span>
    </div>

    <div x-show="errorMessage" x-transition class="p-4 mb-4 text-xs font-medium text-rose-700 bg-rose-50 rounded-lg border border-rose-200">
        ⚠️ <span x-text="errorMessage"></span>
    </div>

    <form @submit.prevent="submitForm" class="space-y-4 text-xs">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" required placeholder="Nama Anda..." class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Alamat Email</label>
            <input type="email" name="email" required placeholder="nama@email.com" class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Subjek / Perihal</label>
            <input type="text" name="subject" required placeholder="Contoh: Penawaran Kerja Sama" class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Isi Pesan</label>
            <textarea name="pesan" required rows="5" placeholder="Tuliskan pesan lengkap Anda di sini..." class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <button type="submit" 
                :disabled="loading"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg cursor-pointer transition-all disabled:opacity-50 text-center">
            <span x-show="!loading">🚀 Kirim Pesan Sekarang</span>
            <span x-show="loading" style="display: none;">⏳ Sedang Mengirim...</span>
        </button>
    </form>
</div>