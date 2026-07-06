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

        // 👇 PENGAMAN: Pastikan dia berhak memodifikasi pegawai ini
        $pegawai = Pegawai::aksesPribadi(auth()->user())->findOrFail($request->pegawai_id);

        // Gunakan Database Transaction agar jika salah satu gagal, semua dibatalkan
        DB::transaction(function () use ($request, $pegawai) {
            // 1. Simpan ke riwayat kenaikan pangkat
            KenaikanPangkat::create($request->all());

            // 2. Otomatis update kolom pangkat_golongan di tabel pegawai
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

        // 👇 PENGAMAN: Pastikan dia berhak menghapus data dari pegawai ini
        $pegawai = Pegawai::aksesPribadi(auth()->user())->findOrFail($pegawaiId);

        DB::transaction(function () use ($pangkatYangDihapus, $pegawaiId, $pegawai) {
            // 1. Hapus riwayat pangkat terpilih
            $pangkatYangDihapus->delete();

            // 2. Cari riwayat pangkat TERBARU yang masih tersisa setelah penghapusan
            $pangkatTerakhirSisa = KenaikanPangkat::where('pegawai_id', $pegawaiId)
                                    ->latest('tanggal_sk_kp')
                                    ->first();

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