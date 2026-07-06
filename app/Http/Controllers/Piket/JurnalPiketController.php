<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\PetugasPiket;
use App\Models\IzinKeluarSiswa;
use App\Models\IzinPegawai;
use App\Models\KetidakhadiranSiswa;
use App\Models\KetidakhadiranPegawai;
use App\Models\CatatanPiketHarian;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pegawai;
use App\Models\MataPelajaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalPiketController extends Controller
{
    /**
     * =========================================================================
     * PENGAMAN OTORITAS PIKET (SECURITY GATE)
     * =========================================================================
     */
    private function checkOtoritasPiket($tanggal)
    {
        $user = Auth::user();

        // 1. Jika punya izin super admin (Kepsek/Admin), bebas cek piket hari apa saja
        // (Pastikan permission ini Anda buat nanti di tabel Manajemen Hak Akses)
        if ($user->hasPermission('akses-semua-piket')) {
            return true;
        }

        // 2. Cek apakah user adalah pegawai
        $pegawai = Pegawai::where('user_id', $user->id)->first();
        if (!$pegawai) {
            abort(403, 'Akses Ditolak: Akun Anda tidak terhubung dengan profil Pegawai.');
        }

        $namaHari = Carbon::parse($tanggal)->translatedFormat('l');

        // 3. Cari jadwal piket pada hari tersebut
        $petugasHariIni = PetugasPiket::where('hari', $namaHari)->first();
        
        if (!$petugasHariIni) {
            abort(403, "Akses Ditolak: Belum ada daftar master petugas piket yang diatur untuk hari {$namaHari}.");
        }

        // 4. Verifikasi apakah pegawai ini adalah Penanggung Jawab ATAU Anggota Piket
        $isPenanggungJawab = $petugasHariIni->penanggung_jawab_id == $pegawai->id;
        
        $isAnggota = false;
        if (is_array($petugasHariIni->anggota_piket)) {
            // Cek apakah ID pegawai ada di dalam array anggota_piket
            $isAnggota = in_array((string)$pegawai->id, $petugasHariIni->anggota_piket) || in_array($pegawai->id, $petugasHariIni->anggota_piket);
        }

        // 5. Tendang jika dia mencoba masuk di luar jadwal piketnya
        if (!$isPenanggungJawab && !$isAnggota) {
            
            // UBAH BARIS ABORT MENJADI DD SEMENTARA:
            //dd('SISTEM BEKERJA! SAYA DITOLAK OLEH CONTROLLER KARENA BUKAN JADWAL PIKET SAYA!');
            
        }
    }


    /**
     * Halaman Pusat Kendali Jurnal Piket (Berdasarkan Tanggal Hari Ini)
     */
     public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        
        // 👇 TAMBAHKAN BARIS INI SEMENTARA UNTUK MELACAK ERROR
        $namaHari = Carbon::parse($tanggal)->translatedFormat('l');
        $petugas = \App\Models\PetugasPiket::where('hari', $namaHari)->first();
        //dd('HARI INI TERBACA: ' . $namaHari, 'DATA PETUGAS: ', $petugas);
        
        // 👇 PENGAMAN (Security Check)
        $this->checkOtoritasPiket($tanggal);


        $izinSiswa = IzinKeluarSiswa::with(['kelas', 'siswa'])->where('tanggal', $tanggal)->get();
        $izinPegawai = IzinPegawai::with(['pegawai', 'mataPelajaran', 'invaler'])->where('tanggal', $tanggal)->get();
        $absenSiswa = KetidakhadiranSiswa::with(['kelas', 'siswa'])->where('tanggal', $tanggal)->get();
        $absenPegawai = KetidakhadiranPegawai::with(['pegawai', 'mataPelajaran'])->where('tanggal', $tanggal)->get();
        $catatanHarian = CatatanPiketHarian::with('pembuatCatatan')->where('tanggal', $tanggal)->first();

        $daftarKelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $daftarSiswa = Siswa::orderBy('nama_lengkap', 'asc')->get();
        $daftarPegawai = Pegawai::orderBy('nama_lengkap', 'asc')->get();
        $daftarMapel = MataPelajaran::orderBy('nama_mapel', 'asc')->get();

        return view('piket.jurnal.index', compact(
            'tanggal', 'namaHari', 'petugasHariIni', 
            'izinSiswa', 'izinPegawai', 'absenSiswa', 'absenPegawai', 'catatanHarian',
            'daftarKelas', 'daftarSiswa', 'daftarPegawai', 'daftarMapel'
        ));
    }

    /**
     * POST: Simpan Izin Keluar Siswa Baru
     */
    public function storeIzinSiswa(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id',
            'siswa_id' => 'required|exists:siswa,id',
            'waktu_keluar' => 'required',
            'alasan_keluar' => 'required|string'
        ]);

        // 👇 PENGAMAN
        $this->checkOtoritasPiket($request->tanggal);

        IzinKeluarSiswa::create([
            'tanggal' => $request->tanggal,
            'kelas_id' => $request->kelas_id,
            'siswa_id' => $request->siswa_id,
            'waktu_keluar' => $request->waktu_keluar,
            'alasan_keluar' => $request->alasan_keluar,
            'status' => 'Belum Kembali'
        ]);

        return redirect()->back()->with('success', 'Izin keluar siswa berhasil dicatat.');
    }

    /**
     * PUT: Konfirmasi Siswa Telah Kembali ke Sekolah
     */
    public function kembaliSiswa($id)
    {
        $izin = IzinKeluarSiswa::findOrFail($id);
        
        // 👇 PENGAMAN (Ambil tanggal dari data izinnya)
        $this->checkOtoritasPiket($izin->tanggal);

        $izin->update([
            'waktu_kembali' => Carbon::now()->toTimeString(),
            'status' => 'Sudah Kembali',
            'tanda_tangan_piket' => 'Dikonfirmasi oleh: ' . (Auth::user()->name ?? 'Petugas Piket')
        ]);

        return redirect()->back()->with('success', 'Konfirmasi kedatangan siswa berhasil diperbarui.');
    }

    /**
     * POST: Simpan Izin Keluar Pegawai Baru
     */
    public function storeIzinPegawai(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'pegawai_id' => 'required|exists:pegawai,id',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
            'waktu_keluar' => 'required',
            'alasan_keluar' => 'required|string',
            'invaler_id' => 'nullable|exists:pegawai,id'
        ]);

        // 👇 PENGAMAN
        $this->checkOtoritasPiket($request->tanggal);

        IzinPegawai::create([
            'tanggal' => $request->tanggal,
            'pegawai_id' => $request->pegawai_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'waktu_keluar' => $request->waktu_keluar,
            'alasan_keluar' => $request->alasan_keluar,
            'invaler_id' => $request->invaler_id,
            'status' => 'Belum Kembali'
        ]);

        return redirect()->back()->with('success', 'Izin keluar pegawai berhasil dicatat.');
    }

    /**
     * PUT: Konfirmasi Pegawai Telah Kembali ke Sekolah
     */
    public function kembaliPegawai($id)
    {
        $izin = IzinPegawai::findOrFail($id);
        
        // 👇 PENGAMAN
        $this->checkOtoritasPiket($izin->tanggal);

        $izin->update([
            'waktu_kembali' => Carbon::now()->toTimeString(),
            'status' => 'Sudah Kembali',
            'tanda_tangan_piket' => 'Dikonfirmasi oleh: ' . (Auth::user()->name ?? 'Petugas Piket')
        ]);

        return redirect()->back()->with('success', 'Konfirmasi kedatangan pegawai berhasil diperbarui.');
    }

    /**
     * POST: Catat Ketidakhadiran Siswa
     */
    public function storeAbsenSiswa(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id',
            'siswa_id' => 'required|exists:siswa,id',
            'keterangan' => 'required|in:Sakit,Izin,Alpha'
        ]);

        // 👇 PENGAMAN
        $this->checkOtoritasPiket($request->tanggal);

        KetidakhadiranSiswa::create($request->all());

        return redirect()->back()->with('success', 'Ketidakhadiran siswa berhasil direkam.');
    }

    /**
     * POST: Catat Ketidakhadiran Pegawai / Guru
     */
    public function storeAbsenPegawai(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'pegawai_id' => 'required|exists:pegawai,id',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
            'keterangan' => 'required|in:Sakit,Izin,Alpha',
            'tindak_lanjut' => 'nullable|string'
        ]);

        // 👇 PENGAMAN
        $this->checkOtoritasPiket($request->tanggal);

        KetidakhadiranPegawai::create($request->all());

        return redirect()->back()->with('success', 'Ketidakhadiran pegawai berhasil direkam.');
    }

    /**
     * POST/PUT: Simpan atau Perbarui Catatan Kejadian Penting Harian
     */
    public function storeCatatan(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'catatan_kejadian' => 'required|string'
        ]);

        // 👇 PENGAMAN
        $this->checkOtoritasPiket($request->tanggal);

        CatatanPiketHarian::updateOrCreate(
            ['tanggal' => $request->tanggal],
            [
                'catatan_kejadian' => $request->catatan_kejadian,
                'pegawai_id' => Auth::user()->pegawai_id ?? null 
            ]
        );

        return redirect()->back()->with('success', 'Catatan jurnal kejadian harian berhasil disimpan.');
    }
}