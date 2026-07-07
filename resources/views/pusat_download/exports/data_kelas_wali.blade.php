<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Anggota {{ $kelas->nama_kelas }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        
        .info-table { width: 100%; margin-bottom: 15px; font-weight: bold; }
        .info-table td { padding: 3px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px 8px; }
        .data-table th { background-color: #f0f0f0; text-align: center; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        .footer { margin-top: 30px; width: 100%; }
        .ttd { float: right; width: 300px; text-align: center; }
        
        /* Clearfix untuk mengatasi float margin */
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="header">
        <h2>DAFTAR ANGGOTA KELOMPOK WALI / BIMBINGAN</h2>
        <h3>{{ $nama_sekolah }}</h3>
        <p>Tahun Ajaran {{ $tahun_ajaran }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="18%">Nama Kelompok</td>
            <td width="32%">: {{ $kelas->nama_kelas }} (Grade {{ $kelas->tingkat }})</td>
            <td width="18%">Pembimbing / Wali</td>
            <td width="32%">: {{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '-' }}</td>
        </tr>
        <tr>
            <td>Total Laki-laki</td>
            <td>: {{ $laki_laki }} Siswa</td>
            <td>Total Perempuan</td>
            <td>: {{ $perempuan }} Siswa</td>
        </tr>
    </table>

    <!-- Sesuai permintaan: Hanya No, NISN, NIPD, Nama Lengkap, dan Jenis Kelamin -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NISN</th>
                <th width="15%">NIPD / NIK</th>
                <th width="50%">Nama Lengkap</th>
                <th width="15%">L/P</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($anggota as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $item->siswa->nisn ?? '-' }}</td>
                    <td class="text-center">{{ $item->siswa->nik ?? '-' }}</td>
                    <td class="text-left">{{ $item->siswa->nama_lengkap }}</td>
                    <td class="text-center">{{ $item->siswa->jenis_kelamin == 'Laki-Laki' || $item->siswa->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data anggota yang terdaftar di kelompok ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer clearfix">
        <div class="ttd">
            <p>Cibitung, {{ date('d F Y') }}</p>
            <p>Wali Pembimbing,</p>
            <br><br><br><br>
            <p><strong><u>{{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '.....................................' }}</u></strong></p>
            <p>NIP. {{ $kelas->waliKelas ? $kelas->waliKelas->nip : '-' }}</p>
        </div>
    </div>

</body>
</html>