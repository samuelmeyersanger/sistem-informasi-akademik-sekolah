<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Hasil SKM</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header h3 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI HASIL SURVEY KEPUASAN MASYARAKAT (SKM)</h2>
        <h3>{{ $nama_sekolah }}</h3>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">No</th>
                <th rowspan="2">Waktu Isi</th>
                <th rowspan="2">Layanan yang Dinilai</th>
                <th colspan="4">Profil Responden</th>
                <th colspan="{{ $unsurs->count() }}">Nilai Unsur (1-4)</th>
                <th rowspan="2">Saran / Masukan</th>
            </tr>
            <tr>
                <th>Umur</th>
                <th>L/P</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                @foreach($unsurs as $u)
                    <th title="{{ $u->pertanyaan }}">{{ $u->kode_unsur }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($respondens as $index => $responden)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $responden->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $responden->layanan ? $responden->layanan->nama_layanan : '-' }}</td>
                    <td class="text-center">{{ $responden->umur ?? '-' }}</td>
                    <td class="text-center">{{ $responden->jenis_kelamin ?? '-' }}</td>
                    <td class="text-center">{{ $responden->pendidikan_terakhir ?? '-' }}</td>
                    <td>{{ $responden->pekerjaan ?? '-' }}</td>
                    
                    {{-- Render Nilai Berdasarkan Unsur --}}
                    @foreach($unsurs as $u)
                        @php
                            // Cari jawaban untuk unsur ini
                            $jawaban = $responden->jawaban->where('unsur_id', $u->id)->first();
                        @endphp
                        <td class="text-center font-bold">
                            {{ $jawaban ? $jawaban->nilai : '-' }}
                        </td>
                    @endforeach
                    
                    <td>{{ $responden->saran_masukan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 8 + $unsurs->count() }}" class="text-center" style="padding: 20px;">Belum ada data responden survei.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 11px;">
        <strong>Keterangan Skala Nilai:</strong><br>
        1 = Buruk &nbsp;&nbsp;|&nbsp;&nbsp; 2 = Cukup &nbsp;&nbsp;|&nbsp;&nbsp; 3 = Baik &nbsp;&nbsp;|&nbsp;&nbsp; 4 = Sangat Baik<br><br>
        <em>*Tabel ini dapat langsung diblok, di-copy, lalu di-paste (Ctrl+V) ke Microsoft Excel untuk diolah lebih lanjut.</em>
    </div>

</body>
</html>