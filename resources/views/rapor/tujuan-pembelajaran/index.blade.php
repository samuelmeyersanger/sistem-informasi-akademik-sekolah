<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Tujuan Pembelajaran') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="tpManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white/80 backdrop-blur-2xl overflow-hidden shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60">
                <div class="p-8 lg:p-10">
                    
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Daftar Tujuan Pembelajaran (TP)</h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Data master capaian TP yang akan digunakan saat input KKTP & Sumatif.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <form action="{{ route('rapor.tujuan-pembelajaran.index') }}" method="GET" class="relative w-full sm:w-64">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi TP..." 
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50/50 border-slate-200 text-sm font-medium rounded-2xl focus:border-indigo-500 focus:ring-indigo-500 shadow-inner">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                </div>
                            </form>

                            <button @click="openModal()" 
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 transition-all">
                                <i class="fa-solid fa-plus mr-2"></i> Tambah TP
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-slate-50/50 rounded-2xl border border-slate-100">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                                <tr class="text-left bg-slate-100/50 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200">
                                    <th class="px-6 py-4 rounded-tl-2xl">Mata Pelajaran & Tingkat</th>
                                    <th class="px-6 py-4 text-center">No TP</th>
                                    <th class="px-6 py-4">Deskripsi Tujuan Pembelajaran</th>
                                    <th class="px-6 py-4 text-center rounded-tr-2xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                @forelse ($tujuanPembelajarans as $tp)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-800">{{ $tp->mataPelajaran ? $tp->mataPelajaran->nama_mapel : 'Mapel Tidak Ditemukan' }}</div>
                                            <span class="inline-block mt-1 px-2 py-0.5 bg-slate-200 text-slate-700 rounded-md text-[10px] font-bold">
                                                Tingkat {{ $tp->tingkat }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center text-indigo-600 font-black text-lg">
                                            TP {{ $tp->nomor_tujuan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-normal min-w-[250px] text-slate-600">
                                            {{ $tp->deskripsi_tujuan ?? $tp->deskripsi }}
                                        </td>
                                        <td class="px-6 py-4 text-center space-x-2">
                                            <button @click="openModal({{ $tp }})" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                                            
                                            <form action="{{ route('rapor.tujuan-pembelajaran.destroy', $tp->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus Tujuan Pembelajaran ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-colors"><i class="fa-solid fa-trash-can"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">
                                            <i class="fa-solid fa-file-circle-question text-4xl mb-3 text-slate-300 block"></i>
                                            Belum ada data Tujuan Pembelajaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">{{ $tujuanPembelajarans->links() }}</div>
                </div>
            </div>
        </div>

        {{-- Modal Alpine --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeModal()"></div>
            
            <div class="relative bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-2xl overflow-hidden w-full max-w-2xl z-10"
                 x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="p-8 border-b border-slate-100"><h3 class="text-xl font-black text-slate-800" x-text="editMode ? 'Edit TP' : 'Tambah TP Baru'"></h3></div>
                
                <form :action="editMode ? '{{ url('rapor/tujuan-pembelajaran') }}/' + form.id : '{{ route('rapor.tujuan-pembelajaran.store') }}'" method="POST" class="p-8">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" x-model="form.mata_pelajaran_id" required
                                   class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-slate-800 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">Pilih Mapel...</option>
                                @foreach ($mapels ?? [] as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Tingkat</label>
                                <select name="tingkat" x-model="form.tingkat" required class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                    <option value="7">Kls 7</option>
                                    <option value="8">Kls 8</option>
                                    <option value="9">Kls 9</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">No TP</label>
                                <input type="number" name="nomor_tujuan" x-model="form.nomor_tujuan" required class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold rounded-xl text-center focus:border-indigo-500 shadow-sm" placeholder="1">
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Deskripsi Tujuan Pembelajaran</label>
                        <textarea name="deskripsi_tujuan" x-model="form.deskripsi_tujuan" required rows="3" class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-medium rounded-xl focus:border-indigo-500 shadow-sm resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <button type="button" @click="closeModal()" class="px-6 py-3 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg transition-all">Simpan TP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function tpManager() {
            return {
                showModal: false, editMode: false,
                form: { id: '', mata_pelajaran_id: '', tingkat: '7', nomor_tujuan: '', deskripsi_tujuan: '' },
                openModal(tp = null) {
                    if (tp) {
                        this.editMode = true;
                        this.form.id = tp.id;
                        this.form.mata_pelajaran_id = tp.mata_pelajaran_id;
                        this.form.tingkat = tp.tingkat;
                        this.form.nomor_tujuan = tp.nomor_tujuan;
                        this.form.deskripsi_tujuan = tp.deskripsi_tujuan || tp.deskripsi;
                    } else {
                        this.editMode = false;
                        this.form.id = ''; this.form.mata_pelajaran_id = ''; this.form.tingkat = '7'; this.form.nomor_tujuan = ''; this.form.deskripsi_tujuan = '';
                    }
                    this.showModal = true;
                },
                closeModal() { this.showModal = false; }
            }
        }
    </script>
</x-app-layout>