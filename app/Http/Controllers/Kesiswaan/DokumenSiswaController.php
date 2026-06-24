<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\DokumenSiswa;
use App\Models\PrestasiSiswa; // 1. TAMBAHKAN MODEL PRESTASI DISINI
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
            'jenis_dokumen' => 'required|string|max:100', 
            'tahun_dokumen' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', 
        ]);

        try {
            if ($request->hasFile('file_dokumen')) {
                $file = $request->file('file_dokumen');
                
                $cleanJenis = str_replace(' ', '_', strtolower($request->jenis_dokumen));
                $namaIdentitas = $siswa->nisn ?? $siswa->nipd ?? $siswa->id;
                $fileName = $namaIdentitas . '_' . $cleanJenis . '_' . time() . '.' . $file->getClientOriginalExtension();

                $filePath = $file->storeAs('dokumen_siswa', $fileName, 'public');

                DokumenSiswa::create([
                    'siswa_id' => $siswa->id,
                    'jenis_dokumen' => $request->jenis_dokumen,
                    'nama_dokumen' => $file->getClientOriginalName(), 
                    'tahun_dokumen' => $request->tahun_dokumen,
                    'file_dokumen' => $filePath, 
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
            if (Storage::disk('public')->exists($dokumen->file_dokumen)) {
                Storage::disk('public')->delete($dokumen->file_dokumen);
            }

            $dokumen->delete();

            return redirect()->route('kesiswaan.siswa.show', $siswaId)
                ->with('success', 'Berkas lampiran dokumen siswa berhasil dihapus secara permanen.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus dokumen: ' . $e->getMessage()]);
        }
    }

    /* ========================================================================= */
    /* 2. TAMBAHKAN METHOD UNTUK PRESTASI SISWA DI BAWAH INI                     */
    /* ========================================================================= */

    /**
     * 3. Menyimpan Prestasi Baru Siswa
     */
    public function storePrestasi(Request $request, $siswa_id)
    {
        $siswa = Siswa::findOrFail($siswa_id);

        $request->validate([
            'jenis_prestasi' => 'required|in:Akademik,Non-Akademik',
            'nama_prestasi' => 'required|string|max:255',
            'tahun_prestasi' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'kelas_id' => 'required'
        ]);

        try {
            $filePath = null;
            if ($request->hasFile('file_sertifikat')) {
                $file = $request->file('file_sertifikat');
                
                // Format nama sertifikat agar rapi
                $cleanNama = str_replace(' ', '_', strtolower(substr($request->nama_prestasi, 0, 30)));
                $namaIdentitas = $siswa->nisn ?? $siswa->nipd ?? $siswa->id;
                $fileName = $namaIdentitas . '_prestasi_' . $cleanNama . '_' . time() . '.' . $file->getClientOriginalExtension();

                $filePath = $file->storeAs('sertifikat_prestasi', $fileName, 'public');
            }

            PrestasiSiswa::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $request->kelas_id,
                'jenis_prestasi' => $request->jenis_prestasi,
                'nama_prestasi' => $request->nama_prestasi,
                'tahun_prestasi' => $request->tahun_prestasi,
                'file_sertifikat' => $filePath,
            ]);

            return redirect()->route('kesiswaan.siswa.show', $siswa->id)
                ->with('success', 'Data prestasi siswa berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menambahkan prestasi: ' . $e->getMessage()]);
        }
    }

    /**
     * 4. Menghapus Prestasi Siswa (Mendukung SoftDeletes)
     */
    public function destroyPrestasi($id)
    {
        $prestasi = PrestasiSiswa::findOrFail($id);
        $siswaId = $prestasi->siswa_id;

        try {
            // Jika ada file fisik sertifikat, Anda bisa hapus (opsional jika menggunakan softdeletes)
            if ($prestasi->file_sertifikat && Storage::disk('public')->exists($prestasi->file_sertifikat)) {
                Storage::disk('public')->delete($prestasi->file_sertifikat);
            }

            $prestasi->delete();

            return redirect()->route('kesiswaan.siswa.show', $siswaId)
                ->with('success', 'Data prestasi berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus prestasi: ' . $e->getMessage()]);
        }
    }
}