<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🗄️</span> {{ __('Backup & Restore Data') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        openUploadConfirm: false,
        isLoading: false,
        fileNameToUpload: '',

        handleFileChange(e) {
            if(e.target.files.length > 0) {
                this.fileNameToUpload = e.target.files[0].name;
                this.openUploadConfirm = true;
            }
        },
        cancelUpload() {
            document.getElementById('backup_file_input').value = '';
            this.openUploadConfirm = false;
        }
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold rounded-2xl shadow-sm flex items-center gap-3">
                    <span class="text-2xl">⚠️</span> {{ session('error') }}
                </div>
            @endif

            {{-- Kartu Aksi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- KARTU 1: CADANGKAN DATABASE --}}
                <div class="bg-white p-8 shadow-xl shadow-slate-200/40 rounded-[2rem] border border-slate-100 flex flex-col justify-between space-y-6 relative overflow-hidden group hover:border-emerald-200 transition-colors">
                    
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-emerald-50 rounded-full blur-3xl opacity-60 pointer-events-none group-hover:bg-emerald-100 transition-colors"></div>
                    
                    <div class="relative z-10 flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl shrink-0 shadow-inner border border-emerald-100">
                            📥
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">Cadangkan Database</h3>
                            <p class="text-xs font-medium text-slate-500 mt-1.5 leading-relaxed">Ekstrak seluruh data dari server menjadi sebuah file <strong class="text-emerald-600">.zip</strong> agar aman dan dapat disimpan di perangkat lokal Anda.</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('master.backup.create') }}" method="POST" @submit="isLoading = true" class="relative z-10 pt-2 border-t border-slate-50">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-lg shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2">
                            <span>📦</span> Buat Backup Server Sekarang
                        </button>
                    </form>
                </div>

                {{-- KARTU 2: UPLOAD & RESTORE --}}
                <div class="bg-white p-8 shadow-xl shadow-slate-200/40 rounded-[2rem] border border-slate-100 flex flex-col justify-between space-y-6 relative overflow-hidden group hover:border-rose-200 transition-colors">
                    
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-rose-50 rounded-full blur-3xl opacity-60 pointer-events-none group-hover:bg-rose-100 transition-colors"></div>
                    
                    <div class="relative z-10 flex items-start gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-rose-100 to-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-3xl shrink-0 shadow-inner border border-rose-100">
                            📤
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">Restore & Timpa Data</h3>
                            <p class="text-xs font-medium text-slate-500 mt-1.5 leading-relaxed">Punya file backup <strong class="text-rose-600">.zip</strong> di laptop? Unggah file tersebut untuk menimpa total *database* server saat ini.</p>
                        </div>
                    </div>
                    
                    <div class="relative z-10 pt-2 border-t border-slate-50">
                        <label for="backup_file_input" class="w-full px-6 py-3.5 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 text-center">
                            <span>⚡</span> Pilih File Zip & Restore
                        </label>
                    </div>
                </div>

            </div>

            {{-- TABEL DAFTAR BACKUP --}}
            <div class="bg-white overflow-hidden shadow-xl shadow-slate-200/40 rounded-[2rem] border border-slate-100">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <span class="p-2 bg-indigo-100 text-indigo-600 rounded-xl">☁️</span>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Arsip File Backup di Server Storage</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-6 text-center w-16">No</th>
                                <th class="p-5">Nama Berkas Archive</th>
                                <th class="p-5 text-center w-32">Kapasitas</th>
                                <th class="p-5 text-center w-48">Waktu Pembuatan</th>
                                <th class="p-5 pr-6 text-center w-32">Unduh File</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($backupList as $index => $backup)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-6 text-center text-slate-400 font-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <span class="text-2xl opacity-70">🗜️</span>
                                            <span class="font-mono text-slate-900 font-bold bg-slate-50 px-2 py-1 rounded-lg border border-slate-200 break-all">{{ $backup['file_name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="p-5 text-center">
                                        <span class="text-[11px] font-black uppercase text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">{{ $backup['file_size'] }}</span>
                                    </td>
                                    <td class="p-5 text-center text-slate-500 text-xs font-semibold whitespace-nowrap">{{ $backup['last_modified'] }}</td>
                                    <td class="p-5 pr-6 text-center">
                                        <a href="{{ route('master.backup.download', $backup['file_name']) }}" 
                                           class="inline-flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Download ZIP">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                🌪️
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">Penyimpanan Kosong</h4>
                                            <span class="text-sm">Belum ada file backup di server. Klik tombol di atas untuk membuat yang pertama.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL DIALOG CONFIRMATION UPLOAD --}}
        <div x-show="openUploadConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="cancelUpload()">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    ☢️
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight">PERINGATAN RESTORE!</h4>
                    <p class="text-sm font-medium text-slate-500 mt-2 leading-relaxed px-2">
                        Anda akan menimpa seluruh sistem. <strong class="text-rose-600">Semua data database saat ini akan dihapus permanen</strong> dan digantikan oleh file yang Anda upload.
                    </p>
                </div>
                
                <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 flex flex-col items-center gap-2">
                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Berkas Yang Akan Diekstrak</span>
                    <strong class="font-mono text-indigo-700 text-xs break-all" x-text="fileNameToUpload"></strong>
                </div>
                
                <form id="upload_restore_form" action="{{ route('master.backup.upload-restore') }}" method="POST" enctype="multipart/form-data" @submit="openUploadConfirm = false; isLoading = true;" class="flex flex-col justify-center gap-3 pt-2 m-0">
                    @csrf
                    
                    <input type="file" id="backup_file_input" name="backup_file" accept=".zip" class="hidden" @change="handleFileChange($event)">
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-3 w-full">
                        <button type="button" @click="cancelUpload()" class="w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-all cursor-pointer">
                            Batalkan Proses
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center gap-2">
                            <span>💣</span> Ya, Timpa Database
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SCREEN LOADING --}}
        <div x-show="isLoading" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/80 backdrop-blur-lg flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-sm w-full shadow-2xl p-10 text-center space-y-6">
                
                <div class="relative w-20 h-20 mx-auto">
                    {{-- Animasi Luar --}}
                    <div class="absolute inset-0 border-4 border-indigo-100 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
                    {{-- Icon Tengah --}}
                    <div class="absolute inset-0 flex items-center justify-center text-2xl animate-pulse">
                        🗄️
                    </div>
                </div>
                
                <div>
                    <h3 class="text-base font-black text-slate-900 uppercase tracking-widest">Memproses Database</h3>
                    <p class="text-xs font-medium text-slate-500 mt-2 leading-relaxed px-4">
                        Sedang mengekstrak file ZIP dan mengkonfigurasi ulang struktur PostgreSQL...<br>
                        <strong class="text-indigo-600 block mt-2">Mohon jangan tutup jendela ini!</strong>
                    </p>
                </div>
                
            </div>
        </div>

    </div>
</x-app-layout>