<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🔗</span> {{ __('Navigasi Footer & Tautan') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div x-data="{ 
        openCreate: false, 
        openEdit: false, 
        openDelete: false,
        
        // Form Fields State
        id: '', group: '', judul: '', url: '', urutan: 1, status: true,
        
        // Custom Delete Confirmation State
        deleteAction: '',
        deleteTargetTitle: ''
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <div>
                        <div class="mb-1 text-base">Gagal memproses data tautan!</div>
                        <p class="text-xs font-medium text-rose-600">Mohon periksa kembali inputan form Anda. Pastikan semua kolom wajib diisi.</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Katalog Menu Footer</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Struktur tautan pendukung (Link Terkait, Bantuan, dll).</p>
                    </div>
                    
                    <button type="button" 
                            @click="
                                id = ''; group = ''; judul = ''; url = ''; urutan = 1; status = true;
                                openCreate = true;
                            " 
                            class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 w-full sm:w-auto">
                        <span>➕</span> Tambah Tautan Baru
                    </button>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-6 w-16 text-center">No</th>
                                <th class="p-5 w-56">Kategori Grup</th>
                                <th class="p-5">Label Teks (Judul)</th>
                                <th class="p-5 text-center w-24">Urutan</th>
                                <th class="p-5 text-center w-36">Visibilitas</th>
                                <th class="p-5 pr-6 text-center w-40">Modifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($links as $index => $link)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-6 text-center font-mono font-bold text-slate-400">
                                        {{ str_pad($links instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($links->firstItem() + $index) : ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="p-5">
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-lg font-black text-[10px] uppercase tracking-wider shadow-sm">
                                            <span>📁</span> {{ $link->group }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-black text-slate-900 text-base mb-1">{{ $link->judul }}</div>
                                        <div class="font-mono text-slate-400 text-[11px] font-bold flex items-center gap-2">
                                            <span class="p-1 bg-slate-100 rounded text-slate-500">🔗</span>
                                            <a href="{{ $link->url }}" target="_blank" class="hover:text-indigo-600 truncate max-w-[200px] sm:max-w-xs transition-colors">{{ $link->url }}</a>
                                        </div>
                                    </td>
                                    <td class="p-5 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-black text-xs shadow-inner">
                                            {{ $link->urutan }}
                                        </span>
                                    </td>
                                    <td class="p-5 text-center whitespace-nowrap">
                                        @if($link->status)
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                                AKTIF TAMPIL
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                                DISEMBUNYIKAN
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" 
                                                    @click="
                                                        id = '{{ $link->id }}';
                                                        group = '{{ addslashes($link->group) }}';
                                                        judul = '{{ addslashes($link->judul) }}';
                                                        url = '{{ addslashes($link->url) }}';
                                                        urutan = '{{ $link->urutan }}';
                                                        status = {{ $link->status ? 'true' : 'false' }};
                                                        openEdit = true;
                                                    "
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Ubah Link">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <button type="button"
                                                    @click="
                                                        deleteAction = '{{ route('master.footer-link.destroy', $link->id) }}';
                                                        deleteTargetTitle = '{{ addslashes($link->judul) }}';
                                                        openDelete = true;
                                                    "
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Link">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                🕸️
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Belum Ada Tautan</h4>
                                            <span class="text-sm">Silakan tambah tautan footer pertama Anda.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(method_exists($links, 'hasPages') && $links->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $links->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL CREATE --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">🌍</span> Tambah Menu Footer
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('master.footer-link.store') }}" method="POST" class="p-6 md:p-8 space-y-5 flex-1 overflow-y-auto bg-white">
                    @csrf
                    
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Grup Menu <span class="text-rose-500">*</span></label>
                        <input type="text" name="group" required placeholder="Contoh: Aplikasi Sekolah, Link Terkait" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-400">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Label Teks Tautan <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" required placeholder="Contoh: Portal E-Rapor" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-400">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Target Alamat URL <span class="text-rose-500">*</span></label>
                        <input type="text" name="url" required placeholder="Contoh: https://erapor.sekolah.sch.id" class="w-full rounded-xl border-slate-200 text-sm font-bold font-mono shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 text-indigo-700 placeholder-slate-400">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Urutan Tampil <span class="text-rose-500">*</span></label>
                        <input type="number" name="urutan" required min="1" value="1" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>
                    
                    <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 flex items-center gap-3">
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input id="status_create" name="status" type="checkbox" value="1" checked class="w-5 h-5 text-indigo-600 bg-white border-slate-300 rounded focus:ring-indigo-500 focus:ring-2 cursor-pointer">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="status_create" class="font-black text-slate-700 cursor-pointer">Aktifkan & Tampilkan</label>
                                <p class="text-[10px] text-slate-500 font-bold mt-0.5">Tautan akan langsung muncul di footer website.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-8">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batalkan</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center">Simpan Tautan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/30 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">📝</span> Edit Tautan Footer
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="'{{ route('master.footer-link.index') }}/' + id" method="POST" class="p-6 md:p-8 space-y-5 flex-1 overflow-y-auto bg-white">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Grup Menu <span class="text-rose-500">*</span></label>
                        <input type="text" name="group" x-model="group" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Label Teks Tautan <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" x-model="judul" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Target Alamat URL <span class="text-rose-500">*</span></label>
                        <input type="text" name="url" x-model="url" required class="w-full rounded-xl border-slate-200 text-sm font-bold font-mono shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 text-indigo-700">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Urutan Tampil <span class="text-rose-500">*</span></label>
                        <input type="number" name="urutan" x-model="urutan" required min="1" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>
                    
                    <div class="bg-amber-50/30 border border-amber-100 rounded-xl p-4 flex items-center gap-3">
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input id="edit_status" name="status" type="checkbox" value="1" x-model="status" class="w-5 h-5 text-amber-500 bg-white border-slate-300 rounded focus:ring-amber-500 focus:ring-2 cursor-pointer">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="edit_status" class="font-black text-slate-700 cursor-pointer">Status Tautan Aktif</label>
                                <p class="text-[10px] text-slate-500 font-bold mt-0.5">Hilangkan centang untuk menyembunyikan tautan ini.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-8">
                        <button type="button" @click="openEdit = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 w-full sm:w-auto">
                            <span>💾</span> Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    ✂️
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Hapus Tautan?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Anda yakin ingin membuang link:
                    </p>
                    <div class="mt-3 p-3 bg-rose-50/50 border border-rose-100 rounded-xl text-rose-700 font-bold italic text-sm break-all">
                        "<span x-text="deleteTargetTitle"></span>"
                    </div>
                </div>
                
                <form :action="deleteAction" method="POST" class="flex justify-center gap-3 w-full pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="flex-1 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md cursor-pointer transition-colors border border-transparent flex items-center justify-center gap-2">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>