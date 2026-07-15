<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\GayaBelajarSoal;
use App\Models\GayaBelajarHasil;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class GayaBelajarController extends Controller
{
    /**
     * =========================================================================
     * AREA 1: GURU BK (MANAJEMEN SOAL & MELIHAT REKAP HASIL)
     * =========================================================================
     */

    // Menampilkan Dashboard Utama Guru BK (Daftar Soal & Rekap Hasil)
    public function index()
    {
        $soal = GayaBelajarSoal::all();
        
        // Menarik semua data hasil gaya belajar beserta profil siswanya
        $hasil = GayaBelajarHasil::with(['siswa.kelas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bk.gaya_belajar.index', compact('soal', 'hasil'));
    }

    // Guru BK: Menyimpan Pertanyaan Baru
    public function storeSoal(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'opsi_visual' => 'required|string',
            'opsi_auditory' => 'required|string',
            'opsi_kinesthetic' => 'required|string',
        ]);

        GayaBelajarSoal::create($request->all());

        return redirect()->back()->with('success', 'Pertanyaan baru berhasil ditambahkan!');
    }

    // Guru BK: Mengupdate Pertanyaan
    public function updateSoal(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'opsi_visual' => 'required|string',
            'opsi_auditory' => 'required|string',
            'opsi_kinesthetic' => 'required|string',
        ]);

        $soal = GayaBelajarSoal::findOrFail($id);
        $soal->update($request->all());

        return redirect()->back()->with('success', 'Pertanyaan berhasil diperbarui!');
    }

    // Guru BK: Menghapus Pertanyaan
    public function destroySoal($id)
    {
        $soal = GayaBelajarSoal::findOrFail($id);
        $soal->delete();

        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus!');
    }


    /**
     * =========================================================================
     * AREA 2: SISWA (MENGISI KUESIONER)
     * =========================================================================
     */

    // Halaman Publik: Menampilkan Form Kuesioner ke Siswa
    public function formSiswa()
    {
        // Ambil semua daftar kelas untuk Dropdown
        $kelasList = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        
        // Ambil semua soal yang tersedia
        $soal = GayaBelajarSoal::all();

        return view('bk.gaya_belajar.form_siswa', compact('kelasList', 'soal'));
    }

    // AJAX Endpoint: Mengambil daftar siswa berdasarkan Kelas yang dipilih
    public function getSiswaByKelas($kelas_id)
    {
        // Cari ID siswa-siswa yang sudah pernah mengisi kuesioner
        $siswaSudahIsi = \App\Models\GayaBelajarHasil::pluck('siswa_id')->toArray();
        // Tarik data siswa yang aktif, dan KECUALIKAN nama yang sudah pernah mengisi
        $siswa = \App\Models\Siswa::where('kelas_id', $kelas_id)
                      ->where('status_siswa', 'Aktif')
                      ->whereNotIn('id', $siswaSudahIsi) // 🟢 Baris ini yang akan menyembunyikan nama mereka
                      ->orderBy('nama_lengkap', 'asc')
                      ->select('id', 'nama_lengkap', 'nipd')
                      ->get();
                      
        return response()->json($siswa);
    }

    // Memproses Jawaban Siswa & Menentukan Gaya Belajar Dominan
    public function submitSiswa(Request $request)
    {
        // 🟢 Tambahkan 'unique:gaya_belajar_hasil,siswa_id' untuk benteng keamanan ekstra
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id|unique:gaya_belajar_hasil,siswa_id',
            'jawaban' => 'required|array',
        ], [
            'siswa_id.unique' => 'Kamu sudah pernah mengisi kuesioner ini sebelumnya!'
        ]);
        $skor_visual = 0;
        $skor_auditory = 0;
        $skor_kinesthetic = 0;
        foreach ($request->jawaban as $soal_id => $tipe) {
            if ($tipe === 'V') $skor_visual++;
            elseif ($tipe === 'A') $skor_auditory++;
            elseif ($tipe === 'K') $skor_kinesthetic++;
        }
        $skorTertinggi = max($skor_visual, $skor_auditory, $skor_kinesthetic);
        $gayaDominan = [];
        if ($skor_visual == $skorTertinggi) $gayaDominan[] = 'Visual';
        if ($skor_auditory == $skorTertinggi) $gayaDominan[] = 'Auditory';
        if ($skor_kinesthetic == $skorTertinggi) $gayaDominan[] = 'Kinesthetic';
        $hasilDominan = implode(' & ', $gayaDominan);
        // 🟢 Ubah dari updateOrCreate menjadi create biasa
        GayaBelajarHasil::create([
            'siswa_id' => $request->siswa_id,
            'skor_visual' => $skor_visual,
            'skor_auditory' => $skor_auditory,
            'skor_kinesthetic' => $skor_kinesthetic,
            'gaya_dominan' => $hasilDominan,
        ]);
        return redirect()->back()->with('success', 'Kuesioner berhasil dikirim! Gaya belajar dominan kamu adalah: ' . $hasilDominan);
    }
}