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
class AbsensiKelasExport implements FromView, WithColumnWidths, WithStyles
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view('pusat_download.exports.absensi', $this->data);
    }
    public function columnWidths(): array
    {
        return [
            'A' => 4,   // No
            'B' => 14,  // NISN
            'C' => 32,  // Nama
            'D' => 5,   // L/P
            'E' => 4, 'F' => 4, 'G' => 4, 'H' => 4, 'I' => 4,
            'J' => 4, 'K' => 4, 'L' => 4, 'M' => 4, 'N' => 4,
            'O' => 4, 'P' => 4, 'Q' => 4, 'R' => 4, 'S' => 4,
            'T' => 4, 'U' => 4, 'V' => 4, 'W' => 4, 'X' => 4,
            'Y' => 4, 'Z' => 4, 'AA' => 4,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Ratakan ke tengah secara vertikal & Aktifkan Wrap Text (Turun ke bawah jika kepanjangan)
        $sheet->getStyle('A1:AA100')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:AA100')->getAlignment()->setWrapText(true);
        $jumlahSiswa = count($this->data['anggota']);
        $barisTerakhir = $jumlahSiswa > 0 ? 6 + $jumlahSiswa : 7;
        // --- ATUR TINGGI BARIS AGAR PROPOSIONAL SEPERTI KERTAS ABSEN ---
        // Baris Kop Header
        $sheet->getRowDimension(1)->setRowHeight(15);
        $sheet->getRowDimension(2)->setRowHeight(15);
        $sheet->getRowDimension(3)->setRowHeight(15);
        
        // Baris Header Tabel
        $sheet->getRowDimension(5)->setRowHeight(20);
        $sheet->getRowDimension(6)->setRowHeight(20);
        // Baris Data Siswa (Dibuat tinggi 22 agar lega)
        for ($i = 7; $i <= $barisTerakhir; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(22);
        }
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
        $sheet->getStyle('A5:AA' . $barisTerakhir)->applyFromArray($styleBorder);
        // Styling Kop Atas (Wali Kelas & Kelas & Tahun Ajaran)
        $sheet->getStyle('A2:F3')->applyFromArray($styleBorder);
        $sheet->getStyle('A2:F3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        $sheet->getStyle('J1:N3')->applyFromArray($styleBorder);
        $sheet->getStyle('J1:N3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        $sheet->getStyle('P3:AA3')->applyFromArray($styleBorder);
        $sheet->getStyle('P3:AA3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        // Styling Header Tabel (Abu-abu & Tebal)
        $sheet->getStyle('A5:AA6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
        $sheet->getStyle('A5:AA6')->getFont()->setBold(true);
        // Rata Kiri untuk Nama Siswa
        if ($jumlahSiswa > 0) {
            $sheet->getStyle('C7:C' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        return [];
    }
}