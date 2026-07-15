<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🔑</span> {{ __('Manajemen Hak Akses') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Buat daftar kunci izin aplikasi dan atur batasan fitur untuk masing-masing jabatan.</p>
            </div>
        </div>
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
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <div class="mt-1">{{ session('error') }}</div>
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Katalog Izin (Permissions)</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Daftar blokir/buka kode fitur (*slug*) yang dapat ditempelkan ke masing-masing *Role*.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                        <form action="{{ route('master.permission.index') }}" method="GET" class="flex items-stretch gap-2 w-full md:w-auto">
                            <div class="relative flex items-center w-full sm:w-64 group">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cari kunci izin, modul..." 
                                       class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                                
                                @if(request('search'))
                                    <a href="{{ route('master.permission.index') }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Bersihkan Pencarian">
                                        <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2 justify-center">
                                <span class="hidden sm:inline">🔍</span> Saring
                            </button>
                        </form>

                        <div class="hidden md:block w-px h-8 bg-slate-200"></div>

                        <button @click="openCreate = true; attachedRoles = []" 
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 w-full sm:w-auto shrink-0">
                            <span>➕</span> Buat Kunci Baru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-8 w-44">Sektor / Modul</th>
                                <th class="p-5 w-60">Kode Kunci Izin (Slug)</th>
                                <th class="p-5">Rincian Penggunaan</th>
                                <th class="p-5 w-64">Dipegang Oleh Jabatan</th>
                                <th class="p-5 pr-8 text-center w-36">Kelola</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($permissions as $perm)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8 align-top">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg text-xs font-black uppercase tracking-wider">
                                            <span>📦</span> {{ $perm->modul }}
                                        </div>
                                    </td>
                                    <td class="p-5 align-top">
                                        <div class="inline-flex flex-col gap-1">
                                            <span class="inline-block px-3 py-1.5 font-mono text-[11px] font-bold tracking-widest text-slate-700 bg-slate-100 border border-slate-200 rounded-lg shadow-inner">
                                                {{ $perm->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-5 align-top">
                                        <div class="text-sm font-bold text-slate-800 leading-snug">
                                            {{ $perm->description ?? '— Tanpa Catatan Deskripsi —' }}
                                        </div>
                                    </td>
                                    <td class="p-5 align-top">
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse($perm->roles as $r)
                                                <span class="inline-flex items-center px-2 py-1 bg-slate-800 text-white font-bold text-[10px] uppercase tracking-widest rounded-md shadow-sm">
                                                    {{ $r->display_name }}
                                                </span>
                                            @empty
                                                <span class="inline-flex items-center px-2 py-1 bg-slate-100 text-slate-400 font-bold text-[10px] uppercase tracking-widest rounded-md border border-slate-200 border-dashed">
                                                    TIDAK DIGUNAKAN
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-5 pr-8 text-center align-top">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="initEdit({{ json_encode($perm) }}, {{ json_encode($perm->roles->pluck('id')) }})" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Ubah Konfigurasi Kunci">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            @if(!in_array($perm->name, ['akses-admin', 'kelola-role', 'kelola-user']))
                                                <button type="button" @click="initDelete('{{ route('master.permission.destroy', $perm->id) }}', '{{ addslashes($perm->name) }}')" 
                                                        class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Kunci Izin">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            @else
                                                <div class="inline-flex items-center justify-center w-9 h-9 bg-slate-100 border border-slate-200 text-slate-400 rounded-xl shadow-inner cursor-not-allowed" title="Permission Inti Sistem (Terkunci)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                @if(request('search')) 🔍 @else 🛡️ @endif
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">
                                                @if(request('search')) Hasil Nihil @else Gudang Izin Kosong @endif
                                            </h4>
                                            <span class="text-sm">
                                                @if(request('search')) 
                                                    Pencarian kata kunci "{{ request('search') }}" tidak menemukan permission apapun.
                                                @else
                                                    Belum ada satupun izin otorisasi yang terdaftar pada sistem database.
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($permissions->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $permissions->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL CREATE PERMISSION --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        <span class="text-2xl">✨</span> Registrasi Izin Akses
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('master.permission.store') }}" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Nama Kelompok Modul <span class="text-rose-500">*</span></label>
                            <input type="text" name="modul" required placeholder="Cth: Kelola Data Siswa" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                        </div>
                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Kode Identifier (Slug) <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required placeholder="Cth: baca-nilai-rapor" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 font-mono bg-slate-50 placeholder-slate-300">
                        </div>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Penjelasan Detail Fungsi (Opsional)</label>
                        <textarea name="description" placeholder="Deskripsikan dengan singkat pembatasan apa yang dilakukan izin ini..." rows="3" class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300"></textarea>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center justify-between">
                            <span>Sematkan ke Jabatan Khusus (Role)</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200 max-h-48 overflow-y-auto shadow-inner">
                            @forelse($roles as $r)
                                <label class="flex items-center gap-3 cursor-pointer group bg-white p-2.5 rounded-lg border border-slate-200 hover:border-indigo-300 transition-colors shadow-sm">
                                    <input type="checkbox" name="roles[]" value="{{ $r->id }}" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700 group-hover:text-indigo-700">{{ $r->display_name }}</span>
                                </label>
                            @empty
                                <div class="col-span-1 sm:col-span-2 text-center text-xs font-bold text-slate-400 p-2">Belum ada role yang bisa dipilih.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Tutup</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>💾</span> Enkripsi Izin Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT PERMISSION --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/30 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        <span class="text-2xl">📝</span> Koreksi Sandi Akses
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="editActionUrl" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Nama Kelompok Modul <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="editModul" name="modul" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                        </div>
                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Kode Identifier (Slug) <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="editName" name="name" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 font-mono bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Penjelasan Detail Fungsi (Opsional)</label>
                        <textarea x-model="editDescription" name="description" rows="3" class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4"></textarea>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center justify-between">
                            <span>Sinkronisasi Kepemilikan Role</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200 max-h-48 overflow-y-auto shadow-inner">
                            @forelse($roles as $r)
                                <label class="flex items-center gap-3 cursor-pointer group bg-white p-2.5 rounded-lg border border-slate-200 hover:border-indigo-300 transition-colors shadow-sm">
                                    <input type="checkbox" name="roles[]" value="{{ $r->id }}" :checked="attachedRoles.includes({{ $r->id }})" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    <span class="text-xs font-bold text-slate-700 group-hover:text-indigo-700">{{ $r->display_name }}</span>
                                </label>
                            @empty
                                <div class="col-span-1 sm:col-span-2 text-center text-xs font-bold text-slate-400 p-2">Belum ada role yang bisa dipilih.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openEdit = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>🔄</span> Terapkan Pembaruan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE PERMISSION --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🧨
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Hancurkan Kunci Izin?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Anda akan memusnahkan permission <strong class="text-slate-800 font-mono" x-text="deleteTargetName"></strong>. Jika sistem masih mengandalkan kunci ini, fitur tersebut mungkin akan jebol terbuka.
                    </p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 w-full pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="flex-1 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors">
                        Urungkan
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md cursor-pointer transition-colors border border-transparent flex items-center justify-center gap-2">
                        Musnahkan
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>