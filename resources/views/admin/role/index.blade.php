<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Hak Akses (Role)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center gap-2 shadow-sm">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl flex items-center gap-2 shadow-sm">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                    <p class="font-bold mb-1">Gagal menyimpan data:</p>
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
                        <h3 class="text-base font-bold text-gray-900">Master Data Role</h3>
                        <p class="text-xs text-gray-500">Atur tingkatan hak akses yang tersedia di dalam sistem aplikasi sekolah.</p>
                    </div>
                    
                    <div>
                        <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1">
                            ➕ Tambah Role Baru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Jabatan / Role</th>
                                <th class="p-4">Kode Identifikasi (Slug)</th>
                                <th class="p-4 text-center">Jumlah Pengguna</th>
                                <th class="p-4 pr-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                       <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($roles as $role)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 font-medium text-gray-950 flex items-center gap-2">
                                        <span class="inline-block w-2 h-2 rounded-full 
                                            {{ in_array($role->name, ['admin', 'guru', 'siswa']) ? 'bg-indigo-500' : 'bg-gray-400' }}">
                                        </span>
                                        {{ $role->display_name }} </td>
                                    <td class="p-4 font-mono text-xs text-gray-500">{{ $role->name }}</td> <td class="p-4 text-center">
                                        <span class="px-2 py-0.5 bg-gray-100 border border-gray-200 text-gray-700 font-bold text-xs rounded-md">
                                            {{ $role->users_count }} User
                                        </span>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button onclick="openEditModal({{ json_encode($role) }})" class="p-1 text-blue-600 hover:underline text-xs font-medium">
                                                📝 Edit
                                            </button>

                                            @if(!in_array($role->name, ['admin', 'guru', 'siswa'])) <form action="{{ route('admin.role.destroy', $role->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 text-rose-600 hover:underline text-xs font-medium">🗑️ Hapus</button>
                                                </form>
                                            @else
                                                <span class="text-[10px] text-gray-400 font-semibold uppercase bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100 cursor-not-allowed">🔒 Sistem</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Belum ada data tingkatan role tambahan di database.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 transform transition-all duration-300 scale-95 opacity-0" id="createModalBox">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-base font-bold text-gray-900">Buat Tingkatan Role Baru</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.role.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Hak Akses / Jabatan *</label>
                    <input type="text" id="create_name" name="name" required oninput="generateCreateSlug()" placeholder="Contoh: Tata Usaha, Kepala Lab" class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Pratinjau Kode Identifikasi (Slug)</label>
                    <input type="text" id="create_slug" readonly placeholder="Otomatis terisi..." class="w-full text-xs rounded-lg border-gray-100 bg-gray-50 text-gray-400 shadow-sm cursor-not-allowed font-mono">
                    <p class="text-[10px] text-gray-400 mt-1">Sistem otomatis memformat tulisan menjadi standar kode sistem.</p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">Buat Role</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 transform transition-all duration-300 scale-95 opacity-0" id="editModalBox">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-base font-bold text-gray-900">Ubah Data Hak Akses</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Hak Akses Baru *</label>
                    <input type="text" id="edit_name" name="name" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Kode Identifikasi (Slug) System</label>
                    <input type="text" id="edit_slug" readonly class="w-full text-xs rounded-lg border-gray-100 bg-gray-50 text-gray-400 shadow-sm cursor-not-allowed font-mono">
                    <p class="text-[10px] text-gray-400 mt-1">Sistem otomatis mengunci slug untuk mencegah kerusakan relasi database.</p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            const modal = document.getElementById('createModal');
            const box = document.getElementById('createModalBox');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 50);
        }

        function closeCreateModal() {
            const modal = document.getElementById('createModal');
            const box = document.getElementById('createModalBox');
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 200);
        }

        function openEditModal(role) {
            const modal = document.getElementById('editModal');
            const box = document.getElementById('editModalBox');
            
            document.getElementById('edit_name').value = role.display_name; // 👈 Isi dengan display_name
            document.getElementById('edit_slug').value = role.name;          // 👈 Isi dengan name pendek
            
            document.getElementById('editForm').action = `/admin/role/${role.id}`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                box.classList.remove('scale-95', 'opacity-0');
                box.classList.add('scale-100', 'opacity-100');
            }, 50);
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            const box = document.getElementById('editModalBox');
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 200);
        }
        function generateCreateSlug() {
            const nameInput = document.getElementById('create_name').value; // Ambil dari input display_name
            const slugInput = document.getElementById('create_slug');
            
            const formattedSlug = nameInput
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_]+/g, '-')
                .replace(/^-+|-+$/g, '');
                
            slugInput.value = formattedSlug; // Dimasukkan ke pratinjau name (kode pendek)
        }
    </script>
</x-app-layout>