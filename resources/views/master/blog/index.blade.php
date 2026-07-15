<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">📰</span> {{ __('Manajemen Artikel & Blog') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Kelola publikasi, draf tulisan, dan informasi sekolah untuk portal utama.</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{ 
        openCreate: false, 
        openEdit: false, 
        openDelete: false,
        deleteAction: '',
        deleteTargetTitle: '',
        editId: '', 
        editJudul: '', 
        editKategoriId: '', 
        editKonten: '', 
        editIsPublished: '1'
    }" class="py-10 bg-slate-50/50 min-h-screen font-sans">
        
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
                        <div class="mb-1 text-base">Gagal mempublikasikan tulisan!</div>
                        <p class="text-xs font-medium text-rose-600">Pastikan semua form wajib bertanda bintang (*) sudah terisi dengan benar.</p>
                    </div>
                </div>
            @endif

            {{-- Wrapper Utama --}}
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10 bg-white/50 backdrop-blur-sm">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Katalog Publikasi</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Daftar seluruh draf dan artikel yang tayang di portal.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                        <form action="{{ route('master.blog.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch gap-3 w-full lg:w-auto">
                            
                            {{-- Dropdown Kategori --}}
                            <div class="relative w-full sm:w-48">
                                <select name="kategori_id" class="w-full text-sm font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl shadow-sm py-2.5 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-colors appearance-none pr-10 cursor-pointer">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Input Search --}}
                            <div class="relative flex items-center w-full sm:w-64 group">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul artikel..." 
                                    class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                                @if(request('search') || request('kategori_id'))
                                    <a href="{{ route('master.blog.index') }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Reset Pencarian">
                                        <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                                Filter
                            </button>
                        </form>

                        <div class="hidden sm:block w-px h-8 bg-slate-200"></div>

                        <button @click="
                            editId = ''; editJudul = ''; editKategoriId = ''; editKonten = ''; editIsPublished = '1';
                            openCreate = true;
                        " class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer w-full sm:w-auto">
                            <span>✍️</span> Tulis Baru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-6 text-center w-16">No</th>
                                <th class="p-5 text-center w-36">Gambar Utama</th>
                                <th class="p-5">Informasi Tulisan</th>
                                <th class="p-5 w-44">Kategori Label</th>
                                <th class="p-5 w-36">Status Publikasi</th>
                                <th class="p-5 pr-6 text-center w-40">Opsi Modifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($blogs as $index => $blog)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-6 text-center text-slate-400 font-bold">
                                        {{ str_pad($blogs instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($blogs->firstItem() + $index) : ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    
                                    <td class="p-5 text-center">
                                        @if($blog->gambar)
                                            <div class="relative w-28 h-16 mx-auto rounded-xl overflow-hidden shadow-sm border border-slate-200 group-hover:shadow-md transition-shadow">
                                                <img src="{{ asset('storage/' . $blog->gambar) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            </div>
                                        @else
                                            <div class="w-28 h-16 mx-auto bg-slate-100/50 text-[10px] text-slate-400 rounded-xl flex flex-col items-center justify-center border border-dashed border-slate-200 font-bold">
                                                <span class="text-lg">🖼️</span>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="p-5">
                                        <div class="font-black text-slate-900 text-base leading-tight">{{ $blog->judul }}</div>
                                        <div class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-3 font-semibold">
                                            <span class="flex items-center gap-1.5 px-2 py-0.5 bg-slate-50 rounded-md border border-slate-100"><span class="text-indigo-400">✍️</span> {{ $blog->user->name ?? 'Penulis Anonim' }}</span>
                                            <span class="flex items-center gap-1.5"><span class="text-amber-400">🕒</span> {{ $blog->created_at->translatedFormat('d M Y, H:i') }}</span>
                                        </div>
                                    </td>
                                    
                                    <td class="p-5 whitespace-nowrap">
                                        <span class="px-3 py-1 font-black text-[10px] uppercase tracking-wider rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 shadow-sm">
                                            {{ $blog->kategori->nama ?? 'Tak Berkategori' }}
                                        </span>
                                    </td>
                                    
                                    <td class="p-5 whitespace-nowrap">
                                        @if($blog->is_published)
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                                Dipublikasi
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="text-amber-500">📝</span>
                                                Draf Disimpan
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="p-5 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button"
                                                    data-konten="{{ $blog->konten }}"
                                                    @click="
                                                        editId = '{{ $blog->id }}';
                                                        editJudul = '{{ addslashes($blog->judul) }}';
                                                        editKategoriId = '{{ $blog->kategori_blog_id }}';
                                                        editIsPublished = '{{ $blog->is_published }}';
                                                        editKonten = $el.getAttribute('data-konten');
                                                        openEdit = true;
                                                    " 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Edit Artikel">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <button type="button" 
                                                    @click="
                                                        deleteAction = '{{ route('master.blog.destroy', $blog->id) }}';
                                                        deleteTargetTitle = '{{ addslashes($blog->judul) }}';
                                                        openDelete = true;
                                                    "
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Artikel">
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
                                                📝
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Belum Ada Artikel</h4>
                                            <span class="text-sm">Silakan buat tulisan pertama Anda untuk dipublikasikan di portal sekolah.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($blogs, 'hasPages') && $blogs->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $blogs->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL CREATE ARTIKEL --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-4xl w-full overflow-hidden flex flex-col max-h-[90vh]" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">✍️</span> Editor Artikel Baru
                    </h3>
                    <button @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('master.blog.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6 bg-white">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-5">
                            <div>
                                <label class="block font-black text-slate-700 text-sm mb-2">Judul Artikel <span class="text-rose-500">*</span></label>
                                <input type="text" name="judul" required placeholder="Tulis judul memikat di sini..." class="w-full rounded-xl border-slate-200 text-base font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            </div>
                            
                            <div>
                                <label class="block font-black text-slate-700 text-sm mb-2">Isi Konten Artikel <span class="text-rose-500">*</span></label>
                                <textarea name="konten" required rows="10" placeholder="Mulai menulis cerita Anda..." class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 leading-relaxed bg-slate-50 focus:bg-white"></textarea>
                            </div>
                        </div>
                        
                        <div class="space-y-5 bg-slate-50/50 p-5 rounded-2xl border border-slate-100 h-fit">
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-wider mb-2">Pilih Kategori <span class="text-rose-500">*</span></label>
                                <select name="kategori_blog_id" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white py-2.5">
                                    <option value="">-- Pilih --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-wider mb-2">Thumbnail Utama</label>
                                <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-slate-500 bg-white border border-slate-200 p-2 rounded-xl cursor-pointer file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                                <p class="text-[10px] text-slate-400 font-bold mt-2">Maks: 2MB. Resolusi 16:9 disarankan.</p>
                            </div>
                            
                            <hr class="border-slate-200">
                            
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-wider mb-2">Status Visibilitas <span class="text-rose-500">*</span></label>
                                <select name="is_published" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white py-2.5">
                                    <option value="1">🟢 Terbitkan Sekarang (Publish)</option>
                                    <option value="0">🟡 Simpan di Draf Dulu</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 mt-6">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors shadow-sm">Batal</button>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 cursor-pointer transition-all hover:-translate-y-0.5 flex items-center gap-2">
                            <span>🚀</span> Simpan & Eksekusi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT ARTIKEL --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-4xl w-full overflow-hidden flex flex-col max-h-[90vh]" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/30 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">📝</span> Ubah Data Artikel
                    </h3>
                    <button @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="'{{ route('master.blog.index') }}/' + editId" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6 bg-white">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-5">
                            <div>
                                <label class="block font-black text-slate-700 text-sm mb-2">Judul Artikel <span class="text-rose-500">*</span></label>
                                <input type="text" name="judul" x-model="editJudul" required class="w-full rounded-xl border-slate-200 text-base font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            </div>
                            
                            <div>
                                <label class="block font-black text-slate-700 text-sm mb-2">Isi Konten Artikel <span class="text-rose-500">*</span></label>
                                <textarea name="konten" x-model="editKonten" required rows="10" class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 leading-relaxed bg-slate-50 focus:bg-white"></textarea>
                            </div>
                        </div>
                        
                        <div class="space-y-5 bg-slate-50/50 p-5 rounded-2xl border border-slate-100 h-fit">
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-wider mb-2">Ubah Kategori <span class="text-rose-500">*</span></label>
                                <select name="kategori_blog_id" x-model="editKategoriId" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white py-2.5">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-wider mb-2">Ganti Gambar Baru</label>
                                <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-slate-500 bg-white border border-slate-200 p-2 rounded-xl cursor-pointer file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-amber-50 file:text-amber-600 hover:file:bg-amber-100">
                                <p class="text-[10px] text-amber-600 font-bold mt-2 bg-amber-50 px-2 py-1 rounded">Biarkan field ini kosong jika tidak ingin mengganti gambar.</p>
                            </div>
                            
                            <hr class="border-slate-200">
                            
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-wider mb-2">Ubah Status Visibilitas <span class="text-rose-500">*</span></label>
                                <select name="is_published" x-model="editIsPublished" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white py-2.5">
                                    <option value="1">🟢 Publikasikan (Tayang)</option>
                                    <option value="0">🟡 Simpan Jadi Draf</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 mt-6">
                        <button type="button" @click="openEdit = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors shadow-sm">Batal</button>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 cursor-pointer transition-all hover:-translate-y-0.5 flex items-center gap-2">
                            <span>💾</span> Perbarui Artikel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE --}}
        <div x-show="openDelete" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
             style="display: none;"
             x-transition>
            
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🗑️
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Hapus Artikel Ini?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Anda yakin ingin membuang artikel:
                    </p>
                    <div class="mt-3 p-3 bg-rose-50/50 border border-rose-100 rounded-xl text-rose-700 font-bold italic text-sm">
                        "<span x-text="deleteTargetTitle"></span>"
                    </div>
                </div>
                
                <form :action="deleteAction" method="POST" class="flex justify-center gap-3 w-full">
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