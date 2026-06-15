<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckApproval;
use App\Models\Tentang;
use App\Models\Blog;
use App\Models\Page;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Publik\BlogController;
use App\Http\Controllers\Publik\PageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\KategoriBlogController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\KomentarBlogController as AdminKomentarBlogController;
use App\Http\Controllers\Admin\TentangController;
use App\Http\Controllers\Admin\ProfilSekolahController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\KontakController;
use App\Http\Controllers\Admin\SettingKontakController;
use App\Http\Controllers\Admin\FooterLinkController;


/*
|--------------------------------------------------------------------------
| Web Routes - Halaman Publik (Tanpa Login)
|--------------------------------------------------------------------------
*/

// 1. Halaman Utama / Landing Page Sekolah
Route::get('/', function () {
    // Ambil data 'Tentang' pertama untuk herosection/sambutan
    $about = Tentang::first();

    // Ambil 3 artikel blog terbaru beserta kategorinya
    $latestPosts = Blog::published()->with('kategori')->take(3)->get();

    // Ambil halaman-halaman dinamis untuk menu navigasi navbar
    $dynamicPages = Page::published()->orderBy('sort_order', 'asc')->get();

    return view('welcome', compact('about', 'latestPosts', 'dynamicPages'));
});

// 2. Rute Fitur Blog Publik
Route::get('/blog', [BlogController::class, 'index'])->name('publik.blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('publik.blog.show');
Route::post('/blog/{id}/komentar', [BlogController::class, 'storeKomentar'])->name('publik.blog.komentar.store');

// Rute untuk membaca halaman dinamis secara publik
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('publik.page.show');

/*
|--------------------------------------------------------------------------
| Protected Routes - Halaman Internal (Wajib Login)
|--------------------------------------------------------------------------
*/

// 3. Halaman Dashboard Utama
// Tambahkan middleware 'approved' setelah 'auth'
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', CheckApproval::class])->name('dashboard');

    Route::middleware(['auth', CheckApproval::class])->group(function () {
        // Rute Profil Bawaan
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        /*
        |--------------------------------------------------------------------------
        | Modul Manajemen Admin (SIAS Back-Office)
        |--------------------------------------------------------------------------
        | Menggunakan Route Resource agar otomatis menghasilkan 7 rute CRUD sekaligus:
        | index, create, store, show, edit, update, destroy
        */
        Route::prefix('admin')->name('admin.')->group(function () {
        // User
        Route::resource('user', UserController::class)->except(['show']); 
        
        // Role
        Route::resource('role', RoleController::class)->except(['show', 'create', 'edit']);
        
        // Permission
        Route::resource('permission', PermissionController::class)->except(['show', 'create', 'edit']);
        
        // Tahun Ajaran
        Route::resource('tahun-ajaran', TahunAjaranController::class)->names('tahun-ajaran');
        
        // Semester
        Route::resource('semester', SemesterController::class)->names('semester');
        
        // Kategori Blog
        Route::resource('kategori-blog', KategoriBlogController::class)->names('kategori-blog');
        
        // Blog
        Route::resource('blog', AdminBlogController::class)->names('blog');
        
        // Komentar Blog (Menggunakan Rute Spesifik agar fungsi Toggle Aktif)
        Route::get('komentar-blog', [AdminKomentarBlogController::class, 'index'])->name('komentar-blog.index');
        Route::delete('komentar-blog/{id}', [AdminKomentarBlogController::class, 'destroy'])->name('komentar-blog.destroy');
        Route::patch('komentar-blog/{id}/toggle', [AdminKomentarBlogController::class, 'toggleApprove'])->name('komentar-blog.toggle');
        
        // Tentang (Menghilangkan kata 'admin/' di depan URL karena sudah dicakup oleh Prefix)
        Route::get('tentang', [TentangController::class, 'index'])->name('tentang.index');
        Route::post('tentang', [TentangController::class, 'storeOrUpdate'])->name('tentang.save');
        Route::post('tentang/reset', [TentangController::class, 'destroy'])->name('tentang.reset');
        
        // Profil Identitas Sekolah (Menghilangkan kata 'admin/' di depan URL)
        Route::get('profil-sekolah', [ProfilSekolahController::class, 'index'])->name('profil-sekolah.index');
        Route::post('profil-sekolah', [ProfilSekolahController::class, 'storeOrUpdate'])->name('profil-sekolah.save');

        // API Wilayah Lokal Sekaligus (Laravolt)
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

        // Page
        Route::resource('page', AdminPageController::class)->names('page');

        // Kontak
        Route::resource('kontak', KontakController::class)->only(['index', 'show', 'destroy'])->names('kontak');
        // Setting Kontak
        Route::get('setting-kontak', [SettingKontakController::class, 'index'])->name('setting-kontak.index');
        Route::post('setting-kontak', [SettingKontakController::class, 'storeOrUpdate'])->name('setting-kontak.save');
        // Footer Link
        Route::resource('footer-link', FooterLinkController::class)->except(['create', 'show', 'edit'])->names('footer-link');
    });
});

// 5. Rute Autentikasi Bawaan Laravel (Login, Register, Logout, dll)
require __DIR__.'/auth.php';