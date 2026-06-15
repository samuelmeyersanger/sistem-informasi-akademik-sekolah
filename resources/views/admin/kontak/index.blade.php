<x-app-layout>
    <x-slot name="header">
        {{ __('Pesan Masuk (Kontak)') }}
    </x-slot>

    <div class="space-y-6">
        <div id="toast-success" class="hidden p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
            <span>✅</span> <span id="toast-message"></span>
        </div>

        <div>
            <h3 class="text-base font-bold text-gray-900">Daftar Hubungi Kami</h3>
            <p class="text-xs text-gray-500">Berikut adalah daftar aspirasi, pertanyaan, atau pesan dari masyarakat dan wali murid yang masuk melalui website.</p>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100/70 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                            <th class="p-4 pl-6">Pengirim / Tanggal</th>
                            <th class="p-4">Subjek / Perihal</th>
                            <th class="p-4">Potongan Pesan</th>
                            <th class="p-4 pr-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                        @forelse($kontaks as $k)
                            <tr id="row-kontak-{{ $k->id }}" class="hover:bg-gray-50/80 transition-colors">
                                <td class="p-4 pl-6">
                                    <p class="font-bold text-gray-900 text-sm">👤 {{ $k->nama }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $k->email }}</p>
                                    <p class="text-[10px] text-indigo-500 font-medium mt-1 font-mono">⏱️ {{ $k->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold rounded border border-slate-200 text-[11px]">
                                        {{ $k->subject }}
                                    </span>
                                </td>
                                <td class="p-4 max-w-xs truncate text-gray-500 italic">
                                    "{{ Str::limit($k->pesan, 60, '...') }}"
                                </td>
                                <td class="p-4 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="viewPesan({{ $k->id }})" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-[11px] font-semibold rounded transition-colors cursor-pointer">
                                            👁️ Baca
                                        </button>
                                        <button onclick="deletePesan({{ $k->id }}, '{{ $k->nama }}')" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-[11px] font-semibold rounded transition-colors cursor-pointer">
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row-kontak">
                                <td colspan="4" class="p-8 text-center text-gray-400 italic bg-gray-50/30">Belum ada pesan masuk dari hubungi kami.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modal-kontak" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade-in">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl w-full max-w-2xl overflow-hidden transform scale-95 transition-transform duration-300">
            
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">📩 Rincian Isi Pesan Masuk</h3>
                </div>
                <button onclick="closeModalKontak()" class="text-gray-400 hover:text-gray-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 border border-slate-100 rounded-xl">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Nama Pengirim</p>
                        <p id="detail-nama" class="font-bold text-gray-900 text-sm mt-0.5"></p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400">Alamat Email</p>
                        <p id="detail-email" class="font-medium text-indigo-600 mt-0.5 font-mono"></p>
                    </div>
                    <div class="col-span-2 border-t border-slate-200/60 pt-2">
                        <p class="text-[10px] uppercase font-bold text-gray-400">Subjek / Perihal</p>
                        <p id="detail-subject" class="font-semibold text-gray-800 mt-0.5"></p>
                    </div>
                    <div class="col-span-2 border-t border-slate-200/60 pt-2">
                        <p class="text-[10px] uppercase font-bold text-gray-400">Waktu Dikirim</p>
                        <p id="detail-tanggal" class="font-medium text-gray-500 mt-0.5"></p>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 mb-1.5">Isi Pesan / Aspirasi Lengkap:</p>
                    <div class="p-4 bg-white border border-gray-200 rounded-xl max-h-60 overflow-y-auto leading-relaxed text-gray-700 font-medium text-xs whitespace-pre-line" id="detail-pesan">
                        </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="button" onclick="closeModalKontak()" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Tutup Bacaan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modal-kontak');

        // Fungsi Membuka Modal Detail Pesan via Fetch AJAX
        async function viewPesan(id) {
            try {
                let response = await fetch(`/admin/kontak/${id}`);
                let data = await response.json();

                if (response.ok) {
                    // Isi komponen elemen modal dengan data dari database
                    document.getElementById('detail-nama').innerText = data.nama;
                    document.getElementById('detail-email').innerText = data.email;
                    document.getElementById('detail-subject').innerText = data.subject;
                    document.getElementById('detail-pesan').innerHTML = data.pesan;
                    document.getElementById('detail-tanggal').innerText = data.tanggal;

                    // Munculkan Modal Popup
                    modal.classList.remove('hidden');
                } else {
                    alert('Gagal mengambil detail pesan.');
                }
            } catch (e) {
                alert('Terjadi kesalahan koneksi ke server.');
            }
        }

        // Fungsi Menutup Modal
        function closeModalKontak() {
            modal.classList.add('hidden');
        }

        // Fungsi Hapus Pesan via AJAX
        async function deletePesan(id, nama) {
            if (!confirm(`Hapus permanen pesan dari "${nama}"? Tindakan ini tidak bisa dibatalkan.`)) return;

            try {
                let response = await fetch(`/admin/kontak/${id}`, {
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
                    // Hapus baris tabel yang bersangkutan langsung di HTML tanpa reload
                    document.getElementById(`row-kontak-${id}`).remove();
                } else {
                    alert('Gagal menghapus pesan.');
                }
            } catch (e) {
                alert('Terjadi kesalahan sistem.');
            }
        }

        // Animasi Toast Notifikasi
        function showToast(message) {
            const toast = document.getElementById('toast-success');
            document.getElementById('toast-message').innerText = message;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 3000);
        }
    </script>
</x-app-layout>