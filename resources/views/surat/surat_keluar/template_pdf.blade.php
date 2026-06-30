<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Resmi Sekolah</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; padding: 10px; }
        .kop-container { width: 100%; text-align: center; margin-bottom: 25px; }
        .gambar-kop { width: 100%; max-height: 140px; object-fit: contain; }
        .garis-pembatas { border-bottom: 3px double #000; margin-top: 5px; }
        .tabel-atribut { width: 100%; margin-bottom: 25px; }
        .tabel-atribut td { vertical-align: top; }
        .isi-surat { text-align: justify; margin-bottom: 40px; min-height: 200px; }
        .ttd-section { float: right; width: 280px; text-align: center; }
        .space-ttd { height: 90px; position: relative; }
        .img-ttd { height: 75px; z-index: 1; }
        .img-stempel { height: 85px; position: absolute; left: 25px; top: -5px; opacity: 0.85; z-index: 2; }
        
        /* Gaya Khusus Tabel Lampiran */
        .tabel-lampiran { width: 100%; border-collapse: collapse; font-size: 10pt; margin-top: 15px; }
        .tabel-lampiran th { border: 1px solid #000; padding: 6px; background-color: #f2f2f2; font-weight: bold; }
        .tabel-lampiran td { border: 1px solid #000; padding: 5px; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="kop-container">
        @if($pengaturan && $pengaturan->kop_surat)
            <img src="{{ public_path('storage/' . $pengaturan->kop_surat) }}" class="gambar-kop">
        @else
            <h2 style="margin:0; text-transform:uppercase; font-size: 14pt;">PEMERINTAH KABUPATEN BEKASI</h2>
            <h1 style="margin:0; text-transform:uppercase; font-size: 16pt;">SMK NEGERI 1 INDONESIA</h1>
            <div class="garis-pembatas"></div>
        @endif
    </div>

    <table class="tabel-atribut">
        <tr><td style="width: 12%;">Nomor</td><td style="width: 2%;">:</td><td style="width: 86%; font-family: monospace;">{{ $surat->nomor_surat }}</td></tr>
        <tr><td>Sifat</td><td>:</td><td>Biasa</td></tr>
        <tr><td>Perihal</td><td>:</td><td><strong>{{ $surat->perihal }}</strong></td></tr>
    </table>

    <div style="margin-bottom: 20px;">Kepada Yth.<br><strong>{{ $surat->tujuan_surat }}</strong><br>di Tempat</div>

    <div class="isi-surat">
        {!! nl2br(e($surat->isi_surat)) !!}
    </div>

    <div class="ttd-section">
        <p style="margin-bottom: 5px;">Kabupaten Bekasi, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y') }}</p>
        <p style="margin: 0;">Kepala Sekolah,</p>
        <div class="space-ttd">
            @if($surat->metode_ttd == 'Digital' && $pengaturan)
                @if($pengaturan->ttd_dan_stempel)
                    <img src="{{ public_path('storage/' . $pengaturan->ttd_dan_stempel) }}" class="img-ttd">
                @elseif($pengaturan->ttd_kepala_sekolah)
                    <img src="{{ public_path('storage/' . $pengaturan->ttd_kepala_sekolah) }}" class="img-ttd">
                    @if($pengaturan->stempel_sekolah) <img src="{{ public_path('storage/' . $pengaturan->stempel_sekolah) }}" class="img-stempel"> @endif
                @endif
            @else
                <div style="height: 75px;"></div>
            @endif
        </div>
        <p style="text-decoration: underline; font-weight: bold; margin: 0;">{{ $surat->penandatangan->name }}</p>
        <p style="margin: 0; font-size: 11pt;">NIP. 198503112010011002</p>
    </div>
    <div class="clear"></div>


    @if($surat->header_1)
        
        <div style="page-break-before: always;"></div>

        <table style="width: 100%; font-size: 11pt; margin-bottom: 15px;">
            <tr><td style="width: 15%;">Lampiran</td><td style="width: 2%;">:</td><td>{{ $surat->perihal }}</td></tr>
            <tr><td>Nomor</td><td>:</td><td style="font-family: monospace;">{{ $surat->nomor_surat }}</td></tr>
        </table>

        <h3 style="text-align: center; font-size: 12pt; text-transform: uppercase;">DAFTAR LAMPIRAN DATA</h3>

        <table class="tabel-lampiran">
            <thead>
                <tr>
                    @if($surat->header_1) <th>{{ $surat->header_1 }}</th> @endif
                    @if($surat->header_2) <th>{{ $surat->header_2 }}</th> @endif
                    @if($surat->header_3) <th>{{ $surat->header_3 }}</th> @endif
                    @if($surat->header_4) <th>{{ $surat->header_4 }}</th> @endif
                    @if($surat->header_5) <th>{{ $surat->header_5 }}</th> @endif
                </tr>
            </thead>
            <tbody>
                @foreach($surat->lampiran as $row)
                <tr>
                    @if($surat->header_1) <td>{{ $row->kolom_1 }}</td> @endif
                    @if($surat->header_2) <td>{{ $row->kolom_2 }}</td> @endif
                    @if($surat->header_3) <td>{{ $row->kolom_3 }}</td> @endif
                    @if($surat->header_4) <td>{{ $row->kolom_4 }}</td> @endif
                    @if($surat->header_5) <td>{{ $row->kolom_5 }}</td> @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 35px; float: right; width: 250px; text-align: center;">
            <p>Kepala Sekolah,</p>
            <div style="height: 60px;"></div>
            <p style="text-decoration: underline; font-weight: bold;">{{ $surat->penandatangan->name }}</p>
        </div>
        <div class="clear"></div>
    @endif

</body>
</html>