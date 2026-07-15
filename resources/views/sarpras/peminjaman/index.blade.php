<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-slate-500">
            <span class="text-indigo-600">Sarpras</span>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800">Logistik & Peminjaman Barang</span>
        </div>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openReturn: false,
        openDelete: false,

        // Form States Edit
        editActionUrl: '',
        editInventarisId: '',
        editPeminjamId: '',
        editTanggalPinjam: '',
        editTanggalKembaliRencana: '',
        editKeperluan: '',
        editCatatan: '',

        // Form States Pengembalian
        returnActionUrl: '',
        returnTargetName: '',
        returnTanggalRealisasi: '{{ date('Y-m-d') }}',
        returnCatatan: '',

        // Form States Hapus
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(p) {
            this.editActionUrl = `/sarpras/peminjaman/${p.id}`;
            this.editInventarisId = p.inventaris_id;
            this.editPeminjamId = p.peminjam_id;
            this.editTanggalPinjam = p.tanggal_pinjam;
            this.editTanggalKembaliRencana = p.tanggal_kembali_rencana;
            this.editKeperluan = p.keperluan;
            this.editCatatan = p.catatan ?? '';
            this.openEdit = true;
        },

        initReturn(actionUrl, itemName) {
            this.returnActionUrl = actionUrl;
            this.returnTargetName = itemName;
            this.openReturn = true;
        },

        initDelete(actionUrl, itemName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = itemName;
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

            @if(session('error'))
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3 animate-fade-in-down">
                    <span class="text-xl">❌</span> {{ session('error') }}
                </div>
            @endif

            {{-- TOOLBAR CONTROL --}}
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 p-6 flex flex-col md:flex-row items-center justify-between gap-5 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
                
                <form method="GET" action="{{ route('sarpras.peminjaman.index') }}" class="w-full md:w-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3 relative z-10">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang / peminjam..." class="w-full sm:w-72 text-sm font-medium rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 shadow-sm pl-10 py-3 bg-slate-50 focus:bg-white transition-all">
                    </div>
                    
                    <select name="status" class="w-full sm:w-48 text-sm font-medium rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 shadow-sm py-3 px-4 bg-slate-50 focus:bg-white transition-all appearance-none cursor-pointer">
                        <option value="">📋 Semua Status</option>
                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>🔴 Sedang Dipinjam</option>
                        <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>🟢 Telah Kembali</option>
                        <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>🟡 Terlambat</option>
                    </select>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-5 py-3 bg-slate-800 hover:bg-slate-900 text-white font-black uppercase tracking-widest text-[10px] rounded-xl transition-all shadow-sm flex items-center justify-center cursor-pointer">
                            Saring Data
                        </button>
                        @if(request('search') || request('status'))
                            <a href="{{ route('sarpras.peminjaman.index') }}" class="px-4 py-3 bg-rose-50 border border-rose-100 hover:bg-rose-100 text-rose-600 font-bold text-xs text-center rounded-xl transition-colors" title="Reset Filter">
                                ✖
                            </a>
                        @endif
                    </div>
                </form>

                <button @click="openCreate = true" class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer relative z-10 shrink-0">
                    <span>➕</span> Transaksi Baru
                </button>
            </div>

            {{-- DATA GRID PEMINJAMAN --}}
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                <div class="p-6 md:p-8 border-b border-slate-100 bg-white/50 backdrop-blur-sm">
                    <h4 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                        <span>📋</span> Logistik & Mutasi Peminjaman Aset
                    </h4>
                    <p class="text-xs font-medium text-slate-500 mt-1">Sistem pencatatan keluar-masuk aset sarana prasarana secara sementara.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                <th class="p-5 pl-8 w-1/4">Informasi Aset Fisik</th>
                                <th class="p-5 w-1/4">Identitas Peminjam</th>
                                <th class="p-5 text-center w-40">Durasi Terjadwal</th>
                                <th class="p-5 text-center w-36">Status</th>
                                <th class="p-5">Keperluan & Catatan Tambahan</th>
                                <th class="p-5 pr-8 text-center w-40">Panel Kendali</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($peminjaman as $p)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200">
                                    <td class="p-5 pl-8">
                                        @if($p->inventaris)
                                            <div class="font-black text-slate-900 text-base leading-tight mb-1">{{ $p->inventaris->nama_barang }}</div>
                                            <div class="font-mono font-bold text-[10px] text-indigo-600 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded uppercase inline-block">
                                                {{ $p->inventaris->kode_barang }}
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                                <span class="text-slate-400 italic text-xs font-medium">Aset telah dihapus dari sistem</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5">
                                        <div class="font-bold text-slate-800 flex items-center gap-2 mb-1">
                                            <span class="text-sm">👤</span>
                                            {{ $p->peminjam->nama_lengkap ?? 'Anonim' }}
                                        </div>
                                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5 ml-6">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            PIC: {{ $p->pencatat->nama_lengkap ?? 'Otomatis' }}
                                        </div>
                                    </td>
                                    <td class="p-5 text-center relative">
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 shadow-sm inline-block min-w-full">
                                            <div class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('d M Y') }}</div>
                                            <div class="text-[10px] font-black text-slate-400 uppercase my-1">sampai</div>
                                            <div class="font-bold {{ $p->status == 'Terlambat' ? 'text-amber-600' : 'text-slate-900' }}">{{ \Carbon\Carbon::parse($p->tanggal_kembali_rencana)->translatedFormat('d M Y') }}</div>
                                        </div>
                                        @if($p->tanggal_kembali_realisasi)
                                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-max text-[9px] font-black text-emerald-700 bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded shadow-sm uppercase tracking-wider">
                                                Realisasi: {{ \Carbon\Carbon::parse($p->tanggal_kembali_realisasi)->translatedFormat('d M') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5 text-center">
                                        <span class="inline-flex items-center px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm border
                                            @if($p->status == 'Dipinjam') bg-sky-50 border-sky-200 text-sky-700
                                            @elseif($p->status == 'Dikembalikan') bg-emerald-50 border-emerald-200 text-emerald-700
                                            @else bg-amber-50 border-amber-200 text-amber-700 @endif">
                                            @if($p->status == 'Dipinjam') 📤
                                            @elseif($p->status == 'Dikembalikan') ✅
                                            @else ⚠️ @endif
                                            &nbsp;{{ $p->status }}
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-bold text-slate-800 line-clamp-2 leading-relaxed" title="{{ $p->keperluan }}">{{ $p->keperluan }}</div>
                                        @if($p->catatan)
                                            <div class="text-[10px] font-medium text-slate-500 italic mt-1.5 p-2 bg-amber-50 border border-amber-100 rounded-lg line-clamp-2" title="{{ $p->catatan }}">
                                                <span class="font-bold">Catatan:</span> {{ $p->catatan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($p->status === 'Dipinjam' || $p->status === 'Terlambat')
                                                <button type="button" @click="initReturn('{{ route('sarpras.peminjaman.kembalikan', $p->id) }}', '{{ addslashes($p->inventaris->nama_barang ?? 'Aset') }}')" class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-xl shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer border border-emerald-400" title="Proses Pengembalian">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                </button>
                                                
                                                <button type="button" @click="initEdit({{ json_encode($p) }})" class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Ubah Administrasi">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                            @endif
                                            
                                            <button type="button" @click="initDelete('{{ route('sarpras.peminjaman.destroy', $p->id) }}', '{{ addslashes($p->inventaris->nama_barang ?? 'Transaksi') }}')" class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Log Permanen">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="text-5xl mb-4 opacity-50">📑</div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Gudang Pencatatan Bersih</h4>
                                            <span class="text-sm font-medium">Tidak ada rekaman log transaksi peminjaman yang cocok dengan kriteria.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- PAGINATION --}}
                @if($peminjaman->hasPages())
                    <div class="p-5 border-t border-slate-100 bg-slate-50/50">
                        {{ $peminjaman->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- MODAL TAMBAH DATA (CREATE)                     --}}
        {{-- ============================================== --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-xl w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 md:px-8 py-5 border-b border-slate-100 bg-indigo-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-indigo-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        ✨ Transaksi Peminjaman Baru
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('sarpras.peminjaman.store') }}" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    
                    <div class="space-y-5 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Pilih Barang / Alat Sarpras <span class="text-rose-500">*</span></label>
                            <select name="inventaris_id" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-white py-3 px-4 shadow-sm">
                                <option value="">-- Pilih Aset (Hanya Stok Tersedia) --</option>
                                @foreach($daftarInventaris as $item)
                                    <option value="{{ $item->id }}">📦 {{ $item->nama_barang }} [{{ $item->kode_barang }}] - Tersedia: {{ $item->jumlah }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Penanggung Jawab / Peminjam <span class="text-rose-500">*</span></label>
                            <select name="peminjam_id" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-white py-3 px-4 shadow-sm">
                                <option value="">-- Pilih Personel --</option>
                                @foreach($daftarPegawai as $peg)
                                    <option value="{{ $peg->id }}">👤 {{ $peg->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tanggal Pengambilan <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_pinjam" required value="{{ date('Y-m-d') }}" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Jadwal Pengembalian <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_kembali_rencana" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tujuan / Keperluan Pinjam <span class="text-rose-500">*</span></label>
                        <input type="text" name="keperluan" required placeholder="Cth: Mengajar praktik biologi ekosistem..." class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Catatan Kondisi / Kelengkapan Tambahan (Opsional)</label>
                        <textarea name="catatan" placeholder="Cth: Kabel power dipinjamkan sepaket, remote tidak ada..." rows="2" class="w-full text-sm font-medium text-slate-800 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 bg-slate-50 py-3 px-4 shadow-inner resize-y"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                        <button type="button" @click="openCreate = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm text-sm">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer text-sm flex items-center justify-center gap-2">
                            <span>📦</span> Setujui Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- MODAL UBAH DATA (EDIT)                         --}}
        {{-- ============================================== --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-xl w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 md:px-8 py-5 border-b border-slate-100 bg-amber-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-amber-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        📝 Revisi Data Peminjaman
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="editActionUrl" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-5 p-5 bg-amber-50/30 border border-amber-100 rounded-2xl">
                        <div>
                            <label class="block text-[10px] font-black text-amber-900 uppercase tracking-widest mb-2">Sesuaikan Fisik Barang <span class="text-rose-500">*</span></label>
                            <select name="inventaris_id" x-model="editInventarisId" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-amber-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm">
                                @foreach($daftarInventaris as $item)
                                    <option value="{{ $item->id }}">📦 {{ $item->nama_barang }} [{{ $item->kode_barang }}]</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-amber-900 uppercase tracking-widest mb-2">Penanggung Jawab / Peminjam <span class="text-rose-500">*</span></label>
                            <select name="peminjam_id" x-model="editPeminjamId" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-amber-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm">
                                @foreach($daftarPegawai as $peg)
                                    <option value="{{ $peg->id }}">👤 {{ $peg->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tanggal Pengambilan <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_pinjam" x-model="editTanggalPinjam" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Jadwal Pengembalian <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_kembali_rencana" x-model="editTanggalKembaliRencana" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tujuan / Keperluan Pinjam <span class="text-rose-500">*</span></label>
                        <input type="text" name="keperluan" x-model="editKeperluan" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Catatan Kondisi Khusus</label>
                        <textarea name="catatan" x-model="editCatatan" rows="2" class="w-full text-sm font-medium text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner resize-y"></textarea>
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
        {{-- MODAL PENGEMBALIAN (RETURN)                    --}}
        {{-- ============================================== --}}
        <div x-show="openReturn" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openReturn = false">
                
                <div class="px-6 md:px-8 py-5 border-b border-slate-100 bg-emerald-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-emerald-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        ↩️ Mutasi Pengembalian
                    </h3>
                    <button type="button" @click="openReturn = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="returnActionUrl" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    @method('PATCH')
                    
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <p class="text-xs font-medium text-slate-600 leading-relaxed">
                            Pencatatan masuk fisik barang: <br>
                            <span class="font-black text-slate-900 text-sm block mt-1" x-text="returnTargetName"></span>
                            <span class="block mt-2 pt-2 border-t border-slate-200 text-emerald-700 font-bold">Kuantitas stok akan direstorasi ke sistem secara otomatis.</span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Tanggal Dikembalikan Aktual <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_kembali_realisasi" x-model="returnTanggalRealisasi" required class="w-full text-sm font-bold text-emerald-800 rounded-xl border-emerald-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-emerald-50 py-3 px-4 shadow-inner">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Checklist Kondisi Barang Masuk</label>
                        <textarea name="catatan" x-model="returnCatatan" placeholder="Cth: Lensa proyektor aman, kabel lengkap, tidak ada cacat..." rows="3" class="w-full text-sm font-medium text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner resize-y"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end gap-3 mt-4">
                        <button type="button" @click="openReturn = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm text-sm">Tunda</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-black rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5 cursor-pointer text-sm">Selesaikan Transaksi</button>
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
                    <h4 class="text-xl font-black text-slate-900 tracking-tight">Hapus Log Bukti Pinjam?</h4>
                    <p class="text-sm font-medium text-slate-500 mt-2 leading-relaxed">
                        Anda yakin akan menghapus permanen log <br><span class="font-black text-slate-800 bg-slate-100 px-2 py-0.5 rounded" x-text="deleteTargetName"></span>? Data ini tidak dapat dipulihkan.
                    </p>
                </div>
                
                <form :action="deleteActionUrl" method="POST" class="flex flex-col sm:flex-row justify-center gap-3 pt-4 relative z-10 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="w-full sm:w-1/2 px-4 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl shadow-sm transition-colors cursor-pointer text-sm">Batal</button>
                    <button type="submit" class="w-full sm:w-1/2 px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-lg shadow-rose-600/30 transition-colors cursor-pointer text-sm">Ya, Hapus Data</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>