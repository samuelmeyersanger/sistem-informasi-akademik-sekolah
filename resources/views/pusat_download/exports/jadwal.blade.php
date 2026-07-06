<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Pelajaran</title>
    <style>
        @page { margin: 1cm; size: folio landscape; }
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; }
        .border-all th, .border-all td { border: 1px solid #000; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .bg-gray { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <table>
        <!-- KOP SURAT / HEADER -->
        <tr>
            <td colspan="6" class="text-center text-bold" style="font-size: 20px;">
                JADWAL PELAJARAN KELAS {{ $kelas->nama_kelas }}
            </td>
        </tr>
        <tr>
            <td colspan="6" class="text-center text-bold" style="font-size: 14px;">
                {{ $nama_sekolah }} | TAHUN AJARAN {{ $tahun_ajaran }}
            </td>
        </tr>
        <tr><td colspan="6" style="height: 15px;"></td></tr>

        <!-- TABEL DATA -->
        <tbody class="border-all text-center">
            <tr class="bg-gray text-bold">
                <th width="5%">Jam Ke</th>
                @foreach($hariList as $hari)
                    <th width="19%">{{ $hari }}</th>
                @endforeach
            </tr>

            @for($jam = 1; $jam <= $maxJam; $jam++)
                <tr>
                    <td class="text-bold" style="font-size: 14px;">{{ $jam }}</td>
                    
                    @foreach($hariList as $hari)
                        <td>
                            <!-- Menampilkan kegiatan Non-KBM (Misal: Istirahat) -->
                            @if(isset($kegiatanMatriks[$jam][$hari]))
                                <div style="font-style: italic; color: #555;">{{ $kegiatanMatriks[$jam][$hari] }}</div>
                            @endif

                            <!-- Menampilkan Jadwal Pelajaran -->
                            @if(isset($matriks[$jam][$hari]))
                                @php
                                    $jadwal = $matriks[$jam][$hari];
                                    
                                    // 1. Nama Mata Pelajaran
                                    $mapel = '';
                                    if ($jadwal->kodeGuru && $jadwal->kodeGuru->mataPelajarans->count() > 0) {
                                        $mapel = $jadwal->kodeGuru->mataPelajarans->pluck('nama_mata_pelajaran')->join(', ');
                                    }
                                    
                                    // 2. Nama Guru
                                    $guru = $jadwal->kodeGuru && $jadwal->kodeGuru->pegawai 
                                            ? $jadwal->kodeGuru->pegawai->nama_lengkap 
                                            : '';
                                            
                                    // 3. Kode Guru
                                    $kode = $jadwal->kodeGuru ? $jadwal->kodeGuru->kode_guru : '-';
                                    
                                    // 4. Ruangan
                                    $ruangan = $jadwal->ruangan ? $jadwal->ruangan->nama_ruangan : 'Blm Diset'; 
                                @endphp
                                
                                <div class="text-bold" style="font-size: 11px; margin-bottom: 2px;">{{ strtoupper($mapel) }}</div>
                                <div style="font-size: 10px; margin-bottom: 2px;">{{ $guru }} ({{ $kode }})</div>
                                <div style="font-size: 9px; color: #555; font-style: italic;">R: {{ $ruangan }}</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>

</body>
</html>