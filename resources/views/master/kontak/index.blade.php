<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">📥</span> {{ __('Inbox Hubungi Kami') }}
                </h2>
            </div>
        </div>
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
            setTimeout(() => { this.showAlert = false; window.location.reload(); }, 1500);
        },

        // Ambil data detail pesan via AJAX Fetch
        async fetchDetail(id) {
            try {
                let response = await fetch(`{{ route('master.kontak.index') }}/${id}`);
                if (response.ok) {
                    this.detail = await response.json();
                    this.openShow = true;
                } else {
                    alert('Gagal mengambil detail pesan. Server tidak merespon.');
                }
            } catch (e) {
                alert('Terjadi kesalahan jaringan.');
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
            
            try {
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
                    alert('Gagal menghapus pesan permanen.');
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menghapus pesan.');
            }
        }
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Floating Toast Alert (Muncul saat berhasil hapus AJAX) --}}
            <div x-show="showAlert" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-[-1rem]"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-[-1rem]"
                 class="fixed top-8 left-1/2 -translate-x-1/2 z-50 p-4 bg-white/90 backdrop-blur border-l-4 border-emerald-500 rounded-xl shadow-2xl flex items-center gap-3" 
                 style="display: none;">
                <span class="text-2xl">✨</span>
                <div>
                    <h4 class="font-black text-slate-800 text-sm">Pesan Dihapus!</h4>
                    <p class="text-[11px] font-bold text-slate-500" x-text="alertMessage"></p>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Kotak Masuk (Inbox)</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Daftar semua kiriman tiket pesan dari form Hubungi Kami.</p>
                    </div>

                    <form action="{{ route('master.kontak.index') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
                        <div class="relative flex items-center w-full md:w-72 group">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, subjek..." 
                                class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                            @if(request('search'))
                                <a href="{{ route('master.kontak.index') }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Reset Pencarian">
                                    <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer whitespace-nowrap flex items-center gap-2">
                            <span class="hidden sm:inline">🔍</span> Cari
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-8 w-64">Identitas Pengirim</th>
                                <th class="p-5">Subjek & Cuplikan Pesan</th>
                                <th class="p-5 w-44">Tgl Kirim</th>
                                <th class="p-5 pr-8 text-center w-40">Opsi Tiket</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($kontaks as $kontak)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8 align-top">
                                        <div class="flex gap-4">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm shrink-0 shadow-inner">
                                                {{ strtoupper(substr($kontak->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-slate-900 text-base leading-tight">{{ $kontak->nama }}</div>
                                                <div class="text-indigo-500 font-bold text-[11px] mt-0.5 truncate max-w-[150px] sm:max-w-[200px]" title="{{ $kontak->email }}">
                                                    {{ $kontak->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 align-top">
                                        <div class="font-black text-slate-800 text-sm leading-snug">{{ $kontak->subject }}</div>
                                        <div class="text-xs text-slate-500 font-medium mt-1.5 line-clamp-2 max-w-lg leading-relaxed bg-slate-50/50 p-2 rounded-lg border border-slate-100 italic">
                                            {{ Str::limit($kontak->pesan, 100) }}
                                        </div>
                                    </td>

                                    <td class="p-5 align-top">
                                        <div class="font-bold text-slate-700">
                                            {{ $kontak->created_at->translatedFormat('d M Y') }}
                                        </div>
                                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">
                                            {{ $kontak->created_at->translatedFormat('H:i') }} WIB
                                        </div>
                                    </td>

                                    <td class="p-5 pr-8 text-center align-top pt-7">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="fetchDetail('{{ $kontak->id }}')" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Baca Pesan Lengkap">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <button type="button" @click="confirmDelete('{{ $kontak->id }}', '{{ addslashes($kontak->nama) }}')" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Pesan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                📭
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Inbox Bersih</h4>
                                            <span class="text-sm">Tidak ada pesan masuk atau belum ada data yang sesuai filter Anda.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($kontaks, 'hasPages') && $kontaks->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $kontaks->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL BACA PESAN (VIEWER) --}}
        <div x-show="openShow" class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-2xl w-full overflow-hidden flex flex-col max-h-[90vh]" @click.away="openShow = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        <span class="text-2xl text-indigo-500">📖</span> Tinjauan Pesan Tiket
                    </h3>
                    <button @click="openShow = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6 bg-white relative">
                    
                    {{-- Header Email (From / To / Date) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50/50 p-5 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xl shrink-0 border border-indigo-100 shadow-inner">
                                👤
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-400 font-black uppercase tracking-widest mb-0.5">Dari (Sender)</span>
                                <span class="font-black text-slate-900 text-sm block" x-text="detail.nama"></span>
                                <a :href="'mailto:' + detail.email" class="font-bold text-[11px] text-indigo-600 hover:underline inline-flex items-center gap-1 mt-0.5" title="Balas Email">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span x-text="detail.email"></span>
                                </a>
                            </div>
                        </div>
                        <div class="flex flex-col md:items-end justify-center pt-3 md:pt-0 border-t border-slate-100 md:border-t-0">
                            <span class="block text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1 md:text-right">Waktu Masuk Sistem</span>
                            <span class="font-black text-slate-700 text-xs inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                                🕒 <span x-text="detail.tanggal"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Isi Body Pesan --}}
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-4 md:p-5 border-b border-slate-100 bg-slate-50/30">
                            <span class="block text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1.5">Perihal / Subjek Pembicaraan</span>
                            <div class="font-black text-slate-900 text-base md:text-lg leading-snug" x-text="detail.subject"></div>
                        </div>
                        <div class="p-4 md:p-6 bg-white min-h-[150px]">
                            <span class="block text-[10px] text-slate-400 font-black uppercase tracking-widest mb-3">Isi Pesan / Body Text</span>
                            <div class="text-slate-800 leading-loose text-sm whitespace-pre-line font-medium" x-text="detail.pesan"></div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="button" @click="openShow = false" class="px-8 py-3.5 bg-slate-800 hover:bg-slate-900 text-white font-black rounded-xl cursor-pointer shadow-lg shadow-slate-800/20 transition-all hover:-translate-y-0.5">
                            Tutup Pesan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL DELETE PESAN --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🗑️
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Hapus Pesan Tiket?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Pesan dari pengirim <strong class="text-slate-800" x-text="deleteTargetSender"></strong> akan dihapus permanen dan tidak bisa dikembalikan.
                    </p>
                </div>
                
                <div class="flex justify-center gap-3 w-full pt-2">
                    <button type="button" @click="openDelete = false" class="flex-1 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors">
                        Urungkan
                    </button>
                    <button type="button" @click="deleteMessage()" class="flex-1 px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md cursor-pointer transition-colors border border-transparent flex items-center justify-center gap-2">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>