<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Halaman Statis (Pages)') }}
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        openDelete: false,
        
        id: '', title: '', slug: '', content: '', meta_description: '', sort_order: 0, is_published: true,
        
        deleteTargetTitle: '',
        alertMessage: '',
        showAlert: false,

        initAlert(msg) {
            this.alertMessage = msg;
            this.showAlert = true;
            setTimeout(() => { this.showAlert = false; window.location.reload(); }, 1500);
        },

        async loadEdit(pageId) {
            try {
                let response = await fetch(`/admin/page/${pageId}/edit`);
                if (!response.ok) throw new Error('Gagal mengambil data halaman.');
                let data = await response.json();
                
                this.id = data.id;
                this.title = data.title;
                this.slug = data.slug;
                this.content = data.content;
                this.meta_description = data.meta_description || '';
                this.sort_order = data.sort_order;
                this.is_published = !!data.is_published;
                this.openEdit = true;
            } catch (err) {
                alert(err.message);
            }
        },

        async submitCreate(e) {
            let formData = new FormData(e.target);
            formData.set('is_published', this.is_published ? '1' : '0');

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

            let response = await fetch(`/admin/page/${this.id}`, {
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
            let response = await fetch(`/admin/page/${this.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });

            let result = await response.json();
            if(response.ok) { 
                this.initAlert(result.success); 
            } else {
                alert('Gagal menghapus halaman.');
            }
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div x-show="showAlert" x-transition class="p-4 text-xs font-medium text-emerald-700 bg-emerald-50 rounded-xl border border-emerald-200" style="display: none;">
                🎉 <span x-text="alertMessage"></span>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Halaman Statis</h3>
                        <p class="text-xs text-gray-500">Kelola halaman struktural seperti Aturan Layanan, Tentang Kami, atau Kebijakan Privasi.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <form action="{{ route('master.page.index') }}" method="GET" class="flex items-center gap-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari halaman..." class="text-xs rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm w-40">
                            <button type="submit" class="px-2.5 py-2 bg-gray-800 hover:bg-gray-700 text-white text-xs font-medium rounded-lg cursor-pointer">Cari</button>
                        </form>

                        <button @click="openCreate = true; title=''; slug=''; content=''; meta_description=''; sort_order=0; is_published=true;" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm cursor-pointer whitespace-nowrap">
                            ➕ Buat Halaman
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100/70 text-xs font-bold text-gray-600 uppercase border-b border-gray-200">
                                <th class="p-4 w-20 text-center">Urutan</th>
                                <th class="p-4">Judul Halaman</th>
                                <th class="p-4">Slug URL Path</th>
                                <th class="p-4 w-32 text-center">Status</th>
                                <th class="p-4 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($pages as $p)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="p-4 text-center font-mono font-bold text-indigo-600 bg-gray-50/40">{{ $p->sort_order }}</td>
                                    <td class="p-4 font-bold text-gray-900 text-sm">{{ $p->title }}</td>
                                    <td class="p-4 font-mono text-gray-500 text-[11px]">/page/{{ $p->slug }}</td>
                                    <td class="p-4 text-center">
                                        @if($p->is_published)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">PUBLISHED</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400">DRAFT</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="loadEdit('{{ $p->id }}')" class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded shadow-sm cursor-pointer">✏️ Edit</button>
                                            <button @click="initDelete('{{ $p->id }}', '{{ addslashes($p->title) }}')" class="px-2 py-1 bg-rose-500 hover:bg-rose-600 text-white font-semibold rounded shadow-sm cursor-pointer">🗑️ Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400 italic">Belum ada data halaman statis yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-2xl w-full overflow-hidden" @click.away="openCreate = false">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900">Buat Halaman Baru</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>
                <form @submit.prevent="submitCreate" class="p-6 space-y-4 text-xs max-h-[85vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Judul Halaman <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required placeholder="Contoh: Tentang Kami" class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Kustom Slug (Opsional)</label>
                            <input type="text" name="slug" placeholder="tentang-kami-kustom" class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Isi Konten Halaman <span class="text-rose-500">*</span></label>
                        <textarea name="content" required rows="8" placeholder="Tulis konten HTML atau teks lengkap halaman disini..." class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Meta Deskripsi (SEO)</label>
                        <input type="text" name="meta_description" placeholder="Deskripsi ringkas untuk Google search..." class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4 items-center bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Nomor Urutan Urut Menu</label>
                            <input type="number" name="sort_order" x-model="sort_order" min="0" required class="w-32 rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex items-center gap-2 pt-4">
                            <input type="checkbox" id="create_pub" x-model="is_published" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="create_pub" class="font-semibold text-gray-700 cursor-pointer">Publikasikan Langsung</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg">Simpan Halaman</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-2xl w-full overflow-hidden" @click.away="openEdit = false">
                <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900">Perbarui Data Halaman</h3>
                    <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
                </div>
                <form @submit.prevent="submitUpdate" class="p-6 space-y-4 text-xs max-h-[85vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Judul Halaman <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" x-model="title" required class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Slug URL Path <span class="text-rose-500">*</span></label>
                            <input type="text" name="slug" x-model="slug" required class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Isi Konten Halaman <span class="text-rose-500">*</span></label>
                        <textarea name="content" x-model="content" required rows="8" class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Meta Deskripsi (SEO)</label>
                        <input type="text" name="meta_description" x-model="meta_description" class="w-full rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4 items-center bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Nomor Urutan Urut Menu</label>
                            <input type="number" name="sort_order" x-model="sort_order" min="0" required class="w-32 rounded-lg border-gray-200 text-xs shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex items-center gap-2 pt-4">
                            <input type="checkbox" id="edit_pub" x-model="is_published" :checked="is_published" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="edit_pub" class="font-semibold text-gray-700 cursor-pointer">Halaman Aktif (Publish)</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg">Perbarui Halaman</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">
                    ⚠️
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Halaman Permanen?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Apakah Anda yakin ingin menghapus halaman <span class="font-bold text-gray-800" x-text="deleteTargetTitle"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="flex justify-center gap-2 pt-2">
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer border border-transparent">
                        Batal
                    </button>
                    <button type="button" @click="executeDelete()" class="px-4 py-2 !bg-rose-600 hover:!bg-rose-700 !text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm border border-transparent">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>