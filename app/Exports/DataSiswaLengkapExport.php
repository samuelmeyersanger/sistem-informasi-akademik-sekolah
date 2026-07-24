<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DataSiswaLengkapExport implements FromView, WithColumnWidths, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('pusat_download.exports.data_siswa_lengkap', $this->data);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 4,   // No
            'B' => 12,  // NISN
            'C' => 12,  // NIPD
            'D' => 28,  // Nama Lengkap
            'E' => 5,   // L/P
            'F' => 15,  // Tempat Lahir
            'G' => 12,  // Tanggal Lahir
            'H' => 10,  // Agama
            'I' => 15,  // Kelas
            'J' => 8,   // Tingkat
            'K' => 18,  // Asal Sekolah
            'L' => 20,  // Nama Ayah
            'M' => 20,  // Nama Ibu
            'N' => 15,  // No HP
            'O' => 40,  // Alamat Lengkap
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Ratakan ke tengah secara vertikal & Aktifkan Wrap Text
        $sheet->getStyle('A1:O2000')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:O2000')->getAlignment()->setWrapText(true);

        $jumlahSiswa = count($this->data['siswa']);
        $barisTerakhir = $jumlahSiswa > 0 ? 5 + $jumlahSiswa : 6;

        // --- BORDERS & COLORS ---
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        // Border Tabel Data
        $sheet->getStyle('A4:O' . $barisTerakhir)->applyFromArray($styleBorder);

        // Styling Kop Atas
        $sheet->getStyle('A4:O4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
        $sheet->getStyle('A4:O4')->getFont()->setBold(true);

        // Rata Kiri untuk kolom teks panjang
        if ($jumlahSiswa > 0) {
            $sheet->getStyle('D5:D' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Nama
            $sheet->getStyle('L5:M' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Ortu
            $sheet->getStyle('O5:O' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Alamat
        }

        return [];
    }
}
