<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen Tahun Ajaran') }}
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
            
            <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Periode Tahun Ajaran</h3>
                    <p class="text-xs text-gray-500">Gunakan halaman ini untuk memanajemen kalender akademik sekolah. Hanya boleh ada 1 tahun ajaran aktif.</p>
                </div>
                
                <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1 self-start sm:self-center cursor-pointer">
                    ➕ Tambah Tahun Ajaran
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6">Nama Tahun Ajaran</th>
                            <th class="p-4 text-center">Status Akses</th>
                            <th class="p-4 pr-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                        @forelse($tahunAjarans as $ta)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4 pl-6 font-bold text-gray-900 text-sm">
                                    📅 {{ $ta->nama_tahun_ajaran }}
                                </td>
                                <td class="p-4 text-center">
                                    @if($ta->is_aktif)
                                        <span class="px-2.5 py-1 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold uppercase rounded-md shadow-sm">
                                            🟢 Aktif Saat Ini
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-gray-50 border border-gray-200 text-gray-400 text-[10px] font-medium uppercase rounded-md">
                                            Non-Aktif (Arsip)
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" onclick="openEditModal({{ json_encode($ta) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                            📝 Edit
                                        </button>

                                        <form action="{{ route('admin.tahun-ajaran.destroy', $ta->id) }}" method="POST" onsubmit="return confirm('Hapus periode {{ $ta->nama_tahun_ajaran }}? Data di dalamnya akan ikut tersembunyi.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Belum ada data tahun ajaran terdaftar.</td>
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
                <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Tahun Ajaran</h3>
                <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Tahun Ajaran *</label>
                    <input type="text" name="nama_tahun_ajaran" required placeholder="Contoh: 2024/2025" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status Publikasi *</label>
                    <select name="is_aktif" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="0">Non-Aktif (Arsip)</option>
                        <option value="1">Aktif (Gunakan Sekarang)</option>
                    </select>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 transform transition-all duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Tahun Ajaran</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
            </div>
            <form id="editForm" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Tahun Ajaran *</label>
                    <input type="text" id="edit_nama_tahun_ajaran" name="nama_tahun_ajaran" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status Publikasi *</label>
                    <select id="edit_is_aktif" name="is_aktif" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="0">Non-Aktif (Arsip)</option>
                        <option value="1">Aktif (Gunakan Sekarang)</option>
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

        function openEditModal(ta) {
            // Mapping field data sesuai property asli model Anda
            document.getElementById('edit_nama_tahun_ajaran').value = ta.nama_tahun_ajaran;
            document.getElementById('edit_is_aktif').value = ta.is_aktif ? 1 : 0;
            
            // Set action route update form secara dinamis berdasarkan ID
            document.getElementById('editForm').action = `/admin/tahun-ajaran/${ta.id}`;

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('flex');
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>