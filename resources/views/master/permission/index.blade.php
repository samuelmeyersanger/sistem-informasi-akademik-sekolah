<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Hak Akses (Permission)') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editModul: '',
        editName: '',
        editDescription: '',
        attachedRoles: [],

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(perm, attachedRoleIds) {
            this.editActionUrl = `/master/permission/${perm.id}`;
            this.editModul = perm.modul;
            this.editName = perm.name;
            this.editDescription = perm.description ?? '';
            this.attachedRoles = attachedRoleIds;
            this.openEdit = true;
        },

        initDelete(actionUrl, permName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = permName;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Master Data Detail Izin (Permission)</h3>
                        <p class="text-xs text-gray-500">Daftar kunci pengaman baris kode fitur aplikasi berdasarkan masing-masing modul.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form action="{{ route('master.permission.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari izin / modul / deskripsi..." class="text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm w-full sm:w-56 pr-8">
                                
                                @if(request('search'))
                                    <a href="{{ route('master.permission.index') }}" class="absolute right-2.5 text-gray-400 hover:text-gray-600 font-bold text-sm" title="Clear Search">
                                        &times;
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer shrink-0">
                                🔍 Cari
                            </button>
                        </form>

                        <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer shrink-0">
                            ➕ Tambah Izin Baru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-40">Modul</th>
                                <th class="p-4 w-52">Kunci Izin (Slug)</th>
                                <th class="p-4">Deskripsi / Kegunaan Fitur</th>
                                <th class="p-4 w-60">Dimiliki Oleh Role</th>
                                <th class="p-4 pr-6 text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($permissions as $perm)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 font-bold text-indigo-600">
                                        📦 {{ $perm->modul }}
                                    </td>
                                    <td class="p-4 font-mono font-medium text-gray-900 bg-gray-50/40">
                                        {{ $perm->name }}
                                    </td>
                                    <td class="p-4 text-gray-500 max-w-xs truncate" title="{{ $perm->description }}">
                                        {{ $perm->description ?? '-' }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($perm->roles as $r)
                                                <span class="px-1.5 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 font-medium text-[10px] rounded">
                                                    {{ $r->display_name }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400 italic text-[10px]">Belum diikat ke role</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="initEdit({{ json_encode($perm) }}, {{ json_encode($perm->roles->pluck('id')) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                                📝 Edit
                                            </button>

                                            @if(!in_array($perm->name, ['akses-admin', 'kelola-role', 'kelola-user']))
                                                <button @click="initDelete('{{ route('master.permission.destroy', $perm->id) }}', '{{ addslashes($perm->name) }}')" type="button" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                                    🗑️ Hapus
                                                </button>
                                            @else
                                                <span class="text-[10px] text-gray-400 font-semibold bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100 cursor-not-allowed">🔒 Inti</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        @if(request('search'))
                                            ❌ Hasil pencarian dengan kata kunci "{{ request('search') }}" tidak ditemukan.
                                        @else
                                            Belum ada data permission yang terdaftar.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($permissions->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $permissions->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Registrasi Kunci Izin Baru</h3>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-xl cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('master.permission.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Modul Induk *</label>
                        <input type="text" name="modul" required placeholder="Contoh: Kesiswaan, Kelola Nilai, Inventaris" class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Pengaman Fitur (Slug) *</label>
                        <input type="text" name="name" required placeholder="Contoh: edit-nilai-rapor" class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Tambahan / Kegunaan</label>
                        <textarea name="description" placeholder="Menjelaskan fungsi pembatasan tombol ini..." rows="2" class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Langsung Tempelkan ke Jabatan (Role):</label>
                        <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded-xl border border-gray-100 max-h-32 overflow-y-auto">
                            @foreach($roles as $r)
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $r->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    {{ $r->display_name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 cursor-pointer">Simpan Izin</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Ubah Data Kunci Izin</h3>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-xl cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Modul Induk *</label>
                        <input type="text" x-model="editModul" name="modul" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Pengaman Fitur (Slug) *</label>
                        <input type="text" x-model="editName" name="name" required class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi Tambahan / Kegunaan</label>
                        <textarea x-model="editDescription" name="description" rows="2" class="w-full text-xs rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Sinkronisasi Hak Akses Role:</label>
                        <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded-xl border border-gray-100 max-h-32 overflow-y-auto">
                            @foreach($roles as $r)
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $r->id }}" :checked="attachedRoles.includes({{ $r->id }})" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    {{ $r->display_name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 cursor-pointer">Simpan Perubahan</button>
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
                    <h4 class="text-sm font-bold text-gray-900">Hapus Kunci Izin Permanen?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus kunci izin <span class="font-mono font-bold text-gray-800" x-text="deleteTargetName"></span>? Tindakan pembatasan baris kode fitur ini akan dibersihkan dari sistem.
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