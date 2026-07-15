<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-3xl">🧠</span> {{ __('Instrumen Gaya Belajar (VAK)') }}
        </h2>
    </x-slot>

    <!-- x-data DIPINDAH KE ATAS SINI AGAR SEMUA TAB BISA PAKAI MODAL YANG SAMA -->
    <div class="py-12 bg-slate-50 min-h-screen" 
         x-data="{ 
            activeTab: 'hasil',
            modalTambah: false, 
            modalEdit: false, 
            editData: {},
            
            // Variabel untuk Popup Hapus Modern
            modalHapus: false,
            hapusUrl: '',
            hapusTitle: '',
            hapusPesan: '',
            bukaModalHapus(url, title, pesan) {
                this.hapusUrl = url;
                this.hapusTitle = title;
                this.hapusPesan = pesan;
                this.modalHapus = true;
            }
         }">
         
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-emerald-700 bg-emerald-100 rounded-lg shadow-sm font-bold flex items-center gap-2">
                    <span class="text-lg">✅</span> {{ session('success') }}
                </div>
            @endif

            <!-- TABS NAVIGATION -->
            <div class="flex space-x-2 border-b border-gray-200">
                <button @click="activeTab = 'hasil'" :class="activeTab === 'hasil' ? 'bg-white border-t-2 border-indigo-500 text-indigo-700 font-bold shadow-sm' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" class="px-6 py-3 rounded-t-lg transition-colors focus:outline-none">
                    📊 Rekapitulasi Hasil Siswa
                </button>
                <button @click="activeTab = 'soal'" :class="activeTab === 'soal' ? 'bg-white border-t-2 border-amber-500 text-amber-700 font-bold shadow-sm' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'" class="px-6 py-3 rounded-t-lg transition-colors focus:outline-none flex items-center gap-2">
                    📝 Manajemen Bank Soal 
                    <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-xs">{{ $soal->count() }}</span>
                </button>
            </div>

            <!-- ========================================== -->
            <!-- TAB 1: HASIL KUESIONER SISWA -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'hasil'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white p-6 sm:p-8 rounded-b-2xl rounded-tr-2xl shadow-sm border border-gray-100">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Daftar Gaya Belajar Siswa</h3>
                    <p class="text-sm text-gray-500">Merekap otomatis dari jawaban form kuesioner.</p>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-4">No</th>
                                <th class="px-6 py-4">Nama Siswa</th>
                                <th class="px-6 py-4 text-center text-blue-600">Visual (V)</th>
                                <th class="px-6 py-4 text-center text-rose-600">Auditory (A)</th>
                                <th class="px-6 py-4 text-center text-emerald-600">Kinesthetic (K)</th>
                                <th class="px-6 py-4">Gaya Dominan</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hasil as $index => $h)
                                <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $h->siswa->nama_lengkap ?? 'Anonim' }}</td>
                                    <td class="px-6 py-4 text-center font-bold">{{ $h->skor_visual }}</td>
                                    <td class="px-6 py-4 text-center font-bold">{{ $h->skor_auditory }}</td>
                                    <td class="px-6 py-4 text-center font-bold">{{ $h->skor_kinesthetic }}</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-xs font-bold border border-indigo-200">
                                            {{ $h->gaya_dominan }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <!-- TOMBOL RESET MENGGUNAKAN POPUP MODERN -->
                                        <button @click="bukaModalHapus('{{ route('bk.gaya_belajar.destroy_hasil', $h->id) }}', 'Reset Data Siswa?', 'Apakah Anda yakin ingin menghapus data gaya belajar {{ $h->siswa->nama_lengkap ?? 'siswa ini' }}? Namanya akan muncul kembali di form dan ia harus mengisi kuesioner dari awal.')" type="button" class="text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 p-2 rounded-lg font-bold text-xs transition-colors shadow-sm border border-rose-200">
                                            🔄 Reset
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                        <span class="text-3xl block mb-2">📭</span> Belum ada siswa yang mengisi kuesioner.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: MANAJEMEN BANK SOAL -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'soal'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white p-6 sm:p-8 rounded-b-2xl rounded-tl-2xl shadow-sm border border-gray-100">
                
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Daftar Pertanyaan Kuesioner</h3>
                        <p class="text-sm text-gray-500">Soal ini yang akan dibaca & dipilih oleh siswa di halaman pengisian.</p>
                    </div>
                    <button @click="modalTambah = true" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg transition-all flex items-center gap-2">
                        <span>➕</span> Tambah Soal
                    </button>
                </div>

                <div class="space-y-4">
                    @forelse($soal as $index => $s)
                        <div class="p-5 border border-gray-200 rounded-2xl bg-gray-50 hover:bg-white hover:shadow-md transition-all">
                            <div class="flex justify-between items-start gap-4">
                                <div>
                                    <div class="font-bold text-gray-800 text-lg mb-3">
                                        <span class="text-amber-500 mr-1">{{ $index + 1 }}.</span> {{ $s->pertanyaan }}
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                        <div class="bg-blue-50 text-blue-800 p-3 rounded-xl border border-blue-100"><b class="block mb-1">A. (Visual)</b> {{ $s->opsi_visual }}</div>
                                        <div class="bg-rose-50 text-rose-800 p-3 rounded-xl border border-rose-100"><b class="block mb-1">B. (Auditory)</b> {{ $s->opsi_auditory }}</div>
                                        <div class="bg-emerald-50 text-emerald-800 p-3 rounded-xl border border-emerald-100"><b class="block mb-1">C. (Kinesthetic)</b> {{ $s->opsi_kinesthetic }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 min-w-[90px]">
                                    <button @click="editData = {{ $s }}; modalEdit = true" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-lg transition-colors text-center">Edit</button>
                                    
                                    <!-- TOMBOL HAPUS SOAL MENGGUNAKAN POPUP MODERN -->
                                    <button @click="bukaModalHapus('{{ route('bk.gaya_belajar.destroy_soal', $s->id) }}', 'Hapus Soal Kuesioner?', 'Apakah Anda yakin ingin menghapus pertanyaan ini? Pertanyaan ini akan hilang dari form siswa secara permanen.')" type="button" class="w-full px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold rounded-lg transition-colors text-center">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-gray-500">
                            <span class="text-3xl block mb-2">📭</span> Belum ada kuesioner, silakan klik Tambah Soal.
                        </div>
                    @endforelse
                </div>
            </div> <!-- End Tab 2 -->

            <!-- ========================================================================= -->
            <!-- ⚠️ POPUP MODAL HAPUS / RESET (DESAIN MODERN SEPERTI REFERENSI GAMBAR) -->
            <!-- ========================================================================= -->
            <div x-show="modalHapus" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                    <div x-show="modalHapus" @click="modalHapus = false" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" aria-hidden="true"></div>
                    
                    <div x-show="modalHapus" x-transition.scale.origin.center class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-8 text-center">
                        
                        <!-- Ikon Peringatan -->
                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 mb-6">
                            <span class="text-4xl">⚠️</span>
                        </div>
                        
                        <!-- Teks -->
                        <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-4" id="modal-title" x-text="hapusTitle"></h3>
                        <p class="text-sm text-gray-500 mb-8 px-2" x-text="hapusPesan"></p>
                        
                        <!-- Form dan Tombol -->
                        <form :action="hapusUrl" method="POST" class="flex justify-center gap-3">
                            @csrf @method('DELETE')
                            <button type="button" @click="modalHapus = false" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl transition-colors focus:outline-none">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition-colors shadow-md focus:outline-none">
                                Ya, Lanjutkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MODAL TAMBAH (Kode tetap sama) -->
            <div x-show="modalTambah" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                    <div x-show="modalTambah" @click="modalTambah = false" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
                    <div x-show="modalTambah" x-transition.scale class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <form action="{{ route('bk.gaya_belajar.store_soal') }}" method="POST">
                            @csrf
                            <div class="bg-white px-8 pt-8 pb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">➕ Tambah Pertanyaan</h3>
                                <div class="space-y-4">
                                    <div><label class="block text-sm font-bold text-gray-700 mb-1">Isi Pertanyaan</label><textarea name="pertanyaan" rows="3" required class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500"></textarea></div>
                                    <div><label class="block text-sm font-bold text-blue-700 mb-1">Opsi Visual</label><input type="text" name="opsi_visual" required class="w-full rounded-xl border-blue-200 bg-blue-50 focus:border-blue-500"></div>
                                    <div><label class="block text-sm font-bold text-rose-700 mb-1">Opsi Auditory</label><input type="text" name="opsi_auditory" required class="w-full rounded-xl border-rose-200 bg-rose-50 focus:border-rose-500"></div>
                                    <div><label class="block text-sm font-bold text-emerald-700 mb-1">Opsi Kinesthetic</label><input type="text" name="opsi_kinesthetic" required class="w-full rounded-xl border-emerald-200 bg-emerald-50 focus:border-emerald-500"></div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-8 py-5 sm:flex sm:flex-row-reverse border-t border-gray-100 gap-3">
                                <button type="submit" class="w-full inline-flex justify-center rounded-xl bg-amber-600 px-6 py-2.5 text-base font-bold text-white hover:bg-amber-700 sm:w-auto sm:text-sm">💾 Simpan Soal</button>
                                <button type="button" @click="modalTambah = false" class="mt-3 w-full inline-flex justify-center rounded-xl bg-white px-6 py-2.5 text-base font-bold text-gray-700 border border-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MODAL EDIT (Kode tetap sama) -->
            <div x-show="modalEdit" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                    <div x-show="modalEdit" @click="modalEdit = false" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
                    <div x-show="modalEdit" x-transition.scale class="relative inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <form :action="'{{ url('/bk/gaya-belajar/soal') }}/' + editData.id" method="POST">
                            @csrf @method('PUT')
                            <div class="bg-white px-8 pt-8 pb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">✏️ Edit Pertanyaan</h3>
                                <div class="space-y-4">
                                    <div><label class="block text-sm font-bold text-gray-700 mb-1">Isi Pertanyaan</label><textarea name="pertanyaan" x-model="editData.pertanyaan" rows="3" required class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500"></textarea></div>
                                    <div><label class="block text-sm font-bold text-blue-700 mb-1">Opsi Visual</label><input type="text" name="opsi_visual" x-model="editData.opsi_visual" required class="w-full rounded-xl border-blue-200 bg-blue-50 focus:border-blue-500"></div>
                                    <div><label class="block text-sm font-bold text-rose-700 mb-1">Opsi Auditory</label><input type="text" name="opsi_auditory" x-model="editData.opsi_auditory" required class="w-full rounded-xl border-rose-200 bg-rose-50 focus:border-rose-500"></div>
                                    <div><label class="block text-sm font-bold text-emerald-700 mb-1">Opsi Kinesthetic</label><input type="text" name="opsi_kinesthetic" x-model="editData.opsi_kinesthetic" required class="w-full rounded-xl border-emerald-200 bg-emerald-50 focus:border-emerald-500"></div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-8 py-5 sm:flex sm:flex-row-reverse border-t border-gray-100 gap-3">
                                <button type="submit" class="w-full inline-flex justify-center rounded-xl bg-gray-900 px-6 py-2.5 text-base font-bold text-white hover:bg-black sm:w-auto sm:text-sm">💾 Perbarui</button>
                                <button type="button" @click="modalEdit = false" class="mt-3 w-full inline-flex justify-center rounded-xl bg-white px-6 py-2.5 text-base font-bold text-gray-700 border border-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>