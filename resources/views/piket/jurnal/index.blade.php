<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    📓 Jurnal & Kendali Pusat Guru Piket
                </h2>
                <p class="text-xs text-gray-500 mt-1">Hari Operasional: <span class="font-semibold text-indigo-600">{{ $namaHari }}, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span></p>
            </div>
            <form action="{{ route('piket.dashboard') }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </form>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100/50 min-h-screen" x-data="{ 
        tabAktif: 'izin', 
        openModalSiswa: false, 
        openModalPegawai: false,
        openModalAbsenSiswa: false,
        openModalAbsenPegawai: false
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-gradient-to-r from-slate-900 to-indigo-950 p-6 rounded-2xl text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="text-[10px] bg-indigo-500/30 text-white border border-indigo-400/30 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Jurnal Piket Harian</span>
                    <h3 class="text-white font-bold mt-2">Personel Piket Hari {{ $namaHari }}</h3>
                    <p class="text-white mt-0.5">Sistem otomatis mencocokkan jadwal penugasan harian.</p>
                </div>
                <div class="flex flex-wrap gap-4 text-xs">
                    <div class="bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/5">
                        <p class="text-white text-[10px] uppercase">Ketua / Penanggung Jawab</p>
                        <p class="font-semibold text-emerald-400 mt-0.5">{{ $petugasHariIni?->penanggungJawab?->nama_lengkap ?? '⚠️ Belum diploting' }}</p>
                    </div>
                    <div class="bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/5">
                        <p class="text-white text-[10px] uppercase">Anggota Tim Bertugas</p>
                        <div class="flex gap-1 mt-0.5 font-medium">
                            @if($petugasHariIni && count($petugasHariIni->objek_anggota_piket) > 0)
                                {{ implode(', ', $petugasHariIni->objek_anggota_piket->pluck('nama_lengkap')->toArray()) }}
                            @else
                                <span class="text-white">Tidak ada anggota</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex border-b border-gray-200 gap-2 text-sm font-medium">
                <button @click="tabAktif = 'izin'" :class="tabAktif === 'izin' ? 'border-indigo-600 text-indigo-600 border-b-2' : 'text-gray-500 hover:text-gray-700'" class="py-2 px-4 cursor-pointer transition-all">
                    🚗 Izin Keluar-Masuk
                </button>
                <button @click="tabAktif = 'absen'" :class="tabAktif === 'absen' ? 'border-indigo-600 text-indigo-600 border-b-2' : 'text-gray-500 hover:text-gray-700'" class="py-2 px-4 cursor-pointer transition-all">
                    ❌ Ketidakhadiran / Absen
                </button>
            </div>

            <div x-show="tabAktif === 'izin'" class="space-y-6" x-transition>
                <div class="flex flex-wrap gap-2 justify-end">
                    <button @click="openModalSiswa = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                        ➕ Catat Izin Keluar Siswa
                    </button>
                    <button @click="openModalPegawai = true" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                        ➕ Catat Izin Keluar Pegawai
                    </button>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-xs text-gray-700 uppercase">
                        📋 Daftar Log Izin Keluar Siswa (Hari Ini)
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-100/50 text-gray-600 font-semibold border-b border-gray-100">
                                    <th class="p-3 pl-6">Nama Siswa</th>
                                    <th class="p-3">Kelas</th>
                                    <th class="p-3 text-center">Keluar</th>
                                    <th class="p-3 text-center">Kembali</th>
                                    <th class="p-3">Alasan Keluar</th>
                                    <th class="p-3 text-center">Tanda Tangan</th>
                                    <th class="p-3 text-center">Status</th>
                                    <th class="p-3 pr-6 text-center w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse($izinSiswa as $item)
                                    <tr>
                                        <td class="p-3 pl-6 font-semibold text-gray-900">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                                        <td class="p-3">{{ $item->kelas->nama_kelas ?? '-' }}</td>
                                        <td class="p-3 text-center font-mono">{{ \Carbon\Carbon::parse($item->waktu_keluar)->format('H:i') }}</td>
                                        <td class="p-3 text-center font-mono text-emerald-600 font-semibold">
                                            {{ $item->waktu_kembali ? \Carbon\Carbon::parse($item->waktu_kembali)->format('H:i') : '--:--' }}
                                        </td>
                                        <td class="p-3 max-w-xs truncate" title="{{ $item->alasan_keluar }}">{{ $item->alasan_keluar }}</td>
                                        <td class="p-3 text-center">
                                            @if($item->tanda_tangan_siswa)
                                                <img src="{{ $item->tanda_tangan_siswa }}" class="h-8 mx-auto border bg-slate-50 rounded" alt="Sign">
                                            @else
                                                <span class="text-gray-400 italic">None</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-center">
                                            <span class="px-2 py-0.5 font-bold rounded-md text-[10px] {{ $item->status === 'Sudah Kembali' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200 animate-pulse' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="p-3 pr-6 text-center">
                                            @if($item->status === 'Belum Kembali')
                                                <form action="{{ route('piket.izin-siswa.kembali', $item->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-medium shadow-sm transition-colors cursor-pointer">
                                                        ✔️ Konfirmasi Kembali
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-8 text-center italic text-gray-400 bg-gray-50/20">Tidak ada data izin keluar siswa hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-xs text-slate-700 uppercase">
                        📋 Daftar Log Izin Keluar Pegawai / Guru (Hari Ini)
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-100/50 text-gray-600 font-semibold border-b border-gray-100">
                                    <th class="p-3 pl-6">Nama Pegawai</th>
                                    <th class="p-3">Mata Pelajaran</th>
                                    <th class="p-3 text-center">Keluar</th>
                                    <th class="p-3 text-center">Kembali</th>
                                    <th class="p-3">Alasan Keluar</th>
                                    <th class="p-3">Guru Pengganti (Invaler)</th>
                                    <th class="p-3 text-center">Tanda Tangan</th>
                                    <th class="p-3 text-center">Status</th>
                                    <th class="p-3 pr-6 text-center w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse($izinPegawai as $item)
                                    <tr>
                                        <td class="p-3 pl-6 font-semibold text-gray-900">{{ $item->pegawai->nama_lengkap ?? '-' }}</td>
                                        <td class="p-3">{{ $item->mataPelajaran->nama_mapel ?? 'Bukan Jam Mengajar' }}</td>
                                        <td class="p-3 text-center font-mono">{{ \Carbon\Carbon::parse($item->waktu_keluar)->format('H:i') }}</td>
                                        <td class="p-3 text-center font-mono text-emerald-600 font-semibold">
                                            {{ $item->waktu_kembali ? \Carbon\Carbon::parse($item->waktu_kembali)->format('H:i') : '--:--' }}
                                        </td>
                                        <td class="p-3 max-w-xs truncate" title="{{ $item->alasan_keluar }}">{{ $item->alasan_keluar }}</td>
                                        <td class="p-3 font-medium text-indigo-600">{{ $item->invaler->nama_lengkap ?? 'Tidak ada' }}</td>
                                        <td class="p-3 text-center">
                                            @if($item->tanda_tangan_pegawai)
                                                <img src="{{ $item->tanda_tangan_pegawai }}" class="h-8 mx-auto border bg-slate-50 rounded" alt="Sign">
                                            @else
                                                <span class="text-gray-400 italic">None</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-center">
                                            <span class="px-2 py-0.5 font-bold rounded-md text-[10px] {{ $item->status === 'Sudah Kembali' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200 animate-pulse' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="p-3 pr-6 text-center">
                                            @if($item->status === 'Belum Kembali')
                                                <form action="{{ route('piket.izin-pegawai.kembali', $item->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-medium shadow-sm transition-colors cursor-pointer">
                                                        ✔️ Konfirmasi Kembali
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-8 text-center italic text-gray-400 bg-gray-50/20">Tidak ada data izin keluar pegawai hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div x-show="tabAktif === 'absen'" class="space-y-6" x-transition style="display: none;">
                <div class="flex flex-wrap gap-2 justify-end">
                    <button @click="openModalAbsenSiswa = true" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                        ➕ Rekam Absen Siswa Mangkir
                    </button>
                    <button @click="openModalAbsenPegawai = true" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all cursor-pointer">
                        ➕ Rekam Absen Guru Berhalangan
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-xs text-rose-700 uppercase">❌ Rekap Ketidakhadiran Siswa</div>
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-100/50 text-gray-600 border-b border-gray-100 font-semibold">
                                    <th class="p-3 pl-6">Nama Siswa</th>
                                    <th class="p-3">Kelas</th>
                                    <th class="p-3 text-center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse($absenSiswa as $as)
                                    <tr>
                                        <td class="p-3 pl-6 font-medium">{{ $as->siswa->nama_lengkap ?? '-' }}</td>
                                        <td class="p-3">{{ $as->kelas->nama_kelas ?? '-' }}</td>
                                        <td class="p-3 text-center">
                                            <span class="px-2 py-0.5 font-bold rounded text-[10px] {{ $as->keterangan == 'Sakit' ? 'bg-sky-100 text-sky-800' : ($as->keterangan == 'Izin' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                                {{ $as->keterangan }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="p-6 text-center italic text-gray-400">Nihil</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-xs text-amber-700 uppercase">❌ Rekap Ketidakhadiran Guru / Staff</div>
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-100/50 text-gray-600 border-b border-gray-100 font-semibold">
                                    <th class="p-3 pl-6">Nama Guru</th>
                                    <th class="p-3">Mapel</th>
                                    <th class="p-3 text-center">Keterangan</th>
                                    <th class="p-3">Tindak Lanjut Kelas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse($absenPegawai as $ap)
                                    <tr>
                                        <td class="p-3 pl-6 font-medium">{{ $ap->pegawai->nama_lengkap ?? '-' }}</td>
                                        <td class="p-3">{{ $ap->mataPelajaran->nama_mapel ?? 'Semua Jam' }}</td>
                                        <td class="p-3 text-center">
                                            <span class="px-2 py-0.5 font-bold rounded text-[10px] {{ $ap->keterangan == 'Sakit' ? 'bg-sky-100 text-sky-800' : ($ap->keterangan == 'Izin' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                                {{ $ap->keterangan }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-gray-500 italic">{{ $ap->tindak_lanjut ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-6 text-center italic text-gray-400">Nihil</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <div>
                    <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">📝 Catatan Jurnal & Kejadian Penting Hari Ini</h4>
                    <p class="text-xs text-gray-500">Gunakan kolom ini untuk menulis rangkuman peristiwa insidental di lingkungan sekolah (misal: kerusakan fasilitas, tawuran, kunjungan tamu negara, dll).</p>
                </div>
                <form action="{{ route('piket.catatan.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <textarea name="catatan_kejadian" rows="4" required placeholder="Tulis catatan kejadian di sini secara kronologis..." class="w-full text-xs rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm placeholder:italic">{{ $catatanHarian?->catatan_kejadian }}</textarea>
                    <div class="flex justify-between items-center text-[11px] text-gray-400">
                        <p>{{ $catatanHarian ? 'Terakhir diperbarui oleh: ' . ($catatanHarian->pembuatCatatan->nama_lengkap ?? 'Sistem') : 'Belum diisi hari ini.' }}</p>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-indigo-950 text-white rounded-lg font-semibold shadow transition-all cursor-pointer">
                            💾 Simpan Berita Acara Jurnal
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <div x-show="openModalSiswa" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl p-6 space-y-4" @click.away="openModalSiswa = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="font-bold text-xs uppercase text-gray-900">Form Izin Keluar Siswa</h3>
                    <button @click="openModalSiswa = false" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('piket.izin-siswa.store') }}" method="POST" class="space-y-3 text-left text-xs"
                      x-data="{
                            isDrawing: false,
                            canvas: null,
                            ctx: null,
                            initCanvas() {
                                this.canvas = $refs.canvasSiswa;
                                this.ctx = this.canvas.getContext('2d');
                                this.ctx.strokeStyle = '#1e3a8a';
                                this.ctx.lineWidth = 3;
                                this.ctx.lineCap = 'round';
                            },
                            getPos(e) {
                                let rect = this.canvas.getBoundingClientRect();
                                let clientX = e.touches ? e.touches[0].clientX : e.clientX;
                                let clientY = e.touches ? e.touches[0].clientY : e.clientY;
                                return { x: clientX - rect.left, y: clientY - rect.top };
                            },
                            start(e) {
                                this.isDrawing = true;
                                let pos = this.getPos(e);
                                this.ctx.beginPath();
                                this.ctx.moveTo(pos.x, pos.y);
                            },
                            draw(e) {
                                if(!this.isDrawing) return;
                                e.preventDefault();
                                let pos = this.getPos(e);
                                this.ctx.lineTo(pos.x, pos.y);
                                this.ctx.stroke();
                            },
                            stop() {
                                if(!this.isDrawing) return;
                                this.isDrawing = false;
                                $refs.inputSignSiswa.value = this.canvas.toDataURL();
                            },
                            clearSign() {
                                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                                $refs.inputSignSiswa.value = '';
                            }
                      }" x-init="setTimeout(() => initCanvas(), 200)">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Pilih Kelas *</label>
                        <select name="kelas_id" required class="w-full text-xs rounded-lg border-gray-300">
                            <option value="">-- Pilih Ruang Kelas --</option>
                            @foreach($daftarKelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Nama Siswa *</label>
                        <select name="siswa_id" required class="w-full text-xs rounded-lg border-gray-300">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($daftarSiswa as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Jam Keluar *</label>
                        <input type="time" name="waktu_keluar" value="{{ \Carbon\Carbon::now()->format('H:i') }}" required class="w-full text-xs rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Alasan Keluar *</label>
                        <input type="text" name="alasan_keluar" placeholder="Misal: Fotocopy berkas, Sakit pulang lambat" required class="w-full text-xs rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Tanda Tangan Fisik Siswa *</label>
                        <div class="border-2 border-dashed border-gray-300 bg-slate-50 rounded-xl relative overflow-hidden h-32">
                            <canvas x-ref="canvasSiswa" width="400" height="128" class="w-full h-full cursor-crosshair touch-none"
                                @mousedown="start" @mousemove="draw" @mouseup="stop" @mouseleave="stop"
                                @touchstart="start" @touchmove="draw" @touchend="stop">
                            </canvas>
                            <button type="button" @click="clearSign()" class="absolute bottom-2 right-2 px-2 py-1 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-bold rounded shadow-sm transition-colors cursor-pointer">
                                🗑️ Clear Canvas
                            </button>
                        </div>
                        <input type="hidden" name="tanda_tangan_siswa" x-ref="inputSignSiswa" required>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t">
                        <button type="button" @click="openModalSiswa = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg cursor-pointer">Simpan Surat Izin</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openModalPegawai" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl p-6 space-y-4" @click.away="openModalPegawai = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="font-bold text-xs uppercase text-gray-900">Form Izin Keluar Pegawai</h3>
                    <button @click="openModalPegawai = false" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer">&times;</button>
                </div>
                
                <form action="{{ route('piket.izin-pegawai.store') }}" method="POST" class="space-y-3 text-left text-xs"
                      x-data="{
                            isDrawing: false, canvas: null, ctx: null,
                            initCanvas() {
                                this.canvas = $refs.canvasPegawai; this.ctx = this.canvas.getContext('2d');
                                this.ctx.strokeStyle = '#0f172a'; this.ctx.lineWidth = 3; this.ctx.lineCap = 'round';
                            },
                            getPos(e) {
                                let rect = this.canvas.getBoundingClientRect();
                                let clientX = e.touches ? e.touches[0].clientX : e.clientX;
                                let clientY = e.touches ? e.touches[0].clientY : e.clientY;
                                return { x: clientX - rect.left, y: clientY - rect.top };
                            },
                            start(e) { this.isDrawing = true; let pos = this.getPos(e); this.ctx.beginPath(); this.ctx.moveTo(pos.x, pos.y); },
                            draw(e) { if(!this.isDrawing) return; e.preventDefault(); let pos = this.getPos(e); this.ctx.lineTo(pos.x, pos.y); this.ctx.stroke(); },
                            stop() { if(!this.isDrawing) return; this.isDrawing = false; $refs.inputSignPegawai.value = this.canvas.toDataURL(); },
                            clearSign() { this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height); $refs.inputSignPegawai.value = ''; }
                      }" x-init="setTimeout(() => initCanvas(), 200)">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Nama Pegawai / Guru *</label>
                        <select name="pegawai_id" required class="w-full text-xs rounded-lg border-gray-300">
                            <option value="">-- Pilih Anggota Staff --</option>
                            @foreach($daftarPegawai as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Mata Pelajaran yang Ditinggal (Opsional)</label>
                        <select name="mata_pelajaran_id" class="w-full text-xs rounded-lg border-gray-300">
                            <option value="">-- Tidak Ada (Bukan Jam Mengajar) --</option>
                            @foreach($daftarMapel as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-gray-600 mb-1">Jam Keluar *</label>
                            <input type="time" name="waktu_keluar" value="{{ \Carbon\Carbon::now()->format('H:i') }}" required class="w-full text-xs rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-600 mb-1">Guru Pengganti (Invaler)</label>
                            <select name="invaler_id" class="w-full text-xs rounded-lg border-gray-300">
                                <option value="">-- Pilih Guru Invaler --</option>
                                @foreach($daftarPegawai as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Alasan Meninggalkan Sekolah *</label>
                        <input type="text" name="alasan_keluar" placeholder="Misal: Rapat di Dinas Pendidikan, Ke Bank" required class="w-full text-xs rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Tanda Tangan Pemohon *</label>
                        <div class="border-2 border-dashed border-gray-300 bg-slate-50 rounded-xl relative overflow-hidden h-32">
                            <canvas x-ref="canvasPegawai" width="400" height="128" class="w-full h-full cursor-crosshair touch-none"
                                @mousedown="start" @mousemove="draw" @mouseup="stop" @mouseleave="stop"
                                @touchstart="start" @touchmove="draw" @touchend="stop">
                            </canvas>
                            <button type="button" @click="clearSign()" class="absolute bottom-2 right-2 px-2 py-1 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-bold rounded shadow-sm transition-colors cursor-pointer">
                                🗑️ Clear Canvas
                            </button>
                        </div>
                        <input type="hidden" name="tanda_tangan_pegawai" x-ref="inputSignPegawai" required>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t">
                        <button type="button" @click="openModalPegawai = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white font-bold rounded-lg cursor-pointer">Terbitkan Izin</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openModalAbsenSiswa" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl p-6 space-y-4" @click.away="openModalAbsenSiswa = false">
                <h3 class="font-bold text-xs uppercase border-b pb-2 text-rose-600">Form Ketidakhadiran Siswa</h3>
                <form action="{{ route('piket.absen-siswa.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div>
                        <label class="block font-semibold mb-1">Kelas *</label>
                        <select name="kelas_id" required class="w-full text-xs rounded-lg border-gray-300">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($daftarKelas as $k) <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Siswa *</label>
                        <select name="siswa_id" required class="w-full text-xs rounded-lg border-gray-300">
                            <option value="">-- Pilih Nama Siswa --</option>
                            @foreach($daftarSiswa as $s) <option value="{{ $s->id }}">{{ $s->nama_lengkap }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Keterangan Mangkir *</label>
                        <select name="keterangan" required class="w-full text-xs rounded-lg border-gray-300">
                            <option value="Sakit">Sakit (Dengan info surat/pesan)</option>
                            <option value="Izin">Izin (Ada keperluan keluarga)</option>
                            <option value="Alpha">Alpha (Tanpa Kabar / Cabut)</option>
                        </select>
                    </div>
                    <div class="pt-2 flex justify-end gap-2 border-t">
                        <button type="button" @click="openModalAbsenSiswa = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-bold rounded-lg cursor-pointer">Rekam Absen</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openModalAbsenPegawai" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-transition>
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl p-6 space-y-4" @click.away="openModalAbsenPegawai = false">
                <h3 class="font-bold text-xs uppercase border-b pb-2 text-amber-600">Form Ketidakhadiran Pegawai / Guru</h3>
                <form action="{{ route('piket.absen-pegawai.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div>
                        <label class="block font-semibold mb-1">Pegawai *</label>
                        <select name="pegawai_id" required class="w-full text-xs rounded-lg border-gray-300">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($daftarPegawai as $p) <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" class="w-full text-xs rounded-lg border-gray-300">
                            <option value="">-- Semua Jam Mengajar / Full Hari --</option>
                            @foreach($daftarMapel as $m) <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Keterangan *</label>
                        <select name="keterangan" required class="w-full text-xs rounded-lg border-gray-300">
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin / Dinas Luar</option>
                            <option value="Alpha">Alpha</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tindak Lanjut / Tugas Kelas</label>
                        <input type="text" name="tindak_lanjut" placeholder="Misal: Diberikan tugas mandiri LKS Bab 3" class="w-full text-xs rounded-lg border-gray-300">
                    </div>
                    <div class="pt-2 flex justify-end gap-2 border-t">
                        <button type="button" @click="openModalAbsenPegawai = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 text-white font-bold rounded-lg cursor-pointer">Rekam Absen</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>