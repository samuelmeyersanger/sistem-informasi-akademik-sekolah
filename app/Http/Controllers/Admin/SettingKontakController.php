<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingKontak;
use Illuminate\Http\Request;

class SettingKontakController extends Controller
{
    /**
     * Menampilkan halaman formulir pengaturan kontak sekolah
     */
    public function index()
    {
        // Mengambil semua data dari database dan mengubahnya menjadi key-value array agar mudah dipanggil di View
        // Contoh hasil: ['telepon' => '021-xxxx', 'email' => 'info@sekolah.sch.id']
        $settings = SettingKontak::pluck('value', 'key')->toArray();

        return view('admin.setting-kontak.index', compact('settings'));
    }

    /**
     * Menyimpan atau memperbarui konfigurasi kontak sekaligus (Bulk Update/Create)
     */
    public function storeOrUpdate(Request $request)
    {
        // Validasi input form sesuai kebutuhan identitas sekolah
        $request->validate([
            'settings.telepon'   => ['nullable', 'string', 'max:50'],
            'settings.whatsapp'  => ['nullable', 'string', 'max:50'],
            'settings.email'     => ['nullable', 'email', 'max:100'],
            'settings.alamat'    => ['nullable', 'string'],
            'settings.facebook'  => ['nullable', 'url', 'max:255'],
            'settings.instagram' => ['nullable', 'url', 'max:255'],
            'settings.youtube'   => ['nullable', 'url', 'max:255'],
        ], [
            'settings.email.email' => 'Format alamat email official sekolah tidak valid.',
            'settings.facebook.url' => 'Tautan Facebook harus berupa URL internet yang valid (https://...).',
            'settings.instagram.url' => 'Tautan Instagram harus berupa URL internet yang valid (https://...).',
            'settings.youtube.url' => 'Tautan Saluran YouTube harus berupa URL internet yang valid (https://...).',
        ]);

        // Looping data input untuk dimasukkan menggunakan metode aman updateOrCreate
        if ($request->has('settings')) {
            foreach ($request->input('settings') as $key => $value) {
                
                // Menentukan deskripsi otomatis berdasarkan key agar database tetap rapi informatif
                $deskripsi = match ($key) {
                    'telepon'   => 'Nomor telepon resmi kantor sekolah',
                    'whatsapp'  => 'Nomor WhatsApp center pelayanan informasi',
                    'email'     => 'Alamat email official korespondensi lembaga',
                    'alamat'    => 'Alamat fisik lengkap instansi sekolah',
                    'facebook'  => 'Tautan halaman profil Facebook sekolah',
                    'instagram' => 'Tautan akun Instagram resmi sekolah',
                    'youtube'   => 'Tautan channels YouTube resmi sekolah',
                    default     => 'Konfigurasi kontak tambahan',
                };

                SettingKontak::updateOrCreate(
                    ['key' => $key], // Kondisi pencarian data unik
                    [
                        'value' => $value, 
                        'deskripsi' => $deskripsi
                    ] // Data yang diisi/diperbarui
                );
            }
        }

        return redirect()->route('admin.setting-kontak.index')->with('success', 'Pengaturan kontak informasi sekolah berhasil diperbarui.');
    }
}