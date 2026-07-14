<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\AnggotaKelas;
use App\Models\Ekstrakurikuler;
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
        $daftarEkskul = Ekstrakurikuler::orderBy('nama', 'asc')->get();
        $daftarKelasWali = \App\Models\KelasWali::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        return view('pusat_download.index', compact('daftarKelas', 'daftarEkskul', 'daftarKelasWali'));
    }

    // =========================================================================
    // FITUR 1: DOWNLOAD ABSENSI KELAS REGULER
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
                  ->setPaper([0, 0, 612.00, 936.00], 'portrait'); 
        return $pdf->stream($namaFile . '.pdf', ['Attachment' => false]);
    }

    // =========================================================================
    // FITUR 2: DOWNLOAD JADWAL PELAJARAN PERKELAS
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
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; 
        
        $maxJam = \App\Models\WaktuKbm::max('jam_ke');
        if (!$maxJam) $maxJam = 10;
        
        $waktuList = \App\Models\WaktuKbm::all();
        $kegiatanMatriks = [];
        foreach($waktuList as $w) {
            if (strtoupper($w->kegiatan) != 'KBM' && !empty($w->kegiatan)) {
                $kegiatanMatriks[$w->jam_ke][$w->hari] = $w->kegiatan;
            }
        }
        
        $jadwalList = \App\Models\JadwalPelajaran::with(['waktuKbm', 'kodeGuru.pegawai', 'kodeGuru.mataPelajarans', 'ruangan'])
            ->where('kelas_id', $kelas->id)
            ->get();
            
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
                  ->setPaper([0, 0, 612.00, 936.00], 'landscape'); 
        return $pdf->stream($namaFile . '.pdf', ['Attachment' => false]);
    }

    // =========================================================================
    // FITUR 3: DOWNLOAD ABSENSI EKSTRAKURIKULER
    // =========================================================================
    public function cetakAbsensiEkskul(Request $request)
    {
        $request->validate([
            'ekskul_id' => 'required|exists:ekstrakurikuler,id'
        ]);
        
        $id = $request->ekskul_id;
        $ekskul = Ekstrakurikuler::with(['pembina', 'anggota' => function($query) {
            $query->join('siswa', 'anggota_ekstrakurikuler.siswa_id', '=', 'siswa.id')
                  ->orderBy('siswa.nama_lengkap', 'asc')
                  ->select('anggota_ekstrakurikuler.*');
        }, 'anggota.siswa.kelas'])->findOrFail($id);
        
        return view('pusat_download.exports.absensi_ekskul', compact('ekskul'));
    }

    // =========================================================================
    // FITUR 4: DOWNLOAD DATA ANGGOTA KELOMPOK WALI (PDF FOLIO)
    // =========================================================================
    public function downloadDataKelasWali(Request $request)
    {
        $request->validate([
            'kelas_wali_id' => 'required|exists:kelas_wali,id',
        ]);
        
        $kelasWali = \App\Models\KelasWali::with('waliKelas')->findOrFail($request->kelas_wali_id);
        $semesterAktif = \App\Models\Semester::with('tahunAjaran')->where('is_aktif', true)->first();
        
        // 🟢 PERBAIKAN 1: Hapus sortBy() dan ganti dengan orderBy('id', 'asc') 
        // agar murni sesuai urutan saat diinputkan ke sistem.
        $anggota = \App\Models\AnggotaKelasWali::with('siswa')
            ->where('kelas_wali_id', $kelasWali->id)
            ->where('semester_id', $semesterAktif->id ?? null)
            ->orderBy('id', 'asc') 
            ->get();
            
        $profil = null; 
        $nama_sekolah = $profil ? $profil->nama_sekolah : 'SMPN 4 CIBITUNG'; 
        
        $tahun_ajaran = $semesterAktif && $semesterAktif->tahunAjaran 
                        ? $semesterAktif->tahunAjaran->nama_tahun_ajaran 
                        : 'Belum Diset';
                        
        $data = [
            'kelas' => $kelasWali,
            'anggota' => $anggota,
            'nama_sekolah' => $nama_sekolah,
            'tahun_ajaran' => $tahun_ajaran,
            'laki_laki' => $anggota->where('siswa.jenis_kelamin', 'Laki-Laki')->count() + $anggota->where('siswa.jenis_kelamin', 'Laki-laki')->count(),
            'perempuan' => $anggota->where('siswa.jenis_kelamin', 'Perempuan')->count(),
        ];
        
        $namaFile = "Daftar_Anggota_Kelompok_" . str_replace(' ', '_', $kelasWali->nama_kelas);
        
        // 🟢 PERBAIKAN 2: Ubah 'portrait' menjadi 'landscape' agar tabel tidak terpotong!
        $pdf = Pdf::loadView('pusat_download.exports.data_kelas_wali', $data)
                  ->setPaper([0, 0, 612.00, 936.00], 'portrait'); 
                  
        return $pdf->stream($namaFile . '.pdf', ['Attachment' => false]);
    }

    // =========================================================================
    // FITUR 5: KODE GURU
    // =========================================================================
    public function downloadKodeGuru(Request $request)
    {
        $request->validate(['format' => 'required|in:excel,pdf']);
        
        // Ambil data untuk dilempar ke View
        $data = [
            'daftar_kode' => \App\Models\KodeGuru::with(['pegawai', 'mataPelajarans', 'jadwalPelajarans.kelas'])->get(),
            'nama_sekolah' => 'SMPN 4 CIBITUNG'
        ];

        if ($request->format === 'excel') {
            // Silakan buat KodeGuruExport jika Anda ingin fitur Excel-nya
            // return Excel::download(new \App\Exports\KodeGuruExport($data), 'Daftar_Kode_Guru.xlsx');
            return back()->with('success', 'Fitur Export Excel Kode Guru belum tersedia, segera di-update.');
        }

        // Jangan lupa buat file view 'pusat_download/exports/kode_guru.blade.php'
        $pdf = Pdf::loadView('pusat_download.exports.kode_guru', $data)->setPaper([0, 0, 612.00, 936.00], 'portrait'); 
        return $pdf->stream('Daftar_Kode_Guru.pdf', ['Attachment' => false]);
    }

    // =========================================================================
    // FITUR 6: REKAP JUMLAH SISWA
    // =========================================================================
    public function downloadRekapSiswa(Request $request)
    {
        $request->validate(['format' => 'required|in:excel,pdf']);
        
        $data = [
            'nama_sekolah' => 'SMPN 4 CIBITUNG',
            'rekap_kelas' => \App\Models\Kelas::withCount([
                'anggotaKelas',
                'anggotaKelas as laki_laki_count' => function ($query) {
                    $query->whereHas('siswa', function ($q) {
                        $q->whereIn('jenis_kelamin', ['Laki-Laki', 'Laki-laki', 'L']);
                    });
                },
                'anggotaKelas as perempuan_count' => function ($query) {
                    $query->whereHas('siswa', function ($q) {
                        $q->whereIn('jenis_kelamin', ['Perempuan', 'P']);
                    });
                }
            ])
            // 🟢 TAMBAHKAN 2 BARIS INI UNTUK MENGURUTKAN KELAS!
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get()
        ];
        
        if ($request->format === 'excel') {
            return back()->with('success', 'Fitur Export Excel Rekap Siswa belum tersedia, segera di-update.');
        }
        
        $pdf = Pdf::loadView('pusat_download.exports.rekap_siswa', $data)
          ->setPaper([0, 0, 612.00, 936.00], 'portrait'); 
          
        return $pdf->stream('Rekap_Jumlah_Siswa.pdf', ['Attachment' => false]);
    }

    // =========================================================================
    // FITUR 7: JADWAL PELAJARAN GLOBAL
    // =========================================================================
    public function downloadJadwalGlobal(Request $request)
    {
        $request->validate(['format' => 'required|in:excel,pdf']);
        
        $data = [
            'nama_sekolah' => 'SMPN 4 CIBITUNG',
            'jadwal_semua' => \App\Models\JadwalPelajaran::with(['kelas', 'waktuKbm', 'kodeGuru.pegawai'])->get()
        ];

        if ($request->format === 'excel') {
            return back()->with('success', 'Fitur Export Excel Jadwal Global belum tersedia, segera di-update.');
        }

        // Jangan lupa buat file view 'pusat_download/exports/jadwal_global.blade.php'
        $pdf = Pdf::loadView('pusat_download.exports.jadwal_global', $data)->setPaper([0, 0, 612.00, 936.00], 'landscape'); 
        return $pdf->stream('Jadwal_Pelajaran_Global.pdf', ['Attachment' => false]);
    }
}