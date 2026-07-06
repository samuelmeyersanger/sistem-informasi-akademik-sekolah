<!DOCTYPE html>
<html>
<head>
    <title>Daftar Hadir</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px; }
        .border-all th, .border-all td { border: 1px solid #000; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-bold { font-weight: bold; }
        .bg-gray { background-color: #f2f2f2; }
        .bg-yellow { background-color: #fff2cc; }
        .valign-middle { vertical-align: middle; }
    </style>
</head>
<body>
    <table>
        <!-- ================= HEADER AREA ================= -->
        <!-- Baris 1 -->
        <tr>
            <td colspan="6" class="text-center text-bold" style="font-size: 9px;">WALI KELAS</td>
            <td colspan="3"></td>
            <!-- Kotak Kelas (Misal: 8,1) dibuat membentang 3 baris ke bawah -->
            <td colspan="5" rowspan="3" class="text-center text-bold bg-yellow valign-middle" style="font-size: 24px; border: 1px solid #000;">
                {{ $kelas->nama_kelas }}
            </td>
            <td colspan="1"></td>
            <td colspan="12" class="text-center text-bold" style="font-size: 13px;">DAFTAR HADIR</td>
        </tr>
        <!-- Baris 2 -->
        <tr>
            <!-- Kotak Nama Wali dibuat membentang 2 baris ke bawah -->
            <td colspan="6" rowspan="2" class="text-center text-bold bg-yellow valign-middle" style="border: 1px solid #000;">
                {{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : 'Belum Ada Wali Kelas' }}
            </td>
            <td colspan="3"></td>
            <!-- Kolom J sampai N sudah diisi rowspan dari Baris 1 -->
            <td colspan="1"></td>
            <td colspan="12" class="text-center text-bold" style="font-size: 13px;">
                {{ $nama_sekolah }}
            </td>
        </tr>
        <!-- Baris 3 -->
        <tr>
            <!-- Kolom A sampai F sudah diisi rowspan dari Baris 2 -->
            <td colspan="3"></td>
            <!-- Kolom J sampai N sudah diisi rowspan dari Baris 1 -->
            <td colspan="1"></td>
            <td colspan="12" class="text-center text-bold bg-yellow" style="border: 1px solid #000; padding: 2px;">
                TAHUN AJARAN {{ $tahun_ajaran }}
            </td>
        </tr>
        
        <!-- SPACING -->
        <tr><td colspan="27" style="height: 15px;"></td></tr>
        <!-- ================= DATA AREA ================= -->
        <tbody class="border-all text-center">
            <tr class="bg-gray text-bold">
                <th rowspan="2" width="3%">No</th>
                <th rowspan="2" width="12%">NISN</th>
                <th rowspan="2" width="25%">Nama</th>
                <th rowspan="2" width="4%">L/P</th>
                <th colspan="20">Tanggal dan Bulan</th>
                <th colspan="3">Jumlah</th>
            </tr>
            <tr class="bg-gray text-bold">
                @for($i=1; $i<=20; $i++)
                    <th width="2%"></th>
                @endfor
                <th width="3%">S</th>
                <th width="3%">I</th>
                <th width="3%">A</th>
            </tr>
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
                    <td colspan="27" style="padding: 10px;">Tidak ada data siswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
        <!-- SPACING -->
        <tr><td colspan="27" style="height: 15px;"></td></tr>
        <!-- ================= FOOTER AREA ================= -->
        <tr>
            <td colspan="12"></td>
            <td colspan="6" class="text-center text-bold">
                JUMLAH<br>
                Laki-laki &nbsp;&nbsp;&nbsp; {{ $laki_laki }}<br>
                Perempuan &nbsp; {{ $perempuan }}
            </td>
            <td colspan="9"></td>
        </tr>
    </table>
</body>
</html>