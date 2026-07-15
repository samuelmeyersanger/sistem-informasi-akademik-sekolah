<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">📄</span> {{ __('Manajemen Halaman Statis') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-color: #e2e8f0;
            background-color: #f8fafc;
            padding: 12px;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            border-color: #e2e8f0;
            height: 250px !important;
            background-color: #ffffff;
        }
        .ql-editor {
            font-size: 14px !important;
            line-height: 1.6 !important;
            color: #334155 !important;
            font-family: inherit;
        }
        .ql-editor p {
            margin-bottom: 8px !important;
        }
        .ql-editor:focus {
            box-shadow: inset 0 0 0 2px rgba(99, 102, 241, 0.2);
            border-radius: 0 0 0.75rem 0.75rem;
        }
        .modal-scroll-area {
            max-height: calc(100vh - 220px);
            overflow-y: auto;
            padding: 24px;
        }
        /* Custom Scrollbar for Modal */
        .modal-scroll-area::-webkit-scrollbar { width: 6px; }
        .modal-scroll-area::-webkit-scrollbar-track { background: transparent; }
        .modal-scroll-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .modal-scroll-area::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,
        
        id: '', title: '', slug: '', content: '', meta_description: '', sort_order: 0, is_published: true,
        
        deleteTargetTitle: '',
        alertMessage: '',
        showAlert: false,
        quillCreate: null,
        quillEdit: null,

        initAlert(msg) {
            this.alertMessage = msg;
            this.showAlert = true;
            setTimeout(() => { this.showAlert = false; window.location.reload(); }, 1500);
        },

        // Inisialisasi Editor saat Modal Create dibuka
        initQuillCreate() {
            this.openCreate = true;
            this.$nextTick(() => {
                if (!this.quillCreate) {
                    this.quillCreate = new Quill('#editor-create', {
                        theme: 'snow',
                        placeholder: 'Mulai menulis konten halaman di sini...',
                        modules: {
                            toolbar: [
                                [{'header': [1, 2, 3, false]}],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{'list': 'ordered'}, {'list': 'bullet'}],
                                ['table'], 
                                ['clean']
                            ]
                        }
                    });
                }
                this.quillCreate.root.innerHTML = ''; 
            });
        },

        async loadEdit(pageId) {
            try {
                let response = await fetch(`/master/page/${pageId}/edit`);
                if (!response.ok) throw new Error('Gagal mengambil data halaman.');
                let data = await response.json();
                
                this.id = data.id;
                this.title = data.title;
                this.slug = data.slug;
                this.meta_description = data.meta_description || '';
                this.sort_order = data.sort_order;
                this.is_published = !!data.is_published;
                
                this.content = data.content || ''; 
                this.openEdit = true;

                setTimeout(() => {
                    if (!this.quillEdit) {
                        this.quillEdit = new Quill('#editor-edit', {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    [{'header': [1, 2, 3, false]}],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{'list': 'ordered'}, {'list': 'bullet'}],
                                    ['table'],
                                    ['clean']
                                ]
                            }
                        });
                    }
                    this.quillEdit.root.innerHTML = this.content;
                }, 150);

            } catch (err) {
                alert(err.message);
            }
        },

        updateSlug() {
            this.slug = this.title.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        },

        async submitCreate(e) {
            let formData = new FormData(e.target);
            formData.set('is_published', this.is_published ? '1' : '0');
            
            if(this.quillCreate) {
                formData.set('content', this.quillCreate.root.innerHTML);
            }

            let response = await fetch('{{ route('master.page.store') }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });

            let result = await response.json();
            if(response.ok) {
                this.openCreate = false;
                this.initAlert(result.success);
            } else { 
                alert(result.message || 'Terjadi kesalahan validasi.'); 
            }
        },

        async submitUpdate(e) {
            let formData = new FormData(e.target);
            formData.append('_method', 'PUT');
            formData.set('is_published', this.is_published ? '1' : '0');
            
            if(this.quillEdit) {
                formData.set('content', this.quillEdit.root.innerHTML);
            }

            let response = await fetch(`/master/page/${this.id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });

            let result = await response.json();
            if(response.ok) {
                this.openEdit = false;
                this.initAlert(result.success);
            } else { 
                alert(result.message || 'Terjadi kesalahan validasi.'); 
            }
        },

        initDelete(pageId, pageTitle) {
            this.id = pageId;
            this.deleteTargetTitle = pageTitle;
            this.openDelete = true;
        },

        async executeDelete() {
            this.openDelete = false;
            let response = await fetch(`/master/page/${this.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });

            let result = await response.json();
            if(response.ok) { 
                this.initAlert(result.success); 
            } else {
                alert('Gagal menghapus halaman permanen.');
            }
        }
    }" class="py-10 bg-slate-50/50 min-h-screen relative font-sans">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Floating Toast Alert --}}
            <div x-show="showAlert" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-[-1rem]"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-[-1rem]"
                 class="fixed top-8 left-1/2 -translate-x-1/2 z-[150] p-4 bg-white/90 backdrop-blur border-l-4 border-emerald-500 rounded-xl shadow-2xl flex items-center gap-3" 
                 style="display: none;">
                <span class="text-2xl">🎉</span>
                <div>
                    <h4 class="font-black text-slate-800 text-sm">Operasi Berhasil!</h4>
                    <p class="text-[11px] font-bold text-slate-500" x-text="alertMessage"></p>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                
                {{-- Efek Latar Belakang Tabel --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>

                <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/50 backdrop-blur-sm relative z-10">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Koleksi Halaman</h3>
                        <p class="text-xs font-medium text-slate-500 mt-1">Daftar halaman informasi independen yang dipublikasikan di situs.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                        <form action="{{ route('master.page.index') }}" method="GET" class="flex items-stretch gap-2 w-full md:w-auto">
                            <div class="relative flex items-center w-full sm:w-64 group">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul halaman..." 
                                    class="w-full text-sm font-medium border-slate-200 bg-white rounded-xl shadow-sm py-2.5 pl-4 pr-10 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder-slate-400">
                                @if(request('search'))
                                    <a href="{{ route('master.page.index') }}" class="absolute right-3 text-slate-400 hover:text-rose-500 font-bold transition-colors cursor-pointer" title="Reset Pencarian">
                                        <svg class="w-5 h-5 bg-slate-100 rounded-full p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-md shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2 justify-center">
                                <span class="hidden sm:inline">🔍</span> Cari
                            </button>
                        </form>

                        <div class="hidden md:block w-px h-8 bg-slate-200"></div>

                        <button @click="title=''; slug=''; content=''; meta_description=''; sort_order=0; is_published=true; initQuillCreate();" 
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 w-full sm:w-auto shrink-0">
                            <span>✍️</span> Rilis Halaman
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest">
                                <th class="p-5 pl-8 w-24 text-center">Urutan</th>
                                <th class="p-5 w-64">Judul Publikasi Halaman</th>
                                <th class="p-5 w-64">Tautan Slug URL</th>
                                <th class="p-5 w-36 text-center">Status Akses</th>
                                <th class="p-5 pr-8 text-center w-36">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @forelse($pages as $p)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-200 group">
                                    <td class="p-5 pl-8 text-center">
                                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 border border-slate-200 font-black text-slate-500 shadow-inner group-hover:bg-indigo-100 group-hover:text-indigo-700 group-hover:border-indigo-200 transition-colors">
                                            {{ $p->sort_order }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-black text-slate-900 text-base flex items-center gap-2">
                                            <span class="text-indigo-400">📄</span> {{ $p->title }}
                                        </div>
                                    </td>
                                    <td class="p-5">
                                        <a href="/page/{{ $p->slug }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 font-mono text-[11px] font-bold tracking-wider text-slate-500 hover:text-indigo-600 hover:border-indigo-300 bg-slate-50 border border-slate-200 rounded-lg shadow-sm transition-colors group/link" title="Buka Halaman (Tab Baru)">
                                            /page/{{ $p->slug }}
                                            <svg class="w-3 h-3 opacity-0 group-hover/link:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </td>
                                    <td class="p-5 text-center">
                                        @if($p->is_published)
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                                DIPUBLIKASI
                                            </div>
                                        @else
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                                DRAFT (SEMBUNYI)
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-5 pr-8 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click="loadEdit('{{ $p->id }}')" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Revisi Konten">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <button type="button" @click="initDelete('{{ $p->id }}', '{{ addslashes($p->title) }}')" 
                                                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 rounded-xl transition-all cursor-pointer shadow-sm hover:shadow-md" title="Hapus Halaman">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border border-slate-100">
                                                @if(request('search')) 🔍 @else 📰 @endif
                                            </div>
                                            <h4 class="font-black text-slate-700 text-lg mb-1">
                                                @if(request('search')) Pencarian Nihil @else Ruang Halaman Kosong @endif
                                            </h4>
                                            <span class="text-sm">
                                                @if(request('search'))
                                                    Tidak ditemukan judul halaman statis yang cocok.
                                                @else
                                                    Anda belum merilis halaman statis apapun di sistem ini.
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL BUAT HALAMAN --}}
        <div x-show="openCreate" class="fixed inset-0 z-[100] overflow-hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-4xl w-full flex flex-col overflow-hidden" @click.away="openCreate = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">✨</span> Desain Halaman Baru
                    </h3>
                    <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form @submit.prevent="submitCreate" class="flex flex-col overflow-hidden m-0">
                    <div class="modal-scroll-area space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Titel / Judul Utama <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" x-model="title" @keyup="updateSlug()" required placeholder="Cth: Ketentuan & Kebijakan" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                            </div>
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Identitas URL (Slug) <span class="text-rose-500">*</span></label>
                                <input type="text" name="slug" x-model="slug" required placeholder="ketentuan-kebijakan" class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 font-mono bg-slate-50 placeholder-slate-300">
                            </div>
                        </div>

                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center justify-between">
                                <span>Bodi Konten Penjelasan <span class="text-rose-500">*</span></span>
                                <span class="text-[9px] text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded uppercase">Quill Editor Active</span>
                            </label>
                            <div class="bg-white rounded-xl shadow-sm">
                                <div id="editor-create"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                            <div class="md:col-span-8">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Deskripsi SEO Singkat (Meta)</label>
                                <input type="text" name="meta_description" x-model="meta_description" placeholder="Cuplikan 1 kalimat agar mudah dicari di Google..." class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 placeholder-slate-300">
                            </div>
                            <div class="md:col-span-4 bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-col justify-center gap-3">
                                <div>
                                    <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1">Index Urutan</label>
                                    <input type="number" name="sort_order" x-model="sort_order" min="0" required class="w-full rounded-lg border-slate-200 text-sm font-bold shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <input type="checkbox" id="create_pub" x-model="is_published" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    <label for="create_pub" class="font-bold text-slate-700 text-xs cursor-pointer select-none">Tayangkan ke Publik</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3 rounded-b-[2rem]">
                        <button type="button" @click="openCreate = false" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors shadow-sm text-sm">Tutup Form</button>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 cursor-pointer transition-all hover:-translate-y-0.5 text-sm flex items-center gap-2">
                            <span>💾</span> Rilis Publikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT HALAMAN --}}
        <div x-show="openEdit" class="fixed inset-0 z-[100] overflow-hidden flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-4xl w-full flex flex-col overflow-hidden" @click.away="openEdit = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50/30 flex justify-between items-center">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="text-2xl">📝</span> Koreksi Isi Halaman
                    </h3>
                    <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form @submit.prevent="submitUpdate" class="flex flex-col overflow-hidden m-0">
                    <div class="modal-scroll-area space-y-6">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Titel / Judul Utama <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" x-model="title" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            </div>
                            <div>
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Identitas URL (Slug) <span class="text-rose-500">*</span></label>
                                <input type="text" name="slug" x-model="slug" required class="w-full rounded-xl border-slate-200 text-sm font-bold shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4 font-mono bg-slate-50">
                            </div>
                        </div>

                        <div>
                            <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2 flex items-center justify-between">
                                <span>Bodi Konten Penjelasan <span class="text-rose-500">*</span></span>
                            </label>
                            <div class="bg-white rounded-xl shadow-sm">
                                <div id="editor-edit"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                            <div class="md:col-span-8">
                                <label class="block font-black text-slate-700 text-xs uppercase tracking-widest mb-2">Deskripsi SEO Singkat (Meta)</label>
                                <input type="text" name="meta_description" x-model="meta_description" class="w-full rounded-xl border-slate-200 text-sm font-medium shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 py-3 px-4">
                            </div>
                            <div class="md:col-span-4 bg-slate-50 p-4 rounded-xl border border-slate-100 flex flex-col justify-center gap-3">
                                <div>
                                    <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1">Index Urutan</label>
                                    <input type="number" name="sort_order" x-model="sort_order" min="0" required class="w-full rounded-lg border-slate-200 text-sm font-bold shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <input type="checkbox" id="edit_pub" x-model="is_published" :checked="is_published" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    <label for="edit_pub" class="font-bold text-slate-700 text-xs cursor-pointer select-none">Tayangkan ke Publik</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3 rounded-b-[2rem]">
                        <button type="button" @click="openEdit = false" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors shadow-sm text-sm">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-black rounded-xl shadow-lg shadow-amber-500/30 cursor-pointer transition-all hover:-translate-y-0.5 text-sm flex items-center gap-2">
                            <span>🔄</span> Terapkan Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL DELETE HALAMAN --}}
        <div x-show="openDelete" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 max-w-md w-full p-8 text-center space-y-6 relative overflow-hidden" @click.away="openDelete = false">
                
                <div class="w-24 h-24 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-5xl mx-auto border border-rose-100 shadow-inner">
                    🧨
                </div>
                
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2">Hapus Halaman Ini?</h4>
                    <p class="text-sm font-medium text-slate-500 leading-relaxed px-2">
                        Menghapus halaman <strong class="text-slate-800" x-text="deleteTargetTitle"></strong> akan membuat rute link lamanya menjadi *error* (404 Not Found) jika diakses pengunjung.
                    </p>
                </div>
                
                <div class="flex justify-center gap-3 w-full pt-2">
                    <button type="button" @click="openDelete = false" class="flex-1 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl cursor-pointer transition-colors">
                        Urungkan
                    </button>
                    <button type="button" @click="executeDelete()" class="flex-1 px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-md cursor-pointer transition-colors border border-transparent flex items-center justify-center gap-2">
                        Tarik Halaman
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>