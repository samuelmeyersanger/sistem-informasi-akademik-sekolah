<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Arsip Surat Masuk') }}
        </h2>
    </x-slot>

    <div x-data="{ 
            openCreate: false, 
            openEdit: false, 
            editData: { id: '', nomor_surat: '', asal_instansi: '', perihal: '', tanggal_surat: '', tanggal_terima: '', sifat: '' } 
         }" 
         class="py-6 bg-slate-100 min-h-[calc(100vh-64px)]">
         
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl shadow-sm">
                    <p class="font-bold">❌ Gagal menyimpan data. Silakan periksa kembali inputan Anda.</p>
                </div>
            @endif

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-xs font-bold text-gray-900 uppercase">Buku Agenda Surat Masuk</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Mencatat seluruh surat dinas, undangan, atau berkas yang diterima oleh instansi sekolah.</p>
                </div>
                <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                    📥 Registrasi Surat Masuk
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="p-4 w-48">Asal Instansi / No. Surat</th>
                                <th class="p-4">Perihal</th>
                                <th class="p-4 w-28">Tgl Surat</th>
                                <th class="p-4 w-28">Tgl Terima</th>
                                <th class="p-4 w-24 text-center">Sifat</th>
                                <th class="p-4 w-44 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-xs text-gray-700">
                            @forelse($suratMasuk as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900 truncate max-w-[180px]">{{ $item->asal_instansi }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $item->nomor_surat }}</div>
                                    </td>
                                    <td class="p-4 font-medium text-gray-800">{{ $item->perihal }}</td>
                                    <td class="p-4 text-gray-500 font-mono">{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                                    <td class="p-4 text-gray-500 font-mono">{{ \Carbon\Carbon::parse($item->tanggal_terima)->format('d/m/Y') }}</td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded border 
                                            {{ $item->sifat == 'Penting' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                            {{ $item->sifat == 'Rahasia' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                            {{ $item->sifat == 'Biasa' ? 'bg-slate-100 text-slate-600 border-slate-200' : '' }}
                                        ">
                                            {{ $item->sifat }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center space-x-1">
                                        <a href="{{ route('surat.masuk.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded">
                                            Buka
                                        </a>

                                        <button @click="editData = { 
                                                    id: '{{ $item->id }}', 
                                                    nomor_surat: '{{ $item->nomor_surat }}', 
                                                    asal_instansi: '{{ $item->asal_instansi }}', 
                                                    perihal: '{{ $item->perihal }}', 
                                                    tanggal_surat: '{{ $item->tanggal_surat }}', 
                                                    tanggal_terima: '{{ $item->tanggal_terima }}', 
                                                    sifat: '{{ $item->sifat }}' 
                                                }; openEdit = true" 
                                                class="text-amber-600 hover:text-amber-900 font-bold bg-amber-50 hover:bg-amber-100 px-2 py-1 rounded cursor-pointer">
                                            Edit
                                        </button>
                                        
                                        <form action="{{ route('surat.masuk.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus seluruh arsip surat masuk ini beserta berkas digitalnya?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-bold p-1">&times;</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center italic text-gray-400">
                                        📭 Belum ada arsip surat masuk yang terdaftar di dalam sistem.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gray-100 space-y-4" @click.away="openCreate = false">
                
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="text-sm font-bold text-gray-900 uppercase flex items-center gap-1.5">📥 Registrasi Surat Masuk</h3>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('surat.masuk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Nomor Surat *</label>
                            <input type="text" name="nomor_surat" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Asal Instansi *</label>
                            <input type="text" name="asal_instansi" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-xs">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Perihal *</label>
                        <input type="text" name="perihal" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-xs">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Tanggal Surat *</label>
                            <input type="date" name="tanggal_surat" required class="w-full rounded-lg border-gray-300 text-xs text-gray-600">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Tanggal Terima *</label>
                            <input type="date" name="tanggal_terima" required class="w-full rounded-lg border-gray-300 text-xs text-gray-600">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Sifat *</label>
                            <select name="sifat" required class="w-full rounded-lg border-gray-300 text-xs text-gray-600">
                                <option value="Biasa">Biasa</option>
                                <option value="Penting">Penting</option>
                                <option value="Rahasia">Rahasia</option>
                            </select>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-50 border border-dashed border-slate-300 rounded-lg">
                        <label class="block font-semibold text-gray-700 mb-1">Unggah Berkas Scan (Wajib PDF, Maks. 5MB) *</label>
                        <input type="file" name="file_surat" accept="application/pdf" required class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:text-xs">
                    </div>
                    <div class="pt-2 flex justify-end gap-2 border-t">
                        <button type="button" @click="openCreate = false" class="px-3 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-sm">📥 Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gray-100 space-y-4" @click.away="openEdit = false">
                
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="text-sm font-bold text-amber-600 uppercase flex items-center gap-1.5">✏️ Ubah Data Surat Masuk</h3>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 font-bold text-xl cursor-pointer">&times;</button>
                </div>
                
                <form :action="'/surat/masuk/' + editData.id" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT') 
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Nomor Surat *</label>
                            <input type="text" name="nomor_surat" x-model="editData.nomor_surat" required class="w-full rounded-lg border-gray-300 text-xs">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Asal Instansi *</label>
                            <input type="text" name="asal_instansi" x-model="editData.asal_instansi" required class="w-full rounded-lg border-gray-300 text-xs">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Perihal *</label>
                        <input type="text" name="perihal" x-model="editData.perihal" required class="w-full rounded-lg border-gray-300 text-xs">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Tanggal Surat *</label>
                            <input type="date" name="tanggal_surat" x-model="editData.tanggal_surat" required class="w-full rounded-lg border-gray-300 text-xs text-gray-600">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Tanggal Terima *</label>
                            <input type="date" name="tanggal_terima" x-model="editData.tanggal_terima" required class="w-full rounded-lg border-gray-300 text-xs text-gray-600">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Sifat *</label>
                            <select name="sifat" x-model="editData.sifat" required class="w-full rounded-lg border-gray-300 text-xs text-gray-600">
                                <option value="Biasa">Biasa</option>
                                <option value="Penting">Penting</option>
                                <option value="Rahasia">Rahasia</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 bg-amber-50/50 border border-dashed border-amber-200 rounded-lg">
                        <label class="block font-semibold text-amber-900 mb-1">Ganti Berkas Scan PDF (Opsional, Biarkan kosong jika tidak diganti)</label>
                        <input type="file" name="file_surat" accept="application/pdf" class="mt-1 block w-full text-xs text-gray-500 file:mr-3 file:text-xs">
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t">
                        <button type="button" @click="openEdit = false" class="px-3 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg shadow-sm">💾 Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>