<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\DokumenPegawai;
use App\Models\Pegawai; // <-- Pastikan model Pegawai di-import
use Illuminate\Http\Request;

class DokumenPegawaiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_dokumen' => 'required|string|max:255',
            'nama_dokumen' => 'required|string|max:255',
            'tahun_dokumen' => 'required|digits:4|integer|min:1900|max:'.(date('Y')+1),
            'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // 👇 PENGAMAN: Pastikan dia berhak mengupload dokumen untuk pegawai ini
        Pegawai::aksesPribadi(auth()->user())->findOrFail($request->pegawai_id);

        $data = $request->all();

        if ($request->hasFile('file_dokumen')) {
            $data['file_dokumen'] = $request->file('file_dokumen')->store('dokumen_pegawai', 'public');
        }

        DokumenPegawai::create($data);

        return redirect()->back()->with('success', 'Dokumen pegawai berhasil diunggah.');
    }

    public function destroy($id)
    {
        $dokumen = DokumenPegawai::findOrFail($id);

        // 👇 PENGAMAN: Pastikan dia berhak menghapus dokumen milik pegawai ini
        Pegawai::aksesPribadi(auth()->user())->findOrFail($dokumen->pegawai_id);

        $dokumen->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
}