<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🛡️</span> {{ __('Level Hak Akses (Roles)') }}
                </h2>
            </div>
        </div>
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
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6 relative z-10">
            
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

            @if ($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">🚧</span> 
                    <div>
                        <div class="mb-1 text-base font-black text-amber-900">Validasi Data Gagal!</div>
                        <ul class="list-disc list-inside text-xs font-medium text-amber-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Katalog Tingkat Jabatan</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Struktur hierarki akses yang tersedia untuk dikaitkan dengan akun pengguna (User).</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                        <form action="{{ route('master.role.index') }}" method="GET" class="flex items-stretch gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-64 group">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Cari label jabatan..." 
                                       class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                                
                                @if(request('search'))
                                    <a href="{{ route('master.role.index') }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Bersihkan Pencarian">
                                        <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2 justify-center">
                                <span class="hidden sm:inline">🔍</span>
                            </button>
                        </form>

                        <div class="hidden sm:block w-px h-8 bg-slate-200"></div>

                        <button @click="openCreate = true" 
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 shrink-0">
                            <span>➕</span> Role Baru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-8">Identitas Jabatan / Role</th>
                                <th class="p-5 w-56">Format Kunci URL (Slug)</th>
                                <th class="p-5 text-center w-40">Total Akun Induk</th>
                                <th class="p-5 pr-8 text-center w-36">Kelola</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($roles as $role)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8">
                                        <div class="font-black text-slate-900 text-base flex items-center gap-2.5">
                                            @if(in_array($role->name, ['admin', 'guru', 'siswa']))
                                                <span class="relative flex h-3 w-3">
                                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                                                </span>
                                            @else
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-slate-300"></span>
                                            @endif
                                            {{ $role->display_name }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <span class="inline-block px-3 py-1.5 font-mono text-[11px] font-bold tracking-widest text-slate-500 bg-slate-100 border border-slate-200 rounded-lg shadow-inner">
                                            {{ $role->name }}
                                        </span>
                                    </td>
                                    <td class="p-5 text-center">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 text-slate-700 text-xs font-black rounded-lg shadow-sm">
                                            <span>👤</span> {{ $role->users_count ?? 0 }}
                                        </div>
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="initEdit({{ json_encode($role) }})" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Ubah Nama Jabatan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            @if(!in_array($role->name, ['admin', 'guru', 'siswa'])) 
                                                <button type="button" @click="initDelete('{{ route('master.role.destroy', $role->id) }}', '{{ addslashes($role->display_name) }}')" 
                                                        class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Role">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            @else
                                                <div class="inline-flex items-center justify-center h-9 px-3 bg-slate-100 border border-slate-200 text-slate-400 rounded-xl shadow-inner cursor-not-allowed gap-1.5" title="Role Inti Sistem (Terkunci)">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    <span class="text-[10px] font-black uppercase tracking-wider hidden sm:inline">Sistem</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                @if(request('search')) 🔍 @else 🛡️ @endif
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">
                                                @if(request('search')) Role Nihil @else Ruang Role Kosong @endif
                                            </h4>
                                            <span class="text-sm">
                                                @if(request('search')) 
                                                    Pencarian kata kunci "{{ request('search') }}" tidak menemukan kecocokan jabatan.
                                                @else
                                                    Sistem saat ini belum memiliki turunan Role kustom apapun.
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($roles->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $roles->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL CREATE ROLE --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-sm w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        <span class="text-2xl">✨</span> Desain Role Baru
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('master.role.store') }}" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10"
                      x-data="{ displayName: '{{ old('display_name', '') }}', get slug() { return this.displayName.toLowerCase().trim().replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-').replace(/^-+|-+$/g, '') } }">
                    @csrf
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Nama Formil Jabatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="display_name" x-model="displayName" required 
                               placeholder="Cth: Bendahara Sekolah" 
                               class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center justify-between">
                            <span>Sistem Slug Identifier</span>
                            <span class="text-[9px] text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded uppercase">Auto</span>
                        </label>
                        <input type="text" :value="slug" disabled placeholder="otomatis-dibuat..." 
                               class="w-full rounded-xl border-slate-100 text-sm font-bold shadow-inner py-3 px-4 bg-slate-50 text-slate-400 cursor-not-allowed font-mono">
                        <input type="hidden" name="name" :value="slug">
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openCreate = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Tutup</button>
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>💾</span> Bangun Role
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT ROLE --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-sm w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/30 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        <span class="text-2xl">📝</span> Modifikasi Role
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="editActionUrl" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Nama Formil Jabatan <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="editDisplayName" name="display_name" required 
                               class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center justify-between">
                            <span>Sistem Slug Identifier</span>
                            <span class="text-[9px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded uppercase">Locked</span>
                        </label>
                        <input type="text" x-model="editName" disabled 
                               class="w-full rounded-xl border-slate-100 text-sm font-bold shadow-inner py-3 px-4 bg-slate-50 text-slate-400 cursor-not-allowed font-mono">
                        <p class="text-[10px] text-slate-400 font-bold mt-2 leading-relaxed">🔒 *Slug bersifat mutlak dan tidak bisa diedit untuk mencegah anomali relasi tabel di database.*</p>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openEdit = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>🔄</span> Update Nama
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE ROLE --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🧨
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Hapus Tingkatan Role?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Tindakan ini akan mengeliminasi role <strong class="text-slate-800" x-text="deleteTargetName"></strong>. Otomatis hak akses para penggunanya akan dicabut!
                    </p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-3 w-full pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="flex-1 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors">
                        Urungkan
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md cursor-pointer transition-colors border border-transparent flex items-center justify-center gap-2">
                        Eksekusi
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>