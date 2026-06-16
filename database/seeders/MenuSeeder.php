<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama jika ada
        Menu::truncate();

        $menus = [
            // 🟢 Kategori: Sistem Pengguna
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Manajemen User',
                'url' => 'admin/user',
                'icon' => 'users', 
                'urutan' => 1,
                'permission_slug' => 'kelola-user',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Master Role', // Perbaikan di sini: 'nama_role' sudah dihapus
                'url' => 'admin/role',
                'icon' => 'shield',
                'urutan' => 2,
                'permission_slug' => 'kelola-user',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Hak Akses (Permission)',
                'url' => 'admin/permission',
                'icon' => 'key',
                'urutan' => 3,
                'permission_slug' => 'kelola-user',
            ],

            // 🟢 Kategori: Akademik
            [
                'kategori' => 'Akademik',
                'nama_menu' => 'Tahun Ajaran',
                'url' => 'admin/tahun-ajaran',
                'icon' => 'calendar',
                'urutan' => 4,
                'permission_slug' => 'kelola-akademik',
            ],
            [
                'kategori' => 'Akademik',
                'nama_menu' => 'Semester',
                'url' => 'admin/semester',
                'icon' => 'academic-cap',
                'urutan' => 5,
                'permission_slug' => 'kelola-akademik',
            ],

            // 🟢 Kategori: Portal Berita
            [
                'kategori' => 'Portal Berita',
                'nama_menu' => 'Kategori Blog',
                'url' => 'admin/kategori-blog',
                'icon' => 'folder',
                'urutan' => 6,
                'permission_slug' => 'kelola-blog',
            ],
            [
                'kategori' => 'Portal Berita',
                'nama_menu' => 'Artikel Blog',
                'url' => 'admin/blog',
                'icon' => 'document-text',
                'urutan' => 7,
                'permission_slug' => 'kelola-blog',
            ],
            [
                'kategori' => 'Portal Berita',
                'nama_menu' => 'Komentar Blog',
                'url' => 'admin/komentar-blog',
                'icon' => 'chat-bubble',
                'urutan' => 8,
                'permission_slug' => 'kelola-blog',
            ],

            // 🟢 Kategori: Pengaturan Website
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Pengaturan Logo',
                'url' => 'admin/pengaturan-logo',
                'icon' => 'photograph',
                'urutan' => 9,
                'permission_slug' => 'kelola-pengaturan',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Tentang Sekolah',
                'url' => 'admin/tentang',
                'icon' => 'information-circle',
                'urutan' => 10,
                'permission_slug' => 'kelola-pengaturan',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Profil Sekolah',
                'url' => 'admin/profil-sekolah',
                'icon' => 'office-building',
                'urutan' => 11,
                'permission_slug' => 'kelola-pengaturan',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Halaman Statis',
                'url' => 'admin/page',
                'icon' => 'collection',
                'urutan' => 12,
                'permission_slug' => 'kelola-pengaturan',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Pesan Masuk Kontak',
                'url' => 'admin/kontak',
                'icon' => 'mail',
                'urutan' => 13,
                'permission_slug' => 'kelola-pengaturan',
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}