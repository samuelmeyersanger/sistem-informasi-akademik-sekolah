<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Backup & Restore Database') }}
        </h2>
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
    }" class="py-12 bg-slate-900/10 min-h-screen relative">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- KARTU 1: CADANGKAN DATABASE --}}
                <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-gray-100 flex flex-col justify-between space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">🟢 Cadangkan Database</h3>
                        <p class="text-xs text-gray-500 mt-1">Buat file backup `.zip` baru untuk mengamankan data yang ada di server Sail saat ini.</p>
                    </div>
                    <form action="{{ route('master.backup.create') }}" method="POST" @submit="isLoading = true">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            ➕ Buat & Simpan Backup Baru
                        </button>
                    </form>
                </div>

                {{-- KARTU 2: UPLOAD & RESTORE --}}
                <div class="bg-white p-6 shadow-sm sm:rounded-2xl border border-gray-100 flex flex-col justify-between space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">🔴 Upload & Restore</h3>
                        <p class="text-xs text-gray-500 mt-1">Punya file backup di laptop? Unggah file `.zip` tersebut untuk menimpa database saat ini.</p>
                    </div>
                    <div>
                        {{-- Tombol pemicu klik input file --}}
                        <label for="backup_file_input" class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg shadow-sm transition-all flex items-center justify-center gap-1.5 cursor-pointer text-center">
                            📤 Upload File Zip & Restore
                        </label>
                    </div>
                </div>

            </div>

            {{-- TABEL DAFTAR BACKUP --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-xs font-bold text-gray-700 uppercase">File Backup yang Tersimpan di Server</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-12 text-center">No</th>
                                <th class="p-4">Nama File</th>
                                <th class="p-4 text-center w-24">Ukuran</th>
                                <th class="p-4 text-center w-40">Tanggal</th>
                                <th class="p-4 pr-6 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($backupList as $index => $backup)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-4 pl-6 text-center text-gray-400">{{ $index + 1 }}</td>
                                    <td class="p-4 font-mono text-gray-900 break-all">{{ $backup['file_name'] }}</td>
                                    <td class="p-4 text-center text-gray-500">{{ $backup['file_size'] }}</td>
                                    <td class="p-4 text-center text-gray-500 whitespace-nowrap">{{ $backup['last_modified'] }}</td>
                                    <td class="p-4 pr-6 text-center">
                                        <a href="{{ route('master.backup.download', $backup['file_name']) }}" 
                                           class="inline-block px-2.5 py-1.5 bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 font-bold text-[11px] rounded-md shadow-sm transition-colors cursor-pointer">
                                            📥 Download
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic bg-gray-50/30">
                                        📦 Belum ada file backup di server. Klik tombol kiri atas untuk membuat baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL DIALOG CONFIRMATION --}}
        <div x-show="openUploadConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Konfirmasi Upload & Restore!</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Anda mengunggah berkas backup dari komputer. <span class="font-bold text-rose-600">Seluruh database saat ini akan dihapus dan digantikan total</span> oleh isi file yang Anda upload.
                    </p>
                </div>
                <div class="bg-gray-50 p-2.5 rounded-lg text-[10px] text-gray-600 border border-gray-200 break-all text-left">
                    <strong>File yang di-upload:</strong> <span class="font-mono text-indigo-600" x-text="fileNameToUpload"></span>
                </div>
                
                {{-- 🟢 PERBAIKAN FORM: Input File dimasukkan ke dalam form ini sejak awal --}}
                <form id="upload_restore_form" action="{{ route('master.backup.upload-restore') }}" method="POST" enctype="multipart/form-data" @submit="openUploadConfirm = false; isLoading = true;" class="flex flex-col justify-center gap-2 pt-2 m-0">
                    @csrf
                    
                    {{-- Input file ditaruh di sini agar terbaca sempurna oleh Controller --}}
                    <input type="file" id="backup_file_input" name="backup_file" accept=".zip" class="hidden" @change="handleFileChange($event)">
                    
                    <div class="flex justify-center gap-2 w-full">
                        <button type="button" @click="cancelUpload()" class="w-1/2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="w-1/2 px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">
                            Ya, Upload & Timpa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SCREEN LOADING --}}
        <div x-show="isLoading" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-md flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-xs w-full shadow-2xl p-6 text-center space-y-4">
                <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Memproses Database...</h3>
                    <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">
                        Sedang mengekstrak zip dan menyelaraskan struktur data PostgreSQL Sail Anda. Mohon tunggu sejenak...
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>