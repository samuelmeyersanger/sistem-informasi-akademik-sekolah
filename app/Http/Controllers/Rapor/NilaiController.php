<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    /**
     * Menampilkan Halaman Single Page Input Nilai (Matriks TP + PSTS + PSAS)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $kelas_id = $request->input('kelas_id');
        $mata_pelajaran_id = $request->input('mata_pelajaran_id');
        // ====================================================================
        // GEMBOK KELAS & MAPEL (CUKUP PANGGIL HELPER DARI MODEL PEGAWAI)
        // ====================================================================
        if ($user->hasPermission('akses-semua-data')) {
            $kelases = \App\Models\Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
            $mapels = \App\Models\MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        } else {
            $pegawai = \App\Models\Pegawai::where('user_id', $user->id)->first();
            if ($pegawai) {
                // SANGAT BERSIH DAN RAPI:
                $kelases = \App\Models\Kelas::whereIn('id', $pegawai->getKelasIdsDiampu())->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
                $mapels = \App\Models\MataPelajaran::whereIn('id', $pegawai->getMapelIdsDiampu())->orderBy('nama_mapel', 'asc')->get();
            } else {
                $kelases = collect();
                $mapels = collect();
            }
        }
        $siswas = collect();
        $tujuanPembelajarans = collect();
        $nilaiData = []; 
        if ($kelas_id && $mata_pelajaran_id) {
            
            // PENCEGAHAN URL HACKING
            if (!$kelases->contains('id', $kelas_id) || !$mapels->contains('id', $mata_pelajaran_id)) {
                abort(403, 'Akses Ditolak! Anda tidak memiliki jadwal mengajar di Kelas/Mapel ini.');
            }
            $kelas = \App\Models\Kelas::find($kelas_id);
            if ($kelas) {
                // Ambil TP (Untuk membuat kolom dinamis Sumatif 1, Sumatif 2, dst)
                $tujuanPembelajarans = \App\Models\TujuanPembelajaran::where('mata_pelajaran_id', $mata_pelajaran_id)
                                        ->where('tingkat', $kelas->tingkat)
                                        ->orderBy('nomor_tujuan', 'asc')
                                        ->get();
                // Ambil Anak
                $siswas = \App\Models\Siswa::where('kelas_id', $kelas_id)->orderBy('nama_lengkap', 'asc')->get();
                $semesterAktif = \App\Models\Semester::active()->first();
                $semester_aktif_id = $semesterAktif ? $semesterAktif->id : null;

                // Ambil Riwayat Nilai
                $nilais = \App\Models\Nilai::where('kelas_id', $kelas_id)
                             ->where('mata_pelajaran_id', $mata_pelajaran_id)
                             ->where('semester_id', $semester_aktif_id)
                             ->get();
                
                foreach ($nilais as $n) {
                    if(isset($n->tujuan_pembelajaran_id)) {
                        $nilaiData[$n->siswa_id][$n->tujuan_pembelajaran_id] = $n;
                    } else {
                        $nilaiData[$n->siswa_id] = $n;
                    }
                }
            }
        }
        return view('rapor.nilai.index', compact('kelases', 'mapels', 'siswas', 'tujuanPembelajarans', 'nilaiData', 'kelas_id', 'mata_pelajaran_id'));
    }

    /**
     * Kalkulasi Otomatis dan Simpan Massal (Bulk Save)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'          => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'nilai'             => ['required', 'array'], 
        ]);

        $kelas_id = $request->kelas_id;
        $mapel_id = $request->mata_pelajaran_id;
        
        $semesterAktif = \App\Models\Semester::active()->first();
        $semester_aktif_id = $semesterAktif ? $semesterAktif->id : null;
        
        $sukses_tersimpan = 0;

        foreach ($request->nilai as $siswa_id => $data) {
            
            $sumatif_array = $data['sumatif'] ?? []; // Ini adalah Array berisi daftar Nilai TP (dari input JSON)
            
            // Tangkap nilai Ujian, jadikan angka desimal. Jika kosong, anggap 0.
            $psts = isset($data['psts']) && $data['psts'] !== '' ? (float) $data['psts'] : 0;
            $psas = isset($data['psas']) && $data['psas'] !== '' ? (float) $data['psas'] : 0;

            // 1. MESIN HITUNG: Rata-Rata Sumatif
            $total_sumatif = 0;
            $jumlah_tp_terisi = 0;
            $rata_sumatif = 0;

            // Melakukan looping ke semua input kolom nilai TP milik 1 siswa ini
            foreach ($sumatif_array as $tp_id => $nilai_tp) {
                if ($nilai_tp !== null && $nilai_tp !== '') {
                    $total_sumatif += (float) $nilai_tp;
                    $jumlah_tp_terisi++;
                }
            }

            // Jika ada nilai Sumatif yang diisi, hitung rata-ratanya
            if ($jumlah_tp_terisi > 0) {
                $rata_sumatif = $total_sumatif / $jumlah_tp_terisi;
            }

            // 2. MESIN HITUNG: Nilai Rapor Akhir (60% + 20% + 20%)
            $nilai_rapor = (0.6 * $rata_sumatif) + (0.2 * $psts) + (0.2 * $psas);

            // Hanya simpan jika guru mengisi minimal salah satu nilai (biar database tidak penuh angka nol 0 semua)
            if ($jumlah_tp_terisi > 0 || $psts > 0 || $psas > 0) {
                
                Nilai::updateOrCreate(
                    [
                        'siswa_id'          => $siswa_id,
                        'kelas_id'          => $kelas_id,
                        'mata_pelajaran_id' => $mapel_id,
                        'semester_id'       => $semester_aktif_id,
                    ],
                    [
                        'nilai_sumatif' => $sumatif_array, // Otomatis diconvert ke JSON oleh fitur $casts di Model!
                        'rata_sumatif'  => round($rata_sumatif, 2),
                        'psts'          => $psts,
                        'psas'          => $psas,
                        'nilai_rapor'   => round($nilai_rapor, 2),
                    ]
                );
                
                $sukses_tersimpan++;
            }
        }

        return redirect()->route('rapor.nilai.index', [
            'kelas_id'          => $kelas_id,
            'mata_pelajaran_id' => $mapel_id
        ])->with('success', $sukses_tersimpan . ' Data Nilai Rapor (Sumatif, PSTS, PSAS) berhasil dikalkulasi dan disimpan!');
    }
}