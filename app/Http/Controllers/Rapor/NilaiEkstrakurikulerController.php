<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\NilaiEkstrakurikuler;
use App\Models\Ekstrakurikuler;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class NilaiEkstrakurikulerController extends Controller
{
    /**
     * Menampilkan Halaman Single Page Input Nilai Ekskul
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        // 1. Menangkap filter dari form Dropdown
        $ekstrakurikuler_id = $request->input('ekstrakurikuler_id');
        $kelas_id = $request->input('kelas_id');
        // 2. GEMBOK EKSKUL: Hanya tarik ekskul yang dipegang oleh user (Pembina)
        $ekstrakurikulers = \App\Models\Ekstrakurikuler::aksesPembina($user)
                                ->where('is_aktif', true)
                                ->orderBy('nama', 'asc')
                                ->get();
        // 3. KELAS DIBIARKAN TERBUKA: Agar pembina bisa mencari muridnya dari kelas manapun
        $kelases = \App\Models\Kelas::orderBy('tingkat', 'asc')
                        ->orderBy('nama_kelas', 'asc')
                        ->get();
        $siswas = collect(); // Koleksi kosong secara default
        // Jika Guru sudah memilih Ekskul dan Kelas, baru kita munculkan daftar Siswanya
        if ($ekstrakurikuler_id && $kelas_id) {
            
            // PENCEGAHAN URL HACKING (Mencegah guru menilai ekskul milik guru lain)
            if (!$ekstrakurikulers->contains('id', $ekstrakurikuler_id)) {
                abort(403, 'Akses Ditolak! Anda bukan Pembina dari Ekstrakurikuler ini.');
            }
            
            // AMBIL SISWA YANG HANYA IKUT EKSKUL TERSEBUT
            $siswas = \App\Models\Siswa::where('kelas_id', $kelas_id)
                ->whereHas('ekskulYangDiikuti', function($query) use ($ekstrakurikuler_id) {
                    $query->where('ekstrakurikuler_id', $ekstrakurikuler_id);
                })
                ->orderBy('nama_lengkap', 'asc')
                ->with(['nilaiEkstrakurikuler' => function($query) use ($ekstrakurikuler_id) {
                    $query->where('ekstrakurikuler_id', $ekstrakurikuler_id);
                }])
                ->get();
        }
        return view('rapor.nilai-ekstrakurikuler.index', compact(
            'ekstrakurikulers', 
            'kelases', 
            'siswas', 
            'ekstrakurikuler_id', 
            'kelas_id'
        ));
    }

    /**
     * Menyimpan atau Memperbarui Nilai secara Massal (Bulk Save)
     * Ini dipanggil ketika Guru mengklik tombol "Simpan Semua Nilai" di tabel
     */
    public function store(Request $request)
    {
        $request->validate([
            'ekstrakurikuler_id' => ['required', 'exists:ekstrakurikuler,id'],
            'kelas_id'           => ['required', 'exists:kelas,id'],
            'nilai'              => ['required', 'array'], // Menangkap inputan berantai dari form tabel
        ]);

        $ekskul_id = $request->ekstrakurikuler_id;
        $sukses_tersimpan = 0;

        // Melakukan perulangan untuk setiap baris siswa di tabel
        foreach ($request->nilai as $siswa_id => $data) {
            // Hanya simpan jika guru mengisi predikatnya
            if (!empty($data['predikat'])) {
                
                // updateOrCreate akan:
                // 1. Meng-Update data jika siswa tersebut sudah punya nilai di ekskul ini
                // 2. Meng-Insert data baru jika siswa tersebut belum punya nilai
                NilaiEkstrakurikuler::updateOrCreate(
                    [
                        'siswa_id'           => $siswa_id,
                        'ekstrakurikuler_id' => $ekskul_id,
                    ],
                    [
                        'predikat'  => $data['predikat'],
                        'deskripsi' => $data['deskripsi'] ?? null,
                    ]
                );
                
                $sukses_tersimpan++;
            }
        }

        // Kembalikan ke halaman yang sama (lengkap dengan filter URL-nya agar tabel tidak tertutup)
        return redirect()->route('rapor.nilai-ekstrakurikuler.index', [
            'ekstrakurikuler_id' => $request->ekstrakurikuler_id,
            'kelas_id'           => $request->kelas_id
        ])->with('success', $sukses_tersimpan . ' Nilai Ekstrakurikuler berhasil disimpan!');
    }

    /**
     * Menghapus Nilai (Tombol HAPUS di ujung kanan tabel)
     */
    public function destroy(Request $request, $id)
    {
        $nilai = NilaiEkstrakurikuler::findOrFail($id);
        
        $nilai->delete();

        // Tetap berada di halaman filter yang sama
        return redirect()->back()->with('success', 'Nilai Ekstrakurikuler berhasil dihapus/dibatalkan.');
    }
}