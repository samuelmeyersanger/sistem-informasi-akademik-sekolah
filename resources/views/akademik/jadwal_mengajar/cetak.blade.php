<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Mengajar - {{ $pegawai->nama_lengkap ?? 'Guru' }}</title>
    <style>
        @page { size: landscape; margin: 20mm; }
        body { font-family: 'Arial', sans-serif; font-size: 12px; color: #000; }
        
        /* Kop Header */
        .kop-surat { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .teks-tengah { text-align: center; flex: 1; }
        .logo-sekolah { width: 80px; height: 80px; object-fit: contain; }
        
        /* Tabel Jadwal */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; vertical-align: top; }
        th { background-color: #f3f4f6; }
        
        .mapel-box { background-color: #e0e7ff; font-weight: bold; padding: 4px; border-radius: 4px; margin-bottom: 4px; font-size: 11px; border: 1px solid #c7d2fe; }
        .kelas-box { font-size: 11px; margin-bottom: 2px; }
        .ruang-box { font-size: 10px; color: #555; }
        
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="btn-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 18px; background: #dc2626; color: white; border: none; cursor: pointer; border-radius: 6px; font-weight: bold;">
            📄 Simpan sebagai PDF
        </button>
    </div>

    <div class="kop-surat">
        <img src="{{ asset('images/logo_sekolah.png') }}" class="logo-sekolah" alt="Logo">
        <div class="teks-tengah">
            <h2 style="margin: 0; font-size: 18px;">JADWAL MENGAJAR GURU</h2>
            <h1 style="margin: 5px 0; font-size: 20px;">SMP NEGERI 4 CIBITUNG</h1>
            <p style="margin: 0; font-size: 12px;">Semester: Ganjil / Genap &nbsp;&nbsp;|&nbsp;&nbsp; Tahun Ajaran: 2026/2027</p>
        </div>
        <div style="width: 80px; height: 80px;"></div> 
    </div>

    <div style="margin-bottom: 15px; font-weight: bold; font-size: 14px;">
        Nama Guru : {{ $pegawai->nama_lengkap ?? '___________________________' }} <br>
        NIP / NUPTK : {{ $pegawai->nip ?? '-' }} / {{ $pegawai->nuptk ?? '-' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Waktu</th>
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                    <th>{{ $hari }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $waktuPerJam = $daftarWaktu->groupBy('jam_ke'); @endphp

            @forelse($waktuPerJam as $jamKe => $waktuList)
                @php $contohWaktu = $waktuList->first(); @endphp
                <tr>
                    <td style="background-color: #f9fafb;">
                        <b>Jam {{ $jamKe }}</b><br>
                        <span style="font-size: 10px; color: #666;">
                            {{ \Carbon\Carbon::parse($contohWaktu->jam_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($contohWaktu->jam_selesai)->format('H:i') }}
                        </span>
                    </td>
                    
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                        @php
                            $jadwal = $jadwalPelajaran->first(function($item) use ($hari, $jamKe) {
                                return $item->waktuKbm->hari === $hari && $item->waktuKbm->jam_ke == $jamKe;
                            });
                        @endphp
                        
                        <td>
                            @if($jadwal)
                                <div class="mapel-box">
                                    {{ $jadwal->mataPelajaran->nama_mapel ?? 'Mata Pelajaran' }}
                                </div>
                                <div class="kelas-box">Kelas: {{ $jadwal->kelas->nama_kelas ?? '-' }}</div>
                                <div class="ruang-box">R. {{ $jadwal->ruangan->nama_ruangan ?? '-' }}</div>
                            @else
                                <span style="color: #ccc;">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 20px;">Belum ada jadwal mengajar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>