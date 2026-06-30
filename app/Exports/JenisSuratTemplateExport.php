<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class JenisSuratTemplateExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Template Jenis Surat';
    }

    /**
     * Berikan contoh baris kosong atau instruksi pengisian
     */
    public function collection()
    {
        return new Collection([
            [
                'kode_klasifikasi' => '420',
                'nama_jenis'       => 'Surat Tugas Guru',
                'format_nomor'     => '[NOMOR]/[KODE]/SMK-1/[BULAN]/[TAHUN]'
            ],
            [
                'kode_klasifikasi' => '800',
                'nama_jenis'       => 'Surat Keputusan Kepsek',
                'format_nomor'     => '[NOMOR]/[KODE]/SK/[TAHUN]'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'KODE_KLASIFIKASI',
            'NAMA_JENIS_SURAT',
            'FORMAT_SUSUNAN_NOMOR'
        ];
    }
}