<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\FooterLink;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

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

        // =========================================================================
        // 8. KUSTOMISASI TOTAL EMAIL RESET PASSWORD (SOLUSI TERBAIK & FIX TEXT)
        // =========================================================================
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $sekolah = \Illuminate\Support\Facades\DB::table('profil_sekolah')->first();
            $namaSekolah = $sekolah->nama_sekolah ?? 'SMP NEGERI 4 CIBITUNG';
            $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()], false));

            return (new MailMessage)
                ->subject('SIAS - Permintaan Atur Ulang Kata Sandi')
                ->greeting('Halo!')
                ->line('Anda menerima email ini karena kami menerima permintaan untuk mengatur ulang kata sandi (reset password) akun Anda di Sistem Informasi Aktivitas Sekolah (SIAS).')
                ->action('Atur Ulang Kata Sandi', $resetUrl)
                ->line('Tautan (link) pemulihan kata sandi ini hanya akan berlaku selama ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' menit.')
                ->line('Jika Anda tidak merasa meminta pengaturan ulang kata sandi, Anda dapat mengabaikan email ini dengan aman.')
                // Teks Link Alternatif dimasukkan langsung di sini agar rapi
                ->line('Jika Anda mengalami kendala saat mengklik tombol "Atur Ulang Kata Sandi", silakan salin dan tempel URL di bawah ini ke browser web Anda:')
                ->line($resetUrl)
                ->salutation("Salam hangat,\n" . $namaSekolah);
        });
    }
}