<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Master Klasifikasi Jenis Surat') }}
        </h2>
    </x-slot>

    <div x-data="{ openCreate: false, openEdit: false, editData: { id: '', kode_klasifikasi: '', nama_jenis: '', format_nomor: '' } }" class="py-6 bg-slate-100 min-h-[calc(100vh-64px)]">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl shadow-sm space-y-1">
                    <p class="font-bold">❌ Gagal melakukan Import Data:</p>
                    <ul class="list-disc pl-4 space-y-0.5 text-[11px]">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="text-xs font-bold text-gray-900 uppercase">Daftar Aturan Penomoran</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Gunakan tag pencocokan otomatis: <code class="bg-slate-100 px-1 py-0.5 text-indigo-600 font-mono text-[10px]">[NOMOR]</code>, <code class="bg-slate-100 px-1 py-0.5 text-indigo-600 font-mono text-[10px]">[KODE]</code>, <code class="bg-slate-100 px-1 py-0.5 text-indigo-600 font-mono text-[10px]">[BULAN]</code>, <code class="bg-slate-100 px-1 py-0.5 text-indigo-600 font-mono text-[10px]">[TAHUN]</code></p>
                </div>
                
                <div class="flex flex-wrap gap-2 w-full md:w-auto">
                    <a href="{{ route('surat.jenis.download-template') }}" class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer flex items-center gap-1">
                        📥 Download Template
                    </a>
                    
                    <form action="{{ route('surat.jenis.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-1 bg-slate-50 border p-1 rounded-lg">
                        @csrf
                        <input type="file" name="file_excel" required class="text-[10px] text-gray-500 max-w-[150px] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 cursor-pointer">
                        <button type="submit" class="px-2 py-1 bg-teal-600 hover:bg-teal-700 text-white text-[10px] font-bold rounded-md transition-colors cursor-pointer">
                            🚀 Import
                        </button>
                    </form>

                    <button @click="openCreate = true" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        ➕ Tambah Manual
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="p-4 w-20">Kode</th>
                            <th class="p-4">Nama Jenis Surat</th>
                            <th class="p-4">Template Format Nomor</th>
                            <th class="p-4 w-28 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-xs text-gray-700">
                        @forelse($jenisSurat as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 font-mono font-bold text-indigo-600">{{ $item->kode_klasifikasi }}</td>
                                <td class="p-4 font-semibold text-gray-900">{{ $item->nama_jenis }}</td>
                                <td class="p-4 font-mono text-slate-500 bg-slate-50/40">{{ $item->format_nomor }}</td>
                                <td class="p-4 text-center space-x-1">
                                    <button @click="editData = { id: '{{ $item->id }}', kode_klasifikasi: '{{ $item->kode_klasifikasi }}', nama_jenis: '{{ $item->nama_jenis }}', format_nomor: '{{ $item->format_nomor }}' }; openEdit = true" 
                                            class="text-indigo-600 hover:text-indigo-900 font-bold">Edit</button>
                                    <span class="text-gray-300">|</span>
                                    <form action="{{ route('surat.jenis.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus klasifikasi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-900 font-bold">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center italic text-gray-400">Belum ada aturan format surat yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Aturan Surat</h3>
                    <button @click="openCreate = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
                </div>
                <form action="{{ route('surat.jenis.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kode Klasifikasi *</label>
                        <input type="text" name="kode_klasifikasi" required placeholder="Contoh: 421.1 atau 800" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Jenis Surat *</label>
                        <input type="text" name="nama_jenis" required placeholder="Contoh: Surat Tugas Kepsek" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Format Susunan Nomor *</label>
                        <input type="text" name="format_nomor" required placeholder="Contoh: [NOMOR]/[KODE]/SMK-1/[BULAN]/[TAHUN]" class="w-full rounded-lg border-gray-300 font-mono text-xs focus:ring-indigo-500">
                    </div>
                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-gray-100 space-y-4" @click.away="openEdit = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Ubah Aturan Surat</h3>
                    <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
                </div>
                <form :action="'/surat/jenis/' + editData.id" method="POST" class="space-y-3 text-xs">
                    @csrf @method('PUT')
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kode Klasifikasi *</label>
                        <input type="text" name="kode_klasifikasi" x-model="editData.kode_klasifikasi" required class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Jenis Surat *</label>
                        <input type="text" name="nama_jenis" x-model="editData.nama_jenis" required class="w-full rounded-lg border-gray-300 text-xs focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Format Susunan Nomor *</label>
                        <input type="text" name="format_nomor" x-model="editData.format_nomor" required class="w-full rounded-lg border-gray-300 font-mono text-xs focus:ring-indigo-500">
                    </div>
                    <div class="pt-2 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>