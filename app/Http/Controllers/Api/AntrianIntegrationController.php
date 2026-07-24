<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AntrianIntegrationController extends Controller
{
    /**
     * Mendapatkan daftar kelas aktif pada Tahun Ajaran yang aktif.
     */
    public function getKelas(Request $request)
    {
        try {
            // Ambil Tahun Ajaran Aktif
            $tahunAktif = TahunAjaran::active()->first();

            if (!$tahunAktif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada Tahun Ajaran yang aktif saat ini.',
                    'data' => []
                ], 404);
            }

            // Ambil daftar kelas yang terkait dengan tahun ajaran aktif melalui relasi semester
            // Atau jika kelas terkait langsung dengan semester, dan semester terkait dengan tahun ajaran:
            // Mari asumsikan kelas terkait semester_id, dan semester_id memiliki tahun_ajaran_id.
            // Kita bisa menarik kelas dari Semester yang aktif di Tahun Ajaran ini.
            
            // Periksa daftar kelas dari semester aktif
            $semesterAktifIds = \App\Models\Semester::where('tahun_ajaran_id', $tahunAktif->id)
                                                    ->where('is_aktif', true)
                                                    ->pluck('id');

            $kelasList = Kelas::whereIn('semester_id', $semesterAktifIds)
                              ->orderBy('tingkat')
                              ->orderBy('nama_kelas')
                              ->select('id', 'nama_kelas', 'tingkat')
                              ->get();

            return response()->json([
                'success' => true,
                'data' => $kelasList
            ]);
        } catch (\Exception $e) {
            Log::error('Error getKelas (API Antrian): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
            ], 500);
        }
    }

    /**
     * Mengecek pengguna berdasarkan Kategori dan Nomor Induk.
     */
    public function cekPengguna(Request $request)
    {
        $kategori = $request->query('kategori'); // 'siswa' atau 'pegawai'
        $nomor = $request->query('nomor_induk');

        if (!$kategori || !$nomor) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori dan Nomor Induk harus diisi.'
            ], 400);
        }

        try {
            if ($kategori === 'siswa') {
                // Cari siswa aktif berdasarkan NISN atau NIPD
                $siswa = Siswa::where(function($query) use ($nomor) {
                                  $query->where('nisn', $nomor)
                                        ->orWhere('nipd', $nomor);
                              })
                              ->where('status_siswa', 'Aktif') // Sesuaikan dengan nilai enum yang valid (misal: 'Aktif')
                              ->with('kelas') // Load relasi kelas untuk dikirimkan juga
                              ->first();

                if ($siswa) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'nama_lengkap' => $siswa->nama_lengkap,
                            'kelas' => $siswa->kelas ? $siswa->kelas->nama_kelas : null,
                            'kelas_id' => $siswa->kelas_id
                        ]
                    ]);
                }
            } elseif ($kategori === 'pegawai') {
                // Cari pegawai berdasarkan NIP (Asumsi ada kolom nip/nuptk/nik)
                $pegawai = Pegawai::where('nip', $nomor)
                                  ->orWhere('nuptk', $nomor)
                                  // tambahkan filter status aktif jika ada: ->where('status_pegawai', 'Aktif')
                                  ->first();
                if ($pegawai) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'nama_lengkap' => $pegawai->nama_lengkap,
                            'jabatan' => $pegawai->jabatan // optional
                        ]
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau status tidak aktif.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error cekPengguna (API Antrian): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.'
            ], 500);
        }
    }

    /**
     * Otentikasi pengguna untuk e-Antrian SSO
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kredensial tidak valid.'
                ], 401);
            }

            // Dapatkan daftar nama role dari relasi roles
            $roles = $user->roles->pluck('name')->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error authenticate (API Antrian): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.'
            ], 500);
        }
    }
}
