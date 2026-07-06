<?php
namespace App\Http\Controllers;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\AnggotaKelas;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiKelasExport;
use App\Exports\JadwalKelasExport;
use Barryvdh\DomPDF\Facade\Pdf;
class PusatDownloadController extends Controller
{
    public function index()
    {
        $daftarKelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        return view('pusat_download.index', compact('daftarKelas'));
    }
    // =========================================================================
    // FITUR 1: DOWNLOAD ABSENSI
    // =========================================================================
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
        // Sesuaikan jika punya Model ProfilSekolah
        $profil = null; 
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
        $pdf = Pdf::loadView('pusat_download.exports.absensi', $data)
                  ->setPaper('folio', 'portrait');
        return $pdf->download($namaFile . '.pdf');
    }
    // =========================================================================
    // FITUR 2: DOWNLOAD JADWAL PELAJARAN
    // =========================================================================
    public function downloadJadwal(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'format' => 'required|in:excel,pdf'
        ]);
        $kelas = Kelas::findOrFail($request->kelas_id);
        $semesterAktif = Semester::with('tahunAjaran')->where('is_aktif', true)->first();
        
        $profil = null; 
        $nama_sekolah = $profil ? $profil->nama_sekolah : 'SMPN 4 CIBITUNG'; 
        
        $tahun_ajaran = $semesterAktif && $semesterAktif->tahunAjaran 
                        ? $semesterAktif->tahunAjaran->nama_tahun_ajaran 
                        : 'Belum Diset';
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; // Bisa ditambah 'Sabtu'
        // Jam maksimal
        $maxJam = \App\Models\WaktuKbm::max('jam_ke');
        if (!$maxJam) $maxJam = 10;
        // Ambil waktu non-KBM (contoh: Istirahat)
        $waktuList = \App\Models\WaktuKbm::all();
        $kegiatanMatriks = [];
        foreach($waktuList as $w) {
            if (strtoupper($w->kegiatan) != 'KBM' && !empty($w->kegiatan)) {
                $kegiatanMatriks[$w->jam_ke][$w->hari] = $w->kegiatan;
            }
        }
        // Ambil data Jadwal Pelajaran (Beserta Relasi Lengkap: Waktu, Guru, Mapel, Ruangan)
        $jadwalList = \App\Models\JadwalPelajaran::with(['waktuKbm', 'kodeGuru.pegawai', 'kodeGuru.mataPelajarans', 'ruangan'])
            ->where('kelas_id', $kelas->id)
            ->get();
        // Susun ke matriks [jam_ke][hari]
        $matriks = [];
        foreach ($jadwalList as $j) {
            if ($j->waktuKbm) {
                $matriks[$j->waktuKbm->jam_ke][$j->hari] = $j;
            }
        }
        $data = [
            'kelas' => $kelas,
            'nama_sekolah' => $nama_sekolah,
            'tahun_ajaran' => $tahun_ajaran,
            'hariList' => $hariList,
            'maxJam' => $maxJam,
            'matriks' => $matriks,
            'kegiatanMatriks' => $kegiatanMatriks,
        ];
        $namaFile = "Jadwal_Pelajaran_Kelas_" . str_replace(' ', '_', $kelas->nama_kelas);
        if ($request->format === 'excel') {
            return Excel::download(new JadwalKelasExport($data), $namaFile . '.xlsx');
        }
        $pdf = Pdf::loadView('pusat_download.exports.jadwal', $data)
                  ->setPaper('folio', 'landscape');
        return $pdf->download($namaFile . '.pdf');
    }
}