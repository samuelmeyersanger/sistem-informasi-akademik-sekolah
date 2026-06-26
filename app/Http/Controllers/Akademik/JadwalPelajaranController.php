<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\KodeGuru;
use App\Models\WaktuKbm;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JadwalPelajaranController extends Controller
{
    /**
     * Menampilkan matriks atau daftar jadwal pelajaran.
     * Mendukung filter berdasarkan kelas untuk mempermudah pengecekan.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $kelasId = $request->get('kelas_id');

        // Memuat semua relasi terkait untuk efisiensi query (Anti N+1)
        $query = JadwalPelajaran::with(['kelas', 'kodeGuru.pegawai', 'kodeGuru.mataPelajaran', 'waktuKbm', 'ruangan']);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        // Urutkan berdasarkan urutan hari kustom, lalu berdasarkan jam ke melalui relasi waktuKbm
        $jadwal = $query->get()->sortBy(function($item) {
            $hariOrder = array_flip(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $hari = $item->waktuKbm->hari ?? 'Senin';
            $jamKe = $item->waktuKbm->jam_ke ?? 0;
            return ($hariOrder[$hari] ?? 99) . '-' . sprintf('%02d', $jamKe);
        });

        // Mengambil data master untuk kebutuhan dropdown form modal
        $daftarKelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $daftarKodeGuru = KodeGuru::with(['pegawai', 'mataPelajaran'])->get();
        $daftarWaktu = WaktuKbm::orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                               ->orderBy('jam_ke', 'asc')
                               ->get();
        $daftarRuangan = Ruangan::orderBy('nama_ruangan', 'asc')->get();

        return view('akademik.jadwal_pelajaran.index', compact(
            'jadwal', 
            'daftarKelas', 
            'daftarKodeGuru', 
            'daftarWaktu', 
            'daftarRuangan',
            'kelasId'
        ));
    }

    /**
     * Menyimpan jadwal pelajaran baru setelah lolos validasi bentrok.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kelas_id'      => 'required|exists:kelas,id',
            'kode_guru_id'  => 'required|exists:kode_guru,id',
            'waktu_kbm_id'  => 'required|exists:waktu_kbm,id',
            'ruangan_id'    => 'nullable|exists:ruangan,id',
        ]);

        // 1. Ambil data waktu untuk validasi bentrok (Hari dan Jam Ke)
        $waktuPilihan = WaktuKbm::findOrFail($request->waktu_kbm_id);

        // 2. Validasi Bentrok Kelas (Satu kelas tidak boleh punya 2 mapel di jam yang sama)
        $bentrokKelas = JadwalPelajaran::where('kelas_id', $request->kelas_id)
            ->where('waktu_kbm_id', $request->waktu_kbm_id)
            ->exists();

        if ($bentrokKelas) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Bentrok! Kelas tersebut sudah memiliki jadwal pelajaran pada hari dan jam tersebut.']);
        }

        // 3. Validasi Bentrok Guru (Satu guru tidak boleh mengajar di 2 tempat di jam yang sama)
        $guruPilihan = KodeGuru::findOrFail($request->kode_guru_id);
        $bentrokGuru = JadwalPelajaran::where('waktu_kbm_id', $request->waktu_kbm_id)
            ->whereHas('kodeGuru', function($q) use ($guruPilihan) {
                $q->where('pegawai_id', $guruPilihan->pegawai_id);
            })->exists();

        if ($bentrokGuru && $guruPilihan->pegawai_id !== null) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Bentrok! Guru yang bersangkutan sedang mengajar di kelas lain pada jam tersebut.']);
        }

        // 4. Validasi Bentrok Ruangan (Jika ruangan diisi, ruangan tidak boleh dipakai kelas lain di jam yang sama)
        if ($request->ruangan_id) {
            $bentrokRuangan = JadwalPelajaran::where('ruangan_id', $request->ruangan_id)
                ->where('waktu_kbm_id', $request->waktu_kbm_id)
                ->exists();

            if ($bentrokRuangan) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Bentrok! Ruangan tersebut sudah digunakan oleh kelas lain pada jam tersebut.']);
            }
        }

        // Simpan Data
        JadwalPelajaran::create([
            'kelas_id'     => $request->kelas_id,
            'kode_guru_id' => $request->kode_guru_id,
            'waktu_kbm_id' => $request->waktu_kbm_id,
            'ruangan_id'   => $request->ruangan_id,
        ]);

        return redirect()->route('akademik.jadwal-pelajaran.index', ['kelas_id' => $request->kelas_id])
            ->with('success', 'Jadwal pelajaran baru berhasil dipasang.');
    }

    /**
     * Menghapus jadwal pelajaran (Soft Delete).
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $kelasId = $jadwal->kelas_id;
        $jadwal->delete();

        return redirect()->route('akademik.jadwal-pelajaran.index', ['kelas_id' => $kelasId])
            ->with('success', 'Slot jadwal pelajaran berhasil dihapus.');
    }
}