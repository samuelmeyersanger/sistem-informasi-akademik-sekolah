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
            // 20 Kolom untuk Tanggal
            'E' => 4, 'F' => 4, 'G' => 4, 'H' => 4, 'I' => 4,
            'J' => 4, 'K' => 4, 'L' => 4, 'M' => 4, 'N' => 4,
            'O' => 4, 'P' => 4, 'Q' => 4, 'R' => 4, 'S' => 4,
            'T' => 4, 'U' => 4, 'V' => 4, 'W' => 4, 'X' => 4,
            // Kolom S, I, A
            'Y' => 4, 'Z' => 4, 'AA' => 4,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        // 1. Ratakan semua teks ke tengah secara Vertikal
        $sheet->getStyle('A1:AA100')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        // Hitung baris terakhir data (Data mulai dari baris ke-7)
        $jumlahSiswa = count($this->data['anggota']);
        $barisTerakhir = $jumlahSiswa > 0 ? 6 + $jumlahSiswa : 7;
        // --- PENGATURAN BORDER (GARIS TEPI) ---
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        // Terapkan Border ke Tabel Utama (Mulai Baris 5 sampai baris terakhir)
        $sheet->getStyle('A5:AA' . $barisTerakhir)->applyFromArray($styleBorder);
        // --- PENGATURAN WARNA KUNING & BORDER UNTUK KOP ATAS ---
        // Kolom Nama Wali Kelas
        $sheet->getStyle('A2:F2')->applyFromArray($styleBorder);
        $sheet->getStyle('A2:F2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Kolom Nama Kelas
        $sheet->getStyle('J2:N2')->applyFromArray($styleBorder);
        $sheet->getStyle('J2:N2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        $sheet->getStyle('J2:N2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Kolom Tahun Ajaran
        $sheet->getStyle('P3:AA3')->applyFromArray($styleBorder);
        $sheet->getStyle('P3:AA3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        $sheet->getStyle('P3:AA3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // --- PENGATURAN WARNA ABU-ABU UNTUK HEADER TABEL ---
        $sheet->getStyle('A5:AA6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
        $sheet->getStyle('A5:AA6')->getFont()->setBold(true);
        $sheet->getStyle('A5:AA6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // --- PENYESUAIAN ALIGNMENT (RATA KIRI UNTUK NAMA) ---
        if ($jumlahSiswa > 0) {
            $sheet->getStyle('A7:B' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No, NISN (Tengah)
            $sheet->getStyle('C7:C' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   // Nama (Kiri)
            $sheet->getStyle('D7:AA' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Sisanya (Tengah)
        }
        return [];
    }
}