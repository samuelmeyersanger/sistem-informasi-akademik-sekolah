<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Pegawai;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PegawaiImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            // Memastikan sistem hanya membaca data dari sheet bernama 'Template'
            'Template' => new PegawaiDataImportSheet(),
        ];
    }
}

// ⬇️ Seluruh logika import Pegawai dipindahkan ke dalam class sheet khusus ini
class PegawaiDataImportSheet implements ToModel
{
    public function model(array $row)
    {
        // 1. LEWATI BARIS PERTAMA (JUDUL/HEADING)
        if (isset($row[0]) && (strtolower($row[0]) == 'nama_lengkap' || strtolower($row[0]) == 'nama lengkap')) {
            return null;
        }

        // 2. FILTER BARIS KOSONG TOTAL
        $filteredRow = array_filter($row, function($value) {
            return $value !== null && trim($value) !== '';
        });

        if (empty($filteredRow)) {
            return null; 
        }

        // 3. AMBIL DATA BERDASARKAN INDEKS KOLOM (Sesuai dengan Template Export Pegawai)
        // Kolom 0 = nama_lengkap, Kolom 1 = jenis_kelamin, Kolom 2 = nip, dst.
        $nama_lengkap   = isset($row[0]) ? trim($row[0]) : null;
        $jenis_kelamin  = isset($row[1]) ? trim($row[1]) : 'Laki-Laki';
        $nip            = (isset($row[2]) && trim($row[2]) !== '') ? trim($row[2]) : null;
        $nuptk          = (isset($row[3]) && trim($row[3]) !== '') ? trim($row[3]) : null;
        $status_pegawai = isset($row[4]) ? trim($row[4]) : 'HONORER';
        $jenis_ptk      = isset($row[5]) ? trim($row[5]) : 'Guru';
        $email          = (isset($row[6]) && trim($row[6]) !== '') ? trim($row[6]) : null;

        // Validasi minimal: Jika nama lengkap kosong, abaikan baris ini
        if (empty($nama_lengkap)) {
            return null;
        }

        // 4. NORMALISASI ENUM STATUS PEGAWAI (Paksa jadi uppercase: PNS, PPPK, HONORER)
        $status_pegawai = strtoupper($status_pegawai);
        if (!in_array($status_pegawai, ['PNS', 'PPPK', 'HONORER'])) {
            $status_pegawai = 'HONORER'; // Fallback aman sesuai enum
        }

        // 5. NORMALISASI ENUM JENIS PTK (Menjaga kebersihan data string)
        $jenis_ptk_input = strtolower($jenis_ptk);
        if (str_contains($jenis_ptk_input, 'kepala') || str_contains($jenis_ptk_input, 'sekolah')) {
            $jenis_ptk = 'Kepala Sekolah';
        } elseif (str_contains($jenis_ptk_input, 'guru')) {
            $jenis_ptk = 'Guru';
        } else {
            $jenis_ptk = 'Tenaga Kependidikan';
        }

        DB::beginTransaction();

        try {
            // Ambil id semester yang sedang aktif
            $semesterAktif = Semester::where('is_aktif', true)->first(); 
            $semesterId = $semesterAktif ? $semesterAktif->id : null;

            // 🔴 MENGGUNAKAN UPDATE OR CREATE AGAR AMAN DARI DUPLIKAT NIP/NUPTK/EMAIL
            // Sistem akan mencari apakah NIP/NUPTK/Email sudah ada di database. 
            // Jika ada, data lamanya akan di-update (anti unique constraint error).
            $pegawai = Pegawai::updateOrCreate(
                [
                    // Gunakan NIP sebagai kunci utama jika ada, jika tidak ada gunakan email/nama
                    'nip' => $nip,
                ],
                [
                    'nama_lengkap'     => $nama_lengkap,
                    'jenis_kelamin'    => $jenis_kelamin,
                    'nuptk'            => $nuptk,
                    'status_pegawai'   => $status_pegawai,
                    'jenis_ptk'        => $jenis_ptk,
                    'email'            => $email,
                    'semester_id'      => $semesterId,
                    'status_keaktifan' => 'Aktif',
                ]
            );

            DB::commit();
            return $pegawai;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal import baris pegawai: " . $nama_lengkap . " | Error: " . $e->getMessage());
            return null; 
        }
    }
}