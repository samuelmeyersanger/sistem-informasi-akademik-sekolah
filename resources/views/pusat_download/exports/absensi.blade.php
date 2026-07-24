<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Kelas</title>
    <style>
        @page { margin: 1cm; size: 8.5in 13in portrait; }
        body { font-family: Arial, sans-serif; font-size: 9px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        
        .info-table { width: 100%; margin-bottom: 10px; font-weight: bold; font-size: 10px; }
        .info-table td { padding: 2px; }
        
        .data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 3px 1px; overflow: hidden; font-size: 8.5px; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-size: 8px; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; padding-left: 4px !important; }
        
        .nama-siswa { white-space: normal; word-wrap: break-word; line-height: 1.1; }
        
        .footer { margin-top: 15px; width: 100%; font-size: 10px;}
        .ttd { float: right; width: 250px; text-align: center; }
        .clearfix::after { content: ""; clear: both; display: table; }

        /* Menyembunyikan tombol saat dicetak */
        @media print { .btn-print { display: none !important; } }
    </style>
</head>
<body>

    <!-- Tombol Bantuan -->
    <div class="btn-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 18px; background: #dc2626; color: white; border: none; cursor: pointer; border-radius: 6px; font-weight: bold; font-size: 14px;">
            📄 Cetak / Simpan PDF
        </button>
    </div>

    @php
        $logoSetting = \DB::table('pengaturan_logo')->first();
        // Pakai asset() saja karena ini HTML langsung di browser
        $logoPemda = $logoSetting && $logoSetting->logo_pemda ? asset('storage/' . $logoSetting->logo_pemda) : null;
        $logoSekolah = $logoSetting && $logoSetting->logo_sekolah ? asset('storage/' . $logoSetting->logo_sekolah) : null;
    @endphp
    
    <table style="width: 100%; border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 5px;">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: middle;">
                @if($logoPemda)
                    <img src="{{ $logoPemda }}" style="max-height: 65px; max-width: 70px; object-fit: contain;">
                @endif
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle;">
                <h2 style="margin: 2px 0;">DAFTAR HADIR SISWA KELAS {{ $kelas->nama_kelas }}</h2>
                <h3 style="margin: 2px 0;">{{ $nama_sekolah }}</h3>
                <p style="margin: 2px 0; font-size: 10px;">Tahun Ajaran {{ $tahun_ajaran ?? '-' }}</p>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: middle;">
                @if($logoSekolah)
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
                <th rowspan="2" style="width: 17%;">Nama Lengkap</th>
                <th rowspan="2" style="width: 2%;">L/P</th>
                <th rowspan="2" style="width: 4%;">Gaya<br>Belajar</th>
                <th colspan="16">Tanggal dan Bulan</th>
                <th colspan="3">Ket</th>
            </tr>
            <tr>
                @for($i = 1; $i <= 16; $i++)
                    <th style="width: 3.7%;"></th>
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
                    <td class="text-center" style="font-size: 7px; font-weight: bold;">
                        @php 
                            $gb = $item->siswa->hasilGayaBelajar->gaya_dominan ?? '-';
                            if ($gb != '-') {
                                $gb = strtoupper(substr($gb, 0, 1));
                            }
                        @endphp
                        {{ $gb }}
                    </td>
                    
                    @for($i = 1; $i <= 16; $i++)
                        <td></td>
                    @endfor
                    
                    <td></td><td></td><td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="24" class="text-center" style="padding: 10px;">Belum ada data anggota.</td>
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