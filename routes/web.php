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

        // Domain 3: Master Ruang Kelas & Wali Kelas (Pegawai)
        Route::get('/kelas', [KelasController::class, 'index'])->name('kelas');
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');

        // Domain 4: Penempatan, Kenaikan Kelas, & Kelulusan Massal
        Route::get('/anggota-kelas', [AnggotaKelasController::class, 'index'])->name('anggota-kelas.index');
        Route::post('/anggota-kelas/plot', [AnggotaKelasController::class, 'store'])->name('anggota-kelas.store');
        Route::post('/anggota-kelas/naik-kelas', [AnggotaKelasController::class, 'prosesKenaikan'])->name('anggota-kelas.naik-kelas');
        Route::post('/anggota-kelas/lulus', [AnggotaKelasController::class, 'prosesKelulusan'])->name('anggota-kelas.lulus');
        Route::delete('/anggota-kelas/copot/{id}', [AnggotaKelasController::class, 'removeSiswa'])->name('anggota-kelas.remove');
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
    | Prefix 'Kepegawaian.' akan melekat otomatis pada setiap komponen rute di dalam grup.
    |
    */

    Route::prefix('kepegawaian')->name('kepegawain.')->middleware(['permission'])->group(function () {
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai');
        Route::post('/pegawai/store', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::put('/pegawai/update/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::delete('/pegawai/destroy/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
    });
});

// 5. Rute Autentikasi Bawaan Laravel (Login, Register, Logout, dll)
require __DIR__.'/auth.php';