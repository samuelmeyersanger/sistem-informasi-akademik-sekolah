<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Pelajaran Global</title>
    <style>
        /* KUNCI LANDSCAPE: Memaksa orientasi kertas menjadi mendatar saat di-print */
        @page { size: 13in 8.5in landscape; margin: 1cm; }
        
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3 { margin: 2px 0; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; font-size: 11px; }
        
        .kelas-title { font-weight: bold; font-size: 12px; text-align: center; background-color: #fafafa; }
        .jam-item { border-bottom: 1px dotted #ccc; margin-bottom: 3px; padding-bottom: 3px; }
        .jam-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        
        .mapel-name { font-weight: bold; color: #111; }
        .guru-code { color: #d92626; font-weight: bold; font-size: 9px; }

        /* Menyembunyikan tombol merah saat kertas dicetak */
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

    <div class="header">
        <h2>JADWAL PELAJARAN GLOBAL (SELURUH KELAS)</h2>
        <h3>{{ $nama_sekolah }}</h3>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    @php
        // Mengelompokkan data jadwal berdasarkan Kelas
        $groupedByKelas = $jadwal_semua->groupBy(function($j) {
            return $j->kelas ? $j->kelas->tingkat . ' - ' . $j->kelas->nama_kelas : 'Tanpa Kelas';
        })->sortKeys();
        
        $hari_aktif = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th width="8%">Ruang Kelas</th>
                @foreach($hari_aktif as $hari)
                    <th width="18%">{{ $hari }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($groupedByKelas as $namaKelas => $jadwals)
                <tr>
                    <td class="kelas-title">{{ $namaKelas }}</td>
                    
                    @foreach($hari_aktif as $hari)
                        <td>
                            @php
                                $jadwalHariIni = $jadwals->where('hari', $hari)->sortBy(function($j) {
                                    return $j->waktuKbm ? $j->waktuKbm->jam_ke : 99;
                                });
                            @endphp

                            @forelse($jadwalHariIni as $j)
                                <div class="jam-item">
                                    <span>Jam ke-{{ $j->waktuKbm ? $j->waktuKbm->jam_ke : '-' }}:</span><br>
                                    
                                    @php
                                        $nama_mapel = '-';
                                        if($j->mataPelajaran) {
                                            $nama_mapel = $j->mataPelajaran->nama_pelajaran;
                                        } elseif($j->kodeGuru && $j->kodeGuru->mataPelajarans->count() > 0) {
                                            $nama_mapel = $j->kodeGuru->mataPelajarans->first()->nama_pelajaran;
                                        }
                                    @endphp

                                    <span class="mapel-name">{{ $nama_mapel }}</span> 
                                    <span class="guru-code">[{{ $j->kodeGuru ? $j->kodeGuru->kode_guru : '-' }}]</span>
                                </div>
                            @empty
                                <div style="text-align: center; color: #999; font-style: italic; margin-top: 10px;">Kosong</div>
                            @endforelse
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Belum ada jadwal pelajaran yang diinput ke dalam sistem.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>