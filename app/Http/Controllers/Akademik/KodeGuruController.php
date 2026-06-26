<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\KodeGuru;
use App\Models\Pegawai;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KodeGuruController extends Controller
{
    /**
     * Menampilkan daftar kode guru beserta pemetaan mata pelajaran yang diampu.
     * Mendukung fitur pencarian berdasarkan kode guru atau nama pegawai.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');

        // Mengambil data dengan memuat relasi pendukung untuk menghindari N+1 Query Problem
        $query = KodeGuru::with(['pegawai', 'mataPelajaran'])->latest();

        // Fitur pencarian data
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function($p) use ($search) {
                      $p->where('nama_lengkap', 'like', "%{$search}%"); // Sesuaikan dengan field nama di tabel pegawai Anda
                  })
                  ->orWhereHas('mataPelajaran', function($m) use ($search) {
                      $m->where('nama_mapel', 'like', "%{$search}%");
                  });
            });
        }

        $kodeGuru = $query->paginate(15)->withQueryString();

        // Ambil data untuk opsi pilihan pada form modal tambah & edit
        // Diasumsikan status atau role pegawai yang ditampilkan adalah guru, jika tidak ada filter, tampilkan semua pegawai
        $daftarPegawai = Pegawai::orderBy('nama_lengkap', 'asc')->get();
        $daftarMapel = MataPelajaran::orderBy('nomor_urut', 'asc')->get();

        return view('akademik.kode_guru.index', compact('kodeGuru', 'daftarPegawai', 'daftarMapel'));
    }

    /**
     * Menyimpan data kode guru / penugasan baru ke database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kode'                 => 'required|string|max:50|unique:kode_guru,kode',
            'pegawai_id'           => 'nullable|exists:pegawai,id',
            'mata_pelajaran_id'    => 'nullable|exists:mata_pelajaran,id',
            'jumlah_jam_mengajar'  => 'required|integer|min:0',
        ]);

        KodeGuru::create([
            'kode'                 => $request->kode,
            'pegawai_id'           => $request->pegawai_id,
            'mata_pelajaran_id'    => $request->mata_pelajaran_id,
            'jumlah_jam_mengajar'  => $request->jumlah_jam_mengajar,
        ]);

        return redirect()->route('akademik.kode-guru.index')
            ->with('success', "Penugasan Kode Guru '{$request->kode}' berhasil ditambahkan ke dalam sistem.");
    }

    /**
     * Memperbarui data kode guru / penugasan mengajar di database.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $kodeGuru = KodeGuru::findOrFail($id);

        $request->validate([
            'kode'                 => 'required|string|max:50|unique:kode_guru,kode,' . $kodeGuru->id,
            'pegawai_id'           => 'nullable|exists:pegawai,id',
            'mata_pelajaran_id'    => 'nullable|exists:mata_pelajaran,id',
            'jumlah_jam_mengajar'  => 'required|integer|min:0',
        ]);

        $kodeGuru->update([
            'kode'                 => $request->kode,
            'pegawai_id'           => $request->pegawai_id,
            'mata_pelajaran_id'    => $request->mata_pelajaran_id,
            'jumlah_jam_mengajar'  => $request->jumlah_jam_mengajar,
        ]);

        return redirect()->route('akademik.kode-guru.index')
            ->with('success', "Perubahan data Kode Guru '{$kodeGuru->kode}' berhasil disimpan.");
    }

    /**
     * Menghapus data kode guru menggunakan sistem Soft Delete.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $kodeGuru = KodeGuru::findOrFail($id);
        $kode = $kodeGuru->kode;
        
        $kodeGuru->delete();

        return redirect()->route('akademik.kode-guru.index')
            ->with('success', "Kode Guru '{$kode}' berhasil dinonaktifkan (Arsip).");
    }
}