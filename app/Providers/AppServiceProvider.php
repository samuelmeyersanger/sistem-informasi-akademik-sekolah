<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Gate; 
use App\Models\FooterLink; 
use App\Models\Menu; 
use App\Models\Permission; 

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

        // 2. Ambil data Setting Kontak
        if (Schema::hasTable('setting_kontak')) {
            $contactSettings = DB::table('setting_kontak')->pluck('value', 'key')->all();
            View::share('contactSettings', $contactSettings);
        }

        // 3. Ambil data Footer Links
        if (Schema::hasTable('footer_links')) {
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
            View::composer('layouts.navigation', function ($view) {
                if (Auth::check()) {
                    
                    $semuaMenu = Menu::orderBy('urutan', 'asc')->get();
                    
                    $menuLolosCek = $semuaMenu->filter(function ($menu) {
                        
                        // BYPASS UTAMA: Email developer otomatis meloloskan semua menu
                        if (Auth::user()->email === 'samuelmeyersanger@gmail.com') {
                            return true;
                        }
                        
                        if (empty($menu->permission_slug)) {
                            return true;
                        }
                        
                        return Auth::user()->hasPermission($menu->permission_slug);
                    });

                    $view->with('sidebarMenus', $menuLolosCek);
                }
            });
        }

        // =========================================================================
        // 7. DINAMIS GERBANG PERMISSION DARI DATABASE (100% Otomatis)
        // =========================================================================
        if (Schema::hasTable('permissions')) {
            // Ambil data permission dari database
            $allPermissions = Permission::all();

            foreach ($allPermissions as $permission) {
                // Daftarkan ke Laravel Gate Engine menggunakan kolom 'name' di database
                Gate::define($permission->name, function ($user) use ($permission) {
                    
                    // Bypass utama: Email Anda otomatis lolos tanpa cek role
                    if ($user->email === 'samuelmeyersanger@gmail.com') {
                        return true;
                    }

                    // Kembalikan ke fungsi relasi hasPermission yang ada di model User Anda
                    return $user->hasPermission($permission->name);
                });
            }
        }
    }
}