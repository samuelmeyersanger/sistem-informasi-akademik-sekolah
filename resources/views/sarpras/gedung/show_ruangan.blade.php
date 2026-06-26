<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('sarpras.gedung.index') }}" class="hover:text-indigo-600 transition-colors">Gedung</a>
            <span>&raquo;</span>
            <a href="{{ route('sarpras.gedung.show', $ruangan->gedung_id) }}" class="hover:text-indigo-600 transition-colors">{{ $ruangan->gedung->nama_gedung }}</a>
            <span>&raquo;</span>
            <span class="text-gray-800 font-medium">Inventaris Ruang</span>
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
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-xl shadow-sm">
                    <p class="font-bold mb-1 flex items-center gap-1">⚠️ Gagal menyimpan data:</p>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-2xl text-indigo-600 text-2xl">
                        🚪
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-xs uppercase bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded">
                                {{ $ruangan->kode_ruangan }}
                            </span>
                            <h3 class="text-lg font-bold text-gray-900">{{ $ruangan->nama_ruangan }}</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Lokasi: <span class="font-semibold text-gray-700">{{ $ruangan->gedung->nama_gedung }}</span> (Kapasitas: {{ $ruangan->kapasitas }} Orang)
                        </p>
                    </div>
                </div>
                <a href="{{ route('sarpras.gedung.show', $ruangan->gedung_id) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors text-center shrink-0">
                    ⬅️ Kembali ke Daftar Ruangan
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Kartu Inventaris Barang (KIB)</h4>
                        <p class="text-xs text-gray-500">Seluruh aset sarana dan prasarana yang teregistrasi di dalam ruangan ini.</p>
                    </div>
                    <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1 cursor-pointer shrink-0">
                        ➕ Tambah Barang Inventaris
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-20">Foto</th>
                                <th class="p-4">Identitas Barang</th>
                                <th class="p-4 text-center">Jumlah</th>
                                <th class="p-4 text-center">Kondisi</th>
                                <th class="p-4 text-right">Harga Perolehan</th>
                                <th class="p-4 pr-6 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($ruangan->inventaris as $i)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6">
                                        @if($i->foto_barang)
                                            <img src="{{ asset('storage/' . $i->foto_barang) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-100 shadow-sm" alt="Foto Barang">
                                        @else
                                            <div class="w-12 h-12 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 text-lg" title="Tidak ada foto">
                                                📦
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <div class="font-mono font-bold text-indigo-600 mb-0.5">[{{ $i->kode_barang }}]</div>
                                        <div class="font-bold text-gray-900 text-sm">{{ $i->nama_barang }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5 space-x-2">
                                            <span>Merek: <strong>{{ $i->merek ?? '-' }}</strong></span>
                                            <span>&bull;</span>
                                            <span>Tahun: <strong>{{ $i->tahun_pembelian ?? '-' }}</strong></span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center font-bold text-sm text-gray-900">
                                        {{ $i->jumlah }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md shadow-sm border
                                            @if($i->kondisi == 'Baik') bg-green-50 border-green-200 text-green-700
                                            @elseif($i->kondisi == 'Rusak Ringan') bg-amber-50 border-amber-200 text-amber-700
                                            @else bg-rose-50 border-rose-200 text-rose-700 @endif">
                                            {{ $i->kondisi }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-mono font-medium text-gray-900">
                                        {{ $i->harga_perolehan ? 'Rp ' . number_format($i->harga_perolehan, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button type="button" @click="initEdit({{ json_encode($i) }})" class="p-1 text-blue-600 hover:underline font-medium cursor-pointer">
                                                📝 Edit
                                            </button>

                                            <button type="button" @click="initDelete('{{ route('sarpras.gedung.destroyInventaris', $i->id) }}', '{{ addslashes($i->nama_barang) }}')" class="p-1 text-rose-600 hover:underline font-medium cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        Belum ada barang inventaris yang terdaftar di dalam ruangan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Registrasi Barang Inventaris</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('sarpras.gedung.storeInventaris', $ruangan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Registrasi Barang *</label>
                            <input type="text" name="kode_barang" required placeholder="INV-2026-001" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Barang *</label>
                            <input type="text" name="nama_barang" required placeholder="Proyektor Epson" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori / Jenis</label>
                            <input type="text" name="kategori" placeholder="Elektronik" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Merek</label>
                            <input type="text" name="merek" placeholder="Epson" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Model / Tipe</label>
                            <input type="text" name="model" placeholder="EB-X400" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun Pembelian</label>
                            <input type="number" name="tahun_pembelian" placeholder="2026" min="1900" max="2100" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Harga Perolehan (Rp)</label>
                            <input type="number" name="harga_perolehan" placeholder="7500000" min="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kuantitas / Jumlah *</label>
                            <input type="number" name="jumlah" required min="1" value="1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kondisi Barang *</label>
                            <select name="kondisi" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                                <option value="Hilang">Hilang</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Foto Fisik Barang</label>
                            <input type="file" name="foto_barang" accept="image/*" class="w-full text-xs border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 shadow-sm p-1.5 bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Detail Lokasi Spesifik / Deskripsi Tambahan</label>
                        <textarea name="deskripsi" placeholder="Digantung di plafon tengah ruangan, remote disimpan staf tata usaha..." rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Aset</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Data Aset Inventaris</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="editActionUrl" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Registrasi Barang *</label>
                            <input type="text" x-model="editKodeBarang" name="kode_barang" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Barang *</label>
                            <input type="text" x-model="editNamaBarang" name="nama_barang" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori / Jenis</label>
                            <input type="text" x-model="editKategori" name="kategori" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Merek</label>
                            <input type="text" x-model="editMerek" name="merek" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Model / Tipe</label>
                            <input type="text" x-model="editModel" name="model" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun Pembelian</label>
                            <input type="number" x-model="editTahunPembelian" name="tahun_pembelian" min="1900" max="2100" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Harga Perolehan (Rp)</label>
                            <input type="number" x-model="editHargaPerolehan" name="harga_perolehan" min="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kuantitas / Jumlah *</label>
                            <input type="number" x-model="editJumlah" name="jumlah" required min="1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Kondisi Barang *</label>
                            <select x-model="editKondisi" name="kondisi" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                                <option value="Hilang">Hilang</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Ganti Foto Fisik Barang</label>
                            <input type="file" name="foto_barang" accept="image/*" class="w-full text-xs border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 shadow-sm p-1.5 bg-slate-50">
                            <p class="text-[10px] text-gray-400 mt-1">* Kosongkan jika tidak ingin mengubah foto lama</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Detail Lokasi Spesifik / Deskripsi Tambahan</label>
                        <textarea x-model="editDeskripsi" name="deskripsi" rows="2" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Barang dari Ruangan?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus barang <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>? Log aktivitas dan pencatatan inventaris barang ini akan diarsipkan (Soft Delete).
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer border border-transparent">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>