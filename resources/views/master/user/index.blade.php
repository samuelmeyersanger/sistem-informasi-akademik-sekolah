<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">👥</span> {{ __('Kontrol Akses Pengguna') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Audit, plotting jabatan, dan persetujuan akun masuk (login) ke dalam sistem.</p>
            </div>
        </div>
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
                this.modalTitle = 'Persetujuan Akun (Approval)';
                this.modalButtonText = 'Setujui & Plot Akun';
            }
            
            this.openEdit = true;
        },

        initDelete(actionUrl, userName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = userName;
            this.openDelete = true;
        }
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 relative z-10">
            
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

            {{-- SISTEM TAB FILTER ROLE --}}
            <div class="flex flex-wrap items-center gap-2 mb-2 p-1 bg-slate-200/50 rounded-2xl shadow-inner inline-flex">
                <a href="{{ route('master.user.index', array_merge(request()->except(['page', 'role']))) }}" 
                   class="px-5 py-2.5 text-xs font-black tracking-wide uppercase transition-all rounded-xl flex items-center gap-2 shadow-sm
                    {{ !request('role') ? 'bg-white text-indigo-700 shadow-md ring-1 ring-slate-900/5' : 'bg-transparent text-slate-500 hover:text-slate-800 hover:bg-white/50' }}">
                    <span class="{{ !request('role') ? 'text-indigo-500' : 'text-slate-400' }}">🌐</span> Semua Pengguna
                </a>

                @foreach($allRoles as $role)
                    <a href="{{ route('master.user.index', array_merge(request()->except(['page']), ['role' => $role->name])) }}" 
                       class="px-5 py-2.5 text-xs font-black tracking-wide uppercase transition-all rounded-xl flex items-center gap-2 shadow-sm
                        {{ request('role') === $role->name ? 'bg-white text-indigo-700 shadow-md ring-1 ring-slate-900/5' : 'bg-transparent text-slate-500 hover:text-slate-800 hover:bg-white/50' }}">
                        <span class="{{ request('role') === $role->name ? 'text-indigo-500' : 'text-slate-400' }}">
                            @if(in_array($role->name, ['admin'])) 🛡️ 
                            @elseif(in_array($role->name, ['guru'])) 👨‍🏫 
                            @elseif(in_array($role->name, ['siswa'])) 🎓 
                            @else 🏷️ @endif
                        </span> 
                        {{ $role->display_name }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative mt-4">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                            Menampilkan Filter: <span class="text-indigo-600">{{ request('role') ? ucwords(str_replace('_', ' ', request('role'))) : 'Semua User' }}</span>
                        </h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Gunakan kotak pencarian untuk mencari pengguna spesifik dalam kategori ini.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                        <form action="{{ route('master.user.index') }}" method="GET" class="flex items-stretch gap-2 w-full md:w-auto">
                            @if(request('role'))
                                <input type="hidden" name="role" value="{{ request('role') }}">
                            @endif
                            <div class="relative flex items-center w-full sm:w-64 group">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Cari nama atau email..." 
                                       class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                                
                                @if(request('search'))
                                    <a href="{{ route('master.user.index', array_merge(request()->except(['page', 'search']))) }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Hapus Filter">
                                        <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2 justify-center shrink-0">
                                🔍
                            </button>
                        </form>

                        <div class="hidden md:block w-px h-8 bg-slate-200"></div>

                        <button @click="openCreate = true" 
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 shrink-0 w-full sm:w-auto">
                            <span>➕</span> Tambah Manual
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[11px] uppercase tracking-widest">
                                <th class="p-5 pl-8">Identitas Profil</th>
                                <th class="p-5 w-64">Sektor / Jabatan Khusus</th>
                                <th class="p-5 text-center w-40">Status Otoritas</th>
                                <th class="p-5 w-44">Tgl Registrasi</th>
                                <th class="p-5 pr-8 text-center w-36">Kontrol Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($users as $user)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-black text-slate-500 shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-black text-slate-900 text-sm leading-tight">{{ $user->name }}</h4>
                                                <p class="text-[11px] font-bold text-slate-500 mt-0.5">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse($user->roles as $role)
                                                @php
                                                    // Generate warna pastel dinamis
                                                    $hash = md5($role->name);
                                                    $hue = hexdec(substr($hash, 0, 2)) % 360; 
                                                    $bgStyle = "background-color: hsl({$hue}, 85%, 96%); color: hsl({$hue}, 85%, 28%); border: 1px solid hsl({$hue}, 70%, 85%);";
                                                @endphp
                                                <span style="{{ $bgStyle }}" class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm">
                                                    {{ $role->display_name }}
                                                </span>
                                            @empty
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-400 border border-slate-200 shadow-inner">
                                                    Non-Aktif (Unmapped)
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-5 text-center">
                                        @if($user->is_approved)
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                                Valid (Active)
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 border border-rose-200 text-rose-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                                                Pending Approve
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5 text-slate-500 text-[11px] font-bold">
                                        {{ $user->created_at->translatedFormat('d M Y') }}<br>
                                        <span class="text-[10px] font-normal">{{ $user->created_at->translatedFormat('H:i') }} WIB</span>
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="initEdit({{ json_encode($user) }}, {{ json_encode($user->roles->pluck('id')) }})" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border {{ $user->is_approved ? 'border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50' : 'border-indigo-300 text-indigo-600 hover:text-white hover:bg-indigo-600' }} rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="{{ $user->is_approved ? 'Ubah Data Akun' : 'Approve Akun Ini' }}">
                                                @if($user->is_approved)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endif
                                            </button>

                                            <button type="button" @click="initDelete('{{ route('master.user.destroy', $user->id) }}', '{{ addslashes($user->name) }}')" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Basmi Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                @if(request('search')) 🔍 @else 📭 @endif
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">
                                                @if(request('search')) Pencarian Nihil @else Tidak Ada Pengguna @endif
                                            </h4>
                                            <span class="text-sm">
                                                @if(request('search')) 
                                                    Pencarian kata kunci "{{ request('search') }}" di kategori ini kosong.
                                                @else
                                                    Belum ada data anggota/user yang tercatat dalam segmen ini.
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- MODAL CREATE PENGGUNA --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        <span class="text-2xl">✨</span> Registrasi Manual
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('master.user.store') }}" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Identitas Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required 
                               placeholder="Nama Lengkap (Sesuai KTP/Ijazah)" 
                               class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Alamat Email Akses (Login) <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required 
                               placeholder="nama@domain.com" 
                               class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Distribusi Hak Akses <span class="text-rose-500">*</span></label>
                        <div class="space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-100 max-h-48 overflow-y-auto">
                            @foreach($allRoles as $role)
                                <label class="flex items-center gap-3 p-2 hover:bg-white rounded-xl cursor-pointer transition-colors border border-transparent hover:border-slate-200 hover:shadow-sm">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                           class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-colors">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800">{{ $role->display_name }}</span>
                                        <span class="font-mono text-slate-400 text-[10px] uppercase tracking-wider">Sistem: {{ $role->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <input type="hidden" name="is_approved" value="1">
                    
                    <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-5">
                        <div>
                            <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-2">Sandi Sistem Baru <span class="text-rose-500">*</span></label>
                            <input type="password" name="password" required 
                                   class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                        </div>
                        <div>
                            <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-2">Validasi Kata Sandi <span class="text-rose-500">*</span></label>
                            <input type="password" name="password_confirmation" required 
                                   class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" @click="openCreate = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Tutup Form</button>
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>💾</span> Rekam Database
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT & APPROVE PENGGUNA --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center relative overflow-hidden" 
                     :class="editIsApproved ? 'bg-amber-50/30' : 'bg-emerald-50/50'">
                    <div class="absolute right-0 top-0 w-32 h-32 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"
                         :class="editIsApproved ? 'bg-amber-100' : 'bg-emerald-200'"></div>
                    
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        <span class="text-2xl" x-text="editIsApproved ? '📝' : '🛡️'"></span> 
                        <span x-text="modalTitle"></span>
                    </h3>
                    
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="editActionUrl" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Nama Lengkap Pemilik <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="editName" name="name" required 
                               class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Email Akses Login <span class="text-rose-500">*</span></label>
                        <input type="email" x-model="editEmail" name="email" required 
                               class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Distribusi Jabatan Otoritas <span class="text-rose-500">*</span></label>
                        <div class="space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-100 max-h-40 overflow-y-auto shadow-inner">
                            @foreach($allRoles as $role)
                                <label class="flex items-center gap-3 p-2 hover:bg-white rounded-xl cursor-pointer transition-colors border border-transparent hover:border-slate-200 hover:shadow-sm">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" x-model="editRoles" 
                                           class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-colors">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800">{{ $role->display_name }}</span>
                                        <span class="font-mono text-slate-400 text-[10px] uppercase tracking-wider">Slug: {{ $role->name }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Toggle Persetujuan --}}
                    <div class="p-5 bg-white border border-slate-200 shadow-sm rounded-2xl flex items-center justify-between transition-colors" 
                         :class="editIsApproved ? 'border-emerald-200 ring-2 ring-emerald-50' : 'border-rose-200 ring-2 ring-rose-50'">
                        <div class="pr-4">
                            <label class="block text-sm font-black text-slate-800">Izin Akses (Approval)</label>
                            <p class="text-[11px] font-medium text-slate-500 mt-1 leading-relaxed">Menyala berarti user berhak masuk ke halaman dashboard internal (Login Valid).</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="is_approved" value="1" x-model="editIsApproved" class="sr-only peer">
                            <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                        </label>
                    </div>
                    
                    {{-- Ganti Password Section --}}
                    <div class="border-t border-slate-100 pt-5">
                        <div class="p-3 mb-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2 text-amber-800 shadow-sm">
                            <span class="text-base mt-0.5">💡</span>
                            <div class="text-[11px] font-medium leading-relaxed">
                                <strong class="font-black">Opsi Reset Password:</strong> Biarkan kosong dua kotak di bawah ini jika Anda <u>tidak ingin</u> merubah kata sandi asli pengguna.
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-2">Timpa Sandi Baru</label>
                                <input type="password" name="password" placeholder="***" 
                                       class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            </div>
                            <div>
                                <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-2">Validasi Pengetikan</label>
                                <input type="password" name="password_confirmation" placeholder="***" 
                                       class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openEdit = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" 
                                class="px-5 py-3 text-white font-black rounded-xl shadow-lg transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2"
                                :class="editIsApproved ? 'bg-gradient-to-r from-amber-500 to-amber-600 shadow-amber-500/30 hover:from-amber-600 hover:to-amber-700' : 'bg-gradient-to-r from-emerald-600 to-emerald-500 shadow-emerald-500/30 hover:from-emerald-700 hover:to-emerald-600'">
                            <span x-text="editIsApproved ? '🔄' : '✅'"></span>
                            <span x-text="modalButtonText"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE PENGGUNA --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🧨
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Cabut Nyawa Akun Ini?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Perhatian! Anda bersiap mengeliminasi akun pengguna <strong class="text-slate-800 font-black" x-text="deleteTargetName"></strong> dari server secara utuh dan permanen.
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