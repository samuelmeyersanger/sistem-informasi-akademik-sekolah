<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Kategori Blog') }}
        </h2>
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
    }" class="py-12 bg-slate-900/10 min-h-screen relative">
        
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="p-4 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-xl border border-emerald-200">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 text-xs font-medium text-rose-700 bg-rose-50 rounded-xl border border-rose-200">
                    ❌ {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 text-xs font-medium text-rose-700 bg-rose-50 rounded-xl border border-rose-200">
                    ⚠️ Terjadi kesalahan validasi. Silakan periksa kembali form Anda.
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Kategori</h3>
                        <p class="text-xs text-gray-500">Klasifikasi pengelompokan tulisan artikel/blog pada sistem.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ url()->current() }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-48 pr-8">
                                @if(request('search'))
                                    <a href="{{ url()->current() }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm">&times;</a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer shrink-0">
                                Cari
                            </button>
                        </form>

                        <button @click="openCreate = true" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all cursor-pointer flex items-center justify-center gap-1">
                            ➕ Tambah Kategori
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100/70 text-xs font-bold text-gray-600 uppercase border-b border-gray-200">
                                <th class="p-4 w-16 text-center">No</th>
                                <th class="p-4">Nama Kategori</th>
                                <th class="p-4">Slug URL</th>
                                <th class="p-4 text-center w-36">Jumlah Artikel</th>
                                <th class="p-4 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($kategoriBlogs as $index => $kategori)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Penomoran dinamis agar urutan tetap berlanjut saat pindah page --}}
                                    <td class="p-4 text-center font-mono text-gray-400">
                                        {{ $kategoriBlogs instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($kategoriBlogs->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                    <td class="p-4 font-bold text-gray-900 text-sm">{{ $kategori->nama }}</td>
                                    <td class="p-4 font-mono text-gray-500 text-[11px] bg-gray-50/30">{{ $kategori->slug }}</td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-0.5 font-bold rounded-full text-[11px] {{ $kategori->blogs_count > 0 ? 'bg-indigo-50 text-indigo-600' : 'bg-gray-100 text-gray-400' }}">
                                            {{ $kategori->blogs_count ?? 0 }} Konten
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" 
                                                    @click="
                                                        editId = '{{ $kategori->id }}';
                                                        editNama = '{{ addslashes($kategori->nama) }}';
                                                        openEdit = true;
                                                    " 
                                                    class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded shadow-sm transition-all cursor-pointer text-xs">
                                                ✏️ Edit
                                            </button>

                                            <button type="button"
                                                    @click="
                                                        deleteAction = '{{ route('master.kategori-blog.destroy', $kategori->id) }}';
                                                        deleteTargetName = '{{ addslashes($kategori->nama) }}';
                                                        openDelete = true;
                                                    "
                                                    class="px-2 py-1 bg-rose-500 hover:bg-rose-600 text-white font-semibold rounded shadow-sm transition-all cursor-pointer text-xs">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        Tidak ditemukan data kategori blog yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($kategoriBlogs, 'hasPages') && $kategoriBlogs->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $kategoriBlogs->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-md w-full overflow-hidden" @click.away="openCreate = false">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900">Tambah Kategori Baru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('master.kategori-blog.store') }}" method="POST" class="p-5 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" required maxlength="50" placeholder="Contoh: Pengumuman, Opini, Berita" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg cursor-pointer">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-md w-full overflow-hidden" @click.away="openEdit = false">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900">Ubah Nama Kategori</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>
                <form :action="'{{ route('master.kategori-blog.index') }}/' + editId" method="POST" class="p-5 space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" x-model="editNama" required maxlength="50" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-xs shadow-sm">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg cursor-pointer">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Kategori Permanen?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus kategori <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <form :action="deleteAction" method="POST" class="flex justify-center gap-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">Ya, Hapus</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>