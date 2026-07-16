<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 tracking-tight leading-tight uppercase">
            {{ __('Kegiatan Kokurikuler') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="kegiatanManager()">
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
                    <span class="font-bold text-sm">{{ session('error') ?? 'Terjadi kesalahan saat menyimpan data.' }}</span>
                </div>
            @endif

            <div class="bg-white/80 backdrop-blur-2xl overflow-hidden shadow-2xl shadow-indigo-500/10 rounded-[2.5rem] border border-white/60">
                <div class="p-8 lg:p-10">
                    
                    {{-- Header & Pencarian --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Daftar Kegiatan P5</h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Kelola data kegiatan dan tautkan dengan Profil Lulusan yang sesuai.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <form action="{{ route('rapor.kegiatan-kokurikuler.index') }}" method="GET" class="relative w-full sm:w-64">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kegiatan..." 
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50/50 border-slate-200 text-sm font-medium rounded-2xl focus:border-indigo-500 focus:ring-indigo-500 transition-colors shadow-inner">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                </div>
                            </form>

                            <button @click="openModal()" 
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all">
                                <i class="fa-solid fa-plus mr-2"></i> Tambah Kegiatan
                            </button>
                        </div>
                    </div>

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto bg-slate-50/50 rounded-2xl border border-slate-100">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                                <tr class="text-left bg-slate-100/50 text-[10px] uppercase tracking-widest text-slate-500 font-bold border-b border-slate-200">
                                    <th class="px-6 py-4 rounded-tl-2xl">No Urut</th>
                                    <th class="px-6 py-4">Tingkat Kelas</th>
                                    <th class="px-6 py-4">Nama Kegiatan</th>
                                    <th class="px-6 py-4">Profil Lulusan Ditautkan</th>
                                    <th class="px-6 py-4 text-center rounded-tr-2xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                @forelse ($kegiatans as $kegiatan)
                                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                                        <td class="px-6 py-4 text-center text-slate-400 font-bold">{{ $kegiatan->no_urut }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center justify-center px-3 py-1 bg-slate-800 text-white text-xs font-black rounded-lg">
                                                Tingkat {{ $kegiatan->tingkat }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-normal min-w-[200px]">
                                            <div class="font-bold text-slate-800">{{ $kegiatan->nama_kegiatan }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-normal min-w-[250px]">
                                            @if($kegiatan->profilLulusan)
                                                <div class="text-[11px] font-bold text-indigo-700 bg-indigo-100/50 p-2 rounded-lg border border-indigo-200">
                                                    {{ $kegiatan->profilLulusan->dimensi_profil_lulusan }}
                                                </div>
                                            @else
                                                <span class="text-xs text-rose-500 font-bold italic flex items-center gap-1">
                                                    <i class="fa-solid fa-circle-exclamation"></i> Belum ditautkan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center space-x-2">
                                            {{-- Tombol Tautkan Profil --}}
                                            <button @click="openAssignModal({{ $kegiatan }})" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors"
                                                    title="Tautkan Profil Lulusan">
                                                <i class="fa-solid fa-link"></i>
                                            </button>
                                            
                                            {{-- Tombol Edit --}}
                                            <button @click="openModal({{ $kegiatan }})" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors"
                                                    title="Edit Kegiatan">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            
                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('rapor.kegiatan-kokurikuler.destroy', $kegiatan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus kegiatan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-colors"
                                                        title="Hapus">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">
                                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-300 block"></i>
                                            Belum ada data Kegiatan P5.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $kegiatans->links() }}
                    </div>

                </div>
            </div>
        </div>

        {{-- MODAL 1: Tambah / Edit Kegiatan --}}
        <div x-show="showModal" 
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

            <div class="relative bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-2xl overflow-hidden w-full max-w-lg z-10"
                 x-show="showModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                
                <div class="p-8 border-b border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 tracking-tight" x-text="editMode ? 'Edit Kegiatan' : 'Tambah Kegiatan Baru'"></h3>
                </div>
                
                <form :action="editMode ? '{{ url('rapor/kegiatan-kokurikuler') }}/' + form.id : '{{ route('rapor.kegiatan-kokurikuler.store') }}'" method="POST" class="p-8">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">No Urut</label>
                            <input type="number" name="no_urut" x-model="form.no_urut" required
                                   class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-slate-800 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-center">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Tingkat Kelas</label>
                            <select name="tingkat" x-model="form.tingkat" required
                                   class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-bold text-slate-800 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                                <option value="7">Tingkat 7</option>
                                <option value="8">Tingkat 8</option>
                                <option value="9">Tingkat 9</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Kegiatan</label>
                        <textarea name="nama_kegiatan" x-model="form.nama_kegiatan" required rows="3"
                               class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-medium text-slate-800 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm resize-none"></textarea>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <button type="button" @click="closeModal()" class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-800 rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg transition-all">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL 2: Assign Profil Lulusan --}}
        <div x-show="showAssignModal" 
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeAssignModal()"></div>

            <div class="relative bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-2xl overflow-hidden w-full max-w-lg z-10"
                 x-show="showAssignModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                <div class="p-8 border-b border-slate-100 bg-indigo-50/50">
                    <h3 class="text-xl font-black text-indigo-900 tracking-tight">Tautkan Profil Lulusan</h3>
                    <p class="text-xs font-medium text-indigo-600 mt-1">Kegiatan: <span class="font-bold" x-text="assignForm.nama_kegiatan"></span></p>
                </div>
                
                <form :action="'{{ url('rapor/kegiatan-kokurikuler') }}/' + assignForm.id + '/assign-profil'" method="POST" class="p-8">
                    @csrf
                    
                    <div class="mb-8">
                        <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">Pilih Profil Lulusan (P5)</label>
                        <select name="profil_lulusan_id" x-model="assignForm.profil_lulusan_id" required
                               class="w-full px-4 py-3 bg-slate-50 border-slate-200 text-sm font-medium text-slate-800 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Profil Lulusan --</option>
                            @foreach ($profils as $profil)
                                <option value="{{ $profil->id }}">
                                    [{{ $profil->tema }}] - {{ $profil->dimensi_profil_lulusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <button type="button" @click="closeAssignModal()" class="w-full sm:w-auto px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-800 rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-[11px] uppercase tracking-widest rounded-xl shadow-lg transition-all"><i class="fa-solid fa-link mr-2"></i> Simpan Tautan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function kegiatanManager() {
            return {
                showModal: false,
                showAssignModal: false,
                editMode: false,
                form: { id: '', no_urut: '', tingkat: '7', nama_kegiatan: '' },
                assignForm: { id: '', nama_kegiatan: '', profil_lulusan_id: '' },
                
                openModal(data = null) {
                    if (data) {
                        this.editMode = true;
                        this.form.id = data.id;
                        this.form.no_urut = data.no_urut;
                        this.form.tingkat = data.tingkat;
                        this.form.nama_kegiatan = data.nama_kegiatan;
                    } else {
                        this.editMode = false;
                        this.form.id = '';
                        this.form.no_urut = '';
                        this.form.tingkat = '7';
                        this.form.nama_kegiatan = '';
                    }
                    this.showModal = true;
                },
                closeModal() { this.showModal = false; },
                
                openAssignModal(data) {
                    this.assignForm.id = data.id;
                    this.assignForm.nama_kegiatan = data.nama_kegiatan;
                    this.assignForm.profil_lulusan_id = data.profil_lulusan_id || '';
                    this.showAssignModal = true;
                },
                closeAssignModal() { this.showAssignModal = false; }
            }
        }
    </script>
</x-app-layout>