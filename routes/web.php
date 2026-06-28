<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckApproval;
use App\Http\Middleware\CheckPermission; // 🆕 PASTIKAN INI DIIMPORT
use App\Models\Tentang;
use App\Models\Blog;
use App\Models\Page;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Publik\BlogController;
use App\Http\Controllers\Publik\PageController;
use App\Http\Controllers\Publik\KontakPublikController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\TahunAjaranController;
use App\Http\Controllers\Master\SemesterController;
use App\Http\Controllers\Master\KategoriBlogController;
use App\Http\Controllers\Master\BlogController as AdminBlogController;
use App\Http\Controllers\Master\KomentarBlogController as AdminKomentarBlogController;
use App\Http\Controllers\Master\TentangController;
use App\Http\Controllers\Master\ProfilSekolahController;
use App\Http\Controllers\Master\PageController as AdminPageController;
use App\Http\Controllers\Master\KontakController;
use App\Http\Controllers\Master\SettingKontakController;
use App\Http\Controllers\Master\FooterLinkController;
use App\Http\Controllers\Master\PengaturanLogoController;
use App\Http\Controllers\Master\MenuController;
use App\Http\Controllers\Master\ActivityLogController;
use App\Http\Controllers\Kesiswaan\SiswaController;
use App\Http\Controllers\Kesiswaan\DokumenSiswaController;
use App\Http\Controllers\Kesiswaan\KelasController;
use App\Http\Controllers\Kesiswaan\AnggotaKelasController;
use App\Http\Controllers\Kepegawaian\PegawaiController;
use App\Http\Controllers\Kepegawaian\DokumenPegawaiController;
use App\Http\Controllers\Kepegawaian\KenaikanGajiBerkalaController;
use App\Http\Controllers\Kepegawaian\KenaikanPangkatController;
use App\Http\Controllers\Sarpras\GedungController;
use App\Http\Controllers\Sarpras\PeminjamanSarprasController;
use App\Http\Controllers\Akademik\MataPelajaranController;
use App\Http\Controllers\Akademik\KodeGuruController;
use App\Http\Controllers\Akademik\JadwalPelajaranController;
use App\Http\Controllers\Akademik\WaktuKbmController;

/*
|--------------------------------------------------------------------------
| Web Routes - Halaman Publik (Tanpa Login)
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama / Landing Page Sekolah
Route::get('/', function () {
    $about = Tentang::first();
    $latestPosts = Blog::published()->with('kategori')->take(3)->get();
    $dynamicPages = Page::published()->orderBy('sort_order', 'asc')->get();

    return view('welcome', compact('about', 'latestPosts', 'dynamicPages'));
});

// 2. Rute Fitur Blog Publik
Route::get('/blog', [BlogController::class, 'index'])->name('publik.blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('publik.blog.show');
Route::post('/blog/{id}/komentar', [BlogController::class, 'storeKomentar'])->name('publik.blog.komentar.store');

// Rute untuk membaca halaman dinamis secara publik
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('publik.page.show');

// Rute Kontak
Route::get('/contact', [KontakPublikController::class, 'index'])->name('publik.kontak.index');
Route::post('/contact', [KontakPublikController::class, 'store'])->name('publik.kontak.store');


/*
|--------------------------------------------------------------------------
| Protected Routes - Halaman Internal (Wajib Login)
|--------------------------------------------------------------------------
*/

// 3. Halaman Dashboard Utama
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', CheckApproval::class])->name('dashboard');

// 4. Grup Kelompok Back-Office Admin (SIAS)
Route::middleware(['auth', CheckApproval::class])->group(function () {
    
    // Rute Profil Bawaan
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Modul Manajemen Master (SIAS Back-Office)
    |--------------------------------------------------------------------------
    | 🔐 Dikunci menggunakan middleware 'permission' secara tersinkronisasi.
    | Prefix 'master.' akan melekat otomatis pada setiap komponen rute di dalam grup.
    |
    */
    Route::prefix('master')->name('master.')->middleware(['permission'])->group(function () {
        
        // 🟢 Modul Sistem Pengguna (User, Role, Permission)
        Route::resource('user', UserController::class)->except(['show']); 
        Route::resource('role', RoleController::class)->except(['show', 'create', 'edit']);
        Route::resource('permission', PermissionController::class)->except(['show', 'create', 'edit']);
        Route::resource('menu', MenuController::class)->names('menu');
        
        // Log Activity (Lengkap dengan Pintu Write Data / Aksi)
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
        Route::delete('activity-logs/{id}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
        Route::put('activity-logs/{id}', [ActivityLogController::class, 'update'])->name('activity-logs.update');
        
        // 🟢 Modul Akademik (Tahun Ajaran & Semester)
        Route::resource('tahun-ajaran', TahunAjaranController::class)->names('tahun-ajaran');
        Route::resource('semester', SemesterController::class)->names('semester');
        
        // 🟢 Modul Portal Berita (Kategori Blog, Blog, & Komentar)
        Route::resource('kategori-blog', KategoriBlogController::class)->names('kategori-blog');
        Route::resource('blog', AdminBlogController::class)->names('blog');
        
        Route::get('komentar-blog', [AdminKomentarBlogController::class, 'index'])->name('komentar-blog.index');
        Route::delete('komentar-blog/{id}', [AdminKomentarBlogController::class, 'destroy'])->name('komentar-blog.destroy');
        Route::patch('komentar-blog/{id}/toggle', [AdminKomentarBlogController::class, 'toggleApprove'])->name('komentar-blog.toggle');
        
        // 🟢 Modul Identitas Website & Pengaturan
        // Tentang Sekolah
        Route::get('tentang', [TentangController::class, 'index'])->name('tentang.index');
        Route::post('tentang', [TentangController::class, 'storeOrUpdate'])->name('tentang.save');
        Route::post('tentang/reset', [TentangController::class, 'destroy'])->name('tentang.reset');

        // Pengaturan Logo
        Route::get('pengaturan-logo', [PengaturanLogoController::class, 'index'])->name('pengaturan-logo.index');
        Route::post('pengaturan-logo', [PengaturanLogoController::class, 'storeOrUpdate'])->name('pengaturan-logo.save');
        
        // Profil Identitas Sekolah
        Route::get('profil-sekolah', [ProfilSekolahController::class, 'index'])->name('profil-sekolah.index');
        Route::post('profil-sekolah', [ProfilSekolahController::class, 'storeOrUpdate'])->name('profil-sekolah.save');

        // Page Dinamis & Tautan Kaki (Footer Link)
        Route::resource('page', AdminPageController::class)->names('page');
        Route::resource('footer-link', FooterLinkController::class)->except(['create', 'show', 'edit'])->names('footer-link');

        // Kontak Masuk & Setting Kontak
        Route::resource('kontak', KontakController::class)->only(['index', 'show', 'destroy'])->names('kontak');
        Route::get('setting-kontak', [SettingKontakController::class, 'index'])->name('setting-kontak.index');
        Route::post('setting-kontak', [SettingKontakController::class, 'storeOrUpdate'])->name('setting-kontak.save');

        /*
        |--------------------------------------------------------------------------
        | 🔓 API Wilayah Lokal Sekaligus (Laravolt)
        |--------------------------------------------------------------------------
        | Disediakan khusus tanpa pengecekan middleware 'permission' 
        | agar form alamat di modul manapun tidak error terblokir.
        |
        */
        Route::withoutMiddleware([\App\Http\Middleware\CheckPermission::class])->group(function () {
            Route::get('api/provinsi', function() {
                $provinces = \Laravolt\Indonesia\Models\Province::all()->map(function($item) {
                    return ['id' => $item->id, 'code' => $item->code, 'name' => ucwords(strtolower($item->name))];
                });
                return response()->json($provinces);
            })->name('api.provinsi');

            Route::get('api/kota/{provinsi_id}', function($provinsi_id) {
                $cities = \Laravolt\Indonesia\Models\City::where('province_code', $provinsi_id)->get()->map(function($item) {
                    return ['id' => $item->id, 'code' => $item->code, 'name' => ucwords(strtolower($item->name))];
                });
                return response()->json($cities);
            })->name('api.kota');

            Route::get('api/kecamatan/{kota_id}', function($kota_id) {
                $districts = \Laravolt\Indonesia\Models\District::where('city_code', $kota_id)->get()->map(function($item) {
                    return ['id' => $item->id, 'code' => $item->code, 'name' => ucwords(strtolower($item->name))];
                });
                return response()->json($districts);
            })->name('api.kecamatan');

            Route::get('api/kelurahan/{kecamatan_id}', function($kecamatan_id) {
                $villages = \Laravolt\Indonesia\Models\Village::where('district_code', $kecamatan_id)->get()->map(function($item) {
                    return ['id' => $item->id, 'code' => $item->code, 'name' => ucwords(strtolower($item->name))];
                });
                return response()->json($villages);
            })->name('api.kelurahan');
        });

    });

    /*
    |--------------------------------------------------------------------------
    | Modul Manajemen Kesiswaan (SIAS Back-Office)
    |--------------------------------------------------------------------------
    | 🔐 Dikunci menggunakan middleware 'permission' secara tersinkronisasi.
    | Prefix 'Kesiswaan.' akan melekat otomatis pada setiap komponen rute di dalam grup.
    |
    */

    Route::prefix('kesiswaan')->name('kesiswaan.')->middleware(['permission'])->group(function () {
        // Domain 1: Manajemen Data Siswa & Wali Murid
        Route::post('kesiswaan/siswa/{id}/generate-akun', [SiswaController::class, 'generateAkunIndividu'])->name('siswa.generateAkun');

        // Route untuk Generate Massal (Semua siswa yang belum punya akun)
        Route::post('kesiswaan/siswa/generate-akun-massal', [SiswaController::class, 'generateAkunMassal'])->name('siswa.generateMassal');
        Route::get('siswa/download-template', [SiswaController::class, 'downloadTemplate'])->name('siswa.downloadTemplate');
        Route::post('siswa/import-lengkap', [SiswaController::class, 'importSiswaWali'])->name('siswa.importLengkap');
        Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa');
        Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
        Route::get('/siswa/{id}', [SiswaController::class, 'show'])->name('siswa.show');
        Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
        Route::put('/siswa/{id}/status', [SiswaController::class, 'updateStatus'])->name('siswa.updateStatus');
        Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

        // Route dokumen kesiswaan bawaan Anda sebelumnya
        Route::post('siswa/{siswa}/dokumen', [DokumenSiswaController::class, 'store'])->name('dokumen.store');
        Route::delete('dokumen/{id}', [DokumenSiswaController::class, 'destroy'])->name('dokumen.destroy');

        // Tambahan Route untuk Prestasi
        Route::post('siswa/{siswa}/prestasi', [DokumenSiswaController::class, 'storePrestasi'])->name('prestasi.store');
        Route::delete('prestasi/{id}', [DokumenSiswaController::class, 'destroyPrestasi'])->name('prestasi.destroy');

        // Domain 3: Master Ruang Kelas, Detail & Manajemen Anggota Kelas
        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::get('/kelas/{id}', [KelasController::class, 'show'])->name('kelas.show'); // <-- Menampilkan Detail Anggota
        Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');
        
        // Download & Import Anggota Kelas Massal via CSV/Excel
        Route::get('/kelas/{id}/download-template', [KelasController::class, 'downloadTemplateAnggota'])->name('kelas.anggota.downloadTemplate');
        Route::post('/kelas/{id}/import', [KelasController::class, 'importAnggota'])->name('kelas.anggota.import');

        // Sub-aksi Anggota Kelas internal di dalam KelasController
        Route::post('/kelas/anggota/store', [KelasController::class, 'storeAnggota'])->name('kelas.anggota.store');
        Route::post('/kelas/anggota/mutasi', [KelasController::class, 'prosesKenaikan'])->name('kelas.anggota.mutasi');
        Route::post('/kelas/anggota/kelulusan', [KelasController::class, 'prosesKelulusan'])->name('kelas.anggota.kelulusan');
        Route::delete('/kelas/anggota/{id}/remove', [KelasController::class, 'removeSiswa'])->name('kelas.anggota.remove');
        Route::get('kelas/{id}/jadwal', [KelasController::class, 'showJadwal'])->name('kelas.jadwal');
        /*
        |--------------------------------------------------------------------------
        | 🔓 API Wilayah Lokal Sekaligus (Laravolt)
        |--------------------------------------------------------------------------
        | Disediakan khusus tanpa pengecekan middleware 'permission' 
        | agar form alamat di modul manapun tidak error terblokir.
        |
        */
        Route::withoutMiddleware([\App\Http\Middleware\CheckPermission::class])->group(function () {
            Route::get('api/provinsi', function() {
                $provinces = \Laravolt\Indonesia\Models\Province::all()->map(function($item) {
                    return ['id' => $item->id, 'code' => $item->code, 'name' => ucwords(strtolower($item->name))];
                });
                return response()->json($provinces);
            })->name('api.provinsi');

            Route::get('api/kota/{provinsi_id}', function($provinsi_id) {
                $cities = \Laravolt\Indonesia\Models\City::where('province_code', $provinsi_id)->get()->map(function($item) {
                    return ['id' => $item->id, 'code' => $item->code, 'name' => ucwords(strtolower($item->name))];
                });
                return response()->json($cities);
            })->name('api.kota');

            Route::get('api/kecamatan/{kota_id}', function($kota_id) {
                $districts = \Laravolt\Indonesia\Models\District::where('city_code', $kota_id)->get()->map(function($item) {
                    return ['id' => $item->id, 'code' => $item->code, 'name' => ucwords(strtolower($item->name))];
                });
                return response()->json($districts);
            })->name('api.kecamatan');

            Route::get('api/kelurahan/{kecamatan_id}', function($kecamatan_id) {
                $villages = \Laravolt\Indonesia\Models\Village::where('district_code', $kecamatan_id)->get()->map(function($item) {
                    return ['id' => $item->id, 'code' => $item->code, 'name' => ucwords(strtolower($item->name))];
                });
                return response()->json($villages);
            })->name('api.kelurahan');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Manajemen Kepegawaian (SIAS Back-Office)
    |--------------------------------------------------------------------------
    | 🔐 Dikunci menggunakan middleware 'permission' secara tersinkronisasi.
    | Prefix 'kepegawaian.' akan melekat otomatis pada setiap komponen rute di dalam grup.
    |
    */

    Route::prefix('kepegawaian')->name('kepegawaian.')->middleware(['permission'])->group(function () {
        
        Route::post('/kepegawaian/pegawai/generate-individu/{id}', [PegawaiController::class, 'generateAkunIndividu'])->name('pegawai.generateIndividu');
        Route::post('/kepegawaian/pegawai/generate-massal', [PegawaiController::class, 'generateAkunMassal'])->name('pegawai.generateMassal');
        // Master Pegawai (Otomatis menghasilkan: kepegawaian.pegawai.index, kepegawaian.pegawai.show, dll)
        Route::resource('pegawai', PegawaiController::class);
        
        // Aksi Mutasi & Pensiun
        Route::post('pegawai/{id}/mutasi', [PegawaiController::class, 'mutasi'])->name('pegawai.mutasi');
        Route::post('pegawai/{id}/pensiun', [PegawaiController::class, 'pensiun'])->name('pegawai.pensiun');

        // Berkas Dokumen Pegawai
        Route::post('dokumen-pegawai', [DokumenPegawaiController::class, 'store'])->name('dokumen-pegawai.store');
        Route::delete('dokumen-pegawai/{id}', [DokumenPegawaiController::class, 'destroy'])->name('dokumen-pegawai.destroy');

        // Kenaikan Gaji Berkala (KGB)
        Route::post('kgb', [KenaikanGajiBerkalaController::class, 'store'])->name('kgb.store');
        Route::delete('kgb/{id}', [KenaikanGajiBerkalaController::class, 'destroy'])->name('kgb.destroy');

        // Kenaikan Pangkat
        Route::post('kenaikan-pangkat', [KenaikanPangkatController::class, 'store'])->name('kenaikan-pangkat.store');
        Route::delete('kenaikan-pangkat/{id}', [KenaikanPangkatController::class, 'destroy'])->name('kenaikan-pangkat.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Manajemen Sarpras (SIAS Back-Office)
    |--------------------------------------------------------------------------
    | 🔐 Dikunci menggunakan middleware 'permission' secara tersinkronisasi.
    | Prefix 'Sarpras.' akan melekat otomatis pada setiap komponen rute di dalam grup.
    |
    */

    Route::prefix('sarpras')->name('sarpras.')->middleware(['permission'])->group(function () {
        
        // --- FITUR MASTER DATA GEDUNG ---
        Route::get('/gedung', [GedungController::class, 'index'])->name('gedung.index');
        Route::post('/gedung', [GedungController::class, 'store'])->name('gedung.store');
        Route::put('/gedung/{id}', [GedungController::class, 'update'])->name('gedung.update'); // <-- Baru
        Route::get('/gedung/{id}', [GedungController::class, 'show'])->name('gedung.show');
        Route::delete('/gedung/{id}', [GedungController::class, 'destroy'])->name('gedung.destroy');

        // --- FITUR RUANGAN (Sub dari Gedung) ---
        Route::post('/gedung/{gedung_id}/ruangan', [GedungController::class, 'storeRuangan'])->name('gedung.storeRuangan');
        Route::put('/gedung/ruangan/{ruangan_id}', [GedungController::class, 'updateRuangan'])->name('gedung.updateRuangan'); // <-- Baru
        Route::get('/gedung/ruangan/{ruangan_id}', [GedungController::class, 'showRuangan'])->name('gedung.showRuangan');
        Route::delete('/gedung/ruangan/{ruangan_id}', [GedungController::class, 'destroyRuangan'])->name('gedung.destroyRuangan');

        // --- FITUR INVENTARIS BARANG (Sub dari Ruangan) ---
        Route::post('/gedung/ruangan/{ruangan_id}/inventaris', [GedungController::class, 'storeInventaris'])->name('gedung.storeInventaris');
        Route::put('/gedung/inventaris/{inventaris_id}', [GedungController::class, 'updateInventaris'])->name('gedung.updateInventaris'); // <-- Baru
        Route::delete('/gedung/inventaris/{inventaris_id}', [GedungController::class, 'destroyInventaris'])->name('gedung.destroyInventaris');

        // Rute Peminjaman Sarpras Sesuai Struktur DB Terbaru
        Route::get('/peminjaman', [PeminjamanSarprasController::class, 'index'])->name('peminjaman.index');
        Route::post('/peminjaman', [PeminjamanSarprasController::class, 'store'])->name('peminjaman.store');
        Route::put('/peminjaman/{id}', [PeminjamanSarprasController::class, 'update'])->name('peminjaman.update');
        Route::patch('/peminjaman/{id}/kembalikan', [PeminjamanSarprasController::class, 'kembalikan'])->name('peminjaman.kembalikan');
        Route::delete('/peminjaman/{id}', [PeminjamanSarprasController::class, 'destroy'])->name('peminjaman.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Manajemen Akademik (SIAS Back-Office)
    |--------------------------------------------------------------------------
    | 🔐 Dikunci menggunakan middleware 'permission' secara tersinkronisasi.
    | Prefix 'Akademik.' akan melekat otomatis pada setiap komponen rute di dalam grup.
    |
    */

    Route::prefix('akademik')->name('akademik.')->middleware(['permission'])->group(function () {
        // --- MODUL MASTER MATA PELAJARAN ---
        Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
        Route::post('/mata-pelajaran', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
        Route::put('/mata-pelajaran/{id}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
        Route::delete('/mata-pelajaran/{id}', [MataPelajaranController::class, 'destroy'])->name('mata-pelajaran.destroy');

        // --- MODUL KODE GURU / PENUGASAN ---
        Route::get('/kode-guru', [KodeGuruController::class, 'index'])->name('kode-guru.index');
        Route::post('/kode-guru', [KodeGuruController::class, 'store'])->name('kode-guru.store');
        Route::put('/kode-guru/{id}', [KodeGuruController::class, 'update'])->name('kode-guru.update');
        Route::delete('/kode-guru/{id}', [KodeGuruController::class, 'destroy'])->name('kode-guru.destroy');

        // --- MODUL KONFIGURASI WAKTU KBM ---
        Route::get('/waktu-kbm', [WaktuKbmController::class, 'index'])->name('waktu-kbm.index');
        Route::post('/waktu-kbm', [WaktuKbmController::class, 'store'])->name('waktu-kbm.store');
        Route::put('/waktu-kbm/{id}', [WaktuKbmController::class, 'update'])->name('waktu-kbm.update');
        Route::delete('/waktu-kbm/{id}', [WaktuKbmController::class, 'destroy'])->name('waktu-kbm.destroy');

        // --- MODUL UTAMA: JADWAL PELAJARAN ---
        Route::get('/jadwal-pelajaran', [JadwalPelajaranController::class, 'index'])->name('jadwal-pelajaran.index');
        Route::post('/jadwal-pelajaran', [JadwalPelajaranController::class, 'store'])->name('jadwal-pelajaran.store');
        Route::delete('/jadwal-pelajaran/{id}', [JadwalPelajaranController::class, 'destroy'])->name('jadwal-pelajaran.destroy');
        
    });
});

// 5. Rute Autentikasi Bawaan Laravel (Login, Register, Logout, dll)
require __DIR__.'/auth.php';