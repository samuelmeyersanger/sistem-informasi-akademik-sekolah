<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\NilaiKorikuler; // (Mengikuti nama migrasi yang sedikit typo tempo hari)
use App\Models\KegiatanKokurikuler;
use App\Models\Kelas;
use App\Models\ProfilLulusan;
use App\Models\Siswa;
use Illuminate\Http\Request;

class NilaiKokurikulerController extends Controller
{
    /**
     * Menampilkan Halaman Single Page Input Nilai P5 (Kokurikuler)
     */
    public function index(Request $request)
    {
        // 1. Menangkap 3 parameter dari Dropdown secara berurutan
        $kelas_id = $request->input('kelas_id');
        $kegiatan_kokurikuler_id = $request->input('kegiatan_kokurikuler_id');
        $profil_lulusan_id = $request->input('profil_lulusan_id');

        $kelases = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        
        $kegiatans = collect();
        $profils = collect();
        $siswas = collect();
        $nilaiData = [];

        // 2. Jika Kelas dipilih, munculkan Kegiatan P5 HANYA untuk tingkat kelas tersebut (misal: Tingkat 7)
        if ($kelas_id) {
            $kelas = Kelas::find($kelas_id);
            if ($kelas) {
                $kegiatans = KegiatanKokurikuler::where('tingkat', $kelas->tingkat)
                                ->orderBy('no_urut', 'asc')
                                ->get();
            }
        }

        // 3. Jika Kegiatan dipilih, munculkan Profil Lulusan-nya
        if ($kegiatan_kokurikuler_id) {
            $kegiatan = KegiatanKokurikuler::find($kegiatan_kokurikuler_id);
            
            // Memfilter Profil Lulusan khusus yang terikat pada Kegiatan tersebut
            if ($kegiatan && $kegiatan->profil_lulusan_id) {
                $profils = ProfilLulusan::where('id', $kegiatan->profil_lulusan_id)->get();
                
                // Cerdas: Jika guru belum nge-klik profil lulusan, kita otomatiskan terpilih!
                if (!$profil_lulusan_id) {
                    $profil_lulusan_id = $kegiatan->profil_lulusan_id;
                }
            } else {
                // Jika kegiatan belum disetel profil lulusannya, tampilkan semua opsi
                $profils = ProfilLulusan::orderBy('no', 'asc')->get();
            }
        }

        // 4. Jika ketiga Filter di atas sudah ada isinya, panggil data Siswanya
        if ($kelas_id && $kegiatan_kokurikuler_id && $profil_lulusan_id) {
            
            $siswas = Siswa::where('kelas_id', $kelas_id)
                           ->orderBy('nama', 'asc')
                           ->get();

            // Ambil riwayat nilai P5 yang sudah pernah disimpan
            $nilais = NilaiKorikuler::where('kelas_id', $kelas_id)
                                    ->where('kegiatan_kokurikuler_id', $kegiatan_kokurikuler_id)
                                    ->where('profil_lulusan_id', $profil_lulusan_id)
                                    ->get();

            // Konversi ke format Array (ID Siswa) agar gampang dicetak di tabel HTML
            foreach ($nilais as $n) {
                $nilaiData[$n->siswa_id] = $n;
            }
        }

        return view('rapor.nilai-kokurikuler.index', compact(
            'kelases', 'kegiatans', 'profils', 'siswas', 'nilaiData',
            'kelas_id', 'kegiatan_kokurikuler_id', 'profil_lulusan_id'
        ));
    }

    /**
     * Menyimpan atau Memperbarui Predikat P5 secara Massal (Bulk Save)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'                => ['required', 'exists:kelas,id'],
            'kegiatan_kokurikuler_id' => ['required', 'exists:kegiatan_kokurikuler,id'],
            'profil_lulusan_id'       => ['required', 'exists:profil_lulusan,id'],
            'nilai'                   => ['required', 'array'],
        ]);

        $kelas_id = $request->kelas_id;
        $kegiatan_id = $request->kegiatan_kokurikuler_id;
        $profil_id = $request->profil_lulusan_id;
        $sukses_tersimpan = 0;

        // Looping untuk setiap inputan siswa di tabel
        foreach ($request->nilai as $siswa_id => $data) {
            
            // Predikat: Berkembang / Cakap / Mahir
            // Hanya diproses jika guru mengklik/memilih salah satu dropdown Predikat (tidak kosong)
            if (!empty($data['predikat'])) {
                
                NilaiKorikuler::updateOrCreate(
                    [
                        'siswa_id'                => $siswa_id,
                        'kelas_id'                => $kelas_id,
                        'kegiatan_kokurikuler_id' => $kegiatan_id,
                        'profil_lulusan_id'       => $profil_id,
                    ],
                    [
                        'predikat' => $data['predikat'],
                    ]
                );
                
                $sukses_tersimpan++;
            }
        }

        // Redirect kembali ke halaman input tanpa menghilangkan 3 parameter filter-nya
        return redirect()->route('rapor.nilai-kokurikuler.index', [
            'kelas_id'                => $kelas_id,
            'kegiatan_kokurikuler_id' => $kegiatan_id,
            'profil_lulusan_id'       => $profil_id,
        ])->with('success', $sukses_tersimpan . ' Data Predikat P5 berhasil disimpan!');
    }
}