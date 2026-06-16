<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageCompressor
{
    /**
     * Kompres dan simpan gambar ke WebP menggunakan fungsi bawaan PHP (Native GD)
     * Bebas dari error versi package pihak ketiga.
     */
    public function compressAndSave(UploadedFile $file, string $folder, int $maxWidth = 800, int $quality = 65): string
    {
        // PERBAIKAN: Menggunakan fungsi file_get_contents() yang benar
        $imageData = file_get_contents($file->getRealPath());
        $sourceImage = imagecreatefromstring($imageData);

        if ($sourceImage !== false) {
            // Ambil dimensi asli gambar
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);

            // Hitung dimensi baru jika lebar melebihi batas maksimal
            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = floor($height * ($maxWidth / $width));

                // Buat kanvas kosong baru
                $canvas = imagecreatetruecolor($newWidth, $newHeight);

                // Pertahankan transparansi (agar latar belakang logo PNG tidak berubah jadi hitam)
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);

                // Salin dan ubah ukuran gambar ke kanvas baru
                imagecopyresampled($canvas, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                
                // Hapus resource gambar lama dari memori
                imagedestroy($sourceImage);
                $sourceImage = $canvas;
            }

            // Mulai proses kompresi ke format WebP di dalam buffer memori
            ob_start();
            imagewebp($sourceImage, null, $quality);
            $compressedData = ob_get_clean();

            // Bersihkan resource gambar dari memori setelah selesai digunakan
            imagedestroy($sourceImage);

            // Siapkan nama berkas unik dan path penyimpanan
            $filename = Str::random(40) . '.webp';
            $fullPath = rtrim($folder, '/') . '/' . $filename;

            // Simpan file final ke dalam Storage Public
            Storage::disk('public')->put($fullPath, $compressedData);

            return $fullPath;
        }

        // Fallback jika gagal memproses biner gambar (simpan apa adanya)
        return $file->store($folder, 'public');
    }
}