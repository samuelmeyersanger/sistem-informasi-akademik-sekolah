<?php
namespace App\Http\Controllers;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\AnggotaKelas;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiKelasExport;
use Barryvdh\DomPDF\Facade\Pdf;
class PusatDownloadController extends Controller
{
    public function index()
    {
        $daftarKelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        return view('pusat_download.index', compact('daftarKelas'));
    }
    public function downloadAbsensi(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'format' => 'required|in:excel,pdf'
        ]);
        $kelas = Kelas::with('waliKelas')->findOrFail($request->kelas_id);
        $semesterAktif = Semester::with('tahunAjaran')->where('is_aktif', true)->first();
        $anggota = AnggotaKelas::with('siswa')
            ->where('kelas_id', $kelas->id)
            ->where('semester_id', $semesterAktif->id ?? null)
            ->get()
            ->sortBy(function ($item) {
                return $item->siswa->nama_lengkap;
            });
        // 🟢 UBAH INI NANTI sesuai Model Profil Sekolah Anda
        $profil = null; // Contoh: \App\Models\ProfilSekolah::first();
        $nama_sekolah = $profil ? $profil->nama_sekolah : 'SMPN 4 CIBITUNG'; 
        
        $tahun_ajaran = $semesterAktif && $semesterAktif->tahunAjaran 
                        ? $semesterAktif->tahunAjaran->nama_tahun_ajaran 
                        : 'Belum Diset';
        $data = [
            'kelas' => $kelas,
            'anggota' => $anggota,
            'nama_sekolah' => $nama_sekolah,
            'tahun_ajaran' => $tahun_ajaran,
            'laki_laki' => $anggota->where('siswa.jenis_kelamin', 'Laki-laki')->count(),
            'perempuan' => $anggota->where('siswa.jenis_kelamin', 'Perempuan')->count(),
        ];
        $namaFile = "Daftar_Hadir_Kelas_" . str_replace(' ', '_', $kelas->nama_kelas);
        if ($request->format === 'excel') {
            return Excel::download(new AbsensiKelasExport($data), $namaFile . '.xlsx');
        }
        // Render PDF menggunakan kertas F4 (folio) bentuk Potrait
        $pdf = Pdf::loadView('pusat_download.exports.absensi', $data)
                  ->setPaper('folio', 'portrait');
                  
        return $pdf->download($namaFile . '.pdf');
    }
}