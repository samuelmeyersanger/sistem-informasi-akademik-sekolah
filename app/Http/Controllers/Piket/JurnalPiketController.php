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
     * Halaman Pusat Kendali Jurnal Piket (Berdasarkan Tanggal Hari Ini)
     */
    public function index(Request $request)
    {
        // Set tanggal hari ini atau berdasarkan request filter kalender
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $namaHari = Carbon::parse($tanggal)->translatedFormat('l'); // Mengasilkan: Senin, Selasa, dst.

        // 1. Ambil data petugas piket yang bertugas berdasarkan hari terpilih
        $petugasHariIni = PetugasPiket::with('penanggungJawab')
            ->where('hari', $namaHari)
            ->first();

        // 2. Ambil data operasional harian (Izin & Absen) sesuai tanggal terpilih
        $izinSiswa = IzinKeluarSiswa::with(['kelas', 'siswa'])->where('tanggal', $tanggal)->get();
        $izinPegawai = IzinPegawai::with(['pegawai', 'mataPelajaran', 'invaler'])->where('tanggal', $tanggal)->get();
        $absenSiswa = KetidakhadiranSiswa::with(['kelas', 'siswa'])->where('tanggal', $tanggal)->get();
        $absenPegawai = KetidakhadiranPegawai::with(['pegawai', 'mataPelajaran'])->where('tanggal', $tanggal)->get();
        $catatanHarian = CatatanPiketHarian::with('pembuatCatatan')->where('tanggal', $tanggal)->first();

        // 3. Data Master untuk modal pencatatan baru
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

        // Gunakan updateOrCreate agar data per tanggal tidak terduplikasi ganda
        CatatanPiketHarian::updateOrCreate(
            ['tanggal' => $request->tanggal],
            [
                'catatan_kejadian' => $request->catatan_kejadian,
                'pegawai_id' => Auth::user()->pegawai_id ?? null // Mengikat ke id pegawai user login jika ada
            ]
        );

        return redirect()->back()->with('success', 'Catatan jurnal kejadian harian berhasil disimpan.');
    }
}