<!DOCTYPE html>
<html>
<head>
    <title>Daftar Hadir</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .table-main { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table-main th, .table-main td { border: 1px solid #000; padding: 5px; text-align: center; }
        .table-main .text-left { text-align: left; }
        .bg-gray { background-color: #f2f2f2; }
        .bg-yellow { background-color: #fff2cc; }
        .header-table { width: 100%; border: none; font-weight: bold; margin-bottom: 20px;}
        .header-table td { padding: 5px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="30%" style="border: 1px solid #000;" align="center">
                <div style="font-size: 10px;">WALI KELAS</div>
                <div class="bg-yellow" style="padding: 5px; margin-top: 5px; border: 1px solid #000;">
                    {{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : 'Belum Ada Wali Kelas' }}
                </div>
            </td>
            <td width="5%"></td>
            <td width="30%" align="center">
                <div class="bg-yellow" style="font-size: 24px; border: 1px solid #000; padding: 5px;">{{ $kelas->nama_kelas }}</div>
            </td>
            <td width="5%"></td>
            <td width="30%" align="center" style="font-size: 14px;">
                DAFTAR HADIR <br>
                {{ $nama_sekolah }} <br>
                <div class="bg-yellow" style="margin-top: 5px; padding: 3px;">TAHUN AJARAN {{ $tahun_ajaran }}</div>
            </td>
        </tr>
    </table>
    <table class="table-main">
        <thead>
            <tr class="bg-gray">
                <th rowspan="2" width="3%">No</th>
                <th rowspan="2" width="12%">NISN</th>
                <th rowspan="2" width="25%">Nama</th>
                <th rowspan="2" width="3%">L/P</th>
                <th colspan="20">Tanggal dan Bulan</th>
                <th colspan="3">Jumlah</th>
            </tr>
            <tr class="bg-gray">
                <!-- 20 kolom kotak kosong -->
                @for($i=1; $i<=20; $i++)
                    <th width="2%"></th>
                @endfor
                <th width="3%">S</th>
                <th width="3%">I</th>
                <th width="3%">A</th>
            </tr>
        </thead>
        <tbody>
            @forelse($anggota as $index => $item)
                <tr>
                    <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $item->siswa->nisn ?? '-' }}</td>
                    <td class="text-left">{{ $item->siswa->nama_lengkap }}</td>
                    <td>{{ $item->siswa->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</td>
                    @for($i=1; $i<=20; $i++)
                        <td></td>
                    @endfor
                    <td></td><td></td><td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="27">Tidak ada data siswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <table style="width: 100%; margin-top: 20px; font-size: 12px; font-weight: bold;">
        <tr>
            <td width="40%"></td>
            <td width="20%" align="center">
                JUMLAH<br>
                Laki-laki &nbsp;&nbsp;&nbsp; {{ $laki_laki }}<br>
                Perempuan &nbsp; {{ $perempuan }}
            </td>
            <td width="40%"></td>
        </tr>
    </table>
</body>
</html>