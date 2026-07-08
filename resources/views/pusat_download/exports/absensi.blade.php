<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Anggota {{ $kelas->nama_kelas }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; } /* Ukuran font diperkecil agar tabel muat */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        
        .info-table { width: 100%; margin-bottom: 15px; font-weight: bold; font-size: 11px; }
        .info-table td { padding: 3px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-size: 9px; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        /* Memastikan nama siswa tidak terpotong ke bawah */
        .nama-siswa { white-space: nowrap; overflow: hidden; max-width: 150px; }
        
        .footer { margin-top: 30px; width: 100%; font-size: 11px;}
        .ttd { float: right; width: 250px; text-align: center; }
        
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="header">
        <h2>DAFTAR HADIR KELOMPOK WALI / BIMBINGAN</h2>
        <h3>{{ $nama_sekolah }}</h3>
        <p>Tahun Ajaran {{ $tahun_ajaran }}</p>
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
                <th rowspan="2" width="2%">No</th>
                <th rowspan="2" width="8%">NISN</th>
                <th rowspan="2" width="18%">Nama Lengkap</th>
                <th rowspan="2" width="2%">L/P</th>
                <!-- Tanggal 1 sampai 31 -->
                <th colspan="31">Tanggal Pertemuan</th>
                <!-- Total S, I, A -->
                <th colspan="3">Total</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 31; $i++)
                    <th width="1.8%">{{ $i }}</th>
                @endfor
                <th width="2%">S</th>
                <th width="2%">I</th>
                <th width="2%">A</th>
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
                    
                    <!-- Kotak Kosong Tanggal -->
                    @for($i = 1; $i <= 31; $i++)
                        <td></td>
                    @endfor
                    
                    <!-- Kotak Kosong Total -->
                    <td></td><td></td><td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="38" class="text-center" style="padding: 15px;">Belum ada data anggota yang terdaftar di kelompok ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer clearfix">
        <div class="ttd">
            <p>Cibitung, .................................. {{ date('Y') }}</p>
            <p>Wali Pembimbing,</p>
            <br><br><br><br>
            <p><strong><u>{{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '.....................................' }}</u></strong></p>
            <p>NIP. {{ $kelas->waliKelas ? $kelas->waliKelas->nip : '-' }}</p>
        </div>
    </div>

</body>
</html>