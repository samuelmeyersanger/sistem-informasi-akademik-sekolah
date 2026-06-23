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
                'url' => 'master/user',
                'icon' => 'users', 
                'urutan' => 1,
                'permission_slug' => 'user.index',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Master Role', 
                'url' => 'master/role',
                'icon' => 'shield',
                'urutan' => 2,
                'permission_slug' => 'role.index',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Hak Akses (Permission)',
                'url' => 'master/permission',
                'icon' => 'key',
                'urutan' => 3,
                'permission_slug' => 'permission.index',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Konfigurasi Menu',
                'url' => 'master/menu',
                'icon' => 'list',
                'urutan' => 4,
                'permission_slug' => 'menu.index',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Log Aktivitas',
                'url' => 'master/activity-logs',
                'icon' => 'clock',
                'urutan' => 5,
                'permission_slug' => 'activity-logs',
            ],

            // 🟢 Kategori: Akademik
            [
                'kategori' => 'Akademik',
                'nama_menu' => 'Tahun Ajaran',
                'url' => 'master/tahun-ajaran',
                'icon' => 'calendar',
                'urutan' => 6,
                'permission_slug' => 'tahun-ajaran.index',
            ],
            [
                'kategori' => 'Akademik',
                'nama_menu' => 'Semester',
                'url' => 'master/semester',
                'icon' => 'academic-cap',
                'urutan' => 7,
                'permission_slug' => 'semester.index',
            ],

            // 🟢 Kategori: Portal Berita
            [
                'kategori' => 'Portal Berita',
                'nama_menu' => 'Kategori Blog',
                'url' => 'master/kategori-blog',
                'icon' => 'folder',
                'urutan' => 8,
                'permission_slug' => 'kategori-blog.index',
            ],
            [
                'kategori' => 'Portal Berita',
                'nama_menu' => 'Artikel Blog',
                'url' => 'master/blog',
                'icon' => 'document-text',
                'urutan' => 9,
                'permission_slug' => 'blog.index',
            ],
            [
                'kategori' => 'Portal Berita',
                'nama_menu' => 'Komentar Blog',
                'url' => 'master/komentar-blog',
                'icon' => 'chat-bubble',
                'urutan' => 10,
                'permission_slug' => 'komentar-blog.index',
            ],

            // 🟢 Kategori: Pengaturan Website
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Pengaturan Logo',
                'url' => 'master/pengaturan-logo',
                'icon' => 'photograph',
                'urutan' => 11,
                'permission_slug' => 'pengaturan-logo.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Tentang Sekolah',
                'url' => 'master/tentang',
                'icon' => 'information-circle',
                'urutan' => 12,
                'permission_slug' => 'tentang.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Profil Sekolah',
                'url' => 'master/profil-sekolah',
                'icon' => 'office-building',
                'urutan' => 13,
                'permission_slug' => 'profil-sekolah.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Halaman Statis',
                'url' => 'master/page',
                'icon' => 'collection',
                'urutan' => 14,
                'permission_slug' => 'page.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Tautan Kaki (Footer)',
                'url' => 'master/footer-link',
                'icon' => 'link',
                'urutan' => 15,
                'permission_slug' => 'footer-link.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Pesan Masuk Kontak',
                'url' => 'master/kontak',
                'icon' => 'mail',
                'urutan' => 16,
                'permission_slug' => 'kontak.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Info Kontak Sekolah',
                'url' => 'master/setting-kontak',
                'icon' => 'phone',
                'urutan' => 17,
                'permission_slug' => 'setting-kontak.index',
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}