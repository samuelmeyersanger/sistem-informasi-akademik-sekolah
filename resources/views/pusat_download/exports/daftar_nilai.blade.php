<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Nilai - {{ $kelas->nama_kelas ?? 'Kelas' }}</title>
    <style>
        @page { size: landscape; margin: 1cm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11px; }
        
        /* Layout Kop Atas */
        .table-header { width: 100%; margin-bottom: 10px; font-weight: bold; }
        
        .box-wali { border: 1.5px solid #000; text-align: center; background-color: #fce4d6; padding: 6px; width: 280px; font-size: 12px;}
        .box-wali-title { border: 1.5px solid #000; text-align: center; background-color: #e2efda; padding: 4px; font-size: 11px; border-bottom: none;}
        
        .box-kelas { border: 2px solid #000; text-align: center; font-size: 18px; background-color: #fff2cc; padding: 6px; width: 200px; margin: 0 auto; border-bottom: none;}
        .box-kelas-title { border: 2px solid #000; text-align: center; background-color: #ddebf7; padding: 4px; font-size: 13px;}
        
        /* Tabel Data Nilai */
        .table-data { width: 100%; border-collapse: collapse; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 4px 2px; text-align: center; vertical-align: middle; height: 16px;}
        .table-data th { background-color: #ddebf7; font-size: 9px; }
        .text-left { text-align: left !important; padding-left: 5px !important; }
        
        /* Styling Border Tebal Spesifik */
        .border-thick-left { border-left: 2px solid #000 !important; }
        .border-thick-right { border-right: 2px solid #000 !important; }
        .border-thick-bottom { border-bottom: 2px solid #000 !important; }
        .border-thick-top { border-top: 2px solid #000 !important; }
        
        /* Tombol Print */
        .print-btn { display: block; width: 120px; margin: 15px auto; padding: 8px; text-align: center; background: #2563eb; color: white; border-radius: 5px; cursor: pointer; border: none; font-weight: bold;}
        .print-btn:hover { background: #1d4ed8; }
        @media print { .print-btn { display: none; } body { background: white; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn">🖨️ Cetak PDF</button>

    <!-- KOP ATAS (Wali Kelas, Kelas, Info Sekolah) -->
    <table class="table-header">
        <tr>
            <!-- Kiri: Wali Kelas -->
            <td style="width: 30%; vertical-align: bottom;">
                <table style="border-collapse: collapse;">
                    <tr><td class="box-wali-title">WALI KELAS</td></tr>
                    <tr><td class="box-wali">{{ $kelas->waliKelas->nama_lengkap ?? '-' }}</td></tr>
                </table>
            </td>
            
            <!-- Tengah: Nama Kelas -->
            <td style="width: 40%; text-align: center; vertical-align: top;">
                <table style="border-collapse: collapse; margin: 0 auto;">
                    <tr><td class="box-kelas">{{ $kelas->nama_kelas ?? '-' }}</td></tr>
                    <tr><td class="box-kelas-title">Tingkat {{ $kelas->tingkat ?? '' }}</td></tr>
                </table>
            </td>
            
            <!-- Kanan: Info Akademik -->
            <td style="width: 30%; text-align: center; vertical-align: top; font-size: 14px; line-height: 1.4;">
                <div>DAFTAR NILAI</div>
                <div>{{ $nama_sekolah }}</div>
                <div style="background-color: #fff2cc; border: 1.5px solid #000; display: inline-block; padding: 2px 20px; margin-top: 4px; font-size: 12px;">
                    TAHUN AJARAN {{ $tahun_ajaran }}
                </div>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA SISWA & NILAI -->
    <table class="table-data border-thick-top border-thick-bottom border-thick-left border-thick-right">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2%;">No</th>
                <th rowspan="2" style="width: 8%;">NIPD</th>
                <th rowspan="2" style="width: 22%;">NAMA SISWA</th>
                <th rowspan="2" style="width: 3%;" class="border-thick-right">L/P</th>
                <th colspan="10" class="border-thick-right" style="background-color: #e2efda;">NILAI HARIAN</th>
                <th colspan="5" class="border-thick-right">PRAKTIK</th>
                <th colspan="2" style="background-color: #e2efda;">PENILAIAN SUMATIF</th>
            </tr>
            <tr>
                <!-- Nilai Harian -->
                <th style="width: 3%;">N1</th><th style="width: 3%;">R</th>
                <th style="width: 3%;">N1</th><th style="width: 3%;">N2</th><th style="width: 3%;">R</th>
                <th style="width: 3%;">N2</th><th style="width: 3%;">N3</th><th style="width: 3%;">R</th>
                <th style="width: 3%;">N3</th><th style="width: 3%;" class="border-thick-right">R</th>
                
                <!-- Praktik -->
                <th style="width: 3%;">1</th><th style="width: 3%;">2</th><th style="width: 3%;">3</th>
                <th style="width: 3%;">4</th><th style="width: 3%;" class="border-thick-right">5</th>
                
                <!-- Sumatif -->
                <th style="width: 6%;">TENGAH<br>SEMESTER</th>
                <th style="width: 6%;">AKHIR<br>SEMESTER</th>
            </tr>
        </thead>
        <tbody>
            @php $count = 0; @endphp
            @foreach($siswaList as $index => $siswa)
            @php $count++; @endphp
            <tr>
                <td style="font-weight: bold;">{{ str_pad($count, 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $siswa->nipd ?? '' }}</td>
                <td class="text-left" style="font-size: 10px; font-weight: bold;">{{ strtoupper($siswa->nama_lengkap ?? '') }}</td>
                <td class="border-thick-right">{{ substr($siswa->jenis_kelamin ?? 'L', 0, 1) }}</td>
                
                <!-- Kotak Kosong Nilai Harian -->
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class="border-thick-right"></td>
                
                <!-- Kotak Kosong Praktik -->
                <td></td><td></td><td></td><td></td><td class="border-thick-right"></td>
                
                <!-- Kotak Kosong Sumatif -->
                <td></td><td></td>
            </tr>
            @endforeach

            <!-- Baris Kosong Tambahan hingga minimal 40 Baris (Sesuai Layout Kertas) -->
            @for($i = $count + 1; $i <= 40; $i++)
            <tr>
                <td style="font-weight: bold;">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</td>
                <td></td><td class="text-left"></td><td class="border-thick-right"></td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class="border-thick-right"></td>
                <td></td><td></td><td></td><td></td><td class="border-thick-right"></td>
                <td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

</body>
</html>