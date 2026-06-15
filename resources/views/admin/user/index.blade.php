<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna Staf & Guru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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
                        <h3 class="text-base font-bold text-gray-900">Daftar Pengguna</h3>
                        <p class="text-xs text-gray-500">Kelola hak akses akun admin, guru, dan staf administrasi sekolah.</p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <form action="{{ route('admin.user.index') }}" method="GET" class="flex items-center gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-48 sm:w-64">
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors">Cari</button>
                        </form>

                        <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1">
                            ➕ Tambah User
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Nama Lengkap</th>
                                <th class="p-4">Email</th>
                                <th class="p-4">Hak Akses (Role)</th>
                                <th class="p-4">Status Akun</th>
                                <th class="p-4">Terdaftar Pada</th>
                                <th class="p-4 pr-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 font-medium text-gray-950">{{ $user->name }}</td>
                                    <td class="p-4 text-gray-600">{{ $user->email }}</td>
                                    
                                    <td class="p-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($user->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider
                                                    {{ $role->name === 'admin' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
                                                    {{ $role->name === 'guru' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                                    {{ $role->name === 'siswa' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                                ">
                                                    {{ $role->display_name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-400 italic">Belum Diplot</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <td class="p-4">
                                        @if($user->is_approved)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 animate-pulse">
                                                Pending Approve
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-gray-500 text-xs">{{ $user->created_at->translatedFormat('d F Y, H:i') }}</td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="openEditModal({{ json_encode($user) }}, {{ json_encode($user->roles->pluck('id')) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors text-xs font-medium inline-flex items-center gap-0.5" title="Ubah Data / Approval">
                                                📝 {{ $user->is_approved ? 'Edit' : 'Approve' }}
                                            </button>

                                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }} dari sistem?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-xs font-medium" title="Hapus User">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Tidak ditemukan data pengguna di dalam sistem.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-6 border-t border-gray-100 bg-gray-50/30">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 transform transition-all duration-300 scale-95 opacity-0" id="createModalBox">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-base font-bold text-gray-900">Tambah Pengguna Baru</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <form action="{{ route('admin.user.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Email Resmi *</label>
                    <input type="email" name="email" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Hak Akses Sistem (Bisa pilih lebih dari satu) *</label>
                    <div class="space-y-2 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        @foreach($allRoles as $role)
                            <label class="flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span>{{ $role->display_name }} (<span class="font-mono text-gray-400 text-[10px]">{{ strtoupper($role->name) }}</span>)</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="is_approved" value="1">
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Password *</label>
                        <input type="password" name="password" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Konfirmasi *</label>
                        <input type="password" name="password_confirmation" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 transform transition-all duration-300 scale-95 opacity-0" id="editModalBox">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-base font-bold text-gray-900" id="modalEditTitle">Ubah Data Pengguna</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap *</label>
                    <input type="text" id="edit_name" name="name" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Email *</label>
                    <input type="email" id="edit_email" name="email" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Hak Akses Sistem (Bisa pilih lebih dari satu) *</label>
                    <div class="space-y-2 bg-gray-50 p-3 rounded-xl border border-gray-100">
                        @foreach($allRoles as $role)
                            <label class="flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="edit-role-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span>{{ $role->display_name }} (<span class="font-mono text-gray-400 text-[10px]">{{ strtoupper($role->name) }}</span>)</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-gray-800">Status Persetujuan Akses</label>
                        <p class="text-[10px] text-gray-500">Aktifkan untuk memberikan izin masuk dashboard.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_approved" id="edit_is_approved" value="1" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
                
                <div class="p-3 bg-amber-50/60 border border-amber-100 rounded-xl text-[11px] text-amber-800">
                    💡 <strong>Tips Keamanan:</strong> Kosongkan kolom password di bawah ini jika tidak ingin mengubah kata sandi lama staf/guru tersebut.
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Password Baru</label>
                        <input type="password" name="password" class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Konfirmasi Pass</label>
                        <input type="password" name="password_confirmation" class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors" id="modalEditButton">Simpan Perubahan</button>
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

        function openEditModal(user, userRoleIds) {
            const modal = document.getElementById('editModal');
            const box = document.getElementById('editModalBox');
            const title = document.getElementById('modalEditTitle');
            const button = document.getElementById('modalEditButton');
            
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            
            // Logika Otomatisasi Centang Checkbox Sesuai Role Milik User
            const roleCheckboxes = document.querySelectorAll('.edit-role-checkbox');
            roleCheckboxes.forEach(checkbox => {
                if (userRoleIds.includes(parseInt(checkbox.value))) {
                    checkbox.checked = true;
                } else {
                    checkbox.checked = false;
                }
            });
            
            const checkboxApproval = document.getElementById('edit_is_approved');
            if (user.is_approved == 1 || user.is_approved == true) {
                checkboxApproval.checked = true;
                title.innerText = "Ubah Data Pengguna";
                button.innerText = "Simpan Perubahan";
            } else {
                checkboxApproval.checked = false;
                title.innerText = "Persetujuan Akun (Approval User Baru)";
                button.innerText = "Setujui & Plot Akun";
            }
            
            document.getElementById('editForm').action = `/admin/user/${user.id}`;

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
    </script>
</x-app-layout>