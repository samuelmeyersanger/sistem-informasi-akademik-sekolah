<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Kelompok Wali</title>
    <style>
        /* Margin 1cm agar area cetak maksimal */
        @page { margin: 1cm; }
        
        body { font-family: Arial, sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        
        .info-table { width: 100%; margin-bottom: 10px; font-weight: bold; font-size: 10px; }
        .info-table td { padding: 2px; }
        
        /* FIX: table-layout fixed memaksa tabel patuh agar tidak terpotong (meluber) */
        .data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 3px 1px; overflow: hidden; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-size: 8px; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; padding-left: 4px !important; }
        
        /* Mencegah nama siswa membuat tabel melar */
        .nama-siswa { white-space: nowrap; overflow: hidden; }
        
        .footer { margin-top: 15px; width: 100%; font-size: 10px;}
        .ttd { float: right; width: 250px; text-align: center; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="header">
        <h2>DAFTAR HADIR KELOMPOK WALI / BIMBINGAN</h2>
        <h3>{{ $nama_sekolah }}</h3>
        <p>Tahun Ajaran {{ $tahun_ajaran ?? '-' }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama Kelompok</td>
            <td width="35%">: {{ $kelas->nama_kelas }} (Grade {{ $kelas->tingkat }})</td>
            <td width="15%">Bulan</td>
            <td width="35%">: .......................................</td>
        </tr>
        <tr>
            <td>Pembimbing / Wali</td>
            <td>: {{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '-' }}</td>
            <td>Jumlah Siswa</td>
            <td>: Laki-laki ({{ $laki_laki }}), Perempuan ({{ $perempuan }})</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <!-- Persentase lebar diatur kaku agar sisa 70% muat untuk 34 kolom -->
                <th rowspan="2" style="width: 3%;">No</th>
                <th rowspan="2" style="width: 7%;">NISN</th>
                <th rowspan="2" style="width: 17%;">Nama Lengkap</th>
                <th rowspan="2" style="width: 3%;">L/P</th>
                <th colspan="31">Tanggal Pertemuan</th>
                <th colspan="3">Ket</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 31; $i++)
                    <th style="width: 2.05%;">{{ $i }}</th>
                @endfor
                <th style="width: 2.05%;">S</th>
                <th style="width: 2.05%;">I</th>
                <th style="width: 2.05%;">A</th>
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
                    <td colspan="38" class="text-center" style="padding: 10px;">Belum ada data anggota yang terdaftar di kelompok ini.</td>
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