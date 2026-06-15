<x-app-layout>
    <x-slot name="header">
        {{ __('Navigasi Tautan Teks Bawah (Footer Links)') }}
    </x-slot>

    <div class="space-y-6">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                <p class="font-bold mb-1">Gagal memproses data. Silakan cek kembali inputan Anda:</p>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Link Informasi Footer</h3>
                    <p class="text-xs text-gray-500">Kelola tautan cepat/internal eksternal untuk dipajang pada kolom informasi bawah website utama.</p>
                </div>
                
                <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                    ➕ Tambah Link Footer
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6 w-48">Kelompok (Group)</th>
                            <th class="p-4">Label Judul</th>
                            <th class="p-4">Alamat URL</th>
                            <th class="p-4 text-center w-24">Urutan</th>
                            <th class="p-4 text-center w-32">Status</th>
                            <th class="p-4 pr-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                        @forelse($links as $link)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4 pl-6">
                                    <span class="px-2 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-md font-bold text-[10px] uppercase">
                                        📁 {{ $link->group }}
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-gray-900 text-sm">🔗 {{ $link->judul }}</td>
                                <td class="p-4 font-mono text-gray-500 text-[11px] max-w-xs truncate" title="{{ $link->url }}">
                                    {{ $link->url }}
                                </td>
                                <td class="p-4 text-center font-mono font-bold text-gray-600">{{ $link->urutan }}</td>
                                <td class="p-4 text-center">
                                    @if($link->status)
                                        <span class="px-2 py-0.5 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold rounded shadow-sm">AKTIF</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-50 border border-gray-200 text-gray-400 text-[10px] font-medium rounded">NON-AKTIF</span>
                                    @endif
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" onclick="openEditModal({{ json_encode($link) }})" class="text-amber-600 hover:underline font-semibold cursor-pointer">
                                            📝 Edit
                                        </button>

                                        <form action="{{ route('admin.footer-link.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Hapus tautan {{ $link->judul }} dari susunan menu?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:underline font-semibold cursor-pointer">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Belum ada tautan footer yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 transform transition-all duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Tautan Footer</h3>
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form action="{{ route('admin.footer-link.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kelompok Menu / Group *</label>
                    <input type="text" name="group" required placeholder="Contoh: Aplikasi Sekolah, Link Terkait" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Label Teks Tautan / Judul *</label>
                    <input type="text" name="judul" required placeholder="Contoh: Portal SIMPKB atau Kelulusan" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Target Alamat URL *</label>
                    <input type="text" name="url" required placeholder="Contoh: https://ppdb.sekolah.sch.id atau /page/visi-misi" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Urut Tampilan / Urutan *</label>
                    <input type="number" name="urutan" required min="0" value="1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="status_create" name="status" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm">
                    <label for="status_create" class="text-xs font-semibold text-gray-700 cursor-pointer select-none">Aktifkan dan tampilkan langsung di web.</label>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 transform transition-all duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Tautan Footer</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form id="editForm" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kelompok Menu / Group *</label>
                    <input type="text" id="edit_group" name="group" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Label Teks Tautan / Judul *</label>
                    <input type="text" id="edit_judul" name="judul" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Target Alamat URL *</label>
                    <input type="text" id="edit_url" name="url" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nomor Urut Tampilan / Urutan *</label>
                    <input type="number" id="edit_urutan" name="urutan" required min="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="edit_status" name="status" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm">
                    <label for="edit_status" class="text-xs font-semibold text-gray-700 cursor-pointer select-none">Tautan ini aktif digunakan.</label>
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

        function openEditModal(link) {
            // Isi form edit modal secara dinamis dari data baris objek terpilih
            document.getElementById('edit_group').value = link.group;
            document.getElementById('edit_judul').value = link.judul;
            document.getElementById('edit_url').value = link.url;
            document.getElementById('edit_urutan').value = link.urutan;
            document.getElementById('edit_status').checked = !!link.status;
            
            // Set action update route secara dinamis berdasarkan ID
            document.getElementById('editForm').action = `/admin/footer-link/${link.id}`;

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('flex');
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>