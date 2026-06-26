<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\KenaikanGajiBerkala;
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

        KenaikanGajiBerkala::create($request->all());

        return redirect()->back()->with('success', 'Riwayat Kenaikan Gaji Berkala (KGB) berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $kgb = KenaikanGajiBerkala::findOrFail($id);
        $kgb->delete();

        return redirect()->back()->with('success', 'Data riwayat KGB berhasil dihapus.');
    }
}