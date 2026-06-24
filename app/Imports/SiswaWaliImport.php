<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\WaliSiswa;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

class SiswaWaliImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        DB::beginTransaction();

        try {
            // Ambil ID Semester yang aktif di database backend secara otomatis
            $semesterAktif = Semester::where('status', 'Aktif')->first(); 
            $semesterId = $semesterAktif ? $semesterAktif->id : null;

            // Konversi nama teks wilayah ke ID Laravolt
            $provinsiId = null;
            if (!empty($row['provinsi'])) {
                $provinsi = Province::where('name', 'LIKE', '%' . $row['provinsi'] . '%')->first();
                $provinsiId = $provinsi ? $provinsi->id : null;
            }

            $kotaId = null;
            if (!empty($row['kota']) && $provinsiId) {
                $kota = City::where('province_code', $provinsiId)->where('name', 'LIKE', '%' . $row['kota'] . '%')->first();
                $kotaId = $kota ? $kota->id : null;
            }

            $kecamatanId = null;
            if (!empty($row['kecamatan']) && $kotaId) {
                $kecamatan = District::where('city_code', $kotaId)->where('name', 'LIKE', '%' . $row['kecamatan'] . '%')->first();
                $kecamatanId = $kecamatan ? $kecamatan->id : null;
            }

            $kelurahanId = null;
            if (!empty($row['kelurahan_desa']) && $kecamatanId) {
                $kelurahan = Village::where('district_code', $kecamatanId)->where('name', 'LIKE', '%' . $row['kelurahan_desa'] . '%')->first();
                $kelurahanId = $kelurahan ? $kelurahan->id : null;
            }

            // 1. Simpan Data Utama Siswa
            $siswa = Siswa::create([
                'nama_lengkap'          => $row['nama_lengkap'],
                'nik'                   => $row['nik'],
                'nipd'                  => $row['nipd'],
                'nisn'                  => $row['nisn'] ?? null,
                'jenis_kelamin'         => $row['jenis_kelamin'],
                'tempat_lahir'          => $row['tempat_lahir'],
                'tanggal_lahir'         => $row['tanggal_lahir'],
                'agama'                 => $row['agama'],
                'nomor_hp'              => $row['nomor_hp'],
                
                // 🆕 KEDUA FIELD BARU KITA PETAKAN DI SINI
                'asal_sekolah'          => $row['asal_sekolah'],
                'no_peserta_un'         => $row['no_peserta_un'],
                
                'provinsi'              => $provinsiId,
                'kota'                  => $kotaId,
                'kecamatan'             => $kecamatanId,
                'kelurahan_desa'        => $kelurahanId,
                'alamat_lengkap'        => $row['alamat_lengkap'],
                'rt'                    => $row['rt'],
                'rw'                    => $row['rw'],
                'kode_pos'              => $row['kode_pos'],
                'tingkat'               => $row['tingkat'],
                'semester_id'           => $semesterId, // Terkunci otomatis dari backend
                'diterima_pada_tanggal' => $row['diterima_pada_tanggal'],
                'anak_ke'               => $row['anak_ke'],
                'status_siswa'          => 'Aktif',
            ]);

            // 2. Mapping Data Wali (Nama & Pekerjaan)
            $kategoriWali = [
                'Ayah' => [
                    'nama'      => $row['ayah_nama'] ?? null,
                    'pekerjaan' => $row['ayah_pekerjaan'] ?? null,
                    'jk'        => 'Laki-laki'
                ],
                'Ibu' => [
                    'nama'      => $row['ibu_nama'] ?? null,
                    'pekerjaan' => $row['ibu_pekerjaan'] ?? null,
                    'jk'        => 'Perempuan'
                ],
                'Wali' => [
                    'nama'      => $row['wali_nama'] ?? null,
                    'pekerjaan' => $row['wali_pekerjaan'] ?? null,
                    'jk'        => 'Laki-laki'
                ]
            ];

            foreach ($kategoriWali as $hubungan => $data) {
                if (empty($data['nama'])) {
                    continue;
                }

                $wali = WaliSiswa::create([
                    'nama_lengkap'   => $data['nama'],
                    'pekerjaan'      => $data['pekerjaan'],
                    'jenis_kelamin'  => $data['jk'],
                    'nik'            => null,
                    'nomor_hp'       => null,
                    'alamat_lengkap' => $siswa->alamat_lengkap,
                ]);

                $siswa->wali()->attach($wali->id, [
                    'hubungan'   => $hubungan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return $siswa;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}