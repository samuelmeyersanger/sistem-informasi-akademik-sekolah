<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pesan Masuk (Hubungi Kami)') }}
        </h2>
    </x-slot>

    <div x-data="{
        openShow: false,
        openDelete: false,
        
        // Detail Message State
        detail: { nama: '', email: '', subject: '', pesan: '', tanggal: '' },
        
        // Toast Notification State
        alertMessage: '',
        showAlert: false,

        // Custom Delete State
        deleteId: '',
        deleteTargetSender: '',

        initAlert(msg) {
            this.alertMessage = msg;
            this.showAlert = true;
            setTimeout(() => { this.showAlert = false; window.location.reload(); }, 1200);
        },

        // Ambil data detail pesan via AJAX Fetch
        async fetchDetail(id) {
            let response = await fetch(`{{ route('master.kontak.index') }}/${id}`);
            if (response.ok) {
                this.detail = await response.json();
                this.openShow = true;
            } else {
                alert('Gagal mengambil detail pesan.');
            }
        },

        // Konfirmasi Hapus Modal Kustom
        confirmDelete(id, nama) {
            this.deleteId = id;
            this.deleteTargetSender = nama;
            this.openDelete = true;
        },

        // Hapus data pesan via AJAX Fetch
        async deleteMessage() {
            this.openDelete = false;
            
            let response = await fetch(`{{ route('master.kontak.index') }}/${this.deleteId}`, {
                method: 'DELETE',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Accept': 'application/json' 
                }
            });

            let result = await response.json();
            if (response.ok) {
                this.initAlert(result.success);
            } else {
                alert('Gagal menghapus pesan.');
            }
        }
    }" class="py-12 bg-slate-900/10 min-h-screen relative">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div x-show="showAlert" x-transition class="p-4 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-xl border border-emerald-200" style="display: none;">
                🎉 <span x-text="alertMessage"></span>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Kotak Masuk Pesan</h3>
                        <p class="text-xs text-gray-500">Daftar aspirasi, pertanyaan, atau penawaran kerja sama dari formulir kontak website.</p>
                    </div>

                    <form action="{{ route('master.kontak.index') }}" method="GET" class="flex items-center gap-1 w-full sm:w-auto">
                        <div class="relative flex items-center w-full">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, subjek..." class="text-xs rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm w-full sm:w-64 pr-8">
                            @if(request('search'))
                                <a href="{{ route('master.kontak.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Reset">&times;</a>
                            @endif
                        </div>
                        <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer whitespace-nowrap">
                            Cari
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100/70 text-xs font-bold text-gray-600 uppercase border-b border-gray-200">
                                <th class="p-4 w-48">Pengirim</th>
                                <th class="p-4">Subjek / Perihal</th>
                                <th class="p-4 w-44">Waktu Kirim</th>
                                <th class="p-4 text-center w-40">Aksi Kerja</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($kontaks as $kontak)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900 text-sm">{{ $kontak->nama }}</div>
                                        <div class="text-gray-400 text-[11px] mt-0.5 font-mono">{{ $kontak->email }}</div>
                                    </td>
                                    
                                    <td class="p-4">
                                        <div class="font-semibold text-gray-800 text-xs leading-snug">{{ $kontak->subject }}</div>
                                        <div class="text-[11px] text-gray-400 line-clamp-1 mt-1">{{ Str::limit($kontak->pesan, 75) }}</div>
                                    </td>

                                    <td class="p-4 whitespace-nowrap text-gray-500 font-medium">
                                        📅 {{ $kontak->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </td>

                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button @click="fetchDetail('{{ $kontak->id }}')" class="px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded shadow-sm text-[11px] cursor-pointer">
                                                👁️ Baca Pesan
                                            </button>
                                            <button @click="confirmDelete('{{ $kontak->id }}', '{{ addslashes($kontak->nama) }}')" class="px-2.5 py-1.5 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded shadow-sm text-[11px] cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-gray-400 italic">
                                        Tidak ada pesan masuk atau data pencarian tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($kontaks, 'hasPages') && $kontaks->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $kontaks->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openShow" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-lg w-full overflow-hidden" @click.away="openShow = false">
                
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900">Isi Detail Pesan Masuk</h3>
                    <button @click="openShow = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>
                
                <div class="p-6 space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-lg border border-slate-100 text-[11px]">
                        <div>
                            <span class="block text-gray-400 font-medium">Nama Pengirim:</span>
                            <span class="font-bold text-gray-900 text-xs" x-text="detail.nama"></span>
                        </div>
                        <div>
                            <span class="block text-gray-400 font-medium">Alamat Email:</span>
                            <a :href="'mailto:' + detail.email" class="font-mono text-indigo-600 hover:underline font-semibold" x-text="detail.email"></a>
                        </div>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-0.5">Waktu Pengiriman:</span>
                        <span class="font-semibold text-gray-700" x-text="detail.tanggal"></span>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-0.5">Subjek / Perihal:</span>
                        <div class="p-2.5 bg-gray-100/50 rounded border border-gray-200 font-bold text-gray-900 text-xs" x-text="detail.subject"></div>
                    </div>

                    <div>
                        <span class="block text-gray-400 font-medium mb-1">Isi Pesan Lengkap:</span>
                        <div class="p-4 bg-slate-50 border border-gray-100 rounded-lg text-gray-800 leading-relaxed text-xs max-h-48 overflow-y-auto whitespace-pre-line" x-text="detail.pesan"></div>
                    </div>

                    <div class="flex justify-end pt-3 border-t border-gray-100">
                        <button type="button" @click="openShow = false" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white font-bold rounded-lg cursor-pointer">
                            Selesai Membaca
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Pesan Permanen?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus pesan dari <span class="font-bold text-gray-800" x-text="deleteTargetSender"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="flex justify-center gap-2 pt-2">
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="button" @click="deleteMessage()" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">Ya, Hapus</button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>