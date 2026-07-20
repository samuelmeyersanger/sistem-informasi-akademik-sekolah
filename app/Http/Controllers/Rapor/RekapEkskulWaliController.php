<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;

class RekapEkskulWaliController extends Controller
{
    /**
     * Menampilkan Halaman Rekap Nilai Ekskul Khusus Wali Kelas
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Menangkap filter dari form Dropdown
        $kelas_id = $request->input('kelas_id');

        // 2. GEMBOK KELAS: Wali Kelas hanya bisa memfilter kelas yang dia pegang
        $kelases = Kelas::aksesSesuaiWali($user)
                        ->orderBy('tingkat', 'asc')
                        ->orderBy('nama_kelas', 'asc')
                        ->get();

        $siswas = collect(); // Koleksi kosong sebagai default

        // Jika Wali Kelas sudah memilih Kelas di dropdown
        if ($kelas_id) {
            
            // 3. PENCEGAHAN URL HACKING (Mencegah guru mengintip kelas guru lain)
            if (!$kelases->contains('id', $kelas_id)) {
                abort(403, 'Akses Ditolak! Anda bukan Wali dari Kelas ini.');
            }
            
            // 4. AMBIL SEMUA ANAK DI KELAS TERSEBUT
            // Tarik beserta SEMUA nilai ekskul yang mereka miliki (beserta relasi ke master ekstrakurikuler-nya)
            $siswas = Siswa::where('kelas_id', $kelas_id)
                ->orderBy('nama_lengkap', 'asc')
                // Pastikan di model NilaiEkstrakurikuler ada fungsi relasi: public function ekstrakurikuler()
                ->with(['nilaiEkstrakurikuler.ekstrakurikuler']) 
                ->get();
        }

        // 5. Arahkan ke file View/Blade rekap (Silakan sesuaikan nama/lokasi file blade-nya nanti)
        return view('rapor.rekap-ekskul-wali.index', compact(
            'kelases', 
            'siswas', 
            'kelas_id'
        ));
    }
}