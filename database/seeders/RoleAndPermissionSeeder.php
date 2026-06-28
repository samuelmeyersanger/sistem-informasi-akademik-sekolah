<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 🟢 PERBAIKAN UNTUK POSTGRESQL: Bersihkan tabel dengan CASCADE
        // Ini akan otomatis menghapus relasi di tabel pivot tanpa merusak struktur database
        Permission::query()->truncate(); 
        DB::table('permission_role')->truncate();

        // 1. Buat Pilihan Role Utama
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Super Administrator']);
        $guruRole  = Role::firstOrCreate(['name' => 'guru'], ['display_name' => 'Guru Akademik']);
        $siswaRole = Role::firstOrCreate(['name' => 'siswa'], ['display_name' => 'Siswa Aktif']);

        // 2. Pemetaan Hak Akses Berdasarkan Nama Rute Asli Laravel (Wajib pakai master.)
        $permissions = [
            // --- Modul Sistem Pengguna ---
            ['name' => 'master.user.index', 'modul' => 'user', 'description' => 'Melihat daftar pengguna'],
            ['name' => 'master.user.store', 'modul' => 'user', 'description' => 'Menambah pengguna baru'],
            ['name' => 'master.user.update', 'modul' => 'user', 'description' => 'Mengubah data pengguna'],
            ['name' => 'master.user.destroy', 'modul' => 'user', 'description' => 'Menghapus pengguna'],

            ['name' => 'master.role.index', 'modul' => 'role', 'description' => 'Melihat daftar role/hak akses'],
            ['name' => 'master.role.store', 'modul' => 'role', 'description' => 'Menambah role baru'],
            ['name' => 'master.role.update', 'modul' => 'role', 'description' => 'Mengubah hak akses/permission role'],
            ['name' => 'master.role.destroy', 'modul' => 'role', 'description' => 'Menghapus role'],

            ['name' => 'master.permission.index', 'modul' => 'permission', 'description' => 'Melihat daftar izin sistem'],
            ['name' => 'master.permission.store', 'modul' => 'permission', 'description' => 'Menambah izin sistem baru'],
            ['name' => 'master.permission.update', 'modul' => 'permission', 'description' => 'Mengubah data izin sistem'],
            ['name' => 'master.permission.destroy', 'modul' => 'permission', 'description' => 'Menghapus izin sistem'],

            ['name' => 'master.menu.index', 'modul' => 'menu', 'description' => 'Melihat konfigurasi menu navigasi'],
            ['name' => 'master.menu.store', 'modul' => 'menu', 'description' => 'Menambah struktur menu baru'],
            ['name' => 'master.menu.update', 'modul' => 'menu', 'description' => 'Mengubah urutan dan data menu'],
            ['name' => 'master.menu.destroy', 'modul' => 'menu', 'description' => 'Menghapus menu dari sidebar'],

            ['name' => 'master.activity-logs', 'modul' => 'activity-logs', 'description' => 'Melihat log aktivitas sistem'],

            // --- Modul Akademik ---
            ['name' => 'master.tahun-ajaran.index', 'modul' => 'akademik', 'description' => 'Melihat data tahun ajaran'],
            ['name' => 'master.tahun-ajaran.store', 'modul' => 'akademik', 'description' => 'Menambah tahun ajaran baru'],
            ['name' => 'master.tahun-ajaran.update', 'modul' => 'akademik', 'description' => 'Mengubah konfigurasi tahun ajaran'],
            ['name' => 'master.tahun-ajaran.destroy', 'modul' => 'akademik', 'description' => 'Menghapus data tahun ajaran'],

            ['name' => 'master.semester.index', 'modul' => 'akademik', 'description' => 'Melihat data semester'],
            ['name' => 'master.semester.store', 'modul' => 'akademik', 'description' => 'Menambah data semester baru'],
            ['name' => 'master.semester.update', 'modul' => 'akademik', 'description' => 'Mengubah status aktif semester'],
            ['name' => 'master.semester.destroy', 'modul' => 'akademik', 'description' => 'Menghapus data semester'],

            ['name' => 'akademik.mata-pelajaran.index', 'modul' => 'akademik', 'description' => 'Melihat data mata pelajaran'],
            ['name' => 'akademik.mata-pelajaran.store', 'modul' => 'akademik', 'description' => 'Menambah data mata pelajaran baru'],
            ['name' => 'akademik.mata-pelajaran.update', 'modul' => 'akademik', 'description' => 'Mengubah mata pelajaran'],
            ['name' => 'akademik.mata-pelajaran.destroy', 'modul' => 'akademik', 'description' => 'Menghapus data mata pelajaran'],

            ['name' => 'akademik.kode-guru.index', 'modul' => 'akademik', 'description' => 'Melihat data kode guru'],
            ['name' => 'akademik.kode-guru.store', 'modul' => 'akademik', 'description' => 'Menambah data kode guru baru'],
            ['name' => 'akademik.kode-guru.update', 'modul' => 'akademik', 'description' => 'Mengubah kode guru'],
            ['name' => 'akademik.kode-guru.destroy', 'modul' => 'akademik', 'description' => 'Menghapus data kode guru'],

            ['name' => 'akademik.waktu-kbm.index', 'modul' => 'akademik', 'description' => 'Melihat data waktu KBM'],
            ['name' => 'akademik.waktu-kbm.store', 'modul' => 'akademik', 'description' => 'Menambah data slot waktu KBM baru'],
            ['name' => 'akademik.waktu-kbm.update', 'modul' => 'akademik', 'description' => 'Mengubah slot waktu KBM'],
            ['name' => 'akademik.waktu-kbm.destroy', 'modul' => 'akademik', 'description' => 'Menghapus data slot waktu KBM'],

            // --- Modul Kesiswaan ---
            ['name' => 'kesiswaan.siswa.index', 'modul' => 'kesiswaan', 'description' => 'Melihat data siswa'],
            ['name' => 'kesiswaan.siswa.store', 'modul' => 'kesiswaan', 'description' => 'Menambah siswa baru'],
            ['name' => 'kesiswaan.siswa.update', 'modul' => 'kesiswaan', 'description' => 'Mengubah detail siswa'],
            ['name' => 'kesiswaan.siswa.destroy', 'modul' => 'kesiswaan', 'description' => 'Menghapus data siswa'],

            ['name' => 'kesiswaan.kelas.index', 'modul' => 'kesiswaan', 'description' => 'Melihat data kelas'],
            ['name' => 'kesiswaan.kelas.store', 'modul' => 'kesiswaan', 'description' => 'Menambah kelas baru'],
            ['name' => 'kesiswaan.kelas.update', 'modul' => 'kesiswaan', 'description' => 'Mengubah detail kelas'],
            ['name' => 'kesiswaan.kelas.destroy', 'modul' => 'kesiswaan', 'description' => 'Menghapus data kelas'],

            // --- Modul Pegawai ---
            ['name' => 'kepegawaian.pegawai.index', 'modul' => 'kepegawaian', 'description' => 'Melihat data pegawai'],
            ['name' => 'kepegawaian.pegawai.store', 'modul' => 'kepegawaian', 'description' => 'Menambah pegawai baru'],
            ['name' => 'kepegawaian.pegawai.update', 'modul' => 'kepegawaian', 'description' => 'Mengubah detail pegawai'],
            ['name' => 'kepegawaian.pegawai.destroy', 'modul' => 'kepegawaian', 'description' => 'Menghapus data pegawai'],

            // --- Modul Portal Berita ---
            ['name' => 'master.kategori-blog.index', 'modul' => 'blog', 'description' => 'Melihat kategori blog'],
            ['name' => 'master.kategori-blog.store', 'modul' => 'blog', 'description' => 'Menambah kategori blog baru'],
            ['name' => 'master.kategori-blog.update', 'modul' => 'blog', 'description' => 'Mengubah nama kategori blog'],
            ['name' => 'master.kategori-blog.destroy', 'modul' => 'blog', 'description' => 'Menghapus kategori blog'],

            ['name' => 'master.blog.index', 'modul' => 'blog', 'description' => 'Melihat daftar artikel blog'],
            ['name' => 'master.blog.store', 'modul' => 'blog', 'description' => 'Membuat/Publish artikel baru'],
            ['name' => 'master.blog.update', 'modul' => 'blog', 'description' => 'Mengedit isi konten artikel'],
            ['name' => 'master.blog.destroy', 'modul' => 'blog', 'description' => 'Menghapus artikel blog'],

            ['name' => 'master.komentar-blog.index', 'modul' => 'blog', 'description' => 'Melihat moderasi komentar'],
            ['name' => 'master.komentar-blog.toggle', 'modul' => 'blog', 'description' => 'Menyetujui/Menolak komentar tampil'],
            ['name' => 'master.komentar-blog.destroy', 'modul' => 'blog', 'description' => 'Menghapus komentar pengunjung'],

            // --- Modul Identitas Website & Pengaturan ---
            ['name' => 'master.tentang.index', 'modul' => 'pengaturan', 'description' => 'Melihat teks tentang sekolah'],
            ['name' => 'master.tentang.save', 'modul' => 'pengaturan', 'description' => 'Menyimpan/Mengubah teks tentang sekolah'],
            ['name' => 'master.tentang.reset', 'modul' => 'pengaturan', 'description' => 'Mereset data tentang sekolah'],

            ['name' => 'master.pengaturan-logo.index', 'modul' => 'pengaturan', 'description' => 'Melihat logo website'],
            ['name' => 'master.pengaturan-logo.save', 'modul' => 'pengaturan', 'description' => 'Mengunggah/Mengubah logo website'],

            ['name' => 'master.profil-sekolah.index', 'modul' => 'pengaturan', 'description' => 'Melihat data profil sekolah'],
            ['name' => 'master.profil-sekolah.save', 'modul' => 'pengaturan', 'description' => 'Mengubah rincian profil sekolah'],

            ['name' => 'master.page.index', 'modul' => 'pengaturan', 'description' => 'Melihat daftar halaman statis'],
            ['name' => 'master.page.store', 'modul' => 'pengaturan', 'description' => 'Membuat halaman statis baru'],
            ['name' => 'master.page.update', 'modul' => 'pengaturan', 'description' => 'Mengedit komponen halaman statis'],
            ['name' => 'master.page.destroy', 'modul' => 'pengaturan', 'description' => 'Menghapus halaman statis'],

            ['name' => 'master.footer-link.index', 'modul' => 'pengaturan', 'description' => 'Melihat tautan kaki (footer)'],
            ['name' => 'master.footer-link.store', 'modul' => 'pengaturan', 'description' => 'Menambah tautan footer baru'],
            ['name' => 'master.footer-link.update', 'modul' => 'pengaturan', 'description' => 'Mengubah susunan tautan footer'],
            ['name' => 'master.footer-link.destroy', 'modul' => 'pengaturan', 'description' => 'Menghapus tautan footer'],

            ['name' => 'master.kontak.index', 'modul' => 'pengaturan', 'description' => 'Melihat daftar pesan masuk'],
            ['name' => 'master.kontak.destroy', 'modul' => 'pengaturan', 'description' => 'Menghapus pesan masuk kontak'],

            ['name' => 'master.setting-kontak.index', 'modul' => 'pengaturan', 'description' => 'Melihat data kontak info sekolah'],
            ['name' => 'master.setting-kontak.save', 'modul' => 'pengaturan', 'description' => 'Mengubah nomor telepon/email sekolah'],
        ];

        // 3. Masukkan ke database dan pasangkan ke Role secara otomatis
        foreach ($permissions as $perm) {
            $createdPerm = Permission::create($perm);

            // ATURAN 1: Admin otomatis memegang semua hak akses master
            $adminRole->permissions()->attach($createdPerm->id);

            // ATURAN 2: Contoh plot untuk Guru (Hanya bisa melihat Akademik & Portal Berita saja)
            if (in_array($perm['name'], ['tahun-ajaran.index', 'semester.index', 'kategori-blog.index', 'blog.index', 'komentar-blog.index'])) {
                $guruRole->permissions()->attach($createdPerm->id);
            }
            
            // ATURAN 3: Contoh plot untuk Siswa (Hanya bisa melihat Profil Sekolah)
            if (in_array($perm['name'], ['profil-sekolah.index'])) {
                $siswaRole->permissions()->attach($createdPerm->id);
            }
        }

        // 4. Sinkronisasi User Admin Dummy
        $user = User::updateOrCreate(
            ['email' => 'admin@sias.com'],
            [
                'name' => 'Administrator Utama',
                'password' => Hash::make('password123'),
                'role' => 'admin', // Tetap biarkan jika kolom ini memang ada di tabel users
                'is_approved' => true, 
                'email_verified_at' => now(),
            ]
        );

        // 🆕 TAMBAHKAN BARIS INI: Hubungkan user dengan role admin secara database relasi
        // Asumsi: Model User kamu memiliki fungsi relasi bernama roles() atau yang sejenisnya
        if (!$user->roles()->where('role_id', $adminRole->id)->exists()) {
            $user->roles()->attach($adminRole->id);
        }
    }
}