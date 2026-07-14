<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Jumlah Siswa</title>
    <style>
        /* Mengatur ukuran kertas tegak (Portrait) */
        @page { size: portrait; margin: 1cm; }
        
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3, .header p { margin: 2px 0; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 7px; }
        .data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        
        .subtotal { background-color: #e8e8e8; font-weight: bold; }
        .grandtotal { background-color: #d0d0d0; font-weight: bold; font-size: 13px; color: #111; }

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
        <h2>REKAPITULASI JUMLAH SISWA PER KELAS</h2>
        <h3>{{ $nama_sekolah }}</h3>
        <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
    </div>

    @php
        $groupedKelas = $rekap_kelas->groupBy('tingkat')->sortKeys();
        
        // Penampung Grand Total Seluruh Sekolah
        $grandTotal = 0;
        $grandTotalLaki = 0;
        $grandTotalPerempuan = 0;
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Ruang Kelas</th>
                <th width="15%">Grade/Tingkat</th>
                <th width="15%">Laki-laki (L)</th>
                <th width="15%">Perempuan (P)</th>
                <th width="15%">Total Siswa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groupedKelas as $tingkat => $kelas_list)
                @php 
                    $no = 1; 
                    // Penampung Subtotal Per Tingkat (Contoh: Total Tingkat 7)
                    $subTotal = 0;
                    $subTotalL = 0;
                    $subTotalP = 0;
                @endphp
                
                @foreach($kelas_list as $kelas)
                    @php 
                        // Menambahkan angka ke Subtotal & Grand Total
                        $subTotal += $kelas->anggota_kelas_count; 
                        $subTotalL += $kelas->laki_laki_count;
                        $subTotalP += $kelas->perempuan_count;
                        
                        $grandTotal += $kelas->anggota_kelas_count;
                        $grandTotalLaki += $kelas->laki_laki_count;
                        $grandTotalPerempuan += $kelas->perempuan_count;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-left font-bold">Kelas {{ $kelas->nama_kelas }}</td>
                        <td class="text-center">Tingkat {{ $tingkat }}</td>
                        <td class="text-center">{{ $kelas->laki_laki_count }}</td>
                        <td class="text-center">{{ $kelas->perempuan_count }}</td>
                        <td class="text-center" style="font-weight: bold; background-color:#fafafa;">{{ $kelas->anggota_kelas_count }}</td>
                    </tr>
                @endforeach
                
                <!-- Baris Subtotal per Tingkat -->
                <tr class="subtotal">
                    <td colspan="3" class="text-right">Subtotal Tingkat {{ $tingkat }} :</td>
                    <td class="text-center">{{ $subTotalL }}</td>
                    <td class="text-center">{{ $subTotalP }}</td>
                    <td class="text-center">{{ $subTotal }} Siswa</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px;">Belum ada data ruang kelas beserta siswanya.</td>
                </tr>
            @endforelse
            
            <!-- Baris Grand Total Seluruh Sekolah -->
            @if($rekap_kelas->count() > 0)
                <tr class="grandtotal">
                    <td colspan="3" class="text-right" style="padding: 12px;">GRAND TOTAL KESELURUHAN SEKOLAH :</td>
                    <td class="text-center" style="padding: 12px;">{{ $grandTotalLaki }}</td>
                    <td class="text-center" style="padding: 12px;">{{ $grandTotalPerempuan }}</td>
                    <td class="text-center" style="padding: 12px;">{{ $grandTotal }} Siswa</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>
</html>