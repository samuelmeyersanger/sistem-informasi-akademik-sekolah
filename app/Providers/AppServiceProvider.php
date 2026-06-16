<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth; // <-- WAJIB DIIMPORT: Untuk mendeteksi user login
use Illuminate\Support\Facades\Gate; // 🆕 TAMBAHKAN INI: Untuk mengaktifkan fitur Gate Laravel
use App\Models\FooterLink; 
use App\Models\Menu; // <-- WAJIB DIIMPORT: Agar sistem mengenali model Menu Anda
use App\Models\Permission; // 🆕 TAMBAHKAN INI: Agar sistem mengenali model Permission Anda

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Ambil data Pengaturan Logo
        if (Schema::hasTable('pengaturan_logo')) {
            $logoSetting = DB::table('pengaturan_logo')->first();
            View::share('logoSetting', $logoSetting);
        }

        // 2. Ambil data Setting Kontak (Mengubah baris kolom menjadi Array Key-Value)
        if (Schema::hasTable('setting_kontak')) {
            $contactSettings = DB::table('setting_kontak')->pluck('value', 'key')->all();
            View::share('contactSettings', $contactSettings);
        }

        // 3. Ambil data Footer Links (Otomatis dikelompokkan berdasarkan kolom 'group')
        if (Schema::hasTable('footer_links')) {
            // Menggunakan scopeActive yang sudah kita buat di Model FooterLink kemarin
            $footerLinks = FooterLink::active()->get()->groupBy('group');
            View::share('footerLinks', $footerLinks);
        }

        // 4. Ambil data Profil Sekolah Utama
        if (Schema::hasTable('profil_sekolah')) {
            $schoolProfile = DB::table('profil_sekolah')->first();
            View::share('schoolProfile', $schoolProfile);
        }

        // 5. Ambil tahun ajaran yang sedang aktif
        if (Schema::hasTable('tahun_ajaran')) {
            $activeAcademicYear = DB::table('tahun_ajaran')->where('is_aktif', true)->first();
            View::share('activeAcademicYear', $activeAcademicYear);
        }

        // =========================================================================
        // 6. PENGAMBILAN & PENYARINGAN MENU UTAMA (Target: layouts.navigation)
        // =========================================================================
        if (Schema::hasTable('menus')) {
            // Dikunci hanya untuk file navigation agar hemat resource server
            View::composer('layouts.navigation', function ($view) {
                if (Auth::check()) {
                    
                    // Ambil seluruh menu dari database, urutkan berdasarkan kolom urutan
                    $semuaMenu = Menu::orderBy('urutan', 'asc')->get();
                    
                    // Proses penyaringan (filtering) berdasarkan hak akses
                    $menuLolosCek = $semuaMenu->filter(function ($menu) {
                        
                        // BYPASS UTAMA: Jika email Anda ini, loloskan semua menu tanpa syarat
                        if (Auth::user()->email === 'samuelmeyersanger@gmail.com') {
                            return true;
                        }
                        
                        // Jika kolom permission_slug kosong, langsung tampilkan
                        if (empty($menu->permission_slug)) {
                            return true;
                        }
                        
                        return Auth::user()->hasPermission($menu->permission_slug);
                    });

                    // Kirim hasil saringan menu ke file layouts/navigation.blade.php
                    $view->with('sidebarMenus', $menuLolosCek);
                }
            });
        }

        // =========================================================================
        // 🆕 7. TAMBAHAN DI SINI: DINAMIS GERBANG PERMISSION DARI DATABASE
        // =========================================================================
        if (Schema::hasTable('permissions')) {
            // Ambil semua string nama permission yang terdaftar di database Anda (kolom 'name')
            $allPermissions = Permission::pluck('name');

            foreach ($allPermissions as $permission) {
                // Daftarkan ke Laravel Gate Engine secara massal menggunakan looping
                Gate::define($permission, function ($user) use ($permission) {
                    // Mengarahkan pengecekan ke fungsi hasPermission Many-to-Many di model User
                    return $user->hasPermission($permission);
                });
            }
        }

        // 🟢 TAMBAHKAN INI: Daftarkan manual alias string kustom yang Anda pakai di web.php 
        // jika di database tabel permission Anda menggunakan format nama yang berbeda
        $customGates = ['kelola-user', 'kelola-akademik', 'kelola-blog', 'kelola-pengaturan'];
        foreach ($customGates as $gateName) {
            Gate::define($gateName, function ($user) {
                // Karena Anda admin (role => admin), fungsi hasPermission() di User.php 
                // yang kita buat sebelumnya akan otomatis mengembalikan nilai TRUE
                return $user->hasPermission('bypass'); 
            });
        }
    }
}