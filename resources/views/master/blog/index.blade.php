<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Konten Blog / Artikel') }}
        </h2>
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
    }" class="py-12 bg-slate-900/10 min-h-screen">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="p-4 mb-2 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-xl border border-emerald-200">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-2 text-xs font-medium text-rose-700 bg-rose-50 rounded-xl border border-rose-200">
                    ⚠️ Gagal menyimpan data. Silakan periksa kembali isian form modal Anda.
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="p-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Manajemen Artikel</h3>
                        <p class="text-xs text-gray-500">Tulis dan atur publikasi artikel Anda langsung dari pop-up modal sistem.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ route('master.blog.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch gap-2 w-full lg:w-auto">
                            <select name="kategori_id" class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                                @endforeach
                            </select>

                            <div class="relative flex items-center">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-48 pr-8">
                                @if(request('search') || request('kategori_id'))
                                    <a href="{{ route('master.blog.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Reset">&times;</a>
                                @endif
                            </div>

                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer text-center">Filter</button>
                        </form>

                        <button @click="
                            editId = ''; editJudul = ''; editKategoriId = ''; editKonten = ''; editIsPublished = '1';
                            openCreate = true;
                        " class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm text-center cursor-pointer transition-all flex items-center justify-center gap-1">
                            ➕ Tulis Artikel
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100/70 text-xs font-bold text-gray-600 uppercase border-b border-gray-200">
                                <th class="p-4 w-12 text-center">No</th>
                                <th class="p-4 w-28 text-center">Gambar</th>
                                <th class="p-4">Informasi Konten</th>
                                <th class="p-4 w-44">Kategori</th>
                                <th class="p-4 w-36">Status</th>
                                <th class="p-4 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($blogs as $index => $blog)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4 text-center font-mono text-gray-400">
                                        {{ $blogs instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($blogs->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($blog->gambar)
                                            <img src="{{ asset('storage/' . $blog->gambar) }}" class="w-20 h-12 object-cover rounded-md border border-gray-100 shadow-sm mx-auto">
                                        @else
                                            <div class="w-20 h-12 bg-gray-100 text-[10px] text-gray-400 rounded-md flex items-center justify-center border border-dashed border-gray-200 mx-auto">No Image</div>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900 text-sm">{{ $blog->judul }}</div>
                                        <div class="text-[10px] text-gray-400 mt-1 flex items-center gap-2 font-medium">
                                            <span>✍️ {{ $blog->user->name ?? 'Anonim' }}</span>
                                            <span>•</span>
                                            <span>📅 {{ $blog->created_at->format('d M Y, H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 font-semibold rounded bg-indigo-50 border border-indigo-100 text-indigo-600">
                                            {{ $blog->kategori->nama ?? 'Tanpa Kategori' }}
                                        </span>
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        @if($blog->is_published)
                                            <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">🟢 Published</span>
                                        @else
                                            <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-md bg-amber-50 text-amber-700 border border-amber-200">🟡 Draft</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
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
                                                    class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded shadow-sm cursor-pointer transition-all text-[11px]">
                                                ✏️ Edit
                                            </button>

                                            <button type="button" 
                                                    @click="
                                                        deleteAction = '{{ route('master.blog.destroy', $blog->id) }}';
                                                        deleteTargetTitle = '{{ addslashes($blog->judul) }}';
                                                        openDelete = true;
                                                    "
                                                    class="px-2 py-1 bg-rose-500 hover:bg-rose-600 text-white font-semibold rounded shadow-sm cursor-pointer transition-all text-[11px]">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-gray-400 italic bg-gray-50/30">Tidak ditemukan artikel/blog yang tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($blogs, 'hasPages') && $blogs->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">{{ $blogs->links() }}</div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-2xl w-full overflow-hidden" @click.away="openCreate = false">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tulis Artikel Baru</h3>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('master.blog.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs max-h-[80vh] overflow-y-auto">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Judul Artikel <span class="text-rose-500">*</span></label>
                            <input type="text" name="judul" required placeholder="Masukkan judul..." class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                            <select name="kategori_blog_id" required class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Isi Konten Artikel <span class="text-rose-500">*</span></label>
                        <textarea name="konten" required rows="8" placeholder="Tulis isi tulisan lengkap disini..." class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Gambar Utama (Max 2MB)</label>
                            <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                            <select name="is_published" required class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1">Terbitkan Langsung (Publish)</option>
                                <option value="0">Simpan Sebagai Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg cursor-pointer">Publish Konten</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-2xl w-full overflow-hidden" @click.away="openEdit = false">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Ubah Data Artikel</h3>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>
                <form :action="'{{ route('master.blog.index') }}/' + editId" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs max-h-[80vh] overflow-y-auto">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Judul Artikel <span class="text-rose-500">*</span></label>
                            <input type="text" name="judul" x-model="editJudul" required class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                            <select name="kategori_blog_id" x-model="editKategoriId" required class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Isi Konten Artikel <span class="text-rose-500">*</span></label>
                        <textarea name="konten" x-model="editKonten" required rows="8" class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Ganti Gambar Baru (Biarkan kosong jika tetap)</label>
                            <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                            <select name="is_published" x-model="editIsPublished" required class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1">Terbitkan Langsung (Publish)</option>
                                <option value="0">Simpan Sebagai Draft</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg cursor-pointer">Perbarui Artikel</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             style="display: none;"
             x-transition>
            
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Konfirmasi Hapus</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus artikel <span class="font-bold text-gray-800" x-text="deleteTargetTitle"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                
                <form :action="deleteAction" method="POST" class="flex justify-center gap-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>