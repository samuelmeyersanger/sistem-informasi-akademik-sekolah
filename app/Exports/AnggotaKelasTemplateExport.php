<?php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AnggotaKelasTemplateExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $kelasId;
    protected $semesterId;

    public function __construct($kelasId, $semesterId)
    {
        $this->kelasId = $kelasId;
        $this->semesterId = $semesterId;
    }

    /**
     * Berikan Judul Sheet agar polanya sama seperti import siswa Anda
     */
    public function title(): string
    {
        return 'Template';
    }

    /**
     * Ambil data siswa aktif yang belum punya kelas di semester bersangkutan
     */
    public function collection()
    {
        $semesterId = $this->semesterId;

        return Siswa::where('status_siswa', 'Aktif')
            ->whereDoesntHave('anggotaKelas', function ($query) use ($semesterId) {
                $query->where('semester_id', $semesterId);
            })
            ->orderBy('nama_lengkap', 'asc')
            ->get(['id', 'nisn', 'nama_lengkap'])
            ->map(function($siswa) {
                return [
                    'id_siswa'     => $siswa->id,
                    'nisn'         => $siswa->nisn ?? '-',
                    'nama_lengkap' => $siswa->nama_lengkap,
                    'plot_masuk_kelas' => 'N' // Default 'N', admin tinggal ganti 'Y'
                ];
            });
    }

    /**
     * Header Kolom Excel
     */
    public function headings(): array
    {
        return [
            'ID_SISWA',
            'NISN',
            'NAMA_LENGKAP',
            'PLOT_MASUK_KELAS (Y/N)'
        ];
    }
}