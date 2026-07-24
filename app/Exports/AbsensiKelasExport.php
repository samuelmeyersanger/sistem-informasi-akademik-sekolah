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
            'C' => 28,  // Nama
            'D' => 4,   // L/P
            'E' => 6,   // Gaya Belajar
            'F' => 6, 'G' => 6, 'H' => 6, 'I' => 6, 'J' => 6, // 16 Kolom Kosong (sedikit lebih lebar)
            'K' => 6, 'L' => 6, 'M' => 6, 'N' => 6, 'O' => 6,
            'P' => 6, 'Q' => 6, 'R' => 6, 'S' => 6, 'T' => 6,
            'U' => 6, 
            'V' => 4, 'W' => 4, 'X' => 4, // Ket S, I, A
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // Ratakan ke tengah secara vertikal & Aktifkan Wrap Text
        $sheet->getStyle('A1:X100')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:X100')->getAlignment()->setWrapText(true);
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
        $sheet->getStyle('A5:X' . $barisTerakhir)->applyFromArray($styleBorder);
        // Styling Kop Atas (Wali Kelas & Kelas & Tahun Ajaran)
        $sheet->getStyle('A2:F3')->applyFromArray($styleBorder);
        $sheet->getStyle('A2:F3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        $sheet->getStyle('J1:N3')->applyFromArray($styleBorder);
        $sheet->getStyle('J1:N3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        $sheet->getStyle('P3:X3')->applyFromArray($styleBorder);
        $sheet->getStyle('P3:X3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        // Styling Header Tabel (Abu-abu & Tebal)
        $sheet->getStyle('A5:X6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
        $sheet->getStyle('A5:X6')->getFont()->setBold(true);
        // Rata Kiri untuk Nama Siswa
        if ($jumlahSiswa > 0) {
            $sheet->getStyle('C7:C' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
        return [];
    }
}