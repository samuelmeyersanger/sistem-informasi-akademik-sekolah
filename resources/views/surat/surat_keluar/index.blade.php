<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Agenda Surat Keluar') }}</h2>
    </x-slot>

    <div x-data="{ openCreate: false }" class="py-6 bg-slate-100 min-h-[calc(100vh-64px)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl">✅ {{ session('success') }}</div>
            @endif

            <div class="bg-white p-4 rounded-xl border border-gray-200 flex justify-between items-center shadow-sm">
                <div>
                    <h3 class="text-xs font-bold text-gray-900 uppercase">Draf & Surat Keluar Resmi</h3>
                    <p class="text-[11px] text-gray-400">Gunakan file Excel jika surat memiliki daftar lampiran tabel yang panjang (Siswa/Guru/Nilai).</p>
                </div>
                <button @click="openCreate = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg cursor-pointer">
                    📝 Buat Usulan Surat
                </button>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">
                            <th class="p-4">Nomor / Perihal</th>
                            <th class="p-4">Tujuan</th>
                            <th class="p-4 text-center">Lampiran Excel</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-xs text-gray-700">
                        @forelse($suratKeluar as $item)
                            <tr class="hover:bg-gray-50/50">
                                <td class="p-4">
                                    <div class="font-bold font-mono text-gray-900">{{ $item->nomor_surat ?? '✨ [Belum Disetujui]' }}</div>
                                    <div class="font-semibold text-gray-600 mt-0.5">{{ $item->perihal }}</div>
                                </td>
                                <td class="p-4 font-medium">{{ $item->tujuan_surat }}</td>
                                <td class="p-4 text-center">
                                    @if($item->header_1)
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[10px] font-bold">📋 Aktif</span>
                                    @else
                                        <span class="text-gray-400 italic">Tidak Ada</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $item->status == 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $item->status }}</span>
                                </td>
                                <td class="p-4 text-center space-x-1">
                                    @if($item->status == 'Menunggu Persetujuan')
                                        <form action="{{ route('surat.keluar.setujui', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded cursor-pointer">Setujui</button>
                                        </form>
                                    @elseif($item->status == 'Disetujui')
                                        <a href="{{ route('surat.keluar.cetak', $item->id) }}" target="_blank" class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold rounded block sm:inline">🖨️ Cetak PDF</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-8 text-center italic text-gray-400">Belum ada data surat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 bg-gray-900/40 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
            <div class="bg-white rounded-xl max-w-2xl w-full p-6 shadow-xl border border-gray-100 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Buat Usulan Draf Surat</h3>
                    <button @click="openCreate = false" class="text-gray-400 font-bold text-xl cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('surat.keluar.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Klasifikasi Format *</label>
                            <select name="jenis_surat_id" required class="w-full rounded-lg border-gray-300 text-xs">
                                <option value="">-- Pilih --</option>
                                @foreach($jenisSurat as $js)
                                    <option value="{{ $js->id }}">{{ $js->kode_klasifikasi }} - {{ $js->nama_jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Tujuan Surat / Kepada *</label>
                            <input type="text" name="tujuan_surat" required placeholder="Contoh: Orang Tua Siswa" class="w-full rounded-lg border-gray-300 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div><label class="block font-semibold text-gray-700 mb-1">Tanggal *</label><input type="date" name="tanggal_surat" required class="w-full rounded-lg border-gray-300 text-xs"></div>
                        <div><label class="block font-semibold text-gray-700 mb-1">Metode TTD *</label><select name="metode_ttd" required class="w-full rounded-lg border-gray-300 text-xs"><option value="Digital">Digital</option><option value="Basah">Basah</option></select></div>
                        <div><label class="block font-semibold text-gray-700 mb-1">Penandatangan *</label><select name="penandatangan_id" required class="w-full rounded-lg border-gray-300 text-xs">@foreach($daftarKepsek as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Perihal Utama *</label>
                        <input type="text" name="perihal" required placeholder="Contoh: SK Penerimaan PPDB" class="w-full rounded-lg border-gray-300 text-xs">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Isi Surat Resmi *</label>
                        <textarea name="isi_surat" rows="4" required class="w-full rounded-lg border-gray-300 text-xs"></textarea>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                        <label class="block font-bold text-slate-700 mb-1">📎 Upload Excel Lampiran Tabel (Opsional)</label>
                        <input type="file" name="file_excel" class="w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-[10px] text-gray-400 mt-1">Sistem otomatis mendeteksi baris pertama sebagai Judul Kolom (Maksimal 5 Kolom).</p>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t">
                        <button type="button" @click="openCreate = false" class="px-3 py-1.5 bg-gray-100 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white font-semibold rounded-lg shadow-sm">🚀 Kirim Usulan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>