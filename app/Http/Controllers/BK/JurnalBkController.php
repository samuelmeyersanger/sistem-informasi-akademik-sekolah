<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\JurnalHarianBk;
use App\Models\Kelas;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class JurnalBkController extends Controller
{
    /**
     * Menampilkan daftar jurnal harian BK
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = JurnalHarianBk::with(['pegawai', 'kelas']);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('sasaran_kegiatan', 'like', '%' . $search . '%')
                  ->orWhere('kegiatan_layanan', 'like', '%' . $search . '%')
                  ->orWhere('hasil', 'like', '%' . $search . '%');
            });
        }

        $jurnals = $query->latest('tanggal')
                         ->paginate(10)
                         ->appends(['search' => $search]);

        $listKelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        $listGuruBk = Pegawai::orderBy('nama_lengkap', 'asc')->get(); // Nanti bisa difilter khusus jabatan Guru BK

        return view('bk.jurnal.index', compact('jurnals', 'listKelas', 'listGuruBk', 'search'));
    }

    /**
     * Menyimpan jurnal harian baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'pegawai_id' => 'required|exists:pegawai,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'minggu_ke' => 'required|in:I,II,III,IV,V',
            'sasaran_kegiatan' => 'required|string',
            'kegiatan_layanan' => 'required|string',
            'hasil' => 'required|string',
        ]);

        JurnalHarianBk::create($data);

        return redirect()->route('bk.jurnal.index')->with('success', 'Jurnal harian BK berhasil dicatat.');
    }

    /**
     * Memperbarui data jurnal
     */
    public function update(Request $request, JurnalHarianBk $jurnal)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'pegawai_id' => 'required|exists:pegawai,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'minggu_ke' => 'required|in:I,II,III,IV,V',
            'sasaran_kegiatan' => 'required|string',
            'kegiatan_layanan' => 'required|string',
            'hasil' => 'required|string',
        ]);

        $jurnal->update($data);

        return redirect()->route('bk.jurnal.index')->with('success', 'Jurnal harian BK berhasil diperbarui.');
    }

    /**
     * Menghapus data jurnal (Soft Delete)
     */
    public function destroy(JurnalHarianBk $jurnal)
    {
        $jurnal->delete();
        return redirect()->route('bk.jurnal.index')->with('success', 'Jurnal harian BK berhasil dihapus.');
    }
}