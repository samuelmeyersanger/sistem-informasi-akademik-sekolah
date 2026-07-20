<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\TujuanPembelajaran;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class TujuanPembelajaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        // Menarik data beserta relasi Mata Pelajarannya
        $query = TujuanPembelajaran::with('mataPelajaran');
        // Pencarian berdasarkan nomor tujuan atau deksripsinya
        if (!empty($search)) {
            $query->where('nomor_tujuan', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $search . '%');
        }
        // PERUBAHAN ADA DI SINI:
        $tujuanPembelajarans = $query->orderBy('mata_pelajaran_id', 'asc')
                         ->orderBy('tingkat', 'asc')
                         ->orderBy('nomor_tujuan', 'asc')
                         ->paginate(10)
                         ->appends(['search' => $search]);
        $mapels = MataPelajaran::all();
        // DAN DI SINI:
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
        
        $tujuan->delete(); // Otomatis Soft Delete

        return redirect()->route('rapor.tujuan-pembelajaran.index')
            ->with('success', 'Tujuan Pembelajaran berhasil dihapus.');
    }
}