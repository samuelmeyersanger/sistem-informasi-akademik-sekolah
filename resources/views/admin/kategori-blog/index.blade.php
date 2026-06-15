<x-app-layout>
    <x-slot name="header">
        {{ __('Kategori Artikel & Blog') }}
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

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
            
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Kategori Konten</h3>
                    <p class="text-xs text-gray-500">Kelola pengelompokan berita, pengumuman, dan artikel sekolah agar mudah dicari oleh pembaca.</p>
                </div>
                
                <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1 self-start sm:self-center cursor-pointer">
                    ➕ Tambah Kategori
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6">Nama Kategori</th>
                            <th class="p-4">Kode URL (Slug)</th>
                            <th class="p-4 text-center">Jumlah Artikel Terkait</th>
                            <th class="p-4 pr-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                        @forelse($kategoriBlogs as $kat)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4 pl-6 font-bold text-gray-900 text-sm">
                                    🏷️ {{ $kat->nama }}
                                </td>
                                <td class="p-4 font-mono text-gray-500 text-xs">
                                    {{ $kat->slug }}
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-2 py-0.5 font-bold rounded-full {{ $kat->blogs_count > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $kat->blogs_count }} Konten
                                    </span>
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" onclick="openEditModal({{ json_encode($kat) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                            📝 Edit
                                        </button>

                                        <form action="{{ route('admin.kategori-blog.destroy', $kat->id) }}" method="POST" onsubmit="return confirm('Hapus permanen kategori {{ $kat->nama }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Belum ada kategori blog terdaftar.</td>
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
                <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Kategori Baru</h3>
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form action="{{ route('admin.kategori-blog.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kategori *</label>
                    <input type="text" name="nama" required placeholder="Contoh: Info Sekolah, Prestasi" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <p class="text-[10px] text-gray-400 mt-1">Sistem akan otomatis mengonversi nama ini menjadi format URL ramah SEO (slug).</p>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-indigo-700 cursor-pointer">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 transform transition-all duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Kategori</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form id="editForm" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kategori *</label>
                    <input type="text" id="edit_nama" name="nama" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-indigo-700 cursor-pointer">Simpan Perubahan</button>
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

        function openEditModal(kat) {
            document.getElementById('edit_nama').value = kat.nama;
            
            // Mengatur parameter target routing update secara berkala
            document.getElementById('editForm').action = `/admin/kategori-blog/${kat.id}`;

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('flex');
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>