<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Lembar Detail & Disposisi Surat') }}
            </h2>
            <a href="{{ route('surat.masuk.index') }}" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                ⬅️ Kembali ke Agenda
            </a>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-100 min-h-[calc(100vh-64px)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="p-4 mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <div class="lg:col-span-7 space-y-4">
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 space-y-3">
                        <div class="flex justify-between items-start border-b pb-3">
                            <div>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded border bg-indigo-50 text-indigo-700 border-indigo-200">
                                    {{ $surat->sifat }}
                                </span>
                                <h3 class="text-sm font-bold text-gray-900 mt-1.5">{{ $surat->perihal }}</h3>
                            </div>
                            <div class="text-right text-[11px] text-gray-400 font-mono">
                                <div>Diterima: {{ \Carbon\Carbon::parse($item->tanggal_terima ?? now())->format('d M Y') }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <p class="text-gray-400 font-medium text-[11px]">Asal Instansi Pengirim</p>
                                <p class="font-bold text-gray-800 mt-0.5">{{ $surat->asal_instansi }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 font-medium text-[11px]">Nomor Surat Resmi</p>
                                <p class="font-bold font-mono text-gray-800 mt-0.5">{{ $surat->nomor_surat }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 font-medium text-[11px]">Tanggal Tertera di Surat</p>
                                <p class="font-medium text-gray-700 mt-0.5">{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d F Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 font-medium text-[11px]">Petugas Penerima Agenda</p>
                                <p class="font-medium text-gray-700 mt-0.5">{{ $surat->penerima?->name ?? 'Sistem' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200 space-y-2">
                        <div class="flex justify-between items-center px-1">
                            <span class="text-xs font-bold text-gray-700 uppercase flex items-center gap-1">👁️ Lampiran Dokumen Scan</span>
                            <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank" class="text-[11px] font-bold text-indigo-600 hover:underline">Buka Jendela Baru ↗️</a>
                        </div>
                        <div class="w-full h-[550px] bg-slate-100 rounded-lg overflow-hidden border border-gray-200">
                            <iframe src="{{ asset('storage/' . $surat->file_surat) }}" class="w-full h-full" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-6">
                    
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 space-y-4">
                        <div class="border-b pb-2">
                            <h3 class="text-xs font-bold text-gray-900 uppercase flex items-center gap-1">
                                📝 Tulis Instruksi Disposisi Baru
                            </h3>
                            <p class="text-[11px] text-gray-400 mt-0.5">Berikan perintah atau delegasikan tugas surat ini ke guru/staf.</p>
                        </div>

                        <form action="{{ route('surat.surat_masuk.storeDisposisi', $surat->id) }}" method="POST" class="space-y-3 text-xs">
                            @csrf
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Diteruskan Kepada (Pilih Pejabat/Guru) *</label>
                                <select name="kepada_user_id" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-xs text-gray-700">
                                    <option value="">-- Pilih Guru / Staf --</option>
                                    @foreach($daftarGuru as $guru)
                                        <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Sifat Instruksi Tugas *</label>
                                <select name="sifat_disposisi" required class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-xs text-gray-700">
                                    <option value="Biasa">Biasa</option>
                                    <option value="Segera">Segera / Penting</option>
                                    <option value="Sangat Segera">Sangat Segera / Mendesak</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Isi Catatan Instruksi Pimpinan *</label>
                                <textarea name="catatan_instruksi" rows="4" required placeholder="Contoh: Tolong wakili saya menghadiri rapat koordinasi ini dan laporkan hasilnya." class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 text-xs"></textarea>
                            </div>

                            <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm transition-colors cursor-pointer text-center">
                                🚀 Kirim Lembar Disposisi Digital
                            </button>
                        </form>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 space-y-4">
                        <div class="border-b pb-1">
                            <h3 class="text-xs font-bold text-gray-900 uppercase flex items-center gap-1">
                                📊 Alur & Jejak Disposisi Surat
                            </h3>
                        </div>

                        <div class="space-y-4 relative before:absolute before:inset-y-0 before:left-3.5 before:w-0.5 before:bg-slate-200">
                            @forelse($surat->disposisi as $log)
                                <div class="relative flex items-start gap-4 text-xs">
                                    <div class="w-7 h-7 rounded-full bg-indigo-50 border-2 border-indigo-400 flex items-center justify-center font-bold text-indigo-600 text-[10px] z-10 shrink-0 uppercase">
                                        {{ substr($log->pengirim?->name ?? 'A', 0, 1) }}
                                    </div>
                                    
                                    <div class="flex-1 bg-slate-50 border p-3 rounded-lg space-y-1.5">
                                        <div class="flex justify-between items-center border-b pb-1 border-slate-200/60">
                                            <div>
                                                <span class="font-bold text-gray-900">{{ $log->pengirim?->name }}</span>
                                                <span class="text-[10px] text-gray-400"> ke </span>
                                                <span class="font-bold text-gray-700">{{ $log->penerimaTugas?->name }}</span>
                                            </div>
                                            <span class="px-1.5 py-0.5 text-[9px] font-bold rounded uppercase
                                                {{ $log->sifat_disposisi == 'Sangat Segera' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }}
                                            ">
                                                {{ $log->sifat_disposisi }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 italic text-[11px]">"{{ $log->catatan_instruksi }}"</p>
                                        
                                        <div class="flex justify-between items-center text-[10px] text-gray-400 pt-1 font-mono">
                                            <span>{{ $log->created_at->format('d/m/Y H:i') }} WIB</span>
                                            <span class="px-1.5 py-0.5 font-bold rounded text-[9px]
                                                {{ $log->status == 'Selasai' || $log->status == 'Selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}
                                            ">
                                                {{ $log->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center p-4 italic text-gray-400 text-xs">
                                    📭 Belum ada disposisi yang dikeluarkan untuk surat ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>