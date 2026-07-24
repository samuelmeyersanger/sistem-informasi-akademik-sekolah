<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Siswa Lengkap</title>
    <style>
        /* Menggunakan landscape (Tidur) untuk memuat lebih banyak kolom */
        @page { margin: 1cm; size: 13in 8.5in landscape; }
        body { font-family: Arial, sans-serif; font-size: 8px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        
        .data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px 2px; overflow: hidden; font-size: 7.5px; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-size: 8px; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; padding-left: 4px !important; }
        
        .nama-siswa, .alamat-siswa { white-space: normal; word-wrap: break-word; line-height: 1.1; }
        
        .footer { margin-top: 15px; width: 100%; font-size: 10px;}
        .ttd { float: right; width: 250px; text-align: center; }
        .clearfix::after { content: ""; clear: both; display: table; }

        @media print { .btn-print { display: none !important; } }
    </style>
</head>
<body>

    <div class="btn-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 18px; background: #dc2626; color: white; border: none; cursor: pointer; border-radius: 6px; font-weight: bold; font-size: 14px;">
            📄 Cetak / Simpan PDF
        </button>
    </div>

    @php
        $logoSetting = \DB::table('pengaturan_logo')->first();
        $logoPemda = $logoSetting && $logoSetting->logo_pemda ? asset('storage/' . $logoSetting->logo_pemda) : null;
        $logoSekolah = $logoSetting && $logoSetting->logo_sekolah ? asset('storage/' . $logoSetting->logo_sekolah) : null;
    @endphp
    
    <table style="width: 100%; border-bottom: 2px solid #000; margin-bottom: 15px; padding-bottom: 5px;">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: middle;">
                @if($logoPemda)
                    <img src="{{ $logoPemda }}" style="max-height: 65px; max-width: 70px; object-fit: contain;">
                @endif
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle;">
                <h2 style="margin: 2px 0;">MASTER DATA SISWA (LENGKAP)</h2>
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

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 2%;">No</th>
                <th style="width: 5%;">NISN</th>
                <th style="width: 5%;">NIPD</th>
                <th style="width: 12%;">Nama Lengkap</th>
                <th style="width: 2%;">L/P</th>
                <th style="width: 6%;">Tempat Lahir</th>
                <th style="width: 5%;">Tanggal Lahir</th>
                <th style="width: 4%;">Agama</th>
                <th style="width: 6%;">Kelas</th>
                <th style="width: 4%;">Tingkat</th>
                <th style="width: 8%;">Asal Sekolah</th>
                <th style="width: 8%;">Nama Ayah</th>
                <th style="width: 8%;">Nama Ibu</th>
                <th style="width: 7%;">No HP</th>
                <th style="width: 18%;">Alamat Lengkap</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($siswa as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ $item->nisn ?? '-' }}</td>
                    <td class="text-center">{{ $item->nipd ?? '-' }}</td>
                    <td class="text-left nama-siswa">{{ $item->nama_lengkap }}</td>
                    <td class="text-center">{{ $item->jenis_kelamin == 'Laki-Laki' || $item->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</td>
                    <td class="text-center">{{ $item->tempat_lahir ?? '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_lahir ? $item->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $item->agama ?? '-' }}</td>
                    <td class="text-center">{{ $item->kelas ? $item->kelas->nama_kelas : 'Belum Ada Kelas' }}</td>
                    <td class="text-center">{{ $item->tingkat ?? '-' }}</td>
                    <td class="text-center nama-siswa">{{ $item->asal_sekolah ?? '-' }}</td>
                    <td class="text-center nama-siswa">{{ $item->ayah_data->nama_lengkap ?? '-' }}</td>
                    <td class="text-center nama-siswa">{{ $item->ibu_data->nama_lengkap ?? '-' }}</td>
                    <td class="text-center">{{ $item->nomor_hp ?? '-' }}</td>
                    <td class="text-left alamat-siswa">
                        {{ $item->alamat_lengkap ?? '-' }} 
                        @if($item->rt || $item->rw) RT {{ $item->rt ?? '-' }}/RW {{ $item->rw ?? '-' }} @endif
                        @if($item->kelurahan_desa) Desa {{ $item->kelurahan_desa }} @endif
                        @if($item->kecamatan) Kec. {{ $item->kecamatan }} @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" class="text-center" style="padding: 10px;">Belum ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer clearfix">
        <div class="ttd">
            <p>Cibitung, .................................. {{ date('Y') }}</p>
            <p>Admin / Tata Usaha,</p>
            <br><br><br>
            <p><strong><u>.....................................</u></strong></p>
            <p>NIP. ............................</p>
        </div>
    </div>

</body>
</html>
