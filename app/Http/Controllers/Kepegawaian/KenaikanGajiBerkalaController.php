<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\KenaikanGajiBerkala;
use App\Models\Pegawai; // <-- Pastikan model Pegawai di-import
use Illuminate\Http\Request;

class KenaikanGajiBerkalaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'nomor_sk_kgb' => 'required|string|max:255',
            'tanggal_sk_kgb' => 'required|date',
            'nominal_gaji_baru' => 'required|numeric|min:0',
        ]);

        // 👇 PENGAMAN: Pastikan dia berhak memodifikasi pegawai ini
        Pegawai::aksesPribadi(auth()->user())->findOrFail($request->pegawai_id);

        KenaikanGajiBerkala::create($request->all());

        return redirect()->back()->with('success', 'Riwayat Kenaikan Gaji Berkala (KGB) berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $kgb = KenaikanGajiBerkala::findOrFail($id);

        // 👇 PENGAMAN: Pastikan dia berhak menghapus data dari pegawai ini
        Pegawai::aksesPribadi(auth()->user())->findOrFail($kgb->pegawai_id);

        $kgb->delete();

        return redirect()->back()->with('success', 'Data riwayat KGB berhasil dihapus.');
    }
}