<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-slate-500">
            <a href="{{ route('sarpras.gedung.index') }}" class="hover:text-indigo-600 transition-colors">Gedung</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('sarpras.gedung.show', $ruangan->gedung_id) }}" class="hover:text-indigo-600 transition-colors">{{ $ruangan->gedung->nama_gedung }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-indigo-600">Inventaris Ruang</span>
        </div>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editKodeBarang: '',
        editNamaBarang: '',
        editKategori: '',
        editMerek: '',
        editModel: '',
        editTahunPembelian: '',
        editHargaPerolehan: '',
        editKondisi: 'Baik',
        editJumlah: 1,
        editLokasi: '',
        editDeskripsi: '',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(i) {
            this.editActionUrl = `/sarpras/gedung/inventaris/${i.id}`;
            this.editKodeBarang = i.kode_barang;
            this.editNamaBarang = i.nama_barang;
            this.editKategori = i.kategori ?? '';
            this.editMerek = i.merek ?? '';
            this.editModel = i.model ?? '';
            this.editTahunPembelian = i.tahun_pembelian ?? '';
            this.editHargaPerolehan = i.harga_perolehan ? parseFloat(i.harga_perolehan) : '';
            this.editKondisi = i.kondisi;
            this.editJumlah = i.jumlah;
            this.editLokasi = i.lokasi ?? '';
            this.editDeskripsi = i.deskripsi ?? '';
            this.openEdit = true;
        },

        initDelete(actionUrl, iName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = iName;
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

            {{-- HEADER RUANGAN (ROOM SIGNAGE) --}}
            <div class="relative overflow-hidden bg-slate-900 rounded-[2.5rem] shadow-2xl p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-slate-800 group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/50 via-slate-900 to-slate-900"></div>
                <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
                
                <div class="relative z-10 flex items-center gap-5">
                    <div class="w-16 h-16 rounded-[1.25rem] bg-indigo-500/20 backdrop-blur-md border border-indigo-400/30 flex items-center justify-center text-3xl shadow-inner">
                        🚪
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="px-2 py-1 bg-indigo-500 text-white text-[9px] font-black uppercase tracking-widest rounded shadow-sm">
                                {{ $ruangan->kode_ruangan }}
                            </span>
                            <span class="px-2 py-1 bg-emerald-500 text-white text-[9px] font-black uppercase tracking-widest rounded shadow-sm">
                                {{ $ruangan->kapasitas }} Orang
                            </span>
                        </div>
                        <h3 class="text-2xl font-black text-white tracking-tight">{{ $ruangan->nama_ruangan }}</h3>
                        <p class="text-[11px] font-medium text-indigo-200 mt-1 uppercase tracking-wider">
                            📍 Terletak di: {{ $ruangan->gedung->nama_gedung }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('sarpras.gedung.show', $ruangan->gedung_id) }}" class="relative z-10 px-5 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all hover:-translate-x-1 text-center shrink-0 flex items-center gap-2">
                    <span>⬅️</span> Kembali ke Area Gedung
                </a>
            </div>

            {{-- DATA GRID INVENTARIS --}}
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Toolbar Tabel --}}
                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h4 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                            <span>📦</span> Kartu Inventaris Ruang (KIR)
                        </h4>
                        <p class="text-xs font-medium text-slate-500 mt-1">Seluruh aset sarana dan prasarana yang teregistrasi secara sah di dalam ruangan ini.</p>
                    </div>
                    
                    <button @click="openCreate = true" class="px-5 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer shrink-0">
                        <span>➕</span> Aset Baru
                    </button>
                </div>

                {{-- Tabel Data --}}
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                <th class="p-5 pl-8 w-24">Visual</th>
                                <th class="p-5">Identitas Aset</th>
                                <th class="p-5 text-center w-28">Kuantitas</th>
                                <th class="p-5 text-center w-36">Status Fisik</th>
                                <th class="p-5 text-right w-44">Nilai Buku (Rp)</th>
                                <th class="p-5 pr-8 text-center w-36">Panel</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($ruangan->inventaris as $i)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8">
                                        @if($i->foto_barang)
                                            <div class="relative w-14 h-14 rounded-xl overflow-hidden shadow-sm border border-slate-200 group-hover:shadow-md transition-shadow">
                                                <img src="{{ asset('storage/' . $i->foto_barang) }}" class="w-full h-full object-cover" alt="Foto Barang">
                                            </div>
                                        @else
                                            <div class="w-14 h-14 bg-slate-100 rounded-xl border border-slate-200 flex items-center justify-center text-slate-300 text-2xl shadow-sm" title="Tanpa Visual">
                                                📦
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-mono text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 uppercase">
                                                {{ $i->kode_barang }}
                                            </span>
                                        </div>
                                        <div class="font-black text-slate-900 text-base leading-tight mb-1">
                                            {{ $i->nama_barang }}
                                        </div>
                                        <div class="text-[10px] font-bold text-slate-400 flex flex-wrap gap-2 uppercase tracking-wider">
                                            <span class="flex items-center gap-1"><span class="text-slate-300">🏷️</span> {{ $i->merek ?? 'N/A' }}</span>
                                            <span class="text-slate-300">|</span>
                                            <span class="flex items-center gap-1"><span class="text-slate-300">📅</span> {{ $i->tahun_pembelian ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-5 text-center">
                                        <span class="inline-flex items-center justify-center w-10 h-10 bg-slate-100 text-slate-800 font-black text-lg rounded-xl border border-slate-200 shadow-sm">
                                            {{ $i->jumlah }}
                                        </span>
                                    </td>
                                    <td class="p-5 text-center">
                                        <span class="inline-flex items-center px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm border
                                            @if($i->kondisi == 'Baik') bg-emerald-50 border-emerald-200 text-emerald-700
                                            @elseif($i->kondisi == 'Rusak Ringan') bg-amber-50 border-amber-200 text-amber-700
                                            @elseif($i->kondisi == 'Rusak Berat') bg-rose-50 border-rose-200 text-rose-700
                                            @else bg-slate-50 border-slate-200 text-slate-600 @endif">
                                            @if($i->kondisi == 'Baik') 🟢 
                                            @elseif($i->kondisi == 'Rusak Ringan') 🟡 
                                            @elseif($i->kondisi == 'Rusak Berat') 🔴 
                                            @else ⚪ @endif
                                            &nbsp;{{ $i->kondisi }}
                                        </span>
                                    </td>
                                    <td class="p-5 text-right font-mono font-bold text-slate-700 text-sm">
                                        {{ $i->harga_perolehan ? 'Rp ' . number_format($i->harga_perolehan, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="initEdit({{ json_encode($i) }})" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Revisi Aset">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('sarpras.gedung.destroyInventaris', $i->id) }}', '{{ addslashes($i->nama_barang) }}')" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Aset">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="text-5xl mb-4 opacity-50">🪑</div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Ruangan Masih Kosong</h4>
                                            <span class="text-sm font-medium">Belum ada barang inventaris yang dialokasikan ke ruangan ini.</span>
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
            <div class="bg-white rounded-[2rem] max-w-3xl w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openCreate = false">
                
                <div class="px-6 md:px-8 py-5 border-b border-slate-100 bg-emerald-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-emerald-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        ✨ Registrasi Aset Baru
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('sarpras.gedung.storeInventaris', $ruangan->id) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    
                    {{-- Sektor Identitas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nomor/Kode Induk <span class="text-rose-500">*</span></label>
                            <input type="text" name="kode_barang" required placeholder="INV-2026-001" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-white py-3 px-4 shadow-sm uppercase">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Fisik Barang <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_barang" required placeholder="Proyektor Epson" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-white py-3 px-4 shadow-sm">
                        </div>
                    </div>

                    {{-- Sektor Spesifikasi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kategori / Jenis</label>
                            <input type="text" name="kategori" placeholder="Cth: Elektronik" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Pabrikan/Merek</label>
                            <input type="text" name="merek" placeholder="Cth: Epson" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tipe/Model</label>
                            <input type="text" name="model" placeholder="Cth: EB-X400" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                    </div>

                    {{-- Sektor Nilai & Kuantitas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tahun Pengadaan</label>
                            <input type="number" name="tahun_pembelian" placeholder="2026" min="1900" max="2100" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nilai Buku/Harga (Rp)</label>
                            <input type="number" name="harga_perolehan" placeholder="Cth: 7500000" min="0" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Kuantitas Fisik <span class="text-rose-500">*</span></label>
                            <input type="number" name="jumlah" required min="1" value="1" class="w-full text-xl text-center font-black text-emerald-700 rounded-xl border-emerald-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-emerald-50 py-2.5 px-4 shadow-inner">
                        </div>
                    </div>

                    {{-- Sektor Fisik & Visual --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Status Fisik <span class="text-rose-500">*</span></label>
                            <select name="kondisi" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-white py-3 px-4 shadow-sm">
                                <option value="Baik">🟢 Baik (Berfungsi Normal)</option>
                                <option value="Rusak Ringan">🟡 Rusak Ringan (Masih bisa dipakai)</option>
                                <option value="Rusak Berat">🔴 Rusak Berat (Perlu Perbaikan)</option>
                                <option value="Hilang">⚪ Hilang (Tidak ditemukan)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Visual Barang (Foto)</label>
                            <input type="file" name="foto_barang" accept="image/*" class="w-full text-sm font-medium border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-white py-2.5 px-3 file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 shadow-sm cursor-pointer">
                        </div>
                    </div>

                    {{-- Sektor Catatan --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Penempatan Detail / Keterangan Lain</label>
                        <textarea name="deskripsi" placeholder="Cth: Digantung di plafon tengah ruangan, remote disimpan di meja guru..." rows="2" class="w-full text-sm font-medium text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner resize-y"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                        <button type="button" @click="openCreate = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm text-sm">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-black rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5 cursor-pointer text-sm flex items-center justify-center gap-2">
                            <span>💾</span> Simpan Aset Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- MODAL UBAH DATA (EDIT)                         --}}
        {{-- ============================================== --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-3xl w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openEdit = false">
                
                <div class="px-6 md:px-8 py-5 border-b border-slate-100 bg-amber-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-amber-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-amber-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        📝 Pembaruan Data Aset
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="editActionUrl" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    @method('PUT')
                    
                    {{-- Sektor Identitas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                        <div>
                            <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest mb-2">Nomor/Kode Induk <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="editKodeBarang" name="kode_barang" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-indigo-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm uppercase">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest mb-2">Nama Fisik Barang <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="editNamaBarang" name="nama_barang" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-indigo-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm">
                        </div>
                    </div>

                    {{-- Sektor Spesifikasi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kategori / Jenis</label>
                            <input type="text" x-model="editKategori" name="kategori" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Pabrikan/Merek</label>
                            <input type="text" x-model="editMerek" name="merek" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tipe/Model</label>
                            <input type="text" x-model="editModel" name="model" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                    </div>

                    {{-- Sektor Nilai & Kuantitas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tahun Pengadaan</label>
                            <input type="number" x-model="editTahunPembelian" name="tahun_pembelian" min="1900" max="2100" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nilai Buku/Harga (Rp)</label>
                            <input type="number" x-model="editHargaPerolehan" name="harga_perolehan" min="0" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">Kuantitas Fisik <span class="text-rose-500">*</span></label>
                            <input type="number" x-model="editJumlah" name="jumlah" required min="1" class="w-full text-xl text-center font-black text-amber-700 rounded-xl border-amber-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-amber-50 py-2.5 px-4 shadow-inner">
                        </div>
                    </div>

                    {{-- Sektor Fisik & Visual --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Status Fisik <span class="text-rose-500">*</span></label>
                            <select x-model="editKondisi" name="kondisi" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm">
                                <option value="Baik">🟢 Baik (Berfungsi Normal)</option>
                                <option value="Rusak Ringan">🟡 Rusak Ringan (Masih bisa dipakai)</option>
                                <option value="Rusak Berat">🔴 Rusak Berat (Perlu Perbaikan)</option>
                                <option value="Hilang">⚪ Hilang (Tidak ditemukan)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Perbarui Visual (Upload)</label>
                            <input type="file" name="foto_barang" accept="image/*" class="w-full text-sm font-medium border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-2.5 px-3 file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 shadow-sm cursor-pointer">
                            <p class="text-[9px] font-bold text-slate-400 mt-1.5 uppercase tracking-wider">* Kosongkan bila tidak mengganti foto lama</p>
                        </div>
                    </div>

                    {{-- Sektor Catatan --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Penempatan Detail / Keterangan Lain</label>
                        <textarea x-model="editDeskripsi" name="deskripsi" rows="2" class="w-full text-sm font-medium text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner resize-y"></textarea>
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
                    <h4 class="text-xl font-black text-slate-900 tracking-tight">Hapus Barang Ini?</h4>
                    <p class="text-sm font-medium text-slate-500 mt-2 leading-relaxed">
                        Anda yakin akan mencabut aset <br><span class="font-black text-slate-800 bg-slate-100 px-2 py-0.5 rounded" x-text="deleteTargetName"></span> dari buku inventaris ruangan ini? Aset ini akan disembunyikan (*soft delete*).
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