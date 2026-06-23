<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Tentang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TentangController extends Controller
{
    /**
     * Menampilkan halaman manajemen profil "Tentang"
     */
    public function index()
    {
        // Mengambil data pertama karena halaman tentang umumnya hanya butuh 1 record data induk
        $tentang = Tentang::first();

        return view('master.tentang.index', compact('tentang'));
    }

    /**
     * Menyimpan atau memperbarui data profil tentang sekolah
     */
    public function storeOrUpdate(Request $request)
    {
        $tentang = Tentang::first();

        $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'tombol_teks' => ['nullable', 'string', 'max:50'],
            'tombol_url' => ['nullable', 'url', 'max:255'],
            'video_url' => ['nullable', 'url', 'max:255'],
        ], [
            'judul.required' => 'Judul profil wajib diisi.',
            'deskripsi.required' => 'Deskripsi profil tidak boleh kosong.',
            'gambar.max' => 'Ukuran gambar maksimal adalah 2MB.',
            'tombol_url.url' => 'Format tautan tombol harus berupa URL valid (https://...).',
            'video_url.url' => 'Format tautan video harus berupa URL valid (https://...).',
        ]);

        // Tentukan path gambar bawaan
        $gambarPath = $tentang ? $tentang->gambar : null;

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada data sebelumnya
            if ($tentang && $tentang->gambar && Storage::disk('public')->exists($tentang->gambar)) {
                Storage::disk('public')->delete($tentang->gambar);
            }
            // Simpan gambar baru ke folder public/tentang
            $gambarPath = $request->file('gambar')->store('tentang', 'public');
        }

        // Jalankan logika Upsert (Update if exists, Create if not exists)
        Tentang::updateOrCreate(
            ['id' => $tentang ? $tentang->id : null], // Cari berdasarkan ID jika ada
            [
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'gambar' => $gambarPath,
                'tombol_teks' => $request->tombol_teks,
                'tombol_url' => $request->tombol_url,
                'video_url' => $request->video_url,
            ]
        );

        return redirect()->route('master.tentang.index')->with('success', 'Informasi profil Tentang Sekolah berhasil diperbarui.');
    }

    /**
     * Fitur tambahan: Reset/Hapus data ke kondisi kosong jika dibutuhkan (Soft Delete)
     */
    public function destroy()
    {
        $tentang = Tentang::first();
        
        if ($tentang) {
            // Hapus file gambar fisik
            if ($tentang->gambar && Storage::disk('public')->exists($tentang->gambar)) {
                Storage::disk('public')->delete($tentang->gambar);
            }
            
            $tentang->delete(); // Menggunakan soft delete sesuai model Anda
        }

        return redirect()->route('master.tentang.index')->with('success', 'Data profil berhasil direset.');
    }
}