<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Menu Sidebar') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit State Form
        editAction: '',
        editData: { kategori: '', nama_menu: '', url: '', icon: '', urutan: '', permission_slug: '' },

        // Delete State Form
        deleteAction: '',
        deleteTargetName: '',

        initEdit(menu) {
            // Menyesuaikan dengan route master.menu.update secara dinamis
            this.editAction = `{{ route('master.menu.index') }}/${menu.id}`;
            this.editData.kategori = menu.kategori;
            this.editData.nama_menu = menu.nama_menu;
            this.editData.url = menu.url;
            this.editData.icon = menu.icon || '';
            this.editData.urutan = menu.urutan;
            this.editData.permission_slug = menu.permission_slug || '';
            this.openEdit = true;
        },

        initDelete(id, nama) {
            // Menyesuaikan dengan route master.menu.destroy secara dinamis
            this.deleteAction = `{{ route('master.menu.index') }}/${id}`;
            this.deleteTargetName = nama;
            this.openDelete = true;
        }
    }" class="py-12 relative min-h-screen">
        
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                    <p class="font-bold mb-1">Gagal menyimpan data. Silakan periksa kembali form:</p>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Konfigurasi Menu</h3>
                        <p class="text-xs text-gray-500">Kelola barisan tautan navigasi utama di bagian sidebar secara dinamis berdasarkan kategori dan urutan.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ request()->url() }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cari nama menu / kategori..." 
                                       class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                                
                                @if(request('search'))
                                    <a href="{{ request()->url() }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm cursor-pointer" title="Bersihkan Pencarian">
                                        &times;
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer shrink-0">
                                🔍 Cari
                            </button>
                        </form>

                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer shrink-0">
                            ➕ Tambah Menu Baru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-56">Kategori / Nama Menu</th>
                                <th class="p-4">Rute URL</th>
                                <th class="p-4 text-center w-24">Urutan</th>
                                <th class="p-4 w-52">Permission Kunci</th>
                                <th class="p-4 pr-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($menus as $menu)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6">
                                        <span class="block text-[10px] font-bold text-indigo-500 uppercase tracking-wide mb-0.5">{{ $menu->kategori }}</span>
                                        <div class="font-bold text-gray-900 text-sm flex items-center gap-1.5">
                                            <span class="text-gray-400">🔹</span> {{ $menu->nama_menu }}
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-gray-600 text-[11px] bg-gray-50/30">
                                        /{{ $menu->url }}
                                    </td>
                                    <td class="p-4 text-center font-bold text-gray-900 text-sm">
                                        {{ $menu->urutan }}
                                    </td>
                                    <td class="p-4">
                                        @if($menu->permission_slug)
                                            <span class="px-2 py-0.5 bg-amber-50 border border-amber-200 text-amber-700 rounded-md font-mono text-[10px]">
                                                🔒 {{ $menu->permission_slug }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-md font-medium text-[10px]">
                                                🔓 Bebas Akses
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button type="button" @click="initEdit({{ json_encode($menu) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                                📝 Edit
                                            </button>

                                            <button type="button" @click="initDelete('{{ $menu->id }}', '{{ addslashes($menu->nama_menu) }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        @if(request('search'))
                                            ❌ Tidak ada hasil menu yang cocok dengan kata kunci "{{ request('search') }}".
                                        @else
                                            Belum ada struktur menu yang tersimpan di dalam database.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($menus, 'hasPages') && $menus->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $menus->links() }}
                    </div>
                @endif

            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-200" @click.away="openCreate = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Menu Sidebar</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('master.menu.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kategori Kelompok *</label>
                        <input type="text" name="kategori" required placeholder="Contoh: Portal Berita" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Menu Navigasi *</label>
                        <input type="text" name="nama_menu" required placeholder="Contoh: Artikel Blog" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Target Tautan URL *</label>
                        <input type="text" name="url" required placeholder="Contoh: admin/blog" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Icon Slugs</label>
                            <input type="text" name="icon" placeholder="Contoh: document-text" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Urutan *</label>
                            <input type="number" name="urutan" required min="1" placeholder="Mulai: 1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Batas Hak Akses (Permission)</label>
                        <select name="permission_slug" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Terbuka Untuk Umum (No Auth) --</option>
                            @foreach($permissions as $perm)
                                <option value="{{ $perm->name }}">{{ $perm->name }} ({{ $perm->modul }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm cursor-pointer">Simpan Menu</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-200" @click.away="openEdit = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Ubah Konfigurasi Menu</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editAction" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kategori Kelompok *</label>
                        <input type="text" x-model="editData.kategori" name="kategori" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Menu Navigasi *</label>
                        <input type="text" x-model="editData.nama_menu" name="nama_menu" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Target Tautan URL *</label>
                        <input type="text" x-model="editData.url" name="url" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Icon Slugs</label>
                            <input type="text" x-model="editData.icon" name="icon" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Urutan *</label>
                            <input type="number" x-model="editData.urutan" name="urutan" required min="1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Batas Hak Akses (Permission)</label>
                        <select x-model="editData.permission_slug" name="permission_slug" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Terbuka Untuk Umum (No Auth) --</option>
                            @foreach($permissions as $perm)
                                <option value="{{ $perm->name }}">{{ $perm->name }} ({{ $perm->modul }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm cursor-pointer">Simpan Perubahan</button>
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
                    <h4 class="text-sm font-bold text-gray-900">Hapus Menu dari Sidebar?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus menu <span class="font-bold text-gray-800" x-text="deleteTargetName"></span> dari struktur sidebar? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <form :action="deleteAction" method="POST" class="flex justify-center gap-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer border border-transparent">
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