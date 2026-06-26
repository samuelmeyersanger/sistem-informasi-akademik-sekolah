<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Jadwal Pelajaran
        </h2>
    </x-slot>

    <div x-data="{
        openCreate: false,
        openDelete: false,

        // Form States Hapus
        deleteActionUrl: '',
        deleteTargetName: '',

        initDelete(actionUrl, itemName) {
            this.deleteActionUrl = actionUrl;
            this.deleteTargetName = itemName;
            this.openDelete = true;
        }
    }" class="py-12 bg-slate-900/10 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl shadow-sm flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($errors->has('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl shadow-sm flex items-start gap-2">
                    <span class="mt-0.5">⚠️</span>
                    <div>
                        <span class="font-bold">Gagal Menyimpan:</span> {{ $errors->first('error') }}
                    </div>
                </div>
            @endif

            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('akademik.jadwal-pelajaran.index') }}" class="w-full md:w-auto flex items-center gap-2">
                    <select name="kelas_id" onchange="this.form.submit()" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm min-w-[240px]">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($daftarKelas as $k)
                            <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @if($kelasId)
                        <a href="{{ route('akademik.jadwal-pelajaran.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs text-center rounded-lg font-medium transition-colors">Reset</a>
                    @endif
                </form>

                <button @click="openCreate = true" class="w-full md:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                    ➕ Pasang Jadwal Baru
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4 pl-6 w-32">Hari</th>
                                <th class="p-4 text-center w-24">Jam Ke</th>
                                <th class="p-4 text-center w-36">Waktu KBM</th>
                                @if(!$kelasId)
                                    <th class="p-4 w-28">Kelas</th>
                                @endif
                                <th class="p-4">Mata Pelajaran / Guru</th>
                                <th class="p-4 w-44">Ruangan</th>
                                <th class="p-4 pr-6 text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                            @forelse($jadwal as $j)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-4 pl-6">
                                        <span class="font-bold text-gray-900">{{ $j->waktuKbm->hari }}</span>
                                    </td>
                                    <td class="p-4 text-center font-mono font-bold text-indigo-600 text-sm">
                                        {{ $j->waktuKbm->jam_ke }}
                                    </td>
                                    <td class="p-4 text-center font-medium text-gray-500">
                                        {{ date('H:i', strtotime($j->waktuKbm->jam_mulai)) }} 
                                        <span class="mx-1 text-gray-300">-</span> 
                                        {{ date('H:i', strtotime($j->waktuKbm->jam_selesai)) }}
                                    </td>
                                    @if(!$kelasId)
                                        <td class="p-4">
                                            <span class="px-2 py-0.5 font-bold text-indigo-700 bg-indigo-50 rounded border border-indigo-100">{{ $j->kelas->nama_kelas }}</span>
                                        </td>
                                    @endif
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900 text-sm">{{ $j->kodeGuru->mataPelajaran->nama_mapel ?? '-' }}</div>
                                        <div class="text-gray-400 mt-0.5">Code: <span class="font-semibold text-gray-600">{{ $j->kodeGuru->kode }}</span> | {{ $j->kodeGuru->pegawai->nama_lengkap ?? '-' }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1.5 text-gray-800 font-medium">
                                            <span class="text-rose-500">📍</span>
                                            {{ $j->ruangan->nama_ruangan ?? 'Kelas Masing-masing' }}
                                        </div>
                                    </td>
                                    <td class="p-4 pr-6 text-center">
                                        <button type="button" @click="initDelete('{{ route('akademik.jadwal-pelajaran.destroy', $j->id) }}', '{{ $j->waktuKbm->hari }} Jam Ke-{{ $j->waktuKbm->jam_ke }} ({{ $j->kelas->nama_kelas }})')" class="text-rose-600 hover:underline font-semibold cursor-pointer">
                                            🗑️ Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $kelasId ? 6 : 7 }}" class="p-12 text-center text-gray-400 italic bg-gray-50/20">
                                        Tidak ada jadwal pelajaran ditemukan. Silakan pilih spesifik kelas atau pasang jadwal baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-100 p-6 space-y-4" @click.away="openCreate = false">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">📅 Pasang Jadwal Pelajaran</h3>
                    <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('akademik.jadwal-pelajaran.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">1. Pilih Kelas *</label>
                        <select name="kelas_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Kelas Target --</option>
                            @foreach($daftarKelas as $kelas)
                                <option value="{{ $kelas->id }}" {{ old('kelas_id', $kelasId) == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">2. Hari & Slot Waktu *</label>
                        <select name="waktu_kbm_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Hari / Jam Ke --</option>
                            @foreach($daftarWaktu as $waktu)
                                <option value="{{ $waktu->id }}" {{ old('waktu_kbm_id') == $waktu->id ? 'selected' : '' }}>
                                    {{ $waktu->hari }} — Jam Ke-{{ $waktu->jam_ke }} ({{ date('H:i', strtotime($waktu->jam_mulai)) }}-{{ date('H:i', strtotime($waktu->jam_selesai)) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">3. Guru & Mata Pelajaran *</label>
                        <select name="kode_guru_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Guru Pemangku --</option>
                            @foreach($daftarKodeGuru as $kg)
                                <option value="{{ $kg->id }}" {{ old('kode_guru_id') == $kg->id ? 'selected' : '' }}>
                                    [{{ $kg->kode }}] {{ $kg->mataPelajaran->nama_mapel ?? '-' }} — {{ $kg->pegawai->nama_lengkap ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">4. Ruangan Belajar <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <select name="ruangan_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">Gunakan Kelas Masing-masing</option>
                            @foreach($daftarRuangan as $ruang)
                                <option value="{{ $ruang->id }}" {{ old('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                    {{ $ruang->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 bg-indigo-50/50 rounded-xl border border-indigo-100 text-[11px] text-indigo-800 leading-relaxed">
                        ℹ️ <strong>Informasi:</strong> Sistem otomatis melakukan pencegahan bentrok jadwal untuk guru, ruangan, dan kelas di slot waktu yang sama.
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                        <button type="button" @click="openCreate = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors cursor-pointer">Pasang Jadwal</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 max-w-sm w-full p-6 text-center space-y-4" @click.away="openDelete = false">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl mx-auto border border-rose-100">⚠️</div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900">Hapus Slot Jadwal?</h4>
                    <p class="text-xs text-gray-500 mt-1">
                        Anda akan menghapus jadwal pada <span class="font-bold text-gray-800" x-text="deleteTargetName"></span>. Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <form :action="deleteActionUrl" method="POST" class="flex justify-center gap-2 pt-2 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-lg text-xs cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg text-xs cursor-pointer shadow-sm">Ya, Hapus</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>