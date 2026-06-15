<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Pilihan Role Utama di Sekolah
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Super Administrator']);
        $guruRole  = Role::create(['name' => 'guru', 'display_name' => 'Guru Akademik']);
        $siswaRole = Role::create(['name' => 'siswa', 'display_name' => 'Siswa Aktif']);

        // 2. Petakan Hak Akses Spesifik Berdasarkan 9 Modul Anda
        $permissions = [
            // Modul 0: Blog Sekolah
            ['name' => 'blog.create', 'modul' => 'blog', 'description' => 'Membuat & mempublish berita sekolah'],
            ['name' => 'blog.delete', 'modul' => 'blog', 'description' => 'Menghapus berita sekolah'],

            // Modul 1: Sistem Antrian
            ['name' => 'antrian.panggil', 'modul' => 'antrian', 'description' => 'Memanggil nomor antrian di loket'],

            // Modul 2 & 3: PPDB & Data Siswa (Core)
            ['name' => 'siswa.manage', 'modul' => 'siswa', 'description' => 'Mengelola (Tambah/Edit/Hapus) data master siswa'],
            
            // Modul 4: Data Pegawai (Core)
            ['name' => 'pegawai.manage', 'modul' => 'pegawai', 'description' => 'Mengelola data guru dan staf pegawai'],

            // Modul 7: Bimbingan Konseling
            ['name' => 'bk.catat', 'modul' => 'bk', 'description' => 'Mencatat poin pelanggaran atau konseling siswa'],

            // Modul 8: eRapor
            ['name' => 'erapor.input', 'modul' => 'erapor', 'description' => 'Menginput nilai mata pelajaran'],
            ['name' => 'erapor.kunci', 'modul' => 'erapor', 'description' => 'Mengunci nilai (tugas Wali Kelas / Kurikulum)'],
            ['name' => 'erapor.cetak', 'modul' => 'erapor', 'description' => 'Mencetak dokumen PDF rapor'],
        ];

        // 3. Masukkan ke database dan pasangkan ke Role
        foreach ($permissions as $perm) {
            $createdPerm = Permission::create($perm);

            // ATURAN 1: Admin otomatis dapat SEMUA hak akses
            $adminRole->permissions()->attach($createdPerm->id);

            // ATURAN 2: Guru hanya dapat akses eRapor, BK, dan Antrian
            if (in_array($perm['name'], ['erapor.input', 'erapor.cetak', 'bk.catat', 'antrian.panggil'])) {
                $guruRole->permissions()->attach($createdPerm->id);
            }
            
            // ATURAN 3: Siswa hanya dapat akses cetak rapor miliknya sendiri (opsional)
            if (in_array($perm['name'], ['erapor.cetak'])) {
                $siswaRole->permissions()->attach($createdPerm->id);
            }
        }

        // 4. Opsional: Buat 1 User Admin Dummy buat uji coba login nanti
        User::create([
            'name' => 'Administrator Utama',
            'email' => 'admin@sias.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_approved' => true, // 👈 Kunci utamanya ada di sini, harus diset true/1
            'email_verified_at' => now(),
        ]);
    }
}