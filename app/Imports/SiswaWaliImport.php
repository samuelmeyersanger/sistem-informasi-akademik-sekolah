<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SiswaWaliImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            // Memastikan sistem hanya membaca data dari sheet bernama 'Template'
            'Template' => new SiswaDataImportSheet(),
        ];
    }
}

class SiswaDataImportSheet implements \Maatwebsite\Excel\Concerns\ToModel
{
    public function model(array $row)
    {
        // 1. LEWATI BARIS PERTAMA JIKA ITU JUDUL (HEADING)
        if (isset($row[0]) && (strtolower(trim($row[0])) == 'nama_lengkap' || strtolower(trim($row[0])) == 'nama lengkap')) {
            return null;
        }

        // 2. AMBIL DATA BERDASARKAN INDEKS URUTAN KOLOM EXCEL (A=0, B=1, dst.)
        $nama_lengkap   = isset($row[0]) ? trim($row[0]) : null;
        $nik            = isset($row[1]) ? trim($row[1]) : '-';
        $nipd           = isset($row[2]) ? trim($row[2]) : '-';
        $nisn           = isset($row[3]) ? trim($row[3]) : null;
        $jenis_kelamin  = isset($row[4]) ? trim($row[4]) : 'Laki-laki';
        $tempat_lahir   = isset($row[5]) ? trim($row[5]) : '-';
        $tanggal_lahir  = isset($row[6]) ? $this->parseTanggalIndonesia($row[6]) : now()->format('Y-m-d');
        $agama_raw      = isset($row[7]) ? trim($row[7]) : 'Islam';
        $nomor_hp       = isset($row[8]) ? trim($row[8]) : '-';
        $asal_sekolah   = isset($row[9]) ? trim($row[9]) : '-';
        $no_peserta_un  = isset($row[10]) ? trim($row[10]) : '-';
        
        // Kolom L, M, N, O (Indeks 11, 12, 13, 14)
        $text_provinsi  = isset($row[11]) ? trim($row[11]) : null;
        $text_kota      = isset($row[12]) ? trim($row[12]) : null;
        $text_kecamatan = isset($row[13]) ? trim($row[13]) : null;
        $text_kelurahan = isset($row[14]) ? trim($row[14]) : null;
        
        $alamat_lengkap = isset($row[15]) ? trim($row[15]) : 'Alamat belum diisi';
        $rt             = isset($row[16]) ? trim($row[16]) : '00';
        $rw             = isset($row[17]) ? trim($row[17]) : '00';
        $kode_pos       = isset($row[18]) ? trim($row[18]) : '00000';
        $tingkat        = isset($row[19]) ? trim($row[19]) : '7';
        $tgl_diterima   = isset($row[20]) ? $this->parseTanggalIndonesia($row[20]) : now()->format('Y-m-d');
        $anak_ke        = isset($row[21]) ? trim($row[21]) : 1;

        $ayah_nama      = isset($row[22]) ? trim($row[22]) : null;
        $ayah_pekerjaan = isset($row[23]) ? trim($row[23]) : '-';
        $ibu_nama       = isset($row[24]) ? trim($row[24]) : null;
        $ibu_pekerjaan  = isset($row[25]) ? trim($row[25]) : '-';
        $wali_nama      = isset($row[26]) ? trim($row[26]) : null;
        $wali_pekerjaan = isset($row[27]) ? trim($row[27]) : '-';

        // Jika nama kosong, skip baris ini
        if (empty($nama_lengkap)) {
            return null;
        }

        // Normalisasi Agama
        $agama_input = strtolower(trim($agama_raw));
        if (str_contains($agama_input, 'katolik') || str_contains($agama_input, 'katholik')) {
            $agama = 'Katholik';
        } elseif (str_contains($agama_input, 'kristen') || str_contains($agama_input, 'protestan')) {
            $agama = 'Kristen';
        } elseif (str_contains($agama_input, 'budha') || str_contains($agama_input, 'buddha')) {
            $agama = 'Budha';
        } else {
            $agama = ucwords($agama_input); 
        }

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $semesterAktif = \App\Models\Semester::where('is_aktif', true)->first(); 
            $semesterId = $semesterAktif ? $semesterAktif->id : null;

            // --- KONVERSI WILAYAH (SOLUSI ANTI-SPASI & OPERATOR LIKE NYALA) ---
            $provinsiId = null;
            $provinsiCode = null;
            if (!empty($text_provinsi)) {
                $cleanProvinsi = strtoupper(trim($text_provinsi));
                $provinsi = \Laravolt\Indonesia\Models\Province::where('name', 'LIKE', '%' . $cleanProvinsi . '%')->first();
                if ($provinsi) {
                    $provinsiId = $provinsi->id;
                    $provinsiCode = $provinsi->code;
                }
            }
            if (empty($provinsiId)) {
                $fallbackProv = \Laravolt\Indonesia\Models\Province::where('code', '32')->first();
                $provinsiId = $fallbackProv ? $fallbackProv->id : 32;
                $provinsiCode = '32';
            }

            $kotaId = null;
            $kotaCode = null;
            if (!empty($text_kota)) {
                $cleanKota = strtoupper(trim($text_kota));
                $kota = \Laravolt\Indonesia\Models\City::where('province_code', $provinsiCode)
                    ->where('name', 'LIKE', '%' . $cleanKota . '%')
                    ->first();
                if ($kota) {
                    $kotaId = $kota->id;
                    $kotaCode = $kota->code;
                }
            }
            if (empty($kotaId)) {
                $fallbackCity = \Laravolt\Indonesia\Models\City::where('code', '3216')->first();
                $kotaId = $fallbackCity ? $fallbackCity->id : 3216;
                $kotaCode = '3216';
            }

            $kecamatanId = null;
            $kecamatanCode = null;
            if (!empty($text_kecamatan)) {
                $cleanKecamatan = strtoupper(trim($text_kecamatan));
                $kecamatan = \Laravolt\Indonesia\Models\District::where('city_code', $kotaCode)
                    ->where('name', 'LIKE', '%' . $cleanKecamatan . '%')
                    ->first();
                if ($kecamatan) {
                    $kecamatanId = $kecamatan->id;
                    $kecamatanCode = $kecamatan->code;
                }
            }
            if (empty($kecamatanId)) {
                $fallbackDist = \Laravolt\Indonesia\Models\District::where('code', '3216060')->first();
                $kecamatanId = $fallbackDist ? $fallbackDist->id : 3216060;
                $kecamatanCode = '3216060';
            }

            $kelurahanId = null;
            if (!empty($text_kelurahan)) {
                $cleanKelurahan = strtoupper(trim($text_kelurahan));
                $kelurahan = \Laravolt\Indonesia\Models\Village::where('district_code', $kecamatanCode)
                    ->where('name', 'LIKE', '%' . $cleanKelurahan . '%')
                    ->first();
                $kelurahanId = $kelurahan ? $kelurahan->id : null;
            }
            if (empty($kelurahanId)) {
                $firstVillage = \Laravolt\Indonesia\Models\Village::where('district_code', $kecamatanCode)->first();
                $kelurahanId = $firstVillage ? $firstVillage->id : 3216060001;
            }

            // SIMPAN DATA SISWA
            $siswa = \App\Models\Siswa::updateOrCreate(
                ['nipd' => $nipd],
                [
                    'semester_id'           => $semesterId,
                    'nama_lengkap'          => $nama_lengkap,
                    'nik'                   => $nik, 
                    'nisn'                  => $nisn,
                    'jenis_kelamin'         => $jenis_kelamin,
                    'tempat_lahir'          => $tempat_lahir,
                    'tanggal_lahir'         => $tanggal_lahir,
                    'agama'                 => $agama,
                    'nomor_hp'              => $nomor_hp,
                    'asal_sekolah'          => $asal_sekolah,
                    'no_peserta_un'         => $no_peserta_un,
                    'provinsi'              => $provinsiId,
                    'kota'                  => $kotaId,
                    'kecamatan'             => $kecamatanId,
                    'kelurahan_desa'        => $kelurahanId,
                    'alamat_lengkap'        => $alamat_lengkap,
                    'rt'                    => sprintf("%02d", $rt),
                    'rw'                    => sprintf("%02d", $rw),
                    'kode_pos'              => $kode_pos,
                    'tingkat'               => $tingkat,
                    'diterima_pada_tanggal' => $tgl_diterima,
                    'anak_ke'               => is_numeric($anak_ke) ? $anak_ke : 1,
                    'status_siswa'          => 'Aktif',
                    'user_id'               => null,
                    'kelas_id'              => null,
                ]
            );

            // MAPPING WALI
            $kategoriWali = [
                'Ayah' => ['nama' => $ayah_nama, 'pekerjaan' => $ayah_pekerjaan, 'jk' => 'Laki-laki'],
                'Ibu'  => ['nama' => $ibu_nama, 'pekerjaan' => $ibu_pekerjaan, 'jk' => 'Perempuan'],
                'Wali' => ['nama' => $wali_nama, 'pekerjaan' => $wali_pekerjaan, 'jk' => 'Laki-laki']
            ];

            foreach ($kategoriWali as $hubungan => $data) {
                if (empty($data['nama'])) { continue; }

                $wali = \App\Models\WaliSiswa::create([
                    'nama_lengkap'   => $data['nama'],
                    'pekerjaan'      => $data['pekerjaan'],
                    'jenis_kelamin'  => $data['jk'],
                    'nik'            => null,
                    'nomor_hp'       => null,
                    'alamat_lengkap' => $siswa->alamat_lengkap,
                ]);

                $siswa->wali()->syncWithoutDetaching([
                    $wali->id => [
                        'hubungan'   => $hubungan,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return $siswa;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Log::error("Gagal import baris siswa: " . $nama_lengkap . " | Error: " . $e->getMessage());
            return null; 
        }
    }

    private function parseTanggalIndonesia($dateValue)
    {
        if (empty($dateValue)) { return now()->format('Y-m-d'); }
        if (is_numeric($dateValue)) {
            try {
                return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue))->format('Y-m-d');
            } catch (\Exception $e) {}
        }

        $dateString = strtolower(trim($dateValue));
        $bulan = [
            'januari' => 'January', 'februari' => 'February', 'maret' => 'March', 'april' => 'April',
            'mei' => 'May', 'juni' => 'June', 'juli' => 'July', 'agustus' => 'August',
            'september' => 'September', 'oktober' => 'October', 'november' => 'November', 'desember' => 'December'
        ];

        foreach ($bulan as $indo => $inggris) {
            if (strpos($dateString, $indo) !== false) {
                $dateString = str_replace($indo, $inggris, $dateString);
                break;
            }
        }

        try {
            $timestamp = strtotime($dateString);
            return $timestamp ? date('Y-m-d', $timestamp) : now()->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}