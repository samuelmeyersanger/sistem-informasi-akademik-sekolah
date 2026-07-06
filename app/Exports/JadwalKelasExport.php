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

class JadwalKelasExport implements FromView, WithColumnWidths, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // 🟢 Di sini letak rahasianya: Excel mengambil data dari file Blade jadwal yang sama!
        return view('pusat_download.exports.jadwal', $this->data);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // Lebar kolom Jam Ke
            'B' => 28,  // Lebar kolom Senin
            'C' => 28,  // Lebar kolom Selasa
            'D' => 28,  // Lebar kolom Rabu
            'E' => 28,  // Lebar kolom Kamis
            'F' => 28,  // Lebar kolom Jumat
            'G' => 28,  // Lebar kolom Sabtu (jika ada)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Ratakan tengah secara vertikal & hidupkan "Wrap Text" agar Nama Guru muat turun ke bawah
        $sheet->getStyle('A1:G100')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:G100')->getAlignment()->setWrapText(true); 

        // Atur tinggi baris Kop Header agar tidak gepeng
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(25);
        
        // Styling huruf untuk Kop (Besar & Tebal)
        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:G2')->getFont()->setBold(true)->setSize(12);

        // Styling Baris Hari (Baris 4) diberi warna abu-abu
        $sheet->getRowDimension(4)->setRowHeight(25);
        $sheet->getStyle('A4:G4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Mengatur tinggi kotak matriks jadwal (Mulai Baris 5) agar lega untuk teks yang panjang
        $maxJam = $this->data['maxJam'] ?? 10;
        $barisTerakhir = 4 + $maxJam;
        
        for ($i = 5; $i <= $barisTerakhir; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(45); // Dibuat sangat lega (Tinggi 45)
        }

        // Terapkan Garis Kotak-Kotak (Border) pada tabel Jadwal
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        
        $sheet->getStyle('A4:G' . $barisTerakhir)->applyFromArray($styleBorder);
        
        // Posisikan semua teks di tengah-tengah kotak
        $sheet->getStyle('A5:A' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B5:G' . $barisTerakhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}