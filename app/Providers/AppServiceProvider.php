<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\FooterLink; // <-- WAJIB DIIMPORT: Agar Laravel mengenali model FooterLink

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
    }
}