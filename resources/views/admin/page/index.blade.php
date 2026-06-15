<x-app-layout>
    <x-slot name="header">
        {{ __('Pengelolaan Halaman Statis (Pages)') }}
    </x-slot>

    <div class="space-y-6">
        <div id="toast-success" class="hidden p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
            <span>✅</span> <span id="toast-message"></span>
        </div>

        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-base font-bold text-gray-900">Daftar Kustom Halaman</h3>
                <p class="text-xs text-gray-500">Kelola informasi mandiri seperti Sejarah, Visi Misi, atau Fasilitas sekolah menggunakan sistem popup modal.</p>
            </div>
            <button onclick="openModalPage('create')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors cursor-pointer">
                ➕ Buat Halaman Baru
            </button>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6 text-center w-16">Urutan</th>
                            <th class="p-4">Judul Halaman</th>
                            <th class="p-4">Tautan / Slug</th>
                            <th class="p-4 text-center">Status Tayang</th>
                            <th class="p-4 pr-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                        @forelse($pages as $p)
                            <tr id="row-page-{{ $p->id }}" class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4 pl-6 text-center font-mono text-gray-500 bg-gray-50/30">{{ $p->sort_order }}</td>
                                <td class="p-4">
                                    <p class="font-bold text-gray-900 text-sm">📄 {{ $p->title }}</p>
                                    @if($p->meta_description)
                                        <p class="text-xs text-gray-400 mt-0.5 max-w-xs truncate">{{ $p->meta_description }}</p>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-mono text-[11px] rounded border border-slate-200">
                                        /page/{{ $p->slug }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span id="badge-status-{{ $p->id }}" class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded shadow-sm {{ $p->is_published ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-gray-50 border border-gray-200 text-gray-400' }}">
                                        {{ $p->is_published ? '🟢 Publik' : '⚪ Draf' }}
                                    </span>
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openModalPage('edit', {{ $p->id }})" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-[11px] font-semibold rounded transition-colors cursor-pointer">📝 Edit</button>
                                        <button onclick="deletePage({{ $p->id }}, '{{ $p->title }}')" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-[11px] font-semibold rounded transition-colors cursor-pointer">🗑️ Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row-page">
                                <td colspan="5" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Belum ada halaman statis yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-page" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300">
            
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 id="modal-title" class="text-base font-bold text-gray-900">Form Pembuatan Halaman</h3>
                    <p class="text-xs text-gray-500">Kelola rincian judul, pengurutan, beserta komponen isi teks artikel di bawah.</p>
                </div>
                <button onclick="closeModalPage()" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <form id="form-page" onsubmit="submitFormPage(event)" class="flex-1 overflow-y-auto p-6 space-y-4">
                @csrf
                <div id="method-field"></div>
                <input type="hidden" id="page-id">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Halaman *</label>
                        <input type="text" id="title" name="title" required placeholder="Contoh: Visi Misi Lembaga" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan Menu / Sort Order *</label>
                        <input type="number" id="sort_order" name="sort_order" required min="0" value="0" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kustom Tautan URL / Slug (Opsional)</label>
                    <input type="text" id="slug" name="slug" placeholder="Kosongkan jika ingin dibuat otomatis dari judul" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Isi Konten Halaman *</label>
                    <textarea id="content" name="content" rows="10" required placeholder="Tulis isi informasi halaman secara lengkap di sini..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ringkasan Singkat / Meta Description (SEO)</label>
                    <input type="text" id="meta_description" name="meta_description" placeholder="Deskripsi ringkas 1 kalimat untuk pencarian Google..." class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>

                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 flex items-center gap-3">
                    <input type="checkbox" id="is_published" name="is_published" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_published" class="text-xs font-bold text-gray-700 cursor-pointer select-none">Langsung terbitkan halaman ini ke publik website.</label>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" onclick="closeModalPage()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition-colors cursor-pointer">Batal</button>
                    <button type="submit" id="btn-submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">💾 Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modal-page');
        const form = document.getElementById('form-page');
        const methodField = document.getElementById('method-field');
        const pageIdInput = document.getElementById('page-id');

        // Buka popup modal (Mode: 'create' atau 'edit')
        async function openModalPage(mode, id = null) {
            form.reset();
            pageIdInput.value = '';
            methodField.innerHTML = '';
            
            if (mode === 'create') {
                document.getElementById('modal-title').innerText = '➕ Buat Halaman Baru';
                document.getElementById('is_published').checked = true;
                modal.classList.remove('hidden');
            } else if (mode === 'edit') {
                document.getElementById('modal-title').innerText = '📝 Edit Data Halaman';
                
                try {
                    let response = await fetch(`/admin/page/${id}/edit`);
                    let page = await response.json();
                    
                    pageIdInput.value = page.id;
                    document.getElementById('title').value = page.title;
                    document.getElementById('sort_order').value = page.sort_order;
                    document.getElementById('slug').value = page.slug;
                    document.getElementById('content').value = page.content;
                    document.getElementById('meta_description').value = page.meta_description ?? '';
                    document.getElementById('is_published').checked = !!page.is_published;

                    methodField.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
                    modal.classList.remove('hidden');
                } catch (e) {
                    alert('Gagal mengambil data halaman dari server.');
                }
            }
        }

        // Tutup popup modal
        function closeModalPage() {
            modal.classList.add('hidden');
        }

        // Kirim data via AJAX (Store / Update)
        async function submitFormPage(event) {
            event.preventDefault();
            const id = pageIdInput.value;
            const url = id ? `/admin/page/${id}` : '/admin/page';
            const formData = new FormData(form);

            try {
                let response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                let resData = await response.json();

                if(response.ok) {
                    closeModalPage();
                    showToast(resData.success);
                    setTimeout(() => { location.reload(); }, 1000);
                } else {
                    alert('Gagal menyimpan data. Pastikan seluruh inputan wajib terisi dengan benar.');
                }
            } catch (error) {
                alert('Terjadi kesalahan koneksi sistem.');
            }
        }

        // Hapus data via AJAX
        async function deletePage(id, title) {
            if (!confirm(`Apakah Anda yakin ingin menghapus halaman "${title}" secara permanen?`)) return;

            try {
                let response = await fetch(`/admin/page/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                let resData = await response.json();

                if (response.ok) {
                    showToast(resData.success);
                    document.getElementById(`row-page-${id}`).remove();
                } else {
                    alert('Gagal menghapus data.');
                }
            } catch (e) {
                alert('Terjadi kesalahan sistem.');
            }
        }

        // Memunculkan banner toast animasi
        function showToast(message) {
            const toast = document.getElementById('toast-success');
            document.getElementById('toast-message').innerText = message;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 3000);
        }
    </script>
</x-app-layout>