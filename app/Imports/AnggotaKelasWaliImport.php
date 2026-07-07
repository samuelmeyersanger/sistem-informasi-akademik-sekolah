<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\KelasWali;
use App\Models\AnggotaKelasWali;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ToModel;

class AnggotaKelasWaliImport implements WithMultipleSheets
{
    protected $kelasWaliId;
    protected $semesterId;

    public function __construct($kelasWaliId, $semesterId)
    {
        $this->kelasWaliId = $kelasWaliId;
        $this->semesterId = $semesterId;
    }

    public function sheets(): array
    {
        return [
            'Template' => new AnggotaKelasWaliDataSheet($this->kelasWaliId, $this->semesterId),
        ];
    }
}

class AnggotaKelasWaliDataSheet implements ToModel
{
    protected $kelasWaliId;
    protected $semesterId;

    public function __construct($kelasWaliId, $semesterId)
    {
        $this->kelasWaliId = $kelasWaliId;
        $this->semesterId = $semesterId;
    }

    public function model(array $row)
    {
        // 1. Lewati Heading Baris Pertama
        if (isset($row[0]) && (strtolower($row[0]) == 'id_siswa' || strtolower($row[0]) == 'id siswa')) {
            return null;
        }

        // 2. Filter Baris Kosong
        $filteredRow = array_filter($row, function($value) {
            return $value !== null && trim($value) !== '';
        });

        if (empty($filteredRow) || empty($row[0])) {
            return null;
        }

        $siswaId  = trim($row[0]);
        $plotAksi = strtoupper(trim($row[3] ?? 'N'));

        // Jika bernilai 'Y', proses plotting siswa ke dalam kelompok wali target
        if ($plotAksi === 'Y') {
            DB::beginTransaction();
            try {
                $kelasWali = KelasWali::findOrFail($this->kelasWaliId);

                // A. Masukkan atau update relasi anggota kelompok pada semester terpilih
                AnggotaKelasWali::updateOrCreate(
                    [
                        'siswa_id'    => $siswaId,
                        'semester_id' => $this->semesterId
                    ],
                    [
                        'kelas_wali_id' => $this->kelasWaliId,
                        'tingkat'       => $kelasWali->tingkat, // Meyimpan data tingkat saat ini (contoh: 7/8/9)
                    ]
                );

                // B & C. (Update Profil Siswa & Riwayat Akademik DITIADAKAN)
                // Karena ini kelompok wali, kita tidak merusak data kelas akademik utama milik siswa.

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error("Gagal import plotting siswa Kelas Wali ID: " . $siswaId . " | Error: " . $e->getMessage());
            }
        }

        return null; // Laravel-Excel membutuhkan return null jika kita mengolah DB secara manual
    }
}