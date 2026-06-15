<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanLogoController extends Controller
{
    /**
     * Menampilkan halaman upload logo dan kelengkapan dokumen
     */
    public function index()
    {
        // Mengambil data baris pertama, jika kosong buat objek baru kosong
        $logoSetting = PengaturanLogo::first() ?? new PengaturanLogo();
        return view('admin.pengaturan-logo.index', compact('logoSetting'));
    }

    /**
     * Memproses unggahan seluruh aset gambar
     */
    public function storeOrUpdate(Request $request)
    {
        // Validasi ekstensi dan ukuran gambar (maksimal 2MB per gambar)
        $request->validate([
            'logo_pemda'         => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'logo_sekolah'       => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'kop_surat'          => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'ttd_kepala_sekolah' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'stempel_sekolah'    => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'ttd_dan_stempel'    => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $logoSetting = PengaturanLogo::first() ?? new PengaturanLogo();

        // Daftar kolom berkas gambar yang akan diproses secara dinamis
        $fields = ['logo_pemda', 'logo_sekolah', 'kop_surat', 'ttd_kepala_sekolah', 'stempel_sekolah', 'ttd_dan_stempel'];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                // Hapus file lama di storage jika sebelumnya sudah pernah diunggah
                if ($logoSetting->$field && Storage::disk('public')->exists($logoSetting->$field)) {
                    Storage::disk('public')->delete($logoSetting->$field);
                }

                // Simpan file baru ke dalam folder 'uploads/logos' di disk public
                $path = $request->file($field)->store('uploads/logos', 'public');
                $logoSetting->$field = $path;
            }
        }

        $logoSetting->save();

        return redirect()->route('admin.pengaturan-logo.index')->with('success', 'Aset gambar resmi dan kelengkapan dokumen sekolah berhasil diperbarui.');
    }
}