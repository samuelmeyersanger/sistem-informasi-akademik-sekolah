<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🗓️</span> {{ __('Tahun Ajaran') }}
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
        editNamaTahunAjaran: '',
        editIsAktif: '0',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(ta) {
            // 🟢 PERBAIKAN: Mengubah /admin/ menjadi /master/ agar tidak 404
            this.editActionUrl = `/master/tahun-ajaran/${ta.id}`;
            this.editNamaTahunAjaran = ta.nama_tahun_ajaran;
            this.editIsAktif = ta.is_aktif ? '1' : '0';
            this.openEdit = true;
        },

        initDelete(actionUrl, taName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = taName;
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

            @if($errors->any())
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-bold rounded-2xl shadow-sm flex items-start gap-3">
                    <span class="text-2xl">🚧</span> 
                    <div>
                        <div class="mb-1 text-base font-black text-amber-900">Penyimpanan Ditolak!</div>
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

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Katalog Siklus Belajar</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1 max-w-lg leading-relaxed">Hanya boleh ada 1 tahun ajaran "Aktif" pada satu waktu untuk menghindari disonansi pencatatan akademik (KBM, Ekskul, dan Rapor).</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                        <form action="{{ route('master.tahun-ajaran.index') }}" method="GET" class="flex items-stretch gap-2 w-full md:w-auto">
                            <div class="relative flex items-center w-full sm:w-64 group">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Cari tahun ajaran (cth: 2024/2025)..." 
                                       class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                                
                                @if(request('search'))
                                    <a href="{{ route('master.tahun-ajaran.index') }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Hapus Filter">
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
                            <span>➕</span> Tambah Tahun
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-8">Identitas Tahun Ajaran</th>
                                <th class="p-5 text-center w-48">Status Penayangan</th>
                                <th class="p-5 pr-8 text-center w-36">Kelola</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($tahunAjarans as $ta)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8">
                                        <div class="font-black text-slate-900 text-lg flex items-center gap-3">
                                            <span class="text-indigo-400 text-xl">📅</span> {{ $ta->nama_tahun_ajaran }}
                                        </div>
                                    </td>
                                    <td class="p-5 text-center">
                                        @if($ta->is_aktif)
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                                Aktif Saat Ini
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 border border-slate-200 text-slate-400 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-slate-300 rounded-full"></span>
                                                Arsip Lama
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="initEdit({{ json_encode($ta) }})" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Koreksi Tahun Ajaran">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <button type="button" @click="initDelete('{{ route('master.tahun-ajaran.destroy', $ta->id) }}', '{{ addslashes($ta->nama_tahun_ajaran) }}')" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Permanen Tahun Ajaran">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                @if(request('search')) 🔍 @else 🗓️ @endif
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">
                                                @if(request('search')) Data Nihil @else Ruang Data Kosong @endif
                                            </h4>
                                            <span class="text-sm">
                                                @if(request('search')) 
                                                    Pencarian kata kunci "{{ request('search') }}" tidak menemukan hasil.
                                                @else
                                                    Belum ada rekam jejak Tahun Ajaran di sistem saat ini.
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tahunAjarans->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50">
                        {{ $tahunAjarans->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL CREATE TAHUN AJARAN --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-sm w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        <span class="text-2xl">✨</span> Rilis Tahun Ajaran
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('master.tahun-ajaran.store') }}" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Periode Belajar Akademik <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_tahun_ajaran" required 
                               placeholder="Cth: 2024/2025" 
                               class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Atur Status Kepastian <span class="text-rose-500">*</span></label>
                        <select name="is_aktif" required 
                                class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            <option value="0">Arsipkan Langsung (Belum Jalan)</option>
                            <option value="1">Jalankan (Aktif Saat Ini)</option>
                        </select>
                        <p class="text-[10px] font-bold text-amber-600 mt-2 bg-amber-50 p-2 rounded border border-amber-200">
                            💡 Mengaktifkan Tahun Ajaran baru secara langsung bisa mengarsipkan Tahun Ajaran sebelumnya secara sistem!
                        </p>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openCreate = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Tutup</button>
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>💾</span> Validasi Input
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT TAHUN AJARAN --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-sm w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/30 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        <span class="text-2xl">📝</span> Koreksi Periode
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="editActionUrl" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Periode Belajar Akademik <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="editNamaTahunAjaran" name="nama_tahun_ajaran" required 
                               class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Atur Status Kepastian <span class="text-rose-500">*</span></label>
                        <select x-model="editIsAktif" name="is_aktif" required 
                                class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            <option value="0">Arsipkan Langsung (Belum Jalan)</option>
                            <option value="1">Jalankan (Aktif Saat Ini)</option>
                        </select>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 mt-4">
                        <button type="button" @click="openEdit = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm w-full sm:w-auto text-center">Batal</button>
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 cursor-pointer w-full sm:w-auto text-center flex items-center justify-center gap-2">
                            <span>🔄</span> Terapkan Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE TAHUN AJARAN --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🧨
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Vonis Hapus Tahun Akademik?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Anda akan menghilangkan seluruh rekam jejak KBM dan ekstrakurikuler milik Tahun Ajaran <strong class="text-slate-800" x-text="deleteTargetName"></strong>. Data yang dihapus mustahil dipulihkan kembali!
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