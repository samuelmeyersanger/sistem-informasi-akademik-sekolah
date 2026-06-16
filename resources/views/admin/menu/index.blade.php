<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen Menu Sidebar') }}
    </x-slot>

    <div class="space-y-6">
        
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
                    <form action="{{ request()->url() }}" method="GET" class="relative flex items-center min-w-[240px]">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nama menu / kategori..." 
                               class="w-full text-xs pl-8 pr-10 py-2 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-gray-700">
                        
                        <span class="absolute left-2.5 text-gray-400 text-xs pointer-events-none">🔍</span>
                        
                        @if(request('search'))
                            <a href="{{ request()->url() }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm cursor-pointer" title="Bersihkan Pencarian">
                                &times;
                            </a>
                        @endif
                    </form>

                    <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer shrink-0">
                        ➕ Tambah Menu Baru
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6">Kategori / Nama Menu</th>
                            <th class="p-4">Rute URL</th>
                            <th class="p-4 text-center">Urutan</th>
                            <th class="p-4">Permission Kunci</th>
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
                                <td class="p-4 font-mono text-gray-600 text-[11px]">
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
                                        <span class="text-gray-400 italic text-[11px]">Bebas Akses</span>
                                    @endif
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" onclick="openEditModal({{ json_encode($menu) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                            📝 Edit
                                        </button>

                                        <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Hapus menu {{ $menu->nama_menu }} dari struktur sidebar?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 italic bg-gray-50/30">
                                    @if(request('search'))
                                        Tidak ada hasil menu yang cocok dengan kata kunci "{{ request('search') }}".
                                    @else
                                        Belum ada struktur menu yang tersimpan di dalam database.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 transform transition-all duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Menu Sidebar</h3>
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form action="{{ route('admin.menu.store') }}" method="POST" class="p-6 space-y-4">
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
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 transform transition-all duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-sm font-bold text-gray-900 uppercase">Ubah Konfigurasi Menu</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form id="editForm" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kategori Kelompok *</label>
                    <input type="text" id="edit_kategori" name="kategori" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Menu Navigasi *</label>
                    <input type="text" id="edit_nama_menu" name="nama_menu" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Target Tautan URL *</label>
                    <input type="text" id="edit_url" name="url" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Icon Slugs</label>
                        <input type="text" id="edit_icon" name="icon" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Urutan *</label>
                        <input type="number" id="edit_urutan" name="urutan" required min="1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Batas Hak Akses (Permission)</label>
                    <select id="edit_permission_slug" name="permission_slug" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">-- Terbuka Untuk Umum (No Auth) --</option>
                        @foreach($permissions as $perm)
                            <option value="{{ $perm->name }}">{{ $perm->name }} ({{ $perm->modul }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            document.getElementById('createModal').classList.add('flex');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('flex');
            document.getElementById('createModal').classList.add('hidden');
        }

        function openEditModal(menu) {
            document.getElementById('edit_kategori').value = menu.kategori;
            document.getElementById('edit_nama_menu').value = menu.nama_menu;
            document.getElementById('edit_url').value = menu.url;
            document.getElementById('edit_icon').value = menu.icon || '';
            document.getElementById('edit_urutan').value = menu.urutan;
            document.getElementById('edit_permission_slug').value = menu.permission_slug || '';
            
            document.getElementById('editForm').action = `/admin/menu/${menu.id}`;

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('flex');
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>