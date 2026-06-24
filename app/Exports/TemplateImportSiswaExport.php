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
use Laravolt\Indonesia\Models\Province;

class TemplateImportSiswaExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template'  => new MainTemplateSheet(),
            'Referensi' => new ReferenceDataSheet(),
        ];
    }
}

class MainTemplateSheet implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, WithTitle
{
    public function title(): string
    {
        return 'Template';
    }

    public function array(): array
    {
        // 28 kolom setelah ditambah 'no_peserta_un'
        return array_fill(0, 5, array_fill(0, 28, ''));
    }

    public function headings(): array
    {
        return [
            // Data Utama Siswa (Kolom A - V)
            'nama_lengkap', 'nik', 'nipd', 'nisn', 'jenis_kelamin', 
            'tempat_lahir', 'tanggal_lahir', 'agama', 'nomor_hp', 
            'asal_sekolah', 'no_peserta_un', // 🆕 Tambah di sini
            'provinsi', 'kota', 'kecamatan', 'kelurahan_desa', 'alamat_lengkap', 
            'rt', 'rw', 'kode_pos', 'tingkat', 'diterima_pada_tanggal', 'anak_ke',
            
            // Data Orang Tua / Wali (Kolom W - AB)
            'ayah_nama', 'ayah_pekerjaan',
            'ibu_nama', 'ibu_pekerjaan',
            'wali_nama', 'wali_pekerjaan'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // ---- DROPDOWN JENIS KELAMIN (Kolom E) ----
                $validationJK = $sheet->getCell('E2')->getDataValidation();
                $validationJK->setType(DataValidation::TYPE_LIST);
                $validationJK->setErrorStyle(DataValidation::STYLE_STOP);
                $validationJK->setAllowBlank(false);
                $validationJK->setShowDropDown(true);
                $validationJK->setFormula1('"Laki-laki,Perempuan"');

                // ---- DROPDOWN PROVINSI (Kolom L - Bergeser karena ada kolom baru) ----
                $totalProvinces = Province::count();
                $validationProv = $sheet->getCell('L2')->getDataValidation(); // Kolom K bergeser ke L
                $validationProv->setType(DataValidation::TYPE_LIST);
                $validationProv->setErrorStyle(DataValidation::STYLE_STOP);
                $validationProv->setAllowBlank(true);
                $validationProv->setShowDropDown(true);
                $validationProv->setFormula1("Referensi!\$A\$1:\$A\$" . $totalProvinces);

                // Duplikasi dropdown untuk baris 2 sampai 100
                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell("E{$i}")->setDataValidation(clone $validationJK);
                    $sheet->getCell("L{$i}")->setDataValidation(clone $validationProv);
                }
            },
        ];
    }
}

class ReferenceDataSheet implements FromArray, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Referensi';
    }

    public function array(): array
    {
        $provinces = Province::orderBy('name', 'asc')->pluck('name')->toArray();
        return array_map(function($name) {
            return [ucwords(strtolower($name))];
        }, $provinces);
    }
}