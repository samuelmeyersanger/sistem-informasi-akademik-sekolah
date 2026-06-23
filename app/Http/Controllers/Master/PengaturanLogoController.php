<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PengaturanLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\ImageCompressor; // 1. Import Trait Kompresor

class PengaturanLogoController extends Controller
{
    use ImageCompressor; // 2. Daftarkan Trait agar bisa dipakai di class ini

    public function index()
    {
        $logoSetting = PengaturanLogo::first() ?? new PengaturanLogo();
        return view('master.pengaturan-logo.index', compact('logoSetting'));
    }

    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'logo_pemda'         => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'], // max kita longgarkan ke 5MB karena nanti toh otomatis dikompres
            'logo_sekolah'       => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'kop_surat'          => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'ttd_kepala_sekolah' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'stempel_sekolah'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'ttd_dan_stempel'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ]);

        $logoSetting = PengaturanLogo::first() ?? new PengaturanLogo();
        $fields = ['logo_pemda', 'logo_sekolah', 'kop_surat', 'ttd_kepala_sekolah', 'stempel_sekolah', 'ttd_dan_stempel'];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                // Hapus file lama jika ada
                if ($logoSetting->$field && Storage::disk('public')->exists($logoSetting->$field)) {
                    Storage::disk('public')->delete($logoSetting->$field);
                }

                // 3. Eksekusi fungsi kompresi dari Trait
                // Parameter: File request, Nama folder, Lebar maksimal (pixel), Kualitas (60%)
                $path = $this->compressAndSave(
                    $request->file($field), 
                    'uploads/logos', 
                    maxWidth: 600, // Di-resize maksimal lebar 600px (sangat cukup untuk logo/ttd)
                    quality: 60    // Dikompres hingga kualitas 60% (ukuran file akan rontok jadi puluhan KB saja)
                );
                
                $logoSetting->$field = $path;
            }
        }

        $logoSetting->save();

        return redirect()->route('master.pengaturan-logo.index')->with('success', 'Aset gambar berhasil dikompres otomatis ke format WebP ringan dan diperbarui.');
    }
}