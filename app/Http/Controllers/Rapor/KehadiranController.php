<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class KehadiranController extends Controller
{
        /**
     * Menampilkan Halaman Single Page Input Absensi
     */
    public function index(Request $request)
    {
        // 1. Menangkap filter dari form Dropdown
        $kelas_id = $request->input('kelas_id');
        // 2. Mengambil data Master Kelas HANYA yang dipegang oleh Wali Kelas yang sedang login
        $kelases = Kelas::aksesSesuaiWali(auth()->user())
                        ->orderBy('tingkat', 'asc')
                        ->orderBy('nama_kelas', 'asc')
                        ->get();
        $siswas = collect(); // Koleksi kosong secara default
        if ($kelas_id) {
            
            // 3. PENGAMANAN (URL HACKING PREVENTION)
            if (!$kelases->contains('id', $kelas_id)) {
                abort(403, 'Akses Ditolak! Anda tidak memiliki wewenang untuk melihat absensi kelas ini.');
            }
            
            // 4. Ambil semua siswa di kelas tersebut beserta data kehadiran mereka
            $siswas = Siswa::where('kelas_id', $kelas_id)
                ->orderBy('nama_lengkap', 'asc') // Pastikan nama kolomnya benar 'nama_lengkap' atau 'nama'
                ->with(['kehadiran' => function($query) use ($kelas_id) {
                    $query->where('kelas_id', $kelas_id);
                }])
                ->get();
        }
        return view('rapor.kehadiran.index', compact(
            'kelases', 
            'siswas', 
            'kelas_id'
        ));
    }

    /**
     * Menyimpan atau Memperbarui Absensi secara Massal (Bulk Save)
     * Ini dipanggil ketika Guru mengklik tombol "Simpan" di tabel absensi
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'  => ['required', 'exists:kelas,id'],
            'kehadiran' => ['required', 'array'], // Menangkap input array dari form tabel
        ]);

        $kelas_id = $request->kelas_id;
        $sukses_tersimpan = 0;

        // Melakukan perulangan untuk setiap baris siswa di tabel
        foreach ($request->kehadiran as $siswa_id => $data) {
            
            // Konversi nilai kosong (null) dari form menjadi angka 0
            $sakit = $data['sakit'] ?? 0;
            $izin = $data['izin'] ?? 0;
            $alpa = $data['tanpa_keterangan'] ?? 0;
            
            // Simpan atau Perbarui (Update or Create)
            Kehadiran::updateOrCreate(
                [
                    'siswa_id' => $siswa_id,
                    'kelas_id' => $kelas_id,
                ],
                [
                    'sakit'            => (int) $sakit,
                    'izin'             => (int) $izin,
                    'tanpa_keterangan' => (int) $alpa,
                ]
            );
            
            $sukses_tersimpan++;
        }

        // Kembalikan ke halaman yang sama beserta filter kelas_id-nya
        return redirect()->route('rapor.kehadiran.index', [
            'kelas_id' => $kelas_id
        ])->with('success', $sukses_tersimpan . ' Data absensi siswa berhasil disimpan!');
    }
}