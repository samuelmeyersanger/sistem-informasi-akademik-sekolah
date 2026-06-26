<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna Sistem') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editName: '',
        editEmail: '',
        editRoles: [],
        editIsApproved: false,
        modalTitle: 'Ubah Data Pengguna',
        modalButtonText: 'Simpan Perubahan',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(user, userRoleIds) {
            this.editActionUrl = `/master/user/${user.id}`;
            this.editName = user.name;
            this.editEmail = user.email;
            this.editRoles = userRoleIds.map(id => parseInt(id));
            this.editIsApproved = !!(user.is_approved == 1 || user.is_approved == true);
            
            if (this.editIsApproved) {
                this.modalTitle = 'Ubah Data Pengguna';
                this.modalButtonText = 'Simpan Perubahan';
            } else {
                this.modalTitle = 'Persetujuan Akun (Approval User Baru)';
                this.modalButtonText = 'Setujui & Plot Akun';
            }
            
            this.openEdit = true;
        },

        initDelete(actionUrl, userName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = userName;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center gap-2 shadow-sm">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl flex items-center gap-2 shadow-sm">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                    <p class="font-bold mb-1">Gagal menyimpan data. Silakan periksa kembali form:</p>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-wrap items-center border-b border-gray-200 gap-2 bg-white px-4 pt-3 rounded-t-2xl shadow-sm border border-gray-100">
                <a href="{{ route('master.user.index', array_merge(request()->except(['page', 'role']))) }}" 
                   class="px-4 py-2.5 text-xs font-bold border-b-2 transition-all flex items-center gap-1.5
                    {{ !request('role') ? 'border-indigo-600 text-indigo-600 bg-indigo-50/40 rounded-t-lg' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    👥 Semua Pengguna
                </a>

                @foreach($allRoles as $role)
                    <a href="{{ route('master.user.index', array_merge(request()->except(['page']), ['role' => $role->name])) }}" 
                       class="px-4 py-2.5 text-xs font-bold border-b-2 transition-all flex items-center gap-1.5
                        {{ request('role') === $role->name ? 'border-indigo-600 text-indigo-600 bg-indigo-50/40 rounded-t-lg' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        📁 {{ $role->display_name }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-b-2xl border-x border-b border-gray-100 mt-0">
                
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            Daftar {{ request('role') ? ucwords(str_replace('_', ' ', request('role'))) : 'Semua Pengguna' }}
                        </h3>
                        <p class="text-xs text-gray-500">Melihat dan mengelola akun terdaftar pada segmentasi hak akses ini secara fleksibel.</p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <form action="{{ route('master.user.index') }}" method="GET" class="flex items-center gap-2">
                            @if(request('role'))
                                <input type="hidden" name="role" value="{{ request('role') }}">
                            @endif
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-48 sm:w-64">
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer">Cari</button>
                        </form>

                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center gap-1 cursor-pointer">
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
                                                @php
                                                    // 🟢 GENERATE WARNA OTOMATIS BERDASARKAN STRING NAMA ROLE (Anti Hardcode)
                                                    $hash = md5($role->name);
                                                    $hue = hexdec(substr($hash, 0, 2)) % 360; 
                                                    // Ambil warna pastel cerah yang kontras dan nyaman di mata
                                                    $bgStyle = "background-color: hsl({$hue}, 85%, 96%); color: hsl({$hue}, 85%, 28%); border: 1px solid static; border-color: hsl({$hue}, 70%, 85%);";
                                                @endphp
                                                <span style="{{ $bgStyle }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider">
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
                                            <button type="button" @click="initEdit({{ json_encode($user) }}, {{ json_encode($user->roles->pluck('id')) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors text-xs font-medium inline-flex items-center gap-0.5 cursor-pointer" title="Ubah Data / Approval">
                                                📝 {{ $user->is_approved ? 'Edit' : 'Approve' }}
                                            </button>

                                            <button type="button" @click="initDelete('{{ route('master.user.destroy', $user->id) }}', '{{ addslashes($user->name) }}')" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-xs font-medium cursor-pointer" title="Hapus User">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Tidak ditemukan data pengguna di kategori ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-6 border-t border-gray-100 bg-gray-50/30">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Tambah Pengguna Baru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('master.user.store') }}" method="POST" class="space-y-4">
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
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900" x-text="modalTitle"></h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap *</label>
                        <input type="text" x-model="editName" name="name" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Email *</label>
                        <input type="email" x-model="editEmail" name="email" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Hak Akses Sistem (Bisa pilih lebih dari satu) *</label>
                        <div class="space-y-2 bg-gray-50 p-3 rounded-xl border border-gray-100">
                            @foreach($allRoles as $role)
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" x-model="editRoles" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
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
                            <input type="checkbox" name="is_approved" value="1" x-model="editIsApproved" class="sr-only peer">
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
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer" x-text="modalButtonText"></button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Pengguna Sistem?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus pengguna <span class="font-bold text-gray-800" x-text="deleteTargetName"></span> dari sistem? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
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