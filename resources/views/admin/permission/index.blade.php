<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Izin Fitur (Permission)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded-lg text-sm">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 text-red-800 rounded-lg text-sm">⚠️ {{ session('error') }}</div>
            @endif

            <div class="bg-white p-6 shadow sm:rounded-lg border border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 uppercase mb-4">➕ Tambah Izin Fitur Baru</h3>
                <form action="{{ route('admin.permission.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-gray-600 font-medium mb-1">Nama Modul</label>
                            <input type="text" name="modul" required placeholder="Misal: Kesiswaan" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 font-medium mb-1">Nama Fitur / Izin</label>
                            <input type="text" name="name" required placeholder="Misal: Edit Absen" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 font-medium mb-1">Deskripsi Singkat</label>
                            <input type="text" name="description" placeholder="Opsional" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-600 font-bold mb-2">Berikan Izin Ini Langsung Kepada Role:</label>
                        <div class="flex flex-wrap gap-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                            @foreach($roles as $role)
                                <label class="inline-flex items-center text-xs text-gray-700 font-medium cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-1.5">
                                    {{ $role->display_name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-md shadow-sm transition-colors">
                            Simpan Izin Baru
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-100">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-bold text-xs uppercase">
                            <th class="p-4 w-1/5">Modul</th>
                            <th class="p-4 w-1/5">Kode Sistem</th>
                            <th class="p-4 w-1/4">Keterangan</th>
                            <th class="p-4 w-1/4">Pemilik Akses (Role)</th>
                            <th class="p-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                        @forelse($permissions as $permission)
                            <tr class="hover:bg-gray-50/50">
                                <td class="p-4 font-bold text-indigo-600 uppercase">📁 {{ $permission->modul }}</td>
                                <td class="p-4 font-mono font-bold bg-gray-50/50 text-gray-900">{{ $permission->name }}</td>
                                <td class="p-4 text-gray-500">{{ $permission->description ?? '-' }}</td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($permission->roles as $pRole)
                                            <span class="px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded text-[10px] font-bold uppercase shadow-sm">
                                                👤 {{ $pRole->display_name }}
                                            </span>
                                        @empty
                                            <span class="text-gray-400 italic text-[11px]">Belum ada role</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" onclick="openEditModal({{ json_encode($permission) }}, {{ json_encode($permission->roles->pluck('id')) }})" class="text-blue-600 hover:underline font-medium cursor-pointer">
                                            Edit
                                        </button>

                                        @if(!in_array($permission->name, ['akses-admin', 'kelola-role', 'kelola-user']))
                                            <form action="{{ route('admin.permission.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('Hapus izin {{ $permission->name }}?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline font-medium">Hapus</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 italic">Inti</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400 italic">Belum ada data permission yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-lg w-full shadow-xl border border-gray-200">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h3 class="text-sm font-bold text-gray-900 uppercase">📝 Ubah Data & Hak Akses Role</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>
            
            <form id="editForm" action="" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Modul</label>
                        <input type="text" id="edit_modul" name="modul" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Izin Fitur (Name)</label>
                        <input type="text" id="edit_name" name="name" required class="w-full text-xs rounded-lg border-gray-300 shadow-sm font-mono text-indigo-600 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Penjelasan</label>
                    <input type="text" id="edit_description" name="description" class="w-full text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Daftar Akun Jabatan yang Memiliki Izin Ini:</label>
                    <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                        @foreach($roles as $role)
                            <label class="inline-flex items-center text-xs text-gray-700 font-medium cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="edit_role_{{ $role->id }}" class="edit-role-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-1.5">
                                {{ $role->display_name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(permission, attachedRoleIds) {
            // 1. Masukkan data teks ke input modal
            document.getElementById('edit_modul').value = permission.modul;
            document.getElementById('edit_name').value = permission.name;
            document.getElementById('edit_description').value = permission.description ?? '';
            
            // 2. Tentukan Action URL Form secara dinamis
            document.getElementById('editForm').action = `/admin/permission/${permission.id}`;

            // 3. Reset semua checkbox di modal menjadi tidak tercentang terlebih dahulu
            document.querySelectorAll('.edit-role-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });

            // 4. Centang otomatis checkbox jika ID-nya masuk dalam daftar pemilik akses asli
            attachedRoleIds.forEach(roleId => {
                const checkbox = document.getElementById(`edit_role_${roleId}`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });

            // 5. Tampilkan Modal ke layar
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>