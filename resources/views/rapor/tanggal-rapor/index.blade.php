<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🖨️</span> {{ __('Konfigurasi Tanggal Rapor') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Kelola data tempat, waktu, dan penanda tangan untuk cetak rapor per semester.</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,

        // Edit Modal Form States
        editActionUrl: '',
        editTahunAjaranId: '',
        editSemesterId: '',
        editTempatCetak: '',
        editTanggalCetak: '',
        editNamaKepalaSekolah: '',
        editNipKepalaSekolah: '',
        editLabelKepalaSekolah: 'Kepala Sekolah',
        editLabelNipKepalaSekolah: 'NIP.',
        editLabelNipWaliKelas: 'NIP.',

        // Delete Modal Form States
        deleteActionUrl: '',
        deleteTargetName: '',

        initEdit(rapor) {
            this.editActionUrl = `/rapor/tanggal-rapor/${rapor.id}`;
            this.editTahunAjaranId = rapor.tahun_ajaran_id;
            this.editSemesterId = rapor.semester_id;
            this.editTempatCetak = rapor.tempat_cetak;
            // Memotong tanggal menjadi YYYY-MM-DD agar support input type='date'
            this.editTanggalCetak = rapor.tanggal_cetak ? rapor.tanggal_cetak.substring(0, 10) : '';
            this.editNamaKepalaSekolah = rapor.nama_kepala_sekolah;
            this.editNipKepalaSekolah = rapor.nip_kepala_sekolah;
            this.editLabelKepalaSekolah = rapor.label_kepala_sekolah;
            this.editLabelNipKepalaSekolah = rapor.label_nip_kepala_sekolah;
            this.editLabelNipWaliKelas = rapor.label_nip_wali_kelas;
            this.openEdit = true;
        },

        initDelete(actionUrl, targetName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = targetName;
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
                    <span class="text-xl">⚠️</span> {{ session('error') }}
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

            {{-- DATA GRID --}}
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Toolbar Tabel --}}
                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                            <span>📋</span> Katalog Cetak Rapor
                        </h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Daftar konfigurasi atribut tanda tangan dan penerbitan rapor per semester akademik.</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                        <form action="{{ route('rapor.tanggal_rapor.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <div class="relative flex items-center w-full sm:w-auto group">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Cari Tempat / TA..." 
                                       class="text-sm font-medium rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner w-full sm:w-64 pr-10 py-2.5 transition-colors">
                                @if(request('search'))
                                    <a href="{{ route('rapor.tanggal_rapor.index') }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold text-lg transition-colors">&times;</a>
                                @else
                                    <span class="absolute right-3 text-slate-400">🔍</span>
                                @endif
                            </div>
                            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-slate-800/20 cursor-pointer shrink-0">
                                Filter
                            </button>
                        </form>

                        <button @click="openCreate = true" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer shrink-0">
                            <span>➕</span> Setup Baru
                        </button>
                    </div>
                </div>

                {{-- Tabel Data --}}
                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                <th class="p-5 pl-8 w-64">Siklus Periode</th>
                                <th class="p-5">Atribut Terbit Rapor</th>
                                <th class="p-5 w-64">Validasi Pimpinan</th>
                                <th class="p-5 pr-8 text-center w-36">Kontrol</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($tanggalRapors as $rapor)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8">
                                        <div class="inline-flex items-center px-3 py-1 bg-white border border-slate-200 text-slate-800 text-[11px] font-black rounded-lg shadow-sm mb-1 group-hover:border-indigo-200 transition-colors">
                                            TA. {{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '-' }}
                                        </div>
                                        <div class="text-[10px] uppercase font-black text-indigo-600 tracking-wider">
                                            SMT: {{ $rapor->semester->nama_semester ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-black text-slate-900 flex items-center gap-1.5 text-base">
                                            <span>📍</span> {{ $rapor->tempat_cetak }}
                                        </div>
                                        <div class="text-xs font-bold text-slate-500 mt-1 pl-6">
                                            {{ \Carbon\Carbon::parse($rapor->tanggal_cetak)->translatedFormat('d F Y') }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-black text-slate-800 leading-tight">
                                            {{ $rapor->nama_kepala_sekolah }}
                                        </div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                            {{ $rapor->label_nip_kepala_sekolah }} {{ $rapor->nip_kepala_sekolah ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="initEdit({{ json_encode($rapor) }})" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Ubah Parameter">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button type="button" @click="initDelete('{{ route('rapor.tanggal_rapor.destroy', $rapor->id) }}', 'TA {{ addslashes($rapor->tahunAjaran->nama_tahun_ajaran ?? '') }} Semester {{ addslashes($rapor->semester->nama_semester ?? '') }}')" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Permanen">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="text-5xl mb-4 opacity-50">📑</div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Data Belum Diisi</h4>
                                            <span class="text-sm font-medium">Klik tombol "Setup Baru" di atas untuk mulai mengatur variabel cetak rapor.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tanggalRapors->hasPages())
                    <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                        {{ $tanggalRapors->links() }}
                    </div>
                @endif
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
                        ✨ Entri Variabel Rapor Baru
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('rapor.tanggal_rapor.store') }}" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    
                    {{-- Grid 2 Kolom --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Sektor Akademik --}}
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tahun Ajaran <span class="text-rose-500">*</span></label>
                                <select name="tahun_ajaran_id" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-white py-3 px-4 shadow-sm">
                                    <option value="">-- Pilih TA --</option>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Semester Akademik <span class="text-rose-500">*</span></label>
                                <select name="semester_id" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-white py-3 px-4 shadow-sm">
                                    <option value="">-- Pilih Semester --</option>
                                    @foreach($semesters as $smt)
                                        <option value="{{ $smt->id }}">{{ $smt->nama_semester }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        {{-- Sektor Tempat/Waktu --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Domisili Cetak <span class="text-rose-500">*</span></label>
                            <input type="text" name="tempat_cetak" required placeholder="Cth: Surabaya" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tanggal Terbit Rapor <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_cetak" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner text-center">
                        </div>

                        {{-- Sektor Kepala Sekolah --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Pejabat (Kepsek) <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_kepala_sekolah" required placeholder="Cth: Dr. H. Fulan, M.Pd" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nomor Induk (NIP Kepsek)</label>
                            <input type="text" name="nip_kepala_sekolah" placeholder="Bila berstatus PNS" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>

                        {{-- Sektor Labeling --}}
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Gelar Jabatan <span class="text-rose-500">*</span></label>
                                <input type="text" name="label_kepala_sekolah" value="Kepala Sekolah" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-white py-3 px-4 shadow-sm text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Awalan NIP Kepsek <span class="text-rose-500">*</span></label>
                                <input type="text" name="label_nip_kepala_sekolah" value="NIP." required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-white py-3 px-4 shadow-sm text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Awalan NIP Wali <span class="text-rose-500">*</span></label>
                                <input type="text" name="label_nip_wali_kelas" value="NIP." required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 bg-white py-3 px-4 shadow-sm text-center">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                        <button type="button" @click="openCreate = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm text-sm">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white font-black rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5 cursor-pointer text-sm flex items-center justify-center gap-2">
                            <span>💾</span> Validasi Data
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
                        📝 Revisi Pengaturan
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form :action="editActionUrl" method="POST" class="p-6 md:p-8 space-y-6 bg-white relative z-10">
                    @csrf
                    @method('PUT')
                    
                    {{-- Grid 2 Kolom --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Sektor Akademik --}}
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-indigo-50/50 border border-indigo-100 rounded-2xl">
                            <div>
                                <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest mb-2">Tahun Ajaran <span class="text-rose-500">*</span></label>
                                <select x-model="editTahunAjaranId" name="tahun_ajaran_id" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-indigo-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm">
                                    <option value="">-- Pilih TA --</option>
                                    @foreach($tahunAjarans as $ta)
                                        <option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-indigo-900 uppercase tracking-widest mb-2">Semester Akademik <span class="text-rose-500">*</span></label>
                                <select x-model="editSemesterId" name="semester_id" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-indigo-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm">
                                    <option value="">-- Pilih Semester --</option>
                                    @foreach($semesters as $smt)
                                        <option value="{{ $smt->id }}">{{ $smt->nama_semester }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        {{-- Sektor Tempat/Waktu --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Domisili Cetak <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="editTempatCetak" name="tempat_cetak" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tanggal Terbit Rapor <span class="text-rose-500">*</span></label>
                            <input type="date" x-model="editTanggalCetak" name="tanggal_cetak" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner text-center">
                        </div>

                        {{-- Sektor Kepala Sekolah --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Pejabat (Kepsek) <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="editNamaKepalaSekolah" name="nama_kepala_sekolah" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nomor Induk (NIP Kepsek)</label>
                            <input type="text" x-model="editNipKepalaSekolah" name="nip_kepala_sekolah" class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-slate-50 py-3 px-4 shadow-inner">
                        </div>

                        {{-- Sektor Labeling --}}
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6 p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Gelar Jabatan <span class="text-rose-500">*</span></label>
                                <input type="text" x-model="editLabelKepalaSekolah" name="label_kepala_sekolah" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Awalan NIP Kepsek <span class="text-rose-500">*</span></label>
                                <input type="text" x-model="editLabelNipKepalaSekolah" name="label_nip_kepala_sekolah" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Awalan NIP Wali <span class="text-rose-500">*</span></label>
                                <input type="text" x-model="editLabelNipWaliKelas" name="label_nip_wali_kelas" required class="w-full text-sm font-bold text-slate-800 rounded-xl border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 bg-white py-3 px-4 shadow-sm text-center">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-6">
                        <button type="button" @click="openEdit = false" class="px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer shadow-sm text-sm">Tutup Panel</button>
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
                    <h4 class="text-xl font-black text-slate-900 tracking-tight">Hapus Pengaturan?</h4>
                    <p class="text-sm font-medium text-slate-500 mt-2 leading-relaxed">
                        Anda yakin ingin membuang konfigurasi cetak untuk <br><span class="font-black text-slate-800 bg-slate-100 px-2 py-0.5 rounded" x-text="deleteTargetName"></span>?
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