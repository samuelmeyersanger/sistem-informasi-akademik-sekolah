<div class="max-w-2xl mx-auto my-16 bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group"
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
                    e.target.reset(); 
                } else {
                    this.errorMessage = result.message || 'Terjadi kesalahan, silakan periksa kembali isian Anda.';
                }
            } catch (error) {
                this.errorMessage = 'Gagal terhubung ke server. Silakan coba beberapa saat lagi.';
            } finally {
                this.loading = false;
            }
        }
     }">
    
    {{-- Dekorasi Latar --}}
    <div class="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-indigo-500 to-indigo-600"></div>
    <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>

    <div class="p-8 md:p-12 relative z-10">
        {{-- Header Form --}}
        <div class="mb-10 text-center md:text-left">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 text-2xl mb-4 shadow-sm border border-indigo-100">
                📫
            </span>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Mari Terhubung</h2>
            <p class="text-sm font-medium text-slate-500 leading-relaxed max-w-lg mx-auto md:mx-0">Kami selalu terbuka untuk pertanyaan, masukan, dan potensi kolaborasi. Silakan sampaikan pesan Anda melalui formulir di bawah ini.</p>
        </div>

        {{-- Notifikasi Sukses --}}
        <div x-show="successMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" 
             class="p-5 mb-8 text-sm font-bold text-emerald-800 bg-emerald-50 rounded-2xl border border-emerald-200 shadow-sm flex items-start gap-3">
            <span class="text-xl">✅</span> 
            <span x-text="successMessage" class="pt-0.5"></span>
        </div>

        {{-- Notifikasi Error --}}
        <div x-show="errorMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" 
             class="p-5 mb-8 text-sm font-bold text-rose-800 bg-rose-50 rounded-2xl border border-rose-200 shadow-sm flex items-start gap-3">
            <span class="text-xl">⚠️</span> 
            <span x-text="errorMessage" class="pt-0.5"></span>
        </div>

        {{-- Form Input --}}
        <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" required placeholder="Tuliskan nama Anda..." 
                           class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-slate-50 py-3.5 px-4 shadow-inner transition-colors">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Alamat Email</label>
                    <input type="email" name="email" required placeholder="alamat@email.com" 
                           class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-slate-50 py-3.5 px-4 shadow-inner transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Perihal Utama (Subjek)</label>
                <input type="text" name="subject" required placeholder="Misal: Info Pendaftaran Siswa Baru" 
                       class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-slate-50 py-3.5 px-4 shadow-inner transition-colors">
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Isi Pesan Detail</label>
                <textarea name="pesan" required rows="5" placeholder="Tuliskan pesan, pertanyaan, atau keluhan Anda secara lengkap..." 
                          class="w-full text-sm font-medium text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-slate-50 py-3.5 px-4 shadow-inner transition-colors resize-y"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" :disabled="loading" 
                        class="w-full md:w-auto px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-1 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2">
                    <span x-show="!loading" class="flex items-center gap-2"><span>🚀</span> Kirim Pesan Sekarang</span>
                    <span x-show="loading" style="display: none;" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Sistem Memproses...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>