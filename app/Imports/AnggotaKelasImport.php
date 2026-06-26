<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\AnggotaKelas;
use App\Models\RiwayatKelasSiswa;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ToModel;

class AnggotaKelasImport implements WithMultipleSheets
{
    protected $kelasId;
    protected $semesterId;

    public function __construct($kelasId, $semesterId)
    {
        $this->kelasId = $kelasId;
        $this->semesterId = $semesterId;
    }

    public function sheets(): array
    {
        return [
            'Template' => new AnggotaKelasDataSheet($this->kelasId, $this->semesterId),
        ];
    }
}

class AnggotaKelasDataSheet implements ToModel
{
    protected $kelasId;
    protected $semesterId;

    public function __construct($kelasId, $semesterId)
    {
        $this->kelasId = $kelasId;
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

        // Jika bernilai 'Y', proses plotting siswa ke dalam kelas target
        if ($plotAksi === 'Y') {
            DB::beginTransaction();
            try {
                $kelas = Kelas::findOrFail($this->kelasId);

                // A. Masukkan atau update relasi anggota kelas pada semester terpilih
                AnggotaKelas::updateOrCreate(
                    [
                        'siswa_id'    => $siswaId,
                        'semester_id' => $this->semesterId
                    ],
                    [
                        'kelas_id' => $this->kelasId,
                        'tingkat'  => $kelas->tingkat,
                    ]
                );

                // B. Update pointer pintasan kelas terbaru di master data siswa
                Siswa::where('id', $siswaId)->update(['kelas_id' => $this->kelasId]);

                // C. Catat riwayat audit akademik siswa
                RiwayatKelasSiswa::create([
                    'siswa_id'    => $siswaId,
                    'kelas_id'    => $this->kelasId,
                    'tingkat'     => $kelas->tingkat,
                    'semester_id' => $this->semesterId,
                    'keterangan'  => 'Plotting Massal via Import Excel'
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error("Gagal import plotting siswa ID: " . $siswaId . " | Error: " . $e->getMessage());
            }
        }

        return null; // Laravel-Excel membutuhkan return null jika kita mengolah DB manual di dalam model()
    }
}