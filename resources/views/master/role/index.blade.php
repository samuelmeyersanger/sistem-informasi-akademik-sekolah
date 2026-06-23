<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Hak Akses (Role)') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editDisplayName: '',
        editName: '',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(role) {
            // Sinkronisasi endpoint aksi ke rute prefix /master/role/
            this.editActionUrl = `/master/role/${role.id}`;
            this.editDisplayName = role.display_name;
            this.editName = role.name;
            this.openEdit = true;
        },

        initDelete(actionUrl, roleName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = roleName;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
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
                    <p class="font-bold mb-1 flex items-center gap-1">⚠️ Gagal menyimpan data:</p>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-1">
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
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ route('master.role.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama role..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-48 pr-8">
                                
                                @if(request('search'))
                                    <a href="{{ route('master.role.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Hapus Pencarian">
                                        &times;
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg transition-colors cursor-pointer shrink-0">
                                🔍
                            </button>
                        </form>

                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer shrink-0">
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
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($roles as $role)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 font-medium text-gray-950 flex items-center gap-2">
                                        <span class="inline-block w-2 h-2 rounded-full 
                                            {{ in_array($role->name, ['admin', 'guru', 'siswa']) ? 'bg-indigo-500' : 'bg-slate-400' }}">
                                        </span>
                                        {{ $role->display_name }}
                                    </td>
                                    <td class="p-4 font-mono text-xs text-gray-500">{{ $role->name }}</td> 
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-0.5 bg-gray-100 border border-gray-200 text-gray-700 font-bold text-xs rounded-md">
                                            {{ $role->users_count ?? 0 }} User
                                        </span>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button @click="initEdit({{ json_encode($role) }})" class="p-1 text-blue-600 hover:text-blue-800 hover:underline text-xs font-medium cursor-pointer transition-colors">
                                                📝 Edit
                                            </button>

                                            @if(!in_array($role->name, ['admin', 'guru', 'siswa'])) 
                                                <button @click="initDelete('{{ route('master.role.destroy', $role->id) }}', '{{ addslashes($role->display_name) }}')" type="button" class="p-1 text-rose-600 hover:text-rose-800 hover:underline text-xs font-medium cursor-pointer transition-colors">
                                                    🗑️ Hapus
                                                </button>
                                            @else
                                                <span class="text-[10px] text-gray-400 font-bold uppercase bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200 select-none cursor-not-allowed">
                                                    🔒 Sistem
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        @if(request('search'))
                                            ❌ Hasil pencarian dengan kata kunci "{{ request('search') }}" tidak ditemukan.
                                        @else
                                            Belum ada data tingkatan role tambahan di database.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($roles->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $roles->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Buat Tingkatan Role Baru</h3>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-xl cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('master.role.store') }}" method="POST" class="space-y-4"
                      x-data="{ displayName: '{{ old('display_name', '') }}', get slug() { return this.displayName.toLowerCase().trim().replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-').replace(/^-+|-+$/g, '') } }">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Hak Akses / Jabatan *</label>
                        <input type="text" name="display_name" x-model="displayName" required placeholder="Contoh: Tata Usaha, Kepala Lab" class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Pratinjau Kode Identifikasi (Slug)</label>
                        <input type="text" :value="slug" disabled placeholder="Otomatis terisi..." class="w-full text-xs rounded-lg border-gray-200 bg-gray-50 text-gray-400 shadow-sm cursor-not-allowed font-mono">
                        <input type="hidden" name="name" :value="slug">
                        <p class="text-[10px] text-gray-400 mt-1">Sistem otomatis memformat tulisan menjadi struktur parameter URL & sistem.</p>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Buat Role</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Ubah Data Hak Akses</h3>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-xl cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Hak Akses Baru *</label>
                        <input type="text" x-model="editDisplayName" name="display_name" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Kode Identifikasi (Slug) System</label>
                        <input type="text" x-model="editName" disabled class="w-full text-xs rounded-lg border-gray-100 bg-gray-50 text-gray-400 shadow-sm cursor-not-allowed font-mono">
                        <p class="text-[10px] text-gray-400 mt-1">Sistem otomatis mengunci slug asli untuk mencegah kerusakan integritas relasi database.</p>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
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
                    <h4 class="text-sm font-bold text-gray-900">Hapus Role Secara Permanen?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus tingkatan role <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Hak akses dari user yang menggunakan role ini akan dicabut oleh sistem.
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