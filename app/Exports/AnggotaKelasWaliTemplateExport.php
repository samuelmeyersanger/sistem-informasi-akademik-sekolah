<?php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\KelasWali;
use App\Models\Semester;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AnggotaKelasWaliTemplateExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $kelasWaliId;
    protected $semesterId;

    public function __construct($kelasWaliId, $semesterId)
    {
        $this->kelasWaliId = $kelasWaliId;
        $this->semesterId = $semesterId;
    }

    /**
     * Berikan Judul Sheet agar polanya sama seperti import
     */
    public function title(): string
    {
        return 'Template';
    }

    /**
     * Ambil data siswa aktif yang BELUM punya kelompok wali di semester bersangkutan
     */
    public function collection()
    {
        $semesterId = $this->semesterId;

        return Siswa::where('status_siswa', 'Aktif')
            // 👇 Disinilah letak kuncinya! Kita memanggil relasi anggotaKelasWali yang baru
            ->whereDoesntHave('anggotaKelasWali', function ($query) use ($semesterId) {
                $query->where('semester_id', $semesterId);
            })
            ->orderBy('nama_lengkap', 'asc')
            ->get(['id', 'nisn', 'nama_lengkap'])
            ->map(function($siswa) {
                return [
                    'id_siswa'     => $siswa->id,
                    'nisn'         => $siswa->nisn ?? '-',
                    'nama_lengkap' => $siswa->nama_lengkap,
                    'plot_masuk_kelas' => 'N' // Default 'N', wali kelas tinggal ganti 'Y'
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
            'PLOT_MASUK_KELOMPOK (Y/N)' // Disesuaikan nama headernya
        ];
    }
}