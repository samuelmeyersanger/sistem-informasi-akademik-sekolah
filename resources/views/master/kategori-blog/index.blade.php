<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🏷️</span> {{ __('Manajemen Kategori Artikel') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Kelola label taksonomi untuk mengelompokkan tulisan di portal.</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{ 
        openCreate: false, 
        openEdit: false, 
        openDelete: false, 
        editId: '', 
        editNama: '',
        
        // State Kustom Dialog Hapus
        deleteAction: '',
        deleteTargetName: ''
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">❌</span> 
                    <div class="mt-1">{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <div>
                        <div class="mb-1 text-base font-black">Gagal memvalidasi data!</div>
                        <p class="text-xs font-medium text-amber-600">Pastikan field "Nama Kategori" diisi dengan benar dan maksimal 50 karakter.</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Katalog Grup Artikel</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Daftar *tag* atau label kategori untuk merapikan postingan blog.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                        <form action="{{ url()->current() }}" method="GET" class="flex items-stretch gap-2 w-full md:w-auto">
                            <div class="relative flex items-center w-full sm:w-64 group">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..." 
                                    class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                                @if(request('search'))
                                    <a href="{{ url()->current() }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Reset Pencarian">
                                        <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2">
                                <span class="hidden sm:inline">🔍</span> Cari
                            </button>
                        </form>

                        <div class="hidden md:block w-px h-8 bg-slate-200"></div>

                        <button @click="openCreate = true; editNama = ''" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 w-full sm:w-auto">
                            <span>➕</span> Tambah Baru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-6 w-16 text-center">No</th>
                                <th class="p-5 w-64">Nama Kategori Label</th>
                                <th class="p-5 w-48">URL Tautan (Slug)</th>
                                <th class="p-5 text-center w-36">Total Konten</th>
                                <th class="p-5 pr-6 text-center w-40">Opsi Modifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($kategoriBlogs as $index => $kategori)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-6 text-center font-mono font-bold text-slate-400">
                                        {{ str_pad($kategoriBlogs instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($kategoriBlogs->firstItem() + $index) : ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="p-5">
                                        <div class="font-black text-slate-900 text-base flex items-center gap-2">
                                            <span class="text-indigo-400">📁</span> {{ $kategori->nama }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <span class="px-3 py-1 font-mono text-[10px] font-bold tracking-widest text-slate-500 bg-slate-100/80 border border-slate-200 rounded-lg shadow-inner">
                                            /{{ $kategori->slug }}
                                        </span>
                                    </td>
                                    <td class="p-5 text-center">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border {{ $kategori->blogs_count > 0 ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-50 text-slate-500 border-slate-200' }} shadow-sm">
                                            <span class="font-black text-sm">{{ $kategori->blogs_count ?? 0 }}</span>
                                            <span class="text-[10px] font-bold uppercase tracking-wider">Artikel</span>
                                        </div>
                                    </td>
                                    <td class="p-5 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" 
                                                    @click="
                                                        editId = '{{ $kategori->id }}';
                                                        editNama = '{{ addslashes($kategori->nama) }}';
                                                        openEdit = true;
                                                    " 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Ubah Nama">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <button type="button"
                                                    @click="
                                                        deleteAction = '{{ route('master.kategori-blog.destroy', $kategori->id) }}';
                                                        deleteTargetName = '{{ addslashes($kategori->nama) }}';
                                                        openDelete = true;
                                                    "
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Kategori">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                🔖
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Penyimpanan Kategori Kosong</h4>
                                            <span class="text-sm">Tidak ditemukan data kategori yang sesuai atau belum ada kategori yang dibuat.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($kategoriBlogs, 'hasPages') && $kategoriBlogs->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $kategoriBlogs->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL CREATE KATEGORI --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">✨</span> Kategori Baru
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('master.kategori-blog.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Nama Kategori Label <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required maxlength="50" placeholder="Cth: Pengumuman Sekolah, Artikel Prestasi" class="w-full rounded-xl border-slate-200 text-base font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                        <p class="text-[10px] text-slate-400 font-bold mt-2 uppercase tracking-widest">URL Slug akan di-generate otomatis</p>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>💾</span> Simpan Label
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT KATEGORI --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/30 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">📝</span> Ubah Kategori
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="'{{ route('master.kategori-blog.index') }}/' + editId" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block font-black text-slate-700 text-sm mb-2">Perbarui Nama Kategori <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" x-model="editNama" required maxlength="50" class="w-full rounded-xl border-slate-200 text-base font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                        
                        <div class="mt-4 p-4 bg-indigo-50/50 rounded-xl border border-indigo-100 flex items-start gap-3">
                            <span class="text-indigo-500 mt-0.5">ℹ️</span>
                            <p class="text-[11px] text-indigo-800 font-medium leading-relaxed">
                                <strong class="font-black">Catatan:</strong> Mengubah nama kategori juga akan otomatis 
                                memperbarui struktur link/slug URL pada semua artikel yang berada di bawah label ini.
                            </p>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openEdit = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>🔄</span> Perbarui Label
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE KATEGORI --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🗑️
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Hapus Kategori Permanen?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Anda yakin ingin membuang kategori <strong class="text-slate-800" x-text="deleteTargetName"></strong>?
                    </p>
                    <div class="mt-3 p-3 bg-rose-50/50 border border-rose-100 rounded-xl text-rose-700 font-bold italic text-sm">
                        Pastikan tidak ada artikel penting yang tertaut. Tindakan ini tidak dapat dibatalkan.
                    </div>
                </div>
                
                <form :action="deleteAction" method="POST" class="flex justify-center gap-3 w-full pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="flex-1 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors">
                        Urungkan
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md cursor-pointer transition-colors border border-transparent flex items-center justify-center gap-2">
                        Buang
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>