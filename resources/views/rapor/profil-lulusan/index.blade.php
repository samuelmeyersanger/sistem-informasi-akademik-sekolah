<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Profil Lulusan (P5)') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="profilManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Alert Notifikasi --}}
            @if (session('success'))
                <div class="mb-6 bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error') || $errors->any())
                <div class="mb-6 bg-rose-50/80 backdrop-blur-md border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    <span class="font-bold text-sm">{{ session('error') ?? 'Terjadi kesalahan saat menyimpan.' }}</span>
                </div>
            @endif

            <div class="bg-white/80 backdrop-blur-2xl overflow-hidden shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60">
                <div class="p-8 lg:p-10">
                    
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Dimensi Profil Lulusan</h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Kelola elemen dimensi kompetensi lulusan untuk rapor P5.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <form action="{{ route('rapor.profil-lulusan.index') }}" method="GET" class="relative w-full sm:w-64">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dimensi..." 
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50/50 border-slate-200 text-sm font-medium rounded-2xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors shadow-inner">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                </div>
                            </form>

                            <button @click="openModal()" 
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all">
                                <i class="fa-solid fa-plus mr-2"></i> Tambah Profil
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-slate-50/50 rounded-2xl border border-slate-100">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                                <tr class="text-left bg-slate-100/50 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200">
                                    <th class="px-6 py-4 rounded-tl-2xl w-24">Urutan</th>
                                    <th class="px-6 py-4">Dimensi Utama</th>
                                    <th class="px-6 py-4">Subdimensi / Elemen</th>
                                    <th class="px-6 py-4 text-center rounded-tr-2xl w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                @forelse ($profils as $profil)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4 text-slate-400 font-black text-center text-lg">
                                            #{{ $profil->no }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-normal min-w-[200px]">
                                            <div class="font-bold text-slate-800">{{ $profil->tema }}</div>
                                            <div class="inline-block mt-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-md text-[10px] font-bold tracking-wide">
                                                {{ $profil->dimensi_profil_lulusan }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-normal min-w-[250px]">
                                            <div class="text-slate-600 leading-relaxed text-xs">{{ $profil->subdmensi }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center space-x-2">
                                            <button @click="openModal({{ $profil }})" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors"
                                                    title="Edit Profil">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            
                                            <form action="{{ route('rapor.profil-lulusan.destroy', $profil->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus profil ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-colors"
                                                        title="Hapus Profil">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">
                                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-300 block"></i>
                                            Belum ada data Profil Lulusan yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $profils->links() }}
                    </div>

                </div>
            </div>
        </div>

        {{-- Modal Tambah / Edit (Lebar dan Dinamis) --}}
        <div x-show="showModal" 
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

            <div class="relative bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-2xl overflow-hidden w-full max-w-2xl transform transition-all"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-xl font-black text-slate-800 tracking-tight" x-text="editMode ? 'Edit Profil Lulusan' : 'Tambah Profil Baru'"></h3>
                </div>
                
                <form :action="editMode ? '{{ url('rapor/profil-lulusan') }}/' + form.id : '{{ route('rapor.profil-lulusan.store') }}'" method="POST" class="p-8">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-5">
                        <div class="md:col-span-1">
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">No Urut</label>
                            <input type="number" name="no" x-model="form.no" required
                                   class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-slate-800 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-center" 
                                   placeholder="Misal: 1">
                        </div>
                        
                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Tema</label>
                            <input type="text" name="tema" x-model="form.tema" required
                                   class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-slate-800 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors" 
                                   placeholder="Misal: Beriman & Bertakwa...">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Dimensi Profil</label>
                        <input type="text" name="dimensi_profil_lulusan" x-model="form.dimensi_profil_lulusan" required
                               class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-slate-800 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors" 
                               placeholder="Contoh: Akhlak kepada Manusia">
                    </div>
                    
                    <div class="mb-8">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Subdimensi / Elemen Capaian</label>
                        <textarea name="subdmensi" x-model="form.subdmensi" required rows="3"
                               class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-medium text-slate-700 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors resize-none" 
                               placeholder="Tuliskan deksripsi panjang elemen capaian siswa..."></textarea>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <button type="button" @click="closeModal()" 
                                class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-800 bg-transparent hover:bg-slate-100 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all">
                            <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function profilManager() {
            return {
                showModal: false,
                editMode: false,
                form: {
                    id: '',
                    no: '',
                    tema: '',
                    dimensi_profil_lulusan: '',
                    subdmensi: '' // Typo ini saya pertahankan sesuai Database Migration Anda :)
                },
                openModal(profil = null) {
                    if (profil) {
                        this.editMode = true;
                        this.form.id = profil.id;
                        this.form.no = profil.no;
                        this.form.tema = profil.tema;
                        this.form.dimensi_profil_lulusan = profil.dimensi_profil_lulusan;
                        this.form.subdmensi = profil.subdmensi;
                    } else {
                        this.editMode = false;
                        this.form.id = '';
                        this.form.no = '';
                        this.form.tema = '';
                        this.form.dimensi_profil_lulusan = '';
                        this.form.subdmensi = '';
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