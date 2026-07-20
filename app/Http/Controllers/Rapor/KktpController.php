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
        $user = auth()->user();
        // 1. Menangkap filter dari Dropdown
        $kelas_id = $request->input('kelas_id');
        $mata_pelajaran_id = $request->input('mata_pelajaran_id');
        // ====================================================================
        // GEMBOK KELAS & MAPEL (MELEWATI JEMBATAN "KODE GURU")
        // ====================================================================
        
        if ($user->hasPermission('akses-semua-data')) {
            $kelases = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
            $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        } else {
            $pegawai = \App\Models\Pegawai::where('user_id', $user->id)->first();
            
            if ($pegawai) {
                // A. Cari semua "Kode Tugas Mengajar" milik guru ini
                $kodeGurus = \App\Models\KodeGuru::where('pegawai_id', $pegawai->id)->get();
                $kodeGuruIds = $kodeGurus->pluck('id');
                
                // B. Tarik ID Mapel langsung dari tabel KodeGuru
                $mapelIdsDiampu = $kodeGurus->pluck('mata_pelajaran_id')->unique();
                // C. Cari jadwal pelajaran yang menggunakan kode-kode guru di atas
                $jadwalGuru = \App\Models\JadwalPelajaran::whereIn('kode_guru_id', $kodeGuruIds)->get();
                $kelasIdsDiampu = $jadwalGuru->pluck('kelas_id')->unique();
                // D. Eksekusi penarikan data Master Kelas & Mapel
                $kelases = Kelas::whereIn('id', $kelasIdsDiampu)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
                $mapels = MataPelajaran::whereIn('id', $mapelIdsDiampu)->orderBy('nama_mapel', 'asc')->get();
            } else {
                $kelases = collect();
                $mapels = collect();
            }
        }
        $siswas = collect();
        $tujuanPembelajarans = collect();
        $kktpData = []; 
        if ($kelas_id && $mata_pelajaran_id) {
            
            // PENCEGAHAN URL HACKING
            if (!$kelases->contains('id', $kelas_id) || !$mapels->contains('id', $mata_pelajaran_id)) {
                abort(403, 'Akses Ditolak! Anda tidak memiliki jadwal mengajar di Kelas/Mapel ini.');
            }
            $kelas = Kelas::find($kelas_id);
            
            if ($kelas) {
                // Ambil Tujuan Pembelajaran (TP)
                $tujuanPembelajarans = \App\Models\TujuanPembelajaran::where('mata_pelajaran_id', $mata_pelajaran_id)
                                        ->where('tingkat', $kelas->tingkat)
                                        ->orderBy('nomor_tujuan', 'asc')
                                        ->get();
                // Ambil daftar Siswa
                $siswas = \App\Models\Siswa::where('kelas_id', $kelas_id)
                               ->orderBy('nama_lengkap', 'asc')
                               ->get();
                // Ambil data nilai KKTP
                $kktps = \App\Models\Kktp::where('kelas_id', $kelas_id)
                             ->whereIn('tujuan_pembelajaran_id', $tujuanPembelajarans->pluck('id'))
                             ->get();
                
                foreach ($kktps as $k) {
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