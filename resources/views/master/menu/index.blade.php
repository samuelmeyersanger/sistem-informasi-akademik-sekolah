<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🧭</span> {{ __('Manajemen Menu Sidebar') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Konfigurasi struktur navigasi utama aplikasi berdasarkan tingkat hak akses (role).</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit State Form
        editAction: '',
        editData: { kategori: '', nama_menu: '', url: '', icon: '', urutan: '', permission_slug: '' },

        // Delete State Form
        deleteAction: '',
        deleteTargetName: '',

        initEdit(menu) {
            // Menyesuaikan dengan route master.menu.update secara dinamis
            this.editAction = `{{ route('master.menu.index') }}/${menu.id}`;
            this.editData.kategori = menu.kategori;
            this.editData.nama_menu = menu.nama_menu;
            this.editData.url = menu.url;
            this.editData.icon = menu.icon || '';
            this.editData.urutan = menu.urutan;
            this.editData.permission_slug = menu.permission_slug || '';
            this.openEdit = true;
        },

        initDelete(id, nama) {
            // Menyesuaikan dengan route master.menu.destroy secara dinamis
            this.deleteAction = `{{ route('master.menu.index') }}/${id}`;
            this.deleteTargetName = nama;
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
                    <span class="text-2xl">❌</span> 
                    <div class="mt-1">{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">⚠️</span> 
                    <div>
                        <div class="mb-2 text-base font-black">Gagal menyimpan pembaruan menu!</div>
                        <ul class="list-disc list-inside text-xs font-medium text-amber-700 space-y-1 bg-amber-100/50 p-3 rounded-xl border border-amber-200/50">
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

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Katalog Konfigurasi Tautan</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Daftar *parent* dan tautan menu sidebar dinamis berdasarkan grup pengelompokan.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                        <form action="{{ request()->url() }}" method="GET" class="flex items-stretch gap-2 w-full md:w-auto">
                            <div class="relative flex items-center w-full sm:w-64 group">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Cari label atau link..." 
                                       class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                                
                                @if(request('search'))
                                    <a href="{{ request()->url() }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Bersihkan Pencarian">
                                        <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2 justify-center">
                                <span class="hidden sm:inline">🔍</span> Saring
                            </button>
                        </form>

                        <div class="hidden md:block w-px h-8 bg-slate-200"></div>

                        <button @click="openCreate = true; editData = { kategori: '', nama_menu: '', url: '', icon: '', urutan: '', permission_slug: '' }" 
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 w-full sm:w-auto shrink-0">
                            <span>➕</span> Navigasi Baru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-8 w-64">Kategori & Judul Navigasi</th>
                                <th class="p-5 w-48">Rute Modul (URL)</th>
                                <th class="p-5 text-center w-24">Urutan</th>
                                <th class="p-5 w-56">Kunci Akses (Permission)</th>
                                <th class="p-5 pr-8 text-center w-36">Modifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($menus as $menu)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8">
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex w-fit px-2 py-0.5 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded text-[10px] font-black uppercase tracking-widest">
                                                {{ $menu->kategori }}
                                            </span>
                                            <div class="font-black text-slate-900 text-base flex items-center gap-2 mt-1">
                                                <span class="text-slate-400">
                                                    @if($menu->icon)
                                                        {{-- Coba render nama icon (Anda mungkin menggunakan blade x-icon komponen khusus nantinya) --}}
                                                        ⚡
                                                    @else
                                                        🔹
                                                    @endif
                                                </span> 
                                                {{ $menu->nama_menu }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <span class="inline-block px-3 py-1.5 font-mono text-[11px] font-bold tracking-widest text-slate-600 bg-slate-100 border border-slate-200 rounded-lg shadow-inner">
                                            /{{ $menu->url }}
                                        </span>
                                    </td>
                                    <td class="p-5 text-center">
                                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-50 border border-slate-200 font-black text-slate-800 shadow-sm">
                                            {{ $menu->urutan }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        @if($menu->permission_slug)
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-black tracking-wider rounded-lg shadow-sm">
                                                <span>🔒</span>
                                                <span class="font-mono">{{ $menu->permission_slug }}</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black tracking-wider rounded-lg shadow-sm uppercase">
                                                <span>🔓</span>
                                                Bebas Diakses
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="initEdit({{ json_encode($menu) }})" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Ubah Konfigurasi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <button type="button" @click="initDelete('{{ $menu->id }}', '{{ addslashes($menu->nama_menu) }}')" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Buang Menu">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                @if(request('search')) 🔍 @else 🏗️ @endif
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">
                                                @if(request('search')) Hasil Nihil @else Database Menu Kosong @endif
                                            </h4>
                                            <span class="text-sm">
                                                @if(request('search')) 
                                                    Tidak ada kecocokan label menu untuk "{{ request('search') }}".
                                                @else
                                                    Sistem saat ini belum memiliki struktur tata letak sidebar sama sekali.
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($menus, 'hasPages') && $menus->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $menus->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL CREATE MENU --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">✨</span> Rakit Menu Baru
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('master.menu.store') }}" method="POST" class="p-6 md:p-8 space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Kategori Induk <span class="text-rose-500">*</span></label>
                        <input type="text" name="kategori" required placeholder="Cth: Manajemen Pengguna" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Label Menu Navigasi <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_menu" required placeholder="Cth: Data Guru" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Target Tautan Rute <span class="text-rose-500">*</span></label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-mono text-slate-400 font-bold">/</span>
                            <input type="text" name="url" required placeholder="admin/guru" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 pl-8 pr-4 placeholder-slate-300">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Kode Ikon</label>
                            <input type="text" name="icon" placeholder="document-text" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                        </div>
                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Nomor Susunan <span class="text-rose-500">*</span></label>
                            <input type="number" name="urutan" required min="1" placeholder="Mulai: 1" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Proteksi Hak Akses</label>
                        <select name="permission_slug" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 bg-slate-50 cursor-pointer">
                            <option value="">🔓 Terbuka Tanpa Pengecualian</option>
                            @foreach($permissions as $perm)
                                <option value="{{ $perm->name }}">🔒 {{ $perm->name }} ({{ $perm->modul }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openCreate = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>💾</span> Rekam Menu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT MENU --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/30 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">📝</span> Koreksi Data Menu
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="editAction" method="POST" class="p-6 md:p-8 space-y-5">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Kategori Induk <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="editData.kategori" name="kategori" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Label Menu Navigasi <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="editData.nama_menu" name="nama_menu" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Target Tautan Rute <span class="text-rose-500">*</span></label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-mono text-slate-400 font-bold">/</span>
                            <input type="text" x-model="editData.url" name="url" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 pl-8 pr-4">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Kode Ikon</label>
                            <input type="text" x-model="editData.icon" name="icon" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                        </div>
                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Nomor Susunan <span class="text-rose-500">*</span></label>
                            <input type="number" x-model="editData.urutan" name="urutan" required min="1" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Proteksi Hak Akses</label>
                        <select x-model="editData.permission_slug" name="permission_slug" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 bg-slate-50 cursor-pointer">
                            <option value="">🔓 Terbuka Tanpa Pengecualian</option>
                            @foreach($permissions as $perm)
                                <option value="{{ $perm->name }}">🔒 {{ $perm->name }} ({{ $perm->modul }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openEdit = false" class="px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>🔄</span> Terapkan Koreksi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE MENU --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🧨
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Cabut Navigasi?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Bila dihapus, tautan <strong class="text-slate-800" x-text="deleteTargetName"></strong> akan menghilang sepenuhnya dari bilah sidebar seluruh pengguna.
                    </p>
                </div>
                
                <form :action="deleteAction" method="POST" class="flex justify-center gap-3 w-full pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="flex-1 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors">
                        Urungkan
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md cursor-pointer transition-colors border border-transparent flex items-center justify-center gap-2">
                        Cabut Saja
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>