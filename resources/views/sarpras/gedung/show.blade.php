<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-slate-500">
            <a href="{{ route('sarpras.gedung.index') }}" class="hover:text-indigo-600 transition-colors">Gedung</a>
            <span class="text-slate-300">/</span>
            <span class="text-indigo-600">Detail Ruangan Gedung</span>
        </div>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editNamaRuangan: '',
        editKodeRuangan: '',
        editKapasitas: 0,

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(r) {
            this.editActionUrl = `/sarpras/gedung/ruangan/${r.id}`;
            this.editNamaRuangan = r.nama_ruangan;
            this.editKodeRuangan = r.kode_ruangan;
            this.editKapasitas = r.kapasitas;
            this.openEdit = true;
        },

        initDelete(actionUrl, rName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = rName;
            this.openDelete = true;
        }
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 relative z-10">
            
            {{-- ALERT MESSAGES --}}
            @if(session('success'))
                <div class="p-5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3 animate-fade-in-down">
                    <span class="text-xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-2xl shadow-sm animate-fade-in-down">
                    <p class="font-black mb-2 flex items-center gap-2"><span class="text-xl">⚠️</span> Terdapat kendala validasi:</p>
                    <ul class="list-disc list-inside text-xs font-bold space-y-1 pl-7">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- HEADER GEDUNG (BUILDING SIGNAGE) --}}
            <div class="relative overflow-hidden bg-slate-900 rounded-[2.5rem] shadow-2xl p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-slate-800 group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/50 via-slate-900 to-slate-900"></div>
                <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
                
                <div class="relative z-10 flex items-center gap-5">
                    <div class="w-16 h-16 rounded-[1.25rem] bg-indigo-500/20 backdrop-blur-md border border-indigo-400/30 flex items-center justify-center text-3xl shadow-inner">
                        🏢
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="px-2 py-1 bg-indigo-500 text-white text-[9px] font-black uppercase tracking-widest rounded shadow-sm">
                                {{ $gedung->kode_gedung }}
                            </span>
                            <span class="px-2 py-1 bg-slate-700 text-indigo-100 text-[9px] font-black uppercase tracking-widest rounded shadow-sm">
                                📶 {{ $gedung->jumlah_lantai }} Lantai
                            </span>
                        </div>
                        <h3 class="text-2xl font-black text-white tracking-tight">{{ $gedung->nama_gedung }}</h3>
                        <p class="text-[11px] font-medium text-indigo-200 mt-1 max-w-lg leading-relaxed">
                            {{ $gedung->deskripsi ?? 'Bangunan fisik terdaftar dalam sistem aset.' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('sarpras.gedung.index') }}" class="relative z-10 px-5 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all hover:-translate-x-1 text-center shrink-0 flex items-center gap-2">
                    <span>⬅️</span> Direktori Gedung
                </a>
            </div>

            {{-- DATA GRID RUANGAN --}}
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Toolbar Tabel --}}
                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h4 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                            <span>🗺️</span> Pemetaan Ruangan Bangunan
                        </h4>
                        <p class="text-xs font-medium text-slate-500 mt-1">Daftar ruangan yang bernaung secara geografis di dalam {{ $gedung->nama_gedung }}.</p>
                    </div>
                    
                    <button @click="openCreate = true" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer shrink-0">
                        <span>➕</span> Registrasi Ruang
                    </button>
                </div>

                {{-- Tabel Data --}}
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                <th class="p-5 pl-8">Identitas Ruangan</th>
                                <th class="p-5 text-center w-40">Kapasitas Maks</th>
                                <th class="p-5 text-center w-44">Indeks Inventaris</th>
                                <th class="p-5 pr-8 text-center w-48">Panel Kontrol</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($gedung->ruangan as $r)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-[1rem] bg-indigo-50 border border-indigo-100 flex items-center justify-center text-xl shadow-sm text-indigo-500 group-hover:scale-110 transition-transform">
                                                🚪
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="px-2 py-0.5 bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest rounded-md shadow-sm">
                                                        {{ $r->kode_ruangan }}
                                                    </span>
                                                </div>
                                                <div class="font-black text-slate-900 text-base">{{ $r->nama_ruangan }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5 text-center">
                                        <span class="inline-flex items-center justify-center px-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 font-black text-xs rounded-xl shadow-sm">
                                            👥 {{ $r->kapasitas }} Orang
                                        </span>
                                    </td>
                                    <td class="p-5 text-center">
                                        <span class="inline-flex items-center justify-center px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 font-black text-xs rounded-xl shadow-sm">
                                            📦 {{ $r->inventaris_count }} Aset
                                        </span>
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('sarpras.gedung.showRuangan', $r->id) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 rounded-xl transition-all shadow-sm hover:shadow-md" title="Masuk & Lihat Aset">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <button type="button" @click="initEdit({{ json_encode($r) }})" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Renovasi Identitas">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('sarpras.gedung.destroyRuangan', $r->id) }}', '{{ addslashes($r->nama_ruangan) }}')" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Bongkar Ruangan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="text-5xl mb-4 opacity-50">🚪</div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Lorong Kosong</h4>
                                            <span class="text-sm font-medium">Belum ada sekat ruangan yang terdaftar di dalam gedung ini.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- MODAL TAMBAH DATA (CREATE)                     --}}
        {{-- ============================================== --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 md:px-8 py-5 border-b border-slate-100 bg-emerald-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-emerald-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        ✨ Registrasi Ruang Baru
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('sarpras.gedung.storeRuangan', $gedung->id) }}" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    
                    <div class="grid grid-cols-3 gap-5">
                        <div class="col-span-1">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kode <span class="text-rose-500">*</span></label>
                            <input type="text" name="kode_ruangan" required placeholder="Cth: R-101" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner uppercase">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Ruangan <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_ruangan" required placeholder="Cth: Kelas X-A" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Daya Tampung (Kapasitas Orang) <span class="text-rose-500">*</span></label>
                        <input type="number" name="kapasitas" required min="0" value="0" class="w-full text-xl text-center font-black text-emerald-700 rounded-xl border-emerald-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-emerald-50 py-3 px-4 shadow-inner">
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                        <button type="button" @click="openCreate = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm text-sm">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-black rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5 cursor-pointer text-sm flex items-center justify-center gap-2">
                            <span>💾</span> Bangun Ruangan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- MODAL UBAH DATA (EDIT)                         --}}
        {{-- ============================================== --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 md:px-8 py-5 border-b border-slate-100 bg-amber-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-amber-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        📝 Renovasi Identitas Ruang
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="editActionUrl" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-3 gap-5">
                        <div class="col-span-1">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kode <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="editKodeRuangan" name="kode_ruangan" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner uppercase">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Ruangan <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="editNamaRuangan" name="nama_ruangan" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">Daya Tampung (Kapasitas Orang) <span class="text-rose-500">*</span></label>
                        <input type="number" x-model="editKapasitas" name="kapasitas" required min="0" class="w-full text-xl text-center font-black text-amber-700 rounded-xl border-amber-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-amber-50 py-3 px-4 shadow-inner">
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                        <button type="button" @click="openEdit = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm text-sm">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 cursor-pointer text-sm flex items-center justify-center gap-2">
                            <span>🔄</span> Terapkan Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- MODAL HAPUS DATA (DELETE)                      --}}
        {{-- ============================================== --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-sm w-full p-8 text-center space-y-5 relative overflow-hidden" @click.away="openDelete = false">
                <div class="absolute right-0 top-0 w-32 h-32 bg-rose-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                
                <div class="w-20 h-20 bg-rose-50 text-rose-600 rounded-[1.5rem] flex items-center justify-center text-4xl mx-auto border border-rose-100 shadow-sm relative z-10 transform -rotate-6">⚠️</div>
                
                <div class="relative z-10">
                    <h4 class="text-xl font-black text-slate-900 tracking-tight">Bongkar Ruangan?</h4>
                    <p class="text-sm font-medium text-slate-500 mt-2 leading-relaxed">
                        Anda yakin akan menghilangkan ruangan <br><span class="font-black text-slate-800 bg-slate-100 px-2 py-0.5 rounded" x-text="deleteTargetName"></span>? Semua aset di dalamnya otomatis menjadi status "Tanpa Ruangan".
                    </p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex flex-col sm:flex-row justify-center gap-3 pt-4 relative z-10 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="w-full sm:w-1/2 px-4 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer text-sm">Batal</button>
                    <button type="submit" class="w-full sm:w-1/2 px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-lg shadow-rose-600/30 transition-colors cursor-pointer text-sm">Ya, Eksekusi</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>