<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\DokumenSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenSiswaController extends Controller
{
    /**
     * 1. Menyimpan/Upload Dokumen Baru untuk Siswa Tertentu
     */
    public function store(Request $request, $siswa_id)
    {
        $siswa = Siswa::findOrFail($siswa_id);

        $request->validate([
            'jenis_dokumen' => 'required|string|max:100', // Contoh: Akta Kelahiran, KK, Ijazah
            'tahun_dokumen' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', // Batas maksimal 2MB (PDF/Gambar)
        ]);

        try {
            if ($request->hasFile('file_dokumen')) {
                $file = $request->file('file_dokumen');
                
                // Membuat nama berkas yang unik dan rapi: nisn_jenis_timestamp.ekstensi
                $cleanJenis = str_replace(' ', '_', strtolower($request->jenis_dokumen));
                $namaIdentitas = $siswa->nisn ?? $siswa->nipd ?? $siswa->id;
                $fileName = $namaIdentitas . '_' . $cleanJenis . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Menyimpan fisik file ke folder storage/app/public/dokumen_siswa
                $filePath = $file->storeAs('dokumen_siswa', $fileName, 'public');

                // Simpan rekam data informasi ke database
                DokumenSiswa::create([
                    'siswa_id' => $siswa->id,
                    'jenis_dokumen' => $request->jenis_dokumen,
                    'nama_dokumen' => $file->getClientOriginalName(), // Nama asli berkas saat diupload
                    'tahun_dokumen' => $request->tahun_dokumen,
                    'file_dokumen' => $filePath, // Menyimpan path untuk dipanggil asset()
                ]);

                return redirect()->route('kesiswaan.siswa.show', $siswa->id)
                    ->with('success', 'Dokumen lampiran siswa berhasil diunggah.');
            }

            return redirect()->back()->withErrors(['error' => 'Berkas file tidak ditemukan atau rusak.']);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal mengunggah dokumen: ' . $e->getMessage()]);
        }
    }

    /**
     * 2. Menghapus Dokumen Siswa (DB & File Fisik di Storage)
     */
    public function destroy($id)
    {
        $dokumen = DokumenSiswa::findOrFail($id);
        $siswaId = $dokumen->siswa_id;

        try {
            // 1. Cek apakah file fisik ada di storage publik sekolah, jika ada hapus agar hemat ruang disk
            if (Storage::disk('public')->exists($dokumen->file_dokumen)) {
                Storage::disk('public')->delete($dokumen->file_dokumen);
            }

            // 2. Hapus data record dari database (Akan memicu SoftDeletes jika model mengaktifkannya)
            $dokumen->delete();

            return redirect()->route('kesiswaan.siswa.show', $siswaId)
                ->with('success', 'Berkas lampiran dokumen siswa berhasil dihapus secara permanen.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus dokumen: ' . $e->getMessage()]);
        }
    }
}