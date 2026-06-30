<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\PelanggaranSiswa;
use App\Models\SiswaTerlambat;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class KedisiplinanSiswaController extends Controller
{
    /**
     * Halaman Utama Kedisiplinan (Menampung Tab Pelanggaran & Tab Keterlambatan)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $currentTab = $request->input('tab', 'pelanggaran'); // Default ke tab pelanggaran jika tidak ada parameter

        // Ambil data untuk dropdown Form Modal
        $listSiswa = Siswa::orderBy('nama_lengkap', 'asc')->get();
        $listKelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $listPegawai = Pegawai::orderBy('nama_lengkap', 'asc')->get();

        // Inisialisasi variabel agar tidak error di Blade
        $pelanggarans = collect();
        $keterlambatans = collect();

        if ($currentTab === 'pelanggaran') {
            $query = PelanggaranSiswa::with(['siswa', 'kelas', 'pegawai']);
            if (!empty($search)) {
                $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%');
                })->orWhere('jenis_pelanggaran', 'like', '%' . $search . '%');
            }
            $pelanggarans = $query->latest('tanggal')->paginate(10)->appends(['search' => $search, 'tab' => 'pelanggaran']);
        } else {
            $query = SiswaTerlambat::with(['siswa', 'kelas', 'pegawai']);
            if (!empty($search)) {
                $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%');
                })->orWhere('alasan', 'like', '%' . $search . '%');
            }
            $keterlambatans = $query->latest('tanggal')->paginate(10)->appends(['search' => $search, 'tab' => 'terlambat']);
        }

        return view('bk.kedisiplinan.index', compact(
            'pelanggarans', 'keterlambatans', 'listSiswa', 'listKelas', 'listPegawai', 'search', 'currentTab'
        ));
    }

    /**
     * Store Pelanggaran Siswa
     */
    public function storePelanggaran(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'siswa_id' => 'required|exists:siswa,id',
            'kelas_id' => 'required|exists:kelas,id',
            'kategori' => 'required|string', // Ringan, Sedang, Berat
            'jenis_pelanggaran' => 'required|string',
            'deskripsi' => 'required|string',
            'poin' => 'required|integer|min:1',
            'tindak_lanjut' => 'required|string',
            'pegawai_id' => 'required|exists:pegawai,id',
        ]);

        PelanggaranSiswa::create($data);

        return redirect()->route('bk.kedisiplinan.index', ['tab' => 'pelanggaran'])->with('success', 'Data pelanggaran siswa berhasil dicatat.');
    }

    /**
     * Store Siswa Terlambat
     */
    public function storeTerlambat(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'siswa_id' => 'required|exists:siswa,id',
            'kelas_id' => 'required|exists:kelas,id',
            'jam_masuk' => 'required',
            'menit_terlambat' => 'required|integer|min:1',
            'alasan' => 'required|string',
            'tindak_lanjut' => 'nullable|string',
            'pegawai_id' => 'required|exists:pegawai,id',
        ]);

        SiswaTerlambat::create($data);

        return redirect()->route('bk.kedisiplinan.index', ['tab' => 'terlambat'])->with('success', 'Data siswa terlambat berhasil dicatat.');
    }

    /**
     * Destroy Pelanggaran
     */
    public function destroyPelanggaran(PelanggaranSiswa $pelanggaran)
    {
        $pelanggaran->delete();
        return redirect()->route('bk.kedisiplinan.index', ['tab' => 'pelanggaran'])->with('success', 'Data pelanggaran berhasil dihapus.');
    }

    /**
     * Destroy Terlambat
     */
    public function destroyTerlambat(SiswaTerlambat $terlambat)
    {
        $terlambat->delete();
        return redirect()->route('bk.kedisiplinan.index', ['tab' => 'terlambat'])->with('success', 'Data keterlambatan berhasil dihapus.');
    }
}