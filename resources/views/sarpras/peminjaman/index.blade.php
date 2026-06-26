<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Logistik & Peminjaman Sarpras
        </h2>
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
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>❌</span> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('sarpras.peminjaman.index') }}" class="w-full md:w-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang / nama peminjam..." class="w-full sm:w-64 text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm pl-3 pr-8">
                    </div>
                    
                    <select name="status" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>🔴 Sedang Dipinjam</option>
                        <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>🟢 Dikembalikan</option>
                        <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>🟡 Terlambat Kembali</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg text-xs transition-colors cursor-pointer">
                        Saring Data
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('sarpras.peminjaman.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs text-center rounded-lg font-medium">Reset</a>
                    @endif
                </form>

                <button @click="openCreate = true" class="w-full md:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    ➕ Catat Peminjaman Baru
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6">Barang Sarpras</th>
                                <th class="p-4">Nama Peminjam</th>
                                <th class="p-4 text-center">Durasi Pinjam</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4">Keperluan / Catatan</th>
                                <th class="p-4 pr-6 text-center w-48">Aksi Administrasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($peminjaman as $p)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-4 pl-6">
                                        @if($p->inventaris)
                                            <div class="font-bold text-gray-900">{{ $p->inventaris->nama_barang }}</div>
                                            <div class="font-mono text-[10px] text-indigo-600 mt-0.5">[{{ $p->inventaris->kode_barang }}]</div>
                                        @else
                                            <span class="text-gray-400 italic">Aset Telah Dihapus</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <div class="font-medium text-gray-800">{{ $p->peminjam->nama_pegawai ?? 'Tidak Diketahui' }}</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">Petugas: {{ $p->pencatat->nama_pegawai ?? 'System/Self' }}</div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('d M Y') }}</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">s/d {{ \Carbon\Carbon::parse($p->tanggal_kembali_rencana)->translatedFormat('d M Y') }}</div>
                                        @if($p->tanggal_kembali_realisasi)
                                            <div class="text-[10px] font-medium text-emerald-600 mt-1 bg-emerald-50 px-1.5 py-0.5 rounded inline-block">
                                                Realisasi: {{ \Carbon\Carbon::parse($p->tanggal_kembali_realisasi)->translatedFormat('d M Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md shadow-sm border
                                            @if($p->status == 'Dipinjam') bg-amber-50 border-amber-200 text-amber-700
                                            @elseif($p->status == 'Dikembalikan') bg-green-50 border-green-200 text-green-700
                                            @else bg-rose-50 border-rose-200 text-rose-700 @endif">
                                            {{ $p->status }}
                                        </span>
                                    </td>
                                    <td class="p-4 max-w-xs truncate">
                                        <div class="font-medium text-gray-900" title="{{ $p->keperluan }}">{{ $p->keperluan }}</div>
                                        @if($p->catatan)
                                            <div class="text-[10px] text-slate-500 italic mt-0.5" title="{{ $p->catatan }}">✍️ {{ $p->catatan }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-2.5 font-medium">
                                            @if($p->status === 'Dipinjam')
                                                <button type="button" @click="initReturn('{{ route('sarpras.peminjaman.kembalikan', $p->id) }}', '{{ addslashes($p->inventaris->nama_barang ?? 'Aset') }}')" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-bold shadow-sm cursor-pointer">
                                                    ↩️ Kembalikan
                                                </button>
                                                <button type="button" @click="initEdit({{ json_encode($p) }})" class="text-blue-600 hover:underline cursor-pointer">
                                                    Edit
                                                </button>
                                            @endif
                                            
                                            <button type="button" @click="initDelete('{{ route('sarpras.peminjaman.destroy', $p->id) }}', '{{ addslashes($p->inventaris->nama_barang ?? 'Transaksi') }}')" class="text-rose-600 hover:underline cursor-pointer">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-gray-400 italic bg-gray-50/20">
                                        Tidak ditemukan rekaman log transaksi peminjaman sarpras.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($peminjaman->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $peminjaman->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Form Logistik Peminjaman</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('sarpras.peminjaman.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Barang / Alat Sarpras *</label>
                        <select name="inventaris_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Aset (Stok Ready) --</option>
                            @foreach($daftarInventaris as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_barang }} [{{ $item->kode_barang }}] - Sisa Stok: {{ $item->jumlah }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pegawai / Guru Peminjam *</label>
                        <select name="peminjam_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Personel --</option>
                            @foreach($daftarPegawai as $peg)
                                <option value="{{ $peg->id }}">{{ $peg->nama_pegawai }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Pinjam *</label>
                            <input type="date" name="tanggal_pinjam" required value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Estimasi Kembali *</label>
                            <input type="date" name="tanggal_kembali_rencana" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tujuan / Keperluan Pinjam *</label>
                        <input type="text" name="keperluan" required placeholder="Mengajar praktik biologi di Lab..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan Kondisi Khusus (Opsional)</label>
                        <textarea name="catatan" placeholder="Kabel HDMI bawaan agak longgar..." rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Proses Pinjam</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Ubah Data Log Peminjaman</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sesuaikan Barang Sarpras *</label>
                        <select name="inventaris_id" x-model="editInventarisId" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @foreach($daftarInventaris as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_barang }} [{{ $item->kode_barang }}]</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pegawai / Guru Peminjam *</label>
                        <select name="peminjam_id" x-model="editPeminjamId" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @foreach($daftarPegawai as $peg)
                                <option value="{{ $peg->id }}">{{ $peg->nama_pegawai }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Pinjam *</label>
                            <input type="date" name="tanggal_pinjam" x-model="editTanggalPinjam" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Estimasi Kembali *</label>
                            <input type="date" name="tanggal_kembali_rencana" x-model="editTanggalKembaliRencana" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tujuan / Keperluan Pinjam *</label>
                        <input type="text" name="keperluan" x-model="editKeperluan" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan</label>
                        <textarea name="catatan" x-model="editCatatan" rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openReturn" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openReturn = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Proses Pengembalian</h3>
                    <button type="button" @click="openReturn = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="returnActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Mencatat pengembalian fisik barang: <span class="font-bold text-gray-800" x-text="returnTargetName"></span>. Sistem akan otomatis menghitung kalkulasi keterlambatan dan merestorasi kuantitas stok.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Dikembalikan Riil *</label>
                        <input type="date" name="tanggal_kembali_realisasi" x-model="returnTanggalRealisasi" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan Kondisi Barang Saat Kembali</label>
                        <textarea name="catatan" x-model="returnCatatan" placeholder="Barang kembali dengan keadaan bersih & normal..." rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>
                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="openReturn = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm cursor-pointer transition-colors">Selesai & Kembalikan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Batalkan / Hapus Log Transaksi?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Menghapus log peminjaman barang <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>. Jika status saat ini masih **Dipinjam**, maka stok otomatis dikembalikan ke gudang sarpras.
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">Ya, Hapus Data</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>