<?php
namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// 1. Hapus ShouldAutoSize, ganti dengan WithColumnWidths
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
    // 2. Kita atur lebar masing-masing kolom secara manual agar rapi
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // NISN
            'C' => 35,  // Nama
            'D' => 6,   // L/P
            
            // 20 Kolom untuk Tanggal (Lebar 4)
            'E' => 4, 'F' => 4, 'G' => 4, 'H' => 4, 'I' => 4,
            'J' => 4, 'K' => 4, 'L' => 4, 'M' => 4, 'N' => 4,
            'O' => 4, 'P' => 4, 'Q' => 4, 'R' => 4, 'S' => 4,
            'T' => 4, 'U' => 4, 'V' => 4, 'W' => 4, 'X' => 4,
            
            // Kolom S, I, A
            'Y' => 5, 'Z' => 5, 'AA' => 5,
        ];
    }
    // 3. (Opsional) Memastikan teks di tengah secara vertikal
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:AA100')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        return [];
    }
}