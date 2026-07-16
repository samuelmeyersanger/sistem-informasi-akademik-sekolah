<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\CatatanWaliKelas;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class CatatanWaliKelasController extends Controller
{
    /**
     * Menampilkan Halaman Single Page Input Catatan Wali Kelas
     */
    public function index(Request $request)
    {
        // Menangkap filter dari form Dropdown
        $kelas_id = $request->input('kelas_id');

        // Mengambil data Master Kelas untuk Dropdown
        $kelases = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        $siswas = collect(); // Koleksi kosong secara default

        // Jika Guru sudah memilih Kelas, baru kita munculkan daftar Siswanya
        if ($kelas_id) {
            
            // Ambil semua siswa di kelas tersebut beserta catatan mereka sebelumnya (jika ada)
            $siswas = Siswa::where('kelas_id', $kelas_id)
                ->orderBy('nama', 'asc')
                // Pastikan Model Siswa memiliki relasi: public function catatanWaliKelas() { return $this->hasOne(CatatanWaliKelas::class, 'siswa_id'); }
                ->with(['catatanWaliKelas' => function($query) use ($kelas_id) {
                    $query->where('kelas_id', $kelas_id);
                }])
                ->get();
        }

        return view('rapor.catatan-wali-kelas.index', compact(
            'kelases', 
            'siswas', 
            'kelas_id'
        ));
    }

    /**
     * Menyimpan atau Memperbarui Catatan secara Massal (Bulk Save)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'     => ['required', 'exists:kelas,id'],
            'data_catatan' => ['required', 'array'], // Menangkap input berantai (array multidimensi)
        ]);

        $kelas_id = $request->kelas_id;
        $sukses_tersimpan = 0;

        // Melakukan perulangan untuk setiap siswa yang diberi catatan di tabel
        foreach ($request->data_catatan as $siswa_id => $data) {
            
            // Hanya simpan jika guru benar-benar mengetikkan catatan
            // (Agar database tidak penuh oleh teks kosong)
            if (!empty($data['catatan'])) {
                
                // Simpan Baru atau Timpa yang Lama
                CatatanWaliKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswa_id,
                        'kelas_id' => $kelas_id,
                    ],
                    [
                        'catatan'  => $data['catatan'],
                    ]
                );
                
                $sukses_tersimpan++;
            }
        }

        // Kembalikan ke halaman semula tanpa menghilangkan filter dropdown-nya
        return redirect()->route('rapor.catatan-wali-kelas.index', [
            'kelas_id' => $kelas_id
        ])->with('success', $sukses_tersimpan . ' Catatan Wali Kelas berhasil disimpan!');
    }
}