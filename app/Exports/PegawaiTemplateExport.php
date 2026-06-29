<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class PegawaiTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template'  => new MainPegawaiTemplateSheet(),
            'Referensi' => new ReferencePegawaiDataSheet(),
        ];
    }
}

class MainPegawaiTemplateSheet implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
{
    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        // Menyediakan 5 baris kosong dengan total 7 kolom sesuai heading
        return array_fill(0, 5, array_fill(0, 7, ''));
    }

    public function headings(): array
    {
        return [
            'nama_lengkap',
            'jenis_kelamin',
            'nip',
            'nuptk',
            'status_pegawai',
            'jenis_ptk',
            'email'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // ---- 1. DROPDOWN JENIS KELAMIN (Kolom B) ----
                // Menggunakan hardcoded list karena opsinya sedikit dan statis
                $validationJK = $sheet->getCell('B2')->getDataValidation();
                $validationJK->setType(DataValidation::TYPE_LIST);
                $validationJK->setErrorStyle(DataValidation::STYLE_STOP);
                $validationJK->setAllowBlank(false);
                $validationJK->setShowDropDown(true);
                $validationJK->setFormula1('"Laki-Laki,Perempuan"'); // Sesuai enum controller (L & P kapital)

                // ---- 2. DROPDOWN STATUS PEGAWAI (Kolom E) ----
                // Diambil dari data Sheet Referensi Kolom A (PNS, PPPK, HONORER)
                $validationStatus = $sheet->getCell('E2')->getDataValidation();
                $validationStatus->setType(DataValidation::TYPE_LIST);
                $validationStatus->setErrorStyle(DataValidation::STYLE_STOP);
                $validationStatus->setAllowBlank(false);
                $validationStatus->setShowDropDown(true);
                $validationStatus->setFormula1("Referensi!\$A\$1:\$A\$3");

                // ---- 3. DROPDOWN JENIS PTK (Kolom F) ----
                // Diambil dari data Sheet Referensi Kolom B (Kepala Sekolah, Guru, Tenaga Kependidikan)
                $validationPTK = $sheet->getCell('F2')->getDataValidation();
                $validationPTK->setType(DataValidation::TYPE_LIST);
                $validationPTK->setErrorStyle(DataValidation::STYLE_STOP);
                $validationPTK->setAllowBlank(false);
                $validationPTK->setShowDropDown(true);
                $validationPTK->setFormula1("Referensi!\$B\$1:\$B\$3");

                // Loop klonning validation untuk baris 2 sampai 100 agar semua baris punya dropdown
                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("B{$i}")->setDataValidation(clone $validationJK);
                    $sheet->getCell("E{$i}")->setDataValidation(clone $validationStatus);
                    $sheet->getCell("F{$i}")->setDataValidation(clone $validationPTK);
                }
            },
        ];
    }
}

class ReferencePegawaiDataSheet implements FromArray, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Referensi';
    }

    public function array(): array
    {
        // Menyusun data statis referensi agar rapi per kolom
        // Kolom A: Status Pegawai | Kolom B: Jenis PTK
        return [
            ['PNS', 'Kepala Sekolah'],
            ['PPPK', 'Guru'],
            ['HONORER', 'Tenaga Kependidikan'],
        ];
    }
}