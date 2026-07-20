<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\TujuanPembelajaran;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class TujuanPembelajaranController extends Controller
{
    /**
     * FUNGSI BANTUAN:
     * Mengambil ID Mapel dari Model Pegawai.
     */
    private function getMapelIdsDiampu()
    {
        $user = auth()->user();
        if ($user->hasPermission('akses-semua-data')) {
            return true; // Akses penuh untuk Super Admin
        }

        $pegawai = \App\Models\Pegawai::where('user_id', $user->id)->first();
        
        // Panggil fungsi Helper cerdas dari model Pegawai
        return $pegawai ? $pegawai->getMapelIdsDiampu() : [];
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $mapelDiampu = $this->getMapelIdsDiampu();
        
        $query = TujuanPembelajaran::with('mataPelajaran');

        // GEMBOK VIEW (Hanya tampilkan TP dari mapel yang dia ampu)
        if ($mapelDiampu !== true) {
            $query->whereIn('mata_pelajaran_id', $mapelDiampu);
            $mapels = MataPelajaran::whereIn('id', $mapelDiampu)->orderBy('nama_mapel', 'asc')->get();
        } else {
            $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        }

        // Pencarian 
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_tujuan', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        $tujuanPembelajarans = $query->orderBy('mata_pelajaran_id', 'asc')
                         ->orderBy('tingkat', 'asc')
                         ->orderBy('nomor_tujuan', 'asc')
                         ->paginate(10)
                         ->appends(['search' => $search]);

        return view('rapor.tujuan-pembelajaran.index', compact('tujuanPembelajarans', 'search', 'mapels'));
    }

    /**
     * Menyimpan data Tujuan Pembelajaran baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'tingkat'           => ['required', 'in:7,8,9'],
            'nomor_tujuan'      => ['required', 'string', 'max:50'],
            'deskripsi'         => ['required', 'string'],
        ]);

        // PENCEGAHAN HACKING
        $mapelDiampu = $this->getMapelIdsDiampu();
        if ($mapelDiampu !== true && !in_array($request->mata_pelajaran_id, $mapelDiampu)) {
            abort(403, 'Akses Ditolak! Anda tidak mengampu Mata Pelajaran ini.');
        }

        TujuanPembelajaran::create($request->all());

        return redirect()->route('rapor.tujuan-pembelajaran.index')
            ->with('success', 'Tujuan Pembelajaran berhasil disimpan.');
    }

    /**
     * Memperbarui data Tujuan Pembelajaran
     */
    public function update(Request $request, $id)
    {
        $tujuan = TujuanPembelajaran::findOrFail($id);

        $request->validate([
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajaran,id'],
            'tingkat'           => ['required', 'in:7,8,9'],
            'nomor_tujuan'      => ['required', 'string', 'max:50'],
            'deskripsi'         => ['required', 'string'],
        ]);

        // PENCEGAHAN HACKING
        $mapelDiampu = $this->getMapelIdsDiampu();
        if ($mapelDiampu !== true) {
            if (!in_array($tujuan->mata_pelajaran_id, $mapelDiampu) || !in_array($request->mata_pelajaran_id, $mapelDiampu)) {
                abort(403, 'Akses Ditolak! Anda tidak memiliki wewenang untuk mengubah data pada Mata Pelajaran ini.');
            }
        }

        $tujuan->update($request->all());

        return redirect()->route('rapor.tujuan-pembelajaran.index')
            ->with('success', 'Tujuan Pembelajaran berhasil diperbarui.');
    }

    /**
     * Menghapus Tujuan Pembelajaran
     */
    public function destroy($id)
    {
        $tujuan = TujuanPembelajaran::findOrFail($id);
        
        // PENCEGAHAN HACKING 
        $mapelDiampu = $this->getMapelIdsDiampu();
        if ($mapelDiampu !== true && !in_array($tujuan->mata_pelajaran_id, $mapelDiampu)) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki wewenang untuk menghapus data ini.');
        }

        $tujuan->delete(); 

        return redirect()->route('rapor.tujuan-pembelajaran.index')
            ->with('success', 'Tujuan Pembelajaran berhasil dihapus.');
    }
}