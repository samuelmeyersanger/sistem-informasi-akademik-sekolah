<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Unsur Pertanyaan SKM') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="unsurManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 bg-rose-50/80 backdrop-blur-md border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    <span class="font-bold text-sm">Gagal! Kode Unsur mungkin sudah digunakan.</span>
                </div>
            @endif

            <div class="bg-white/80 backdrop-blur-2xl shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60 p-8">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8 border-b border-slate-100 pb-6">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Kuesioner Indeks Kepuasan</h3>
                        <p class="text-sm text-slate-500 font-medium mt-1">Atur unsur / butir pertanyaan untuk aplikasi survei publik.</p>
                    </div>
                    <button @click="openModal()" class="inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg transition-all hover:-translate-y-0.5">
                        <i class="fa-solid fa-plus mr-2"></i> Tambah Unsur
                    </button>
                </div>

                <div class="overflow-x-auto bg-slate-50/50 rounded-2xl border border-slate-100">
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr class="text-left bg-slate-100/50 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200">
                                <th class="px-6 py-4 text-center w-24">Kode</th>
                                <th class="px-6 py-4">Isi Pertanyaan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                            @forelse ($unsurs as $unsur)
                                <tr class="hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 bg-slate-200 text-slate-700 rounded-lg text-xs font-black">{{ $unsur->kode_unsur }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal min-w-[300px]">
                                        <div class="font-medium text-slate-800">{{ $unsur->pertanyaan }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 text-[10px] uppercase tracking-wider font-black rounded-lg {{ $unsur->status ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $unsur->status ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-2">
                                        <button @click="openModal({{ $unsur }})" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                                        <form action="{{ route('skm.unsur.destroy', $unsur->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus Unsur Pertanyaan ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-colors" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">Belum ada butir pertanyaan survei.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">{{ $unsurs->links() }}</div>
            </div>
        </div>

        {{-- MODAL CRUD --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
            
            <div class="relative bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-2xl w-full max-w-xl z-10"
                 x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="p-8 border-b border-slate-100">
                    <h3 class="text-xl font-black text-slate-800" x-text="editMode ? 'Edit Pertanyaan' : 'Tambah Pertanyaan Baru'"></h3>
                </div>
                
                <form :action="editMode ? '{{ url('skm/unsur') }}/' + form.id : '{{ route('skm.unsur.store') }}'" method="POST" class="p-8">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="mb-5">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Kode Unsur (Maks 10 Huruf)</label>
                        <input type="text" name="kode_unsur" x-model="form.kode_unsur" required placeholder="Contoh: U1" maxlength="10"
                               class="w-32 px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-center text-slate-800 rounded-xl focus:border-indigo-500 shadow-sm uppercase">
                    </div>

                    <div class="mb-5">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Isi Pertanyaan</label>
                        <textarea name="pertanyaan" x-model="form.pertanyaan" required rows="3" placeholder="Contoh: Bagaimana pendapat Anda tentang kedisiplinan staf kami?"
                               class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-medium text-slate-800 rounded-xl focus:border-indigo-500 shadow-sm resize-none"></textarea>
                    </div>

                    <template x-if="editMode">
                        <div class="mb-8">
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Status</label>
                            <select name="status" x-model="form.status" required class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold rounded-xl focus:border-indigo-500 shadow-sm">
                                <option value="1">Aktif (Tampilkan)</option>
                                <option value="0">Non-Aktif (Sembunyikan)</option>
                            </select>
                        </div>
                    </template>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <button type="button" @click="closeModal()" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-800 rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg transition-all">Simpan Unsur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function unsurManager() {
            return {
                showModal: false, editMode: false,
                form: { id: '', kode_unsur: '', pertanyaan: '', status: '1' },
                openModal(data = null) {
                    if (data) {
                        this.editMode = true; this.form.id = data.id; this.form.kode_unsur = data.kode_unsur; this.form.pertanyaan = data.pertanyaan; this.form.status = data.status;
                    } else {
                        this.editMode = false; this.form.id = ''; this.form.kode_unsur = ''; this.form.pertanyaan = ''; this.form.status = '1';
                    }
                    this.showModal = true;
                },
                closeModal() { this.showModal = false; }
            }
        }
    </script>
</x-app-layout>