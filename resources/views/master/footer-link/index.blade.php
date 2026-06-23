<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Navigasi Tautan Teks Bawah (Footer Links)') }}
        </h2>
    </x-slot>

    <div x-data="{ 
        openCreate: false, 
        openEdit: false, 
        openDelete: false,
        
        // Form Fields State
        id: '', group: '', judul: '', url: '', urutan: 1, status: true,
        
        // Custom Delete Confirmation State
        deleteAction: '',
        deleteTargetTitle: ''
    }" class="py-12 bg-slate-900/10 min-h-screen relative">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            @if(session('success'))
                <div class="p-4 mb-2 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-xl border border-emerald-200">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-2 text-xs font-medium text-rose-700 bg-rose-50 rounded-xl border border-rose-200">
                    ⚠️ Gagal memproses data. Silakan cek kembali inputan form modal Anda.
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Link Informasi Footer</h3>
                        <p class="text-xs text-gray-500">Kelola tautan cepat internal maupun eksternal untuk dipajang pada kolom informasi bawah website utama.</p>
                    </div>
                    
                    <button type="button" 
                            @click="
                                id = ''; group = ''; judul = ''; url = ''; urutan = 1; status = true;
                                openCreate = true;
                            " 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                        ➕ Tambah Link Footer
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-16 text-center">No</th>
                                <th class="p-4 w-48">Kelompok (Group)</th>
                                <th class="p-4">Label Judul</th>
                                <th class="p-4">Alamat URL</th>
                                <th class="p-4 text-center w-24">Urutan</th>
                                <th class="p-4 text-center w-32">Status</th>
                                <th class="p-4 pr-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($links as $index => $link)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 text-center font-mono text-gray-400">
                                        {{ $links instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($links->firstItem() + $index) : ($index + 1) }}
                                    </td>
                                    <td class="p-4 pl-4">
                                        <span class="px-2 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-md font-bold text-[10px] uppercase">
                                            📁 {{ $link->group }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-bold text-gray-900 text-sm">🔗 {{ $link->judul }}</td>
                                    <td class="p-4 font-mono text-gray-500 text-[11px] max-w-xs truncate" title="{{ $link->url }}">
                                        {{ $link->url }}
                                    </td>
                                    <td class="p-4 text-center font-mono font-bold text-gray-600">{{ $link->urutan }}</td>
                                    <td class="p-4 text-center">
                                        @if($link->status)
                                            <span class="px-2 py-0.5 bg-green-50 border border-green-200 text-green-700 text-[10px] font-bold rounded shadow-sm">AKTIF</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-gray-50 border border-gray-200 text-gray-400 text-[10px] font-medium rounded">NON-AKTIF</span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <button type="button" 
                                                    @click="
                                                        id = '{{ $link->id }}';
                                                        group = '{{ addslashes($link->group) }}';
                                                        judul = '{{ addslashes($link->judul) }}';
                                                        url = '{{ addslashes($link->url) }}';
                                                        urutan = '{{ $link->urutan }}';
                                                        status = {{ $link->status ? 'true' : 'false' }};
                                                        openEdit = true;
                                                    "
                                                    class="text-amber-600 hover:underline font-semibold cursor-pointer text-xs">
                                                📝 Edit
                                            </button>

                                            <button type="button"
                                                    @click="
                                                        deleteAction = '{{ route('master.footer-link.destroy', $link->id) }}';
                                                        deleteTargetTitle = '{{ addslashes($link->judul) }}';
                                                        openDelete = true;
                                                    "
                                                    class="text-rose-600 hover:underline font-semibold cursor-pointer text-xs">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Belum ada tautan footer yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(method_exists($links, 'hasPages') && $links->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80">
                        {{ $links->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 overflow-hidden" @click.away="openCreate = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Tambah Tautan Footer</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('master.footer-link.store') }}" method="POST" class="p-6 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kelompok Menu / Group <span class="text-rose-500">*</span></label>
                        <input type="text" name="group" required placeholder="Contoh: Aplikasi Sekolah, Link Terkait" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Label Teks Tautan / Judul <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" required placeholder="Contoh: Portal SIMPKB atau Kelulusan" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Target Alamat URL <span class="text-rose-500">*</span></label>
                        <input type="text" name="url" required placeholder="Contoh: https://ppdb.sekolah.sch.id atau /page/visi-misi" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nomor Urut Tampilan / Urutan <span class="text-rose-500">*</span></label>
                        <input type="number" name="urutan" required min="0" value="1" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="status_create" name="status" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm">
                        <label for="status_create" class="font-semibold text-gray-700 cursor-pointer select-none">Aktifkan dan tampilkan langsung di web.</label>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 overflow-hidden" @click.away="openEdit = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-900 uppercase">Edit Tautan Footer</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                <form :action="'{{ route('master.footer-link.index') }}/' + id" method="POST" class="p-6 space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Kelompok Menu / Group <span class="text-rose-500">*</span></label>
                        <input type="text" name="group" x-model="group" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Label Teks Tautan / Judul <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" x-model="judul" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Target Alamat URL <span class="text-rose-500">*</span></label>
                        <input type="text" name="url" x-model="url" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Nomor Urut Tampilan / Urutan <span class="text-rose-500">*</span></label>
                        <input type="number" name="urutan" x-model="urutan" required min="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="edit_status" name="status" value="1" x-model="status" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm">
                        <label for="edit_status" class="font-semibold text-gray-700 cursor-pointer select-none">Tautan ini aktif digunakan.</label>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Konfirmasi Hapus Link</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus tautan <span class="font-bold text-gray-800" x-text="deleteTargetTitle"></span> dari susunan menu bawah website?
                    </p>
                </div>
                <form :action="deleteAction" method="POST" class="flex justify-center gap-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">Ya, Hapus</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>