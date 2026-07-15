<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-800 leading-tight flex items-center gap-2">
                    <span class="text-3xl">📓</span> {{ __('Kendali Operasional Piket') }}
                </h2>
                <p class="text-sm font-medium text-slate-500 mt-1">Sistem pencatatan harian untuk izin keluar, presensi, dan kejadian insidental sekolah.</p>
            </div>
            
            {{-- Navigasi Tanggal --}}
            <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-3">
                <div class="px-3 py-1 bg-indigo-50 rounded-xl">
                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block">Periode Jurnal</span>
                    <span class="font-bold text-indigo-700 text-sm">{{ $namaHari }}</span>
                </div>
                <form action="{{ route('piket.dashboard') }}" method="GET" class="flex items-center">
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" 
                           class="text-sm font-bold text-slate-700 rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/50 min-h-screen relative font-sans" x-data="{ 
        tabAktif: 'izin', 
        openModalSiswa: false, 
        openModalPegawai: false,
        openModalAbsenSiswa: false,
        openModalAbsenPegawai: false
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 relative z-10">

            {{-- HERO BANNER: TIM PIKET HARI INI --}}
            <div class="relative overflow-hidden bg-slate-900 rounded-[2rem] shadow-2xl p-6 md:p-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border border-slate-800 group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/50 via-slate-900 to-slate-900"></div>
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 group-hover:scale-110 transition-transform duration-700"></div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 bg-indigo-500/20 border border-indigo-400/30 px-3 py-1.5 rounded-full mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[10px] font-black text-indigo-200 uppercase tracking-widest">Penugasan Sistem</span>
                    </div>
                    <h3 class="text-2xl font-black text-white tracking-tight">Personel Piket Terjadwal</h3>
                    <p class="text-sm text-slate-400 mt-1 font-medium max-w-md leading-relaxed">Hak akses entri jurnal hari ini dilimpahkan penuh kepada koordinator dan anggota tim piket yang tercantum.</p>
                </div>

                <div class="relative z-10 flex flex-wrap gap-4 w-full lg:w-auto">
                    {{-- Penanggung Jawab --}}
                    <div class="flex-1 lg:flex-none bg-white/5 backdrop-blur-md border border-white/10 p-4 rounded-[1.5rem] min-w-[200px]">
                        <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                            <span>👑</span> Koordinator
                        </p>
                        @if($petugasHariIni && $petugasHariIni->penanggungJawab)
                            <div class="font-bold text-white text-base leading-tight">{{ $petugasHariIni->penanggungJawab->nama_lengkap }}</div>
                        @else
                            <div class="font-bold text-rose-400 text-sm flex items-center gap-1.5">⚠️ Belum Diploting</div>
                        @endif
                    </div>

                    {{-- Anggota --}}
                    <div class="flex-1 lg:flex-none bg-white/5 backdrop-blur-md border border-white/10 p-4 rounded-[1.5rem] min-w-[250px]">
                        <p class="text-[10px] font-black text-emerald-300 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                            <span>👥</span> Anggota Tim
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            @if($petugasHariIni && count($petugasHariIni->objek_anggota_piket) > 0)
                                @foreach($petugasHariIni->objek_anggota_piket as $anggota)
                                    <span class="px-2.5 py-1 bg-white/10 text-white text-[11px] font-bold rounded-lg border border-white/5">
                                        {{ $anggota->nama_lengkap }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-sm font-bold text-slate-400 italic">Tidak ada anggota pendamping</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- NAVIGATION TABS --}}
            <div class="flex flex-wrap items-center gap-2 mb-2 p-1.5 bg-slate-200/50 rounded-2xl shadow-inner inline-flex">
                <button @click="tabAktif = 'izin'" 
                        :class="tabAktif === 'izin' ? 'bg-white text-indigo-700 shadow-md ring-1 ring-slate-900/5' : 'bg-transparent text-slate-500 hover:text-slate-800 hover:bg-white/50'" 
                        class="px-6 py-3 text-xs font-black tracking-wide uppercase transition-all rounded-xl flex items-center gap-2 cursor-pointer">
                    <span :class="tabAktif === 'izin' ? 'text-indigo-500' : 'text-slate-400'">🚗</span> 
                    Izin Keluar-Masuk Lingkungan
                </button>
                <button @click="tabAktif = 'absen'" 
                        :class="tabAktif === 'absen' ? 'bg-white text-indigo-700 shadow-md ring-1 ring-slate-900/5' : 'bg-transparent text-slate-500 hover:text-slate-800 hover:bg-white/50'" 
                        class="px-6 py-3 text-xs font-black tracking-wide uppercase transition-all rounded-xl flex items-center gap-2 cursor-pointer">
                    <span :class="tabAktif === 'absen' ? 'text-rose-500' : 'text-slate-400'">❌</span> 
                    Rekam Ketidakhadiran (Absen)
                </button>
            </div>

            {{-- TAB: IZIN KELUAR MASUK --}}
            <div x-show="tabAktif === 'izin'" class="space-y-8" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                
                {{-- Modul Izin Siswa --}}
                <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                    <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/50 backdrop-blur-sm relative z-10">
                        <div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                                <span class="text-indigo-500">🎓</span> Log Izin Keluar Siswa
                            </h3>
                            <p class="text-xs font-medium text-slate-500 mt-1">Daftar murid yang meninggalkan jam pelajaran untuk keperluan tertentu hari ini.</p>
                        </div>
                        <button @click="openModalSiswa = true" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-black text-sm rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 shrink-0">
                            <span>➕</span> Terbitkan Izin Siswa
                        </button>
                    </div>

                    <div class="overflow-x-auto relative z-10">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                    <th class="p-4 pl-8">Identitas Siswa</th>
                                    <th class="p-4 text-center w-24">Keluar</th>
                                    <th class="p-4 text-center w-24">Kembali</th>
                                    <th class="p-4">Keterangan / Alasan</th>
                                    <th class="p-4 text-center w-32">Bukti Fisik</th>
                                    <th class="p-4 text-center w-32">Status Posisi</th>
                                    <th class="p-4 pr-8 text-center w-32">Validasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                @forelse($izinSiswa as $item)
                                    <tr class="hover:bg-indigo-50/30 transition-colors duration-200">
                                        <td class="p-4 pl-8">
                                            <div class="font-black text-slate-900">{{ $item->siswa->nama_lengkap ?? '-' }}</div>
                                            <div class="text-[11px] font-bold text-indigo-500 mt-0.5">Kelas: {{ $item->kelas->nama_kelas ?? '-' }}</div>
                                        </td>
                                        <td class="p-4 text-center font-mono font-bold text-amber-600">{{ \Carbon\Carbon::parse($item->waktu_keluar)->format('H:i') }}</td>
                                        <td class="p-4 text-center font-mono font-bold text-emerald-600">
                                            {{ $item->waktu_kembali ? \Carbon\Carbon::parse($item->waktu_kembali)->format('H:i') : '--:--' }}
                                        </td>
                                        <td class="p-4">
                                            <div class="text-xs max-w-[200px] truncate" title="{{ $item->alasan_keluar }}">{{ $item->alasan_keluar }}</div>
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($item->tanda_tangan_siswa)
                                                <div class="p-1 bg-white border border-slate-200 rounded-lg shadow-sm inline-block">
                                                    <img src="{{ $item->tanda_tangan_siswa }}" class="h-8 max-w-[80px] object-contain" alt="Sign">
                                                </div>
                                            @else
                                                <span class="text-[10px] font-bold text-slate-400 italic bg-slate-100 px-2 py-1 rounded">No Sign</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($item->status === 'Sudah Kembali')
                                                <span class="inline-flex items-center px-2 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                    Di Lingkungan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> Di Luar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 pr-8 text-center">
                                            @if($item->status === 'Belum Kembali')
                                                <form action="{{ route('piket.izin-siswa.kembali', $item->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="w-full px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm transition-colors cursor-pointer">
                                                        Konfirmasi
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xl text-emerald-500">✔️</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-12 text-center">
                                            <div class="text-4xl mb-3 opacity-50">🏃</div>
                                            <p class="text-sm font-bold text-slate-500">Tidak ada log izin siswa hari ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Modul Izin Pegawai --}}
                <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-slate-100 rounded-full blur-3xl opacity-50 pointer-events-none -translate-y-1/2 translate-x-1/2"></div>
                    
                    <div class="p-6 md:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/50 backdrop-blur-sm relative z-10">
                        <div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-2">
                                <span class="text-slate-600">👔</span> Log Izin Keluar Pegawai/Guru
                            </h3>
                            <p class="text-xs font-medium text-slate-500 mt-1">Daftar staf pendidik atau tenaga kependidikan yang melakukan dinas luar/izin.</p>
                        </div>
                        <button @click="openModalPegawai = true" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-black text-sm rounded-xl shadow-lg shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center justify-center gap-2 shrink-0">
                            <span>➕</span> Terbitkan Izin Pegawai
                        </button>
                    </div>

                    <div class="overflow-x-auto relative z-10">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                    <th class="p-4 pl-8">Nama Lengkap</th>
                                    <th class="p-4 w-40">Info KBM</th>
                                    <th class="p-4 text-center w-24">Keluar</th>
                                    <th class="p-4 text-center w-24">Kembali</th>
                                    <th class="p-4">Alasan</th>
                                    <th class="p-4 text-center w-24">Sign</th>
                                    <th class="p-4 text-center w-32">Status</th>
                                    <th class="p-4 pr-8 text-center w-28">Validasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                @forelse($izinPegawai as $item)
                                    <tr class="hover:bg-slate-50/50 transition-colors duration-200">
                                        <td class="p-4 pl-8 font-black text-slate-900">{{ $item->pegawai->nama_lengkap ?? '-' }}</td>
                                        <td class="p-4">
                                            <div class="text-[11px] font-bold text-indigo-600">{{ $item->mataPelajaran->nama_mapel ?? 'Bukan Jam Ajar' }}</div>
                                            @if($item->invaler)
                                                <div class="text-[10px] text-slate-500 mt-0.5"><span class="font-bold">Invaler:</span> {{ $item->invaler->nama_lengkap }}</div>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center font-mono font-bold text-amber-600">{{ \Carbon\Carbon::parse($item->waktu_keluar)->format('H:i') }}</td>
                                        <td class="p-4 text-center font-mono font-bold text-emerald-600">
                                            {{ $item->waktu_kembali ? \Carbon\Carbon::parse($item->waktu_kembali)->format('H:i') : '--:--' }}
                                        </td>
                                        <td class="p-4 text-xs max-w-[150px] truncate" title="{{ $item->alasan_keluar }}">{{ $item->alasan_keluar }}</td>
                                        <td class="p-4 text-center">
                                            @if($item->tanda_tangan_pegawai)
                                                <div class="p-1 bg-white border border-slate-200 rounded-lg shadow-sm inline-block">
                                                    <img src="{{ $item->tanda_tangan_pegawai }}" class="h-6 max-w-[60px] object-contain" alt="Sign">
                                                </div>
                                            @else
                                                <span class="text-[10px] font-bold text-slate-400 italic bg-slate-100 px-2 py-1 rounded">N/A</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($item->status === 'Sudah Kembali')
                                                <span class="inline-flex items-center px-2 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                    Di Lingkungan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm">
                                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> Dinas Luar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 pr-8 text-center">
                                            @if($item->status === 'Belum Kembali')
                                                <form action="{{ route('piket.izin-pegawai.kembali', $item->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="w-full px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm transition-colors cursor-pointer">
                                                        Kembali
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xl text-emerald-500">✔️</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-12 text-center">
                                            <div class="text-4xl mb-3 opacity-50">☕</div>
                                            <p class="text-sm font-bold text-slate-500">Seluruh pegawai dan guru beraktivitas penuh di sekolah hari ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB: ABSENSI --}}
            <div x-show="tabAktif === 'absen'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    {{-- Absen Siswa --}}
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-slate-100 bg-rose-50/30 flex justify-between items-center relative overflow-hidden">
                            <div class="absolute right-0 top-0 w-32 h-32 bg-rose-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-widest relative z-10 flex items-center gap-2">
                                <span class="text-rose-500">❌</span> Rekap Mangkir Siswa
                            </h3>
                            <button @click="openModalAbsenSiswa = true" class="relative z-10 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-black uppercase tracking-wider rounded-xl shadow-md transition-all hover:-translate-y-0.5 cursor-pointer">
                                + Rekam Baru
                            </button>
                        </div>
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                        <th class="p-4 pl-6">Profil Siswa</th>
                                        <th class="p-4 text-center w-32">Alasan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                    @forelse($absenSiswa as $as)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="p-4 pl-6">
                                                <div class="font-black text-slate-900">{{ $as->siswa->nama_lengkap ?? '-' }}</div>
                                                <div class="text-[11px] font-bold text-slate-500 mt-0.5">Kelas: {{ $as->kelas->nama_kelas ?? '-' }}</div>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="px-3 py-1.5 font-black uppercase tracking-wider rounded-lg text-[10px] shadow-sm {{ $as->keterangan == 'Sakit' ? 'bg-sky-50 border border-sky-200 text-sky-700' : ($as->keterangan == 'Izin' ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-rose-50 border border-rose-200 text-rose-700') }}">
                                                    {{ $as->keterangan }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="p-12 text-center text-sm font-bold text-slate-400">Nihil / Kehadiran 100%</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Absen Pegawai --}}
                    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-slate-100 bg-amber-50/30 flex justify-between items-center relative overflow-hidden">
                            <div class="absolute right-0 top-0 w-32 h-32 bg-amber-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-widest relative z-10 flex items-center gap-2">
                                <span class="text-amber-500">❌</span> Guru Berhalangan
                            </h3>
                            <button @click="openModalAbsenPegawai = true" class="relative z-10 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-black uppercase tracking-wider rounded-xl shadow-md transition-all hover:-translate-y-0.5 cursor-pointer">
                                + Rekam Baru
                            </button>
                        </div>
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b-2 border-slate-100 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                        <th class="p-4 pl-6">Profil Pendidik</th>
                                        <th class="p-4 text-center w-24">Alasan</th>
                                        <th class="p-4">Tindak Lanjut Kelas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                    @forelse($absenPegawai as $ap)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="p-4 pl-6">
                                                <div class="font-black text-slate-900">{{ $ap->pegawai->nama_lengkap ?? '-' }}</div>
                                                <div class="text-[11px] font-bold text-slate-500 mt-0.5">{{ $ap->mataPelajaran->nama_mapel ?? 'Kosong/Full Day' }}</div>
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="px-3 py-1.5 font-black uppercase tracking-wider rounded-lg text-[10px] shadow-sm {{ $ap->keterangan == 'Sakit' ? 'bg-sky-50 border border-sky-200 text-sky-700' : ($ap->keterangan == 'Izin' ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-rose-50 border border-rose-200 text-rose-700') }}">
                                                    {{ $ap->keterangan }}
                                                </span>
                                            </td>
                                            <td class="p-4 text-xs font-bold text-indigo-600 leading-tight">
                                                {{ $ap->tindak_lanjut ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="p-12 text-center text-sm font-bold text-slate-400">Nihil / Seluruh Guru Hadir</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            {{-- CATATAN JURNAL --}}
            <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 space-y-6 relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-indigo-500 to-emerald-500"></div>
                <div>
                    <h4 class="text-base font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                        <span>📝</span> Catatan Kejadian Insidental (Jurnal Umum)
                    </h4>
                    <p class="text-xs font-medium text-slate-500 mt-1 pl-7 max-w-3xl">Laporan kronologis peristiwa yang terjadi pada hari operasional ini (misal: penertiban razia rambut, kunjungan pengawas diknas, atau insiden siswa sakit mendadak yang dipulangkan).</p>
                </div>
                <form action="{{ route('piket.catatan.store') }}" method="POST" class="space-y-4 pl-0 sm:pl-7">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <textarea name="catatan_kejadian" rows="5" required 
                              placeholder="Tuliskan rangkuman narasi berita acara di sini..." 
                              class="w-full text-sm font-medium rounded-2xl border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-inner bg-slate-50 p-4">{{ $catatanHarian?->catatan_kejadian }}</textarea>
                    
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50 border border-slate-100 p-4 rounded-xl">
                        <div class="flex items-center gap-2 text-[11px] font-bold text-slate-500 uppercase tracking-widest">
                            <span class="text-lg">👤</span>
                            <span>{{ $catatanHarian ? 'Penyunting Terakhir: ' . ($catatanHarian->pembuatCatatan->nama_lengkap ?? 'Sistem') : 'Formulir Jurnal Kosong' }}</span>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-lg shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                            Simpan Arsip Jurnal
                        </button>
                    </div>
                </form>
            </div>

        </div>

        {{-- MODAL IZIN SISWA --}}
        <div x-show="openModalSiswa" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openModalSiswa = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-indigo-50/80 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-100 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-indigo-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        🎓 Terbitkan Izin Siswa
                    </h3>
                    <button type="button" @click="openModalSiswa = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('piket.izin-siswa.store') }}" method="POST" class="p-6 space-y-4 bg-white relative z-10"
                      x-data="{
                            isDrawing: false, canvas: null, ctx: null,
                            initCanvas() {
                                this.canvas = $refs.canvasSiswa; this.ctx = this.canvas.getContext('2d');
                                this.ctx.strokeStyle = '#4338ca'; this.ctx.lineWidth = 4; this.ctx.lineCap = 'round';
                            },
                            getPos(e) {
                                let rect = this.canvas.getBoundingClientRect();
                                let clientX = e.touches ? e.touches[0].clientX : e.clientX;
                                let clientY = e.touches ? e.touches[0].clientY : e.clientY;
                                return { x: clientX - rect.left, y: clientY - rect.top };
                            },
                            start(e) { this.isDrawing = true; let pos = this.getPos(e); this.ctx.beginPath(); this.ctx.moveTo(pos.x, pos.y); },
                            draw(e) { if(!this.isDrawing) return; e.preventDefault(); let pos = this.getPos(e); this.ctx.lineTo(pos.x, pos.y); this.ctx.stroke(); },
                            stop() { if(!this.isDrawing) return; this.isDrawing = false; $refs.inputSignSiswa.value = this.canvas.toDataURL(); },
                            clearSign() { this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height); $refs.inputSignSiswa.value = ''; }
                      }" x-init="setTimeout(() => initCanvas(), 300)">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Basis Kelas Utama</label>
                        <select name="kelas_id" required class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm py-2.5">
                            <option value="">-- Cari Kelas --</option>
                            @foreach($daftarKelas as $k) <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option> @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Identitas Pemohon (Siswa)</label>
                        <select name="siswa_id" required class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm py-2.5">
                            <option value="">-- Cari Nama --</option>
                            @foreach($daftarSiswa as $s) <option value="{{ $s->id }}">{{ $s->nama_lengkap }}</option> @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Waktu Keluar</label>
                            <input type="time" name="waktu_keluar" value="{{ \Carbon\Carbon::now()->format('H:i') }}" required 
                                   class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm py-2.5 text-center">
                        </div>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Rincian Kepentingan</label>
                        <input type="text" name="alasan_keluar" placeholder="Misal: Beli ATK pramuka..." required 
                               class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm py-2.5">
                    </div>

                    <div class="bg-indigo-50/50 p-4 rounded-[1.5rem] border border-indigo-100">
                        <label class="block font-black text-indigo-900 text-[10px] uppercase tracking-widest mb-2 text-center">Area Tanda Tangan Digital Siswa</label>
                        <div class="border-2 border-dashed border-indigo-300 bg-white rounded-xl relative overflow-hidden h-32 shadow-inner">
                            <canvas x-ref="canvasSiswa" width="350" height="128" class="w-full h-full cursor-crosshair touch-none"
                                @mousedown="start" @mousemove="draw" @mouseup="stop" @mouseleave="stop"
                                @touchstart="start" @touchmove="draw" @touchend="stop">
                            </canvas>
                            <button type="button" @click="clearSign()" class="absolute bottom-2 right-2 px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm transition-colors cursor-pointer border border-rose-200">
                                Ulangi
                            </button>
                        </div>
                        <input type="hidden" name="tanda_tangan_siswa" x-ref="inputSignSiswa" required>
                    </div>

                    <div class="pt-2 flex justify-end gap-3 mt-4 border-t border-slate-100">
                        <button type="button" @click="openModalSiswa = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer text-sm">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 cursor-pointer text-sm">Simpan Izin</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL IZIN PEGAWAI --}}
        <div x-show="openModalPegawai" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openModalPegawai = false">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-slate-200 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
                    <h3 class="text-base font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        👔 Dinas Luar Pegawai
                    </h3>
                    <button type="button" @click="openModalPegawai = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer border border-transparent hover:border-rose-100 relative z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('piket.izin-pegawai.store') }}" method="POST" class="p-6 space-y-4 bg-white relative z-10"
                      x-data="{
                            isDrawing: false, canvas: null, ctx: null,
                            initCanvas() {
                                this.canvas = $refs.canvasPegawai; this.ctx = this.canvas.getContext('2d');
                                this.ctx.strokeStyle = '#1e293b'; this.ctx.lineWidth = 4; this.ctx.lineCap = 'round';
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
                      }" x-init="setTimeout(() => initCanvas(), 300)">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Identitas Pegawai / Staf</label>
                        <select name="pegawai_id" required class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-slate-500/10 focus:border-slate-500 shadow-sm py-2.5">
                            <option value="">-- Cari Nama --</option>
                            @foreach($daftarPegawai as $p) <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option> @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-4 p-3 border border-indigo-100 bg-indigo-50/30 rounded-2xl shadow-inner">
                        <div>
                            <label class="block font-black text-indigo-900 text-[10px] uppercase tracking-widest mb-1.5">Meninggalkan KBM / Mapel?</label>
                            <select name="mata_pelajaran_id" class="w-full text-sm font-bold rounded-xl border-indigo-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm py-2">
                                <option value="">-- Bukan Jam Ajar --</option>
                                @foreach($daftarMapel as $m) <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-black text-indigo-900 text-[10px] uppercase tracking-widest mb-1.5">Guru Pengganti (Invaler)</label>
                            <select name="invaler_id" class="w-full text-sm font-bold rounded-xl border-indigo-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm py-2">
                                <option value="">-- Tidak Perlu Invaler --</option>
                                @foreach($daftarPegawai as $p) <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option> @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Waktu Keluar</label>
                            <input type="time" name="waktu_keluar" value="{{ \Carbon\Carbon::now()->format('H:i') }}" required 
                                   class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-slate-500/10 focus:border-slate-500 shadow-sm py-2.5 text-center">
                        </div>
                    </div>

                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Rincian Kepentingan / Dinas</label>
                        <input type="text" name="alasan_keluar" placeholder="Misal: Ke Koperasi Guru..." required 
                               class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-slate-500/10 focus:border-slate-500 shadow-sm py-2.5">
                    </div>

                    <div class="bg-slate-50 p-4 rounded-[1.5rem] border border-slate-200">
                        <label class="block font-black text-slate-800 text-[10px] uppercase tracking-widest mb-2 text-center">Area Paraf Pemohon</label>
                        <div class="border-2 border-dashed border-slate-300 bg-white rounded-xl relative overflow-hidden h-32 shadow-inner">
                            <canvas x-ref="canvasPegawai" width="350" height="128" class="w-full h-full cursor-crosshair touch-none"
                                @mousedown="start" @mousemove="draw" @mouseup="stop" @mouseleave="stop"
                                @touchstart="start" @touchmove="draw" @touchend="stop">
                            </canvas>
                            <button type="button" @click="clearSign()" class="absolute bottom-2 right-2 px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm transition-colors cursor-pointer border border-rose-200">
                                Ulangi
                            </button>
                        </div>
                        <input type="hidden" name="tanda_tangan_pegawai" x-ref="inputSignPegawai" required>
                    </div>

                    <div class="pt-2 flex justify-end gap-3 mt-4 border-t border-slate-100">
                        <button type="button" @click="openModalPegawai = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors cursor-pointer text-sm">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-black rounded-xl shadow-lg shadow-slate-800/20 transition-all hover:-translate-y-0.5 cursor-pointer text-sm">Proses Izin</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL ABSEN SISWA --}}
        <div x-show="openModalAbsenSiswa" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-sm w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openModalAbsenSiswa = false">
                <div class="px-6 py-5 border-b border-slate-100 bg-rose-50 flex justify-between items-center relative overflow-hidden">
                    <h3 class="text-base font-black text-rose-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        ❌ Mangkir Siswa
                    </h3>
                    <button type="button" @click="openModalAbsenSiswa = false" class="text-slate-400 hover:text-rose-500 hover:bg-rose-50 p-2 rounded-xl transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="{{ route('piket.absen-siswa.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Kelas</label>
                        <select name="kelas_id" required class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 py-2.5">
                            <option value="">-- Pilih --</option>
                            @foreach($daftarKelas as $k) <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Nama Siswa</label>
                        <select name="siswa_id" required class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 py-2.5">
                            <option value="">-- Pilih --</option>
                            @foreach($daftarSiswa as $s) <option value="{{ $s->id }}">{{ $s->nama_lengkap }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Status (Ket.)</label>
                        <select name="keterangan" required class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 py-2.5">
                            <option value="Sakit">Sakit (S)</option>
                            <option value="Izin">Izin / Keluarga (I)</option>
                            <option value="Alpha">Alpha / Bolos (A)</option>
                        </select>
                    </div>
                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="openModalAbsenSiswa = false" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl text-sm">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-rose-600 text-white font-black rounded-xl text-sm shadow-lg shadow-rose-500/30">Rekam Data</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL ABSEN PEGAWAI --}}
        <div x-show="openModalAbsenPegawai" class="fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" style="display: none;" x-transition>
            <div class="bg-white rounded-[2rem] max-w-sm w-full shadow-2xl border border-slate-100 overflow-hidden flex flex-col" @click.away="openModalAbsenPegawai = false">
                <div class="px-6 py-5 border-b border-slate-100 bg-amber-50 flex justify-between items-center relative overflow-hidden">
                    <h3 class="text-base font-black text-amber-900 uppercase tracking-wide flex items-center gap-2 relative z-10">
                        ❌ Guru Berhalangan
                    </h3>
                    <button type="button" @click="openModalAbsenPegawai = false" class="text-slate-400 hover:text-amber-600 hover:bg-amber-100 p-2 rounded-xl transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="{{ route('piket.absen-pegawai.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Pegawai / Guru</label>
                        <select name="pegawai_id" required class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 py-2.5">
                            <option value="">-- Pilih Pendidik --</option>
                            @foreach($daftarPegawai as $p) <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Jam Mengajar Ditinggal</label>
                        <select name="mata_pelajaran_id" class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 py-2.5">
                            <option value="">-- Semua / Bukan Mapel --</option>
                            @foreach($daftarMapel as $m) <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Status Halangan</label>
                        <select name="keterangan" required class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 py-2.5">
                            <option value="Sakit">Sakit Berat (Opname, dsb)</option>
                            <option value="Izin">Izin / Acara Keluarga / Dinas</option>
                            <option value="Alpha">Tanpa Kabar (Alpha)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-black text-slate-700 text-[10px] uppercase tracking-widest mb-1.5">Delegasi Tugas / Arahan Kelas</label>
                        <input type="text" name="tindak_lanjut" placeholder="Misal: Kerjakan LKS Hal. 23..." class="w-full text-sm font-bold rounded-xl border-slate-200 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 py-2.5">
                    </div>
                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="openModalAbsenPegawai = false" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl text-sm">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-amber-500 text-white font-black rounded-xl text-sm shadow-lg shadow-amber-500/30">Catat Ketidakhadiran</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>