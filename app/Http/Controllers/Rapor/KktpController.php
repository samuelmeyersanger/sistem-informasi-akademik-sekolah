<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\Kktp;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TujuanPembelajaran;
use Illuminate\Http\Request;

class KktpController extends Controller
{
    /**
     * Menampilkan Halaman Single Page Input KKTP (Matriks)
     */
    public function index(Request $request)
    {
        // 1. Menangkap filter dari Dropdown (Pilih Kelas & Pilih Mapel)
        $kelas_id = $request->input('kelas_id');
        $mata_pelajaran_id = $request->input('mata_pelajaran_id');

        // Mengambil data Master untuk Dropdown
        $kelases = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get(); // Sesuaikan nama kolom mapel

        $siswas = collect();
        $tujuanPembelajarans = collect();
        $kktpData = []; // Array khusus untuk menyimpan riwayat nilai siswa

        // 2. Jika Guru sudah memilih Kelas dan Mapel
        if ($kelas_id && $mata_pelajaran_id) {
            
            // Cari data Kelas untuk mengetahui "Tingkat" kelas tersebut (misal: tingkat '7')
            $kelas = Kelas::find($kelas_id);
            
            if ($kelas) {
                // 3. Ambil Tujuan Pembelajaran (TP) HANYA untuk Mapel & Tingkat yang sesuai!
                $tujuanPembelajarans = TujuanPembelajaran::where('mata_pelajaran_id', $mata_pelajaran_id)
                                        ->where('tingkat', $kelas->tingkat)
                                        ->orderBy('nomor_tujuan', 'asc')
                                        ->get();

                // 4. Ambil daftar Siswa di kelas tersebut
                $siswas = Siswa::where('kelas_id', $kelas_id)
                               ->orderBy('nama', 'asc')
                               ->get();

                // 5. Ambil data nilai KKTP yang sudah pernah disimpan (jika ada)
                $kktps = Kktp::where('kelas_id', $kelas_id)
                             ->whereIn('tujuan_pembelajaran_id', $tujuanPembelajarans->pluck('id'))
                             ->get();
                
                // Menyusun ulang data KKTP ke format Matriks agar mudah dicek di View Blade (HTML)
                foreach ($kktps as $k) {
                    // Menyimpan data dengan pola: $kktpData[ID_SISWA][ID_TP] = data
                    $kktpData[$k->siswa_id][$k->tujuan_pembelajaran_id] = $k;
                }
            }
        }

        return view('rapor.kktp.index', compact(
            'kelases', 
            'mapels', 
            'siswas', 
            'tujuanPembelajarans', 
            'kktpData',
            'kelas_id',
            'mata_pelajaran_id'
        ));
    }

    /**
     * Menyimpan atau Memperbarui Penilaian KKTP secara Massal (Bulk Save Matrix)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'          => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'kktp'              => ['required', 'array'], 
        ]);

        $kelas_id = $request->kelas_id;
        $sukses_tersimpan = 0;

        // Konsep Input HTML: name="kktp[ID_SISWA][ID_TP][tercapai]"
        // Melakukan perulangan untuk setiap siswa...
        foreach ($request->kktp as $siswa_id => $tps) {
            // ...lalu melakukan perulangan untuk setiap Tujuan Pembelajaran milik siswa tersebut
            foreach ($tps as $tp_id => $data) {
                
                // Cek apakah guru mengisi centang Tercapai (1) atau Tidak Tercapai (1)
                $tercapai = isset($data['tercapai']) ? 1 : 0;
                $tidak_tercapai = isset($data['tidak_tercapai']) ? 1 : 0;

                // Hanya simpan jika salah satu tombol/centang dipilih (mencegah data kosong berserakan)
                if ($tercapai == 1 || $tidak_tercapai == 1) {
                    
                    Kktp::updateOrCreate(
                        [
                            'siswa_id'               => $siswa_id,
                            'kelas_id'               => $kelas_id,
                            'tujuan_pembelajaran_id' => $tp_id,
                        ],
                        [
                            'tercapai'       => $tercapai,
                            'tidak_tercapai' => $tidak_tercapai,
                        ]
                    );
                    
                    $sukses_tersimpan++;
                }
            }
        }

        // Kembalikan ke halaman semula tanpa mereset Filter (Kelas & Mapel)
        return redirect()->route('rapor.kktp.index', [
            'kelas_id'          => $kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id
        ])->with('success', $sukses_tersimpan . ' Titik Penilaian KKTP berhasil disimpan!');
    }
}