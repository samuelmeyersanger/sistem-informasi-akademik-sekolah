<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Manajemen Tema Kokurikuler') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="temaManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Pesan Sukses / Error --}}
            @if (session('success'))
                <div class="mb-6 bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error') || $errors->any())
                <div class="mb-6 bg-rose-50/80 backdrop-blur-md border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    <span class="font-bold text-sm">{{ session('error') ?? 'Terjadi kesalahan saat menyimpan data.' }}</span>
                </div>
            @endif

            {{-- Kotak Kaca Utama --}}
            <div class="bg-white/80 backdrop-blur-2xl overflow-hidden shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60">
                <div class="p-8 lg:p-10">
                    
                    {{-- Header Tabel & Pencarian --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Daftar Tema P5</h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Kelola tema kegiatan Proyek Penguatan Profil Pelajar Pancasila.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            {{-- Form Pencarian --}}
                            <form action="{{ route('rapor.tema-kokurikuler.index') }}" method="GET" class="relative w-full sm:w-64">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tema..." 
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50/50 border-slate-200 text-sm font-medium rounded-2xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors shadow-inner">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                </div>
                            </form>

                            {{-- Tombol Tambah (Alpine Modal) --}}
                            <button @click="openModal()" 
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all">
                                <i class="fa-solid fa-plus mr-2"></i> Tambah Tema
                            </button>
                        </div>
                    </div>

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto bg-slate-50/50 rounded-2xl border border-slate-100">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                                <tr class="text-left bg-slate-100/50 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200">
                                    <th class="px-6 py-4 rounded-tl-2xl">No</th>
                                    <th class="px-6 py-4">Nama Tema</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-center rounded-tr-2xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                @forelse ($temas as $index => $tema)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4 text-slate-400">{{ $temas->firstItem() + $index }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800">{{ $tema->tema }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($tema->is_aktif)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-sm">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-200 text-slate-500 border border-slate-300">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center space-x-2">
                                            {{-- Tombol Edit --}}
                                            <button @click="openModal({{ $tema }})" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors"
                                                    title="Edit Tema">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            
                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('rapor.tema-kokurikuler.destroy', $tema->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tema ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-colors"
                                                        title="Hapus Tema">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">
                                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-300 block"></i>
                                            Belum ada data Tema Kokurikuler yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi --}}
                    <div class="mt-6">
                        {{ $temas->links() }}
                    </div>

                </div>
            </div>
        </div>

        {{-- Modal Tambah / Edit --}}
        <div x-show="showModal" 
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            {{-- Backdrop Hitam Blur --}}
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

            {{-- Kotak Modal --}}
            <div class="relative bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-2xl overflow-hidden w-full max-w-lg transform transition-all"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="p-8 border-b border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 tracking-tight" x-text="editMode ? 'Edit Tema Kokurikuler' : 'Tambah Tema Baru'"></h3>
                </div>
                
                <form :action="editMode ? '{{ url('rapor/tema-kokurikuler') }}/' + form.id : '{{ route('rapor.tema-kokurikuler.store') }}'" method="POST" class="p-8">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    {{-- Input Nama Tema --}}
                    <div class="mb-6">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Tema</label>
                        <input type="text" name="tema" x-model="form.tema" required
                               class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-slate-800 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors" 
                               placeholder="Contoh: Gaya Hidup Berkelanjutan">
                    </div>

                    {{-- Input Status Aktif --}}
                    <div class="mb-8 p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center gap-3">
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input type="checkbox" name="is_aktif" id="is_aktif" x-model="form.is_aktif" value="1"
                                       class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 bg-white">
                            </div>
                            <div class="ml-3 text-sm leading-6">
                                <label for="is_aktif" class="font-bold text-slate-700 cursor-pointer">Status Tema Aktif</label>
                                <p class="text-[11px] text-slate-500 font-medium">Hanya tema aktif yang akan muncul saat guru mengisi nilai.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                        <button type="button" @click="closeModal()" 
                                class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-800 bg-transparent hover:bg-slate-100 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all">
                            <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Tema
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script Logika Alpine --}}
    <script>
        function temaManager() {
            return {
                showModal: false,
                editMode: false,
                form: {
                    id: '',
                    tema: '',
                    is_aktif: false
                },
                openModal(tema = null) {
                    if (tema) {
                        this.editMode = true;
                        this.form.id = tema.id;
                        this.form.tema = tema.tema;
                        // Konversi tipe 1/0 menjadi true/false untuk Alpine checkbox
                        this.form.is_aktif = tema.is_aktif == 1 ? true : false; 
                    } else {
                        this.editMode = false;
                        this.form.id = '';
                        this.form.tema = '';
                        this.form.is_aktif = true; // Default otomatis aktif
                    }
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                }
            }
        }
    </script>
</x-app-layout>