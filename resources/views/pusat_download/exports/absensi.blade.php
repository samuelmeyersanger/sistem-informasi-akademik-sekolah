<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Kelas</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: Arial, sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        
        .info-table { width: 100%; margin-bottom: 10px; font-weight: bold; font-size: 10px; }
        .info-table td { padding: 2px; }
        
        /* Layout fixed dan penyesuaian font agar nama muat utuh */
        .data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 3px 1px; overflow: hidden; font-size: 8.5px; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-size: 8px; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; padding-left: 4px !important; }
        
        .nama-siswa { 
            white-space: normal; /* Mengizinkan teks turun ke baris baru */
            word-wrap: break-word; 
            line-height: 1.1; /* Agar jarak antar baris atas-bawah tidak terlalu renggang */
        }
        
        .footer { margin-top: 15px; width: 100%; font-size: 10px;}
        .ttd { float: right; width: 250px; text-align: center; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    @php
        $logoSetting = \DB::table('pengaturan_logo')->first();
        // Menggunakan public_path agar DomPDF bisa membaca file dari dalam server lokal
        $logoPemda = $logoSetting && $logoSetting->logo_pemda ? public_path('storage/' . $logoSetting->logo_pemda) : null;
        $logoSekolah = $logoSetting && $logoSetting->logo_sekolah ? public_path('storage/' . $logoSetting->logo_sekolah) : null;
    @endphp
    <table style="width: 100%; border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 5px;">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: middle;">
                @if($logoPemda && file_exists($logoPemda))
                    <img src="{{ $logoPemda }}" style="max-height: 65px; max-width: 70px; object-fit: contain;">
                @endif
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle;">
                <h2 style="margin: 2px 0;">DAFTAR HADIR SISWA KELAS {{ $kelas->nama_kelas }}</h2>
                <h3 style="margin: 2px 0;">{{ $nama_sekolah }}</h3>
                <p style="margin: 2px 0; font-size: 10px;">Tahun Ajaran {{ $tahun_ajaran ?? '-' }}</p>
                <p style="margin: 2px 0; font-size: 10px;">Semester {{ $semester ?? '-' }}</p>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: middle;">
                @if($logoSekolah && file_exists($logoSekolah))
                    <img src="{{ $logoSekolah }}" style="max-height: 65px; max-width: 70px; object-fit: contain;">
                @endif
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="15%">Mata Pelajaran</td>
            <td width="35%">: .......................................</td>
            <td width="15%">Bulan</td>
            <td width="35%">: .......................................</td>
        </tr>
        <tr>
            <td>Guru Pengampu</td>
            <td>: .......................................</td>
            <td>Wali Kelas</td>
            <td>: {{ $kelas->waliKelas ? $kelas->waliKelas->nama_lengkap : '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 2%;">No</th>
                <th rowspan="2" style="width: 7%;">NISN</th>
                <th rowspan="2" style="width: 21%;">Nama Lengkap</th>
                <th rowspan="2" style="width: 2%;">L/P</th>
                <th colspan="31">Tanggal Pertemuan</th>
                <th colspan="3">Ket</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 31; $i++)
                    <th style="width: 2%;">{{ $i }}</th>
                @endfor
                <th style="width: 2%;">S</th>
                <th style="width: 2%;">I</th>
                <th style="width: 2%;">A</th>
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
                    <td colspan="38" class="text-center" style="padding: 10px;">Belum ada data anggota yang terdaftar di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer clearfix">
        <div class="ttd">
            <p>Cibitung, .................................. {{ date('Y') }}</p>
            <p>Guru Pengampu,</p>
            <br><br><br>
            <p><strong><u>.....................................</u></strong></p>
            <p>NIP. ............................</p>
        </div>
    </div>

</body>
</html>