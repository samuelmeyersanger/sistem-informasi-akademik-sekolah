<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\KenaikanPangkat;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanPangkatController extends Controller
{
    /**
     * Menyimpan riwayat kenaikan pangkat BARU
     * dan otomatis mengupdate data pokok pegawai.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id'            => 'required|exists:pegawai,id',
            'nomor_sk_kp'           => 'required|string|max:255',
            'tanggal_sk_kp'         => 'required|date',
            'pangkat_golongan_baru' => 'required|string|max:255',
        ]);

        // Gunakan Database Transaction agar jika salah satu gagal, semua dibatalkan
        DB::transaction(function () use ($request) {
            // 1. Simpan ke riwayat kenaikan pangkat
            KenaikanPangkat::create($request->all());

            // 2. Otomatis update kolom pangkat_golongan di tabel pegawai
            $pegawai = Pegawai::findOrFail($request->pegawai_id);
            $pegawai->update([
                'pangkat_golongan' => $request->pangkat_golongan_baru
            ]);
        });

        return redirect()->back()->with('success', 'Riwayat pangkat berhasil dicatat dan pangkat pokok pegawai telah diperbarui.');
    }

    /**
     * Menghapus riwayat kenaikan pangkat
     * dan otomatis mengembalikan pangkat pegawai ke pangkat sebelumnya.
     */
    public function destroy($id)
    {
        $pangkatYangDihapus = KenaikanPangkat::findOrFail($id);
        $pegawaiId = $pangkatYangDihapus->pegawai_id;

        DB::transaction(function () use ($pangkatYangDihapus, $pegawaiId) {
            // 1. Hapus riwayat pangkat terpilih
            $pangkatYangDihapus->delete();

            // 2. Cari riwayat pangkat TERBARU yang masih tersisa setelah penghapusan
            $pangkatTerakhirSisa = KenaikanPangkat::where('pegawai_id', $pegawaiId)
                                    ->latest('tanggal_sk_kp')
                                    ->first();

            $pegawai = Pegawai::findOrFail($pegawaiId);

            if ($pangkatTerakhirSisa) {
                // Jika masih ada riwayat pangkat sebelumnya, kembalikan ke pangkat itu
                $pegawai->update([
                    'pangkat_golongan' => $pangkatTerakhirSisa->pangkat_golongan_baru
                ]);
            } else {
                // Jika semua riwayat pangkat habis dihapus, set menjadi null (kosong)
                $pegawai->update([
                    'pangkat_golongan' => null
                ]);
            }
        });

        return redirect()->back()->with('success', 'Riwayat pangkat berhasil dihapus dan pangkat pokok pegawai telah disesuaikan kembali.');
    }
}