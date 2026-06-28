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
                'permission_slug' => 'master.user.index',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Master Role', 
                'url' => 'master/role',
                'icon' => 'shield',
                'urutan' => 2,
                'permission_slug' => 'master.role.index',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Hak Akses (Permission)',
                'url' => 'master/permission',
                'icon' => 'key',
                'urutan' => 3,
                'permission_slug' => 'master.permission.index',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Konfigurasi Menu',
                'url' => 'master/menu',
                'icon' => 'list',
                'urutan' => 4,
                'permission_slug' => 'master.menu.index',
            ],
            [
                'kategori' => 'Sistem Pengguna',
                'nama_menu' => 'Log Aktivitas',
                'url' => 'master/activity-logs',
                'icon' => 'fas fa-history',
                'urutan' => 5,
                'permission_slug' => 'master.activity-logs',
            ],

            // 🟢 Kategori: Akademik
            [
                'kategori' => 'Akademik',
                'nama_menu' => 'Tahun Ajaran',
                'url' => 'master/tahun-ajaran',
                'icon' => 'calendar',
                'urutan' => 6,
                'permission_slug' => 'master.tahun-ajaran.index',
            ],
            [
                'kategori' => 'Akademik',
                'nama_menu' => 'Semester',
                'url' => 'master/semester',
                'icon' => 'fa-solid fa-calendar-days',
                'urutan' => 7,
                'permission_slug' => 'master.semester.index',
            ],
            [
                'kategori' => 'Akademik',
                'nama_menu' => 'Mata Pelajaran',
                'url' => 'akademik/mata-pelajaran',
                'icon' => 'fas fa-book-open',
                'urutan' => 8,
                'permission_slug' => 'akademik.mata-pelajaran.index',
            ],
            [
                'kategori' => 'Akademik',
                'nama_menu' => 'Kode Guru',
                'url' => 'akademik/kode-guru',
                'icon' => 'fas fa-id-badge',
                'urutan' => 9,
                'permission_slug' => 'akademik.kode-guru.index',
            ],
            [
                'kategori' => 'Akademik',
                'nama_menu' => 'Slot Waktu KBM',
                'url' => 'akademik/waktu-kbm',
                'icon' => 'fas fa-clock',
                'urutan' => 10,
                'permission_slug' => 'akademik.waktu-kbm.index',
            ],

            // 🟢 Kategori: Kesiswaan
            [
                'kategori' => 'Kesiswaan',
                'nama_menu' => 'Data Siswa',
                'url' => 'kesiswaan/siswa',
                'icon' => 'fas fa-user-graduate',
                'urutan' => 11,
                'permission_slug' => 'kesiswaan.siswa.index',
            ],
            [
                'kategori' => 'Kesiswaan',
                'nama_menu' => 'Kelas & Penjadwalan',
                'url' => 'kesiswaan/kelas',
                'icon' => 'fas fa-chalkboard-teacher',
                'urutan' => 12,
                'permission_slug' => 'kesiswaan.kelas.index',
            ],

            // 🟢 Kategori: Kepegawaian
            [
                'kategori' => 'Kepegawaian',
                'nama_menu' => 'Data Pegawai',
                'url' => 'kepegawaian/pegawai',
                'icon' => 'fas fa-user-tie',
                'urutan' => 13,
                'permission_slug' => 'kepegawaian.pegawai.index',
            ],

            // 🟢 Kategori: Ekstrakurikuler (Data Baru 📌)
            [
                'kategori' => 'Ekstrakurikuler',
                'nama_menu' => 'Data Ekstrakurikuler',
                'url' => 'ekskul/ekstrakurikuler',
                'icon' => 'fas fa-trophy',
                'urutan' => 14,
                'permission_slug' => 'ekskul.ekstrakurikuler.index',
            ],

            // 🟢 Kategori: Piket (Urutan digeser +1)
            [
                'kategori' => 'Piket',
                'nama_menu' => 'Dashboard Piket',
                'url' => 'piket/dashboard',
                'icon' => 'fas fa-dashboard',
                'urutan' => 15,
                'permission_slug' => 'piket.dashboard',
            ],
            [
                'kategori' => 'Piket',
                'nama_menu' => 'Matriks Regu Piket',
                'url' => 'piket/petugas',
                'icon' => 'fas fa-users-cog',
                'urutan' => 16,
                'permission_slug' => 'piket.petugas.index',
            ],

            // 🟢 Kategori: Sarpras (Urutan digeser +1)
            [
                'kategori' => 'Sarana Prasarana',
                'nama_menu' => 'Data Bangunan & Asset',
                'url' => 'sarpras/gedung',
                'icon' => 'fas fa-building',
                'urutan' => 17,
                'permission_slug' => 'sarpras.gedung.index',
            ],
            [
                'kategori' => 'Sarana Prasarana',
                'nama_menu' => 'Log Peminjaman Barang',
                'url' => 'sarpras/peminjaman',
                'icon' => 'fas fa-exchange-alt',
                'urutan' => 18,
                'permission_slug' => 'sarpras.peminjaman.index',
            ],

            // 🟢 Kategori: Portal Berita (Urutan digeser +1)
            [
                'kategori' => 'Portal Berita',
                'nama_menu' => 'Kategori Blog',
                'url' => 'master/kategori-blog',
                'icon' => 'folder',
                'urutan' => 19,
                'permission_slug' => 'master.kategori-blog.index',
            ],
            [
                'kategori' => 'Portal Berita',
                'nama_menu' => 'Artikel Blog',
                'url' => 'master/blog',
                'icon' => 'fa-solid fa-newspaper',
                'urutan' => 20,
                'permission_slug' => 'master.blog.index',
            ],
            [
                'kategori' => 'Portal Berita',
                'nama_menu' => 'Komentar Blog',
                'url' => 'master/komentar-blog',
                'icon' => 'fa-solid fa-comments',
                'urutan' => 21,
                'permission_slug' => 'master.komentar-blog.index',
            ],

            // 🟢 Kategori: Pengaturan Website (Urutan digeser +1)
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Pengaturan Logo',
                'url' => 'master/pengaturan-logo',
                'icon' => 'fa-solid fa-palette',
                'urutan' => 22,
                'permission_slug' => 'master.pengaturan-logo.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Tentang Sekolah',
                'url' => 'master/tentang',
                'icon' => 'fa-solid fa-building-columns',
                'urutan' => 23,
                'permission_slug' => 'master.tentang.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Profil Sekolah',
                'url' => 'master/profil-sekolah',
                'icon' => 'fa-solid fa-school',
                'urutan' => 24,
                'permission_slug' => 'master.profil-sekolah.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Halaman Statis',
                'url' => 'master/page',
                'icon' => 'fa-solid fa-file',
                'urutan' => 25,
                'permission_slug' => 'master.page.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Tautan Kaki (Footer)',
                'url' => 'master/footer-link',
                'icon' => 'link',
                'urutan' => 26,
                'permission_slug' => 'master.footer-link.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Pesan Masuk Kontak',
                'url' => 'master/kontak',
                'icon' => 'fa-solid fa-comment',
                'urutan' => 27,
                'permission_slug' => 'master.kontak.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Info Kontak Sekolah',
                'url' => 'master/setting-kontak',
                'icon' => 'phone',
                'urutan' => 28,
                'permission_slug' => 'master.setting-kontak.index',
            ],
            [
                'kategori' => 'Pengaturan Website',
                'nama_menu' => 'Backup & Restore Sistem',
                'url' => 'master/backup',
                'icon' => 'fa-solid fa-database',
                'urutan' => 29,
                'permission_slug' => 'master.backup.index',
            ]
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}