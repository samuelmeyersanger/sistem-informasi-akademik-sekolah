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
        $siswa = Siswa::where('kelas_id', $kelas_id)
                      ->where('status_siswa', 'Aktif')
                      ->orderBy('nama_lengkap', 'asc')
                      ->select('id', 'nama_lengkap', 'nipd')
                      ->get();
                      
        return response()->json($siswa);
    }

    // Memproses Jawaban Siswa & Menentukan Gaya Belajar Dominan
    public function submitSiswa(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jawaban' => 'required|array', // Jawaban berupa array ID_Soal => Tipe (V/A/K)
        ]);

        $skor_visual = 0;
        $skor_auditory = 0;
        $skor_kinesthetic = 0;

        // Hitung total jawaban per tipe
        foreach ($request->jawaban as $soal_id => $tipe) {
            if ($tipe === 'V') $skor_visual++;
            elseif ($tipe === 'A') $skor_auditory++;
            elseif ($tipe === 'K') $skor_kinesthetic++;
        }

        // Tentukan Gaya Belajar Dominan
        $skorTertinggi = max($skor_visual, $skor_auditory, $skor_kinesthetic);
        $gayaDominan = [];

        if ($skor_visual == $skorTertinggi) $gayaDominan[] = 'Visual';
        if ($skor_auditory == $skorTertinggi) $gayaDominan[] = 'Auditory';
        if ($skor_kinesthetic == $skorTertinggi) $gayaDominan[] = 'Kinesthetic';

        // Gabungkan jika seri (contoh: "Visual & Auditory")
        $hasilDominan = implode(' & ', $gayaDominan);

        // Simpan Hasil ke Database (Gunakan updateOrCreate agar jika mengisi ulang, datanya tertimpa)
        GayaBelajarHasil::updateOrCreate(
            ['siswa_id' => $request->siswa_id],
            [
                'skor_visual' => $skor_visual,
                'skor_auditory' => $skor_auditory,
                'skor_kinesthetic' => $skor_kinesthetic,
                'gaya_dominan' => $hasilDominan,
                'updated_at' => now(), // Memaksa timestamp berubah meskipun nilainya sama
            ]
        );

        // Lempar kembali ke halaman sukses
        return redirect()->back()->with('success', 'Kuesioner berhasil dikirim! Gaya belajar dominan kamu adalah: ' . $hasilDominan);
    }
}