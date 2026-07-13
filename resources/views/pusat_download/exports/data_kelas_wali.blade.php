<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir</title>
    <style>
        /* Margin dikurangi menjadi 0.5cm agar ruang horizontal makin maksimal di Portrait */
        @page { margin: 0.5cm; }
        body { font-family: Arial, sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        
        .info-table { width: 100%; margin-bottom: 10px; font-weight: bold; font-size: 9px; }
        .info-table td { padding: 2px; }
        
        /* Font tabel 8px: Masih sangat terbaca, tidak sekecil 6px */
        .data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 2px 0px; overflow: hidden; font-size: 8px; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; padding-left: 3px !important; padding-right: 2px !important; }
        
        /* RAHASIA PORTRAIT: Nama wajib mematah ke bawah jika panjang */
        .nama-siswa { 
            white-space: normal; 
            word-wrap: break-word; 
            line-height: 1.1; 
        }
        
        .footer { margin-top: 15px; width: 100%; font-size: 10px;}
        .ttd { float: right; width: 250px; text-align: center; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    @php
        $logoSetting = \DB::table('pengaturan_logo')->first();
        $logoPemda = $logoSetting && $logoSetting->logo_pemda ? public_path('storage/' . $logoSetting->logo_pemda) : null;
        $logoSekolah = $logoSetting && $logoSetting->logo_sekolah ? public_path('storage/' . $logoSetting->logo_sekolah) : null;
    @endphp
    
    <table style="width: 100%; border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 5px;">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: middle;">
                @if($logoPemda && file_exists($logoPemda))
                    <img src="{{ $logoPemda }}" style="max-height: 55px; max-width: 60px; object-fit: contain;">
                @endif
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle;">
                <h2 style="margin: 2px 0;">DAFTAR HADIR KELOMPOK WALI / BIMBINGAN</h2>
                <h3 style="margin: 2px 0;">{{ $nama_sekolah }}</h3>
                <p style="margin: 2px 0; font-size: 10px;">Tahun Ajaran {{ $tahun_ajaran ?? '-' }}</p>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: middle;">
                @if($logoSekolah && file_exists($logoSekolah))
                    <img src="{{ $logoSekolah }}" style="max-height: 55px; max-width: 60px; object-fit: contain;">
                @endif
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="15%">Nama Kelompok</td>
            <td width="35%">: {{ $kelas->nama_kelas }}</td>
            <td width="15%">Bulan</td>
            <td width="35%">: .......................................</td>
        </tr>
        <tr>
            <td>Pembimbing / Wali</td>
            <td>: {{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '-' }}</td>
            <td>Jumlah Siswa</td>
            <td>: Laki ({{ $laki_laki }}), Prp ({{ $perempuan }})</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <!-- KOMPOSISI PERSENTASE KHUSUS PORTRAIT -->
                <th rowspan="2" style="width: 3%;">No</th>
                <th rowspan="2" style="width: 8%;">NISN</th>
                <th rowspan="2" style="width: 25%;">Nama Lengkap</th>
                <th rowspan="2" style="width: 3%;">L/P</th>
                <th colspan="31">Tanggal Pertemuan</th>
                <th colspan="3">Ket</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 31; $i++)
                    <!-- 34 Kotak dibagi sisa ruang = 1.79% per kotak -->
                    <th style="width: 1.79%;">{{ $i }}</th>
                @endfor
                <th style="width: 1.79%;">S</th>
                <th style="width: 1.79%;">I</th>
                <th style="width: 1.79%;">A</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($anggota as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $item->siswa->nisn ?? '-' }}</td>
                    <td class="text-left nama-siswa">{{ $item->siswa->nama_lengkap }}</td>
                    <td class="text-center">{{ $item->siswa->jenis_kelamin == 'Laki-Laki' || $item->siswa->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</td>
                    
                    @for($i = 1; $i <= 31; $i++)
                        <td></td>
                    @endfor
                    
                    <td></td><td></td><td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="38" class="text-center" style="padding: 10px;">Belum ada data anggota.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer clearfix">
        <div class="ttd">
            <p>Cibitung, .................................. {{ date('Y') }}</p>
            <p>Wali Pembimbing,</p>
            <br><br><br>
            <p><strong><u>{{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '.....................................' }}</u></strong></p>
            <p>NIP. {{ $kelas->waliKelas ? $kelas->waliKelas->nip : '-' }}</p>
        </div>
    </div>

</body>
</html>