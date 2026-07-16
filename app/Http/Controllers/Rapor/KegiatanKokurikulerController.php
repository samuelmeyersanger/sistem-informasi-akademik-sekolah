<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\KegiatanKokurikuler;
use App\Models\TemaKokurikuler;
use App\Models\ProfilLulusan;
use Illuminate\Http\Request;

class KegiatanKokurikulerController extends Controller
{
    /**
     * Menampilkan daftar Kegiatan beserta Relasinya
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Mengambil data sekaligus menarik relasi Tema dan Profil Lulusan
        $query = KegiatanKokurikuler::with(['temaKokurikuler', 'profilLulusan']);

        if (!empty($search)) {
            $query->where('nama_kegiatan_kokurikuler', 'like', '%' . $search . '%')
                  ->orWhere('tujuan_akhir_kegiatan', 'like', '%' . $search . '%');
        }

        $kegiatans = $query->orderBy('no_urut', 'asc')
                           ->paginate(10)
                           ->appends(['search' => $search]);

        // Data master untuk Dropdown di Modal (Ambil Tema yang Aktif Saja)
        $temas = TemaKokurikuler::where('is_aktif', true)->get();
        $profils = ProfilLulusan::orderBy('no', 'asc')->get();

        return view('rapor.kegiatan-kokurikuler.index', compact('kegiatans', 'search', 'temas', 'profils'));
    }

    /**
     * TAHAP 1: Menyimpan data Kegiatan (Tanpa Profil Lulusan)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tema_kokurikuler_id' => ['required', 'exists:tema_kokurikuler,id'],
            'no_urut' => ['required', 'string', 'max:50'],
            'nama_kegiatan_kokurikuler' => ['required', 'string', 'max:255'],
            'tujuan_akhir_kegiatan' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', 'in:7,8,9'],
        ]);

        // Membuat Kegiatan Baru dan sengaja membiarkan Profil Lulusan KOSONG
        KegiatanKokurikuler::create([
            'tema_kokurikuler_id' => $request->tema_kokurikuler_id,
            'no_urut' => $request->no_urut,
            'nama_kegiatan_kokurikuler' => $request->nama_kegiatan_kokurikuler,
            'tujuan_akhir_kegiatan' => $request->tujuan_akhir_kegiatan,
            'tingkat' => $request->tingkat,
            'profil_lulusan_id' => null, // Dibiarkan kosong sesuai konsep Anda
        ]);

        return redirect()->route('rapor.kegiatan-kokurikuler.index')
            ->with('success', 'Kegiatan berhasil dibuat! Silakan klik tombol "Set Profil" untuk melengkapi data.');
    }

    /**
     * TAHAP 2: Fungsi Khusus untuk Memasukkan / Memperbarui Profil Lulusan
     */
    public function assignProfil(Request $request, $id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);

        $request->validate([
            'profil_lulusan_id' => ['required', 'exists:profil_lulusan,id'],
        ]);

        // Hanya memperbarui (mengisi) kolom profil_lulusan_id
        $kegiatan->update([
            'profil_lulusan_id' => $request->profil_lulusan_id,
        ]);

        return redirect()->route('rapor.kegiatan-kokurikuler.index')
            ->with('success', 'Profil Lulusan berhasil dipasangkan pada kegiatan ini.');
    }

    /**
     * Memperbarui data utama Kegiatan (Edit Standard)
     */
    public function update(Request $request, $id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);

        $request->validate([
            'tema_kokurikuler_id' => ['required', 'exists:tema_kokurikuler,id'],
            'no_urut' => ['required', 'string', 'max:50'],
            'nama_kegiatan_kokurikuler' => ['required', 'string', 'max:255'],
            'tujuan_akhir_kegiatan' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', 'in:7,8,9'],
        ]);

        $kegiatan->update([
            'tema_kokurikuler_id' => $request->tema_kokurikuler_id,
            'no_urut' => $request->no_urut,
            'nama_kegiatan_kokurikuler' => $request->nama_kegiatan_kokurikuler,
            'tujuan_akhir_kegiatan' => $request->tujuan_akhir_kegiatan,
            'tingkat' => $request->tingkat,
            // Kita biarkan profil_lulusan_id tidak tersentuh agar tidak tertimpa
        ]);

        return redirect()->route('rapor.kegiatan-kokurikuler.index')
            ->with('success', 'Data kegiatan berhasil diperbarui.');
    }

    /**
     * Menghapus Kegiatan Kokurikuler
     */
    public function destroy($id)
    {
        $kegiatan = KegiatanKokurikuler::findOrFail($id);
        $kegiatan->delete(); // Otomatis Soft Delete

        return redirect()->route('rapor.kegiatan-kokurikuler.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }
}