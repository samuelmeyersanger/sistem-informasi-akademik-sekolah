<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen Artikel & Berita Sekolah') }}
    </x-slot>

    <div class="space-y-6">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
            
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Publikasi Artikel</h3>
                    <p class="text-xs text-gray-500">Kelola rilis berita, info kegiatan, hingga pengumuman resmi sekolah ke portal publik.</p>
                </div>
                
                <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1 self-start sm:self-center cursor-pointer">
                    📝 Tulis Artikel Baru
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6 w-24">Cover</th>
                            <th class="p-4">Informasi Konten</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4 text-center">Status Tayang</th>
                            <th class="p-4 pr-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                        @forelse($blogs as $blog)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4 pl-6">
                                    @if($blog->gambar)
                                        <img src="{{ asset('storage/' . $blog->gambar) }}" alt="Cover" class="w-16 h-10 object-cover rounded-md border border-gray-100 shadow-sm">
                                    @else
                                        <div class="w-16 h-10 bg-gray-100 border border-gray-200 text-gray-400 rounded-md flex items-center justify-center text-[10px] font-medium">No Image</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-gray-900 text-sm max-w-md truncate">{{ $blog->judul }}</p>
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400 mt-1">
                                        <span>✍️ {{ $blog->user->name ?? 'Anonim' }}</span>
                                        <span>•</span>
                                        <span>📅 {{ $blog->created_at->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-medium rounded-md border border-slate-200/50">
                                        🏷️ {{ $blog->kategori->nama ?? 'Umum' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    @if($blog->is_published)
                                        <span class="px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold uppercase rounded-md shadow-sm">
                                            🌐 Publik
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-bold uppercase rounded-md">
                                            🔒 Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" onclick="openEditModal({{ json_encode($blog) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                            📝 Edit
                                        </button>

                                        <form action="{{ route('admin.blog.destroy', $blog->id) }}" method="POST" onsubmit="return confirm('Hapus permanen artikel ini? Gambar terkait akan ikut terhapus.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Belum ada artikel yang ditulis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-gray-100 transform transition-all duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-sm font-bold text-gray-900 uppercase">Tulis Artikel Baru</h3>
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Artikel *</label>
                        <input type="text" name="judul" required placeholder="Masukkan judul berita utama" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori Konten *</label>
                        <select name="kategori_blog_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Opsi Publikasi *</label>
                        <select name="is_published" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="1">Langsung Terbitkan (Publik)</option>
                            <option value="0">Simpan Sebagai Draft (Internal)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Gambar Unggulan (Cover)</label>
                        <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maksimal ukuran 2MB.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Isi Konten Artikel *</label>
                        <textarea name="konten" rows="6" required placeholder="Tuliskan berita lengkap di sini..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-indigo-700 cursor-pointer">Terbitkan Konten</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-gray-100 transform transition-all duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Artikel</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form id="editForm" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Artikel *</label>
                        <input type="text" id="edit_judul" name="judul" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori Konten *</label>
                        <select id="edit_kategori_blog_id" name="kategori_blog_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Opsi Publikasi *</label>
                        <select id="edit_is_published" name="is_published" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="1">Langsung Terbitkan (Publik)</option>
                            <option value="0">Simpan Sebagai Draft (Internal)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Ganti Gambar Unggulan (Biarkan kosong jika tidak diganti)</label>
                        <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Isi Konten Artikel *</label>
                        <textarea id="edit_konten" name="konten" rows="6" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>
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

        function openEditModal(blog) {
            document.getElementById('edit_judul').value = blog.judul;
            document.getElementById('edit_kategori_blog_id').value = blog.kategori_blog_id;
            document.getElementById('edit_konten').value = blog.konten;
            document.getElementById('edit_is_published').value = blog.is_published ? 1 : 0;
            
            // Set dynamic action route target form update
            document.getElementById('editForm').action = `/admin/blog/${blog.id}`;

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('flex');
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>