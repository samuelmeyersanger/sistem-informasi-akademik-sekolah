<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Pegawai;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Ekstrakurikuler;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // =========================================================
        // 1. DASHBOARD ADMIN / PIMPINAN
        // =========================================================
        if ($user->hasPermission('view-dashboard-admin')) {
            $data = [
                'totalSiswa' => Siswa::where('status_siswa', 'Aktif')->count(),
                'totalPegawai' => Pegawai::count(),
                'totalKelas' => Kelas::count(),
                'totalEkskul' => Ekstrakurikuler::count(),
            ];
            return view('dashboard.admin', compact('data'));
        }

        // =========================================================
        // 2. DASHBOARD TENAGA PENDIDIK (GURU)
        // =========================================================
        if ($user->hasPermission('view-dashboard-guru')) {
            $pegawai = Pegawai::where('user_id', $user->id)->first();
            
            // Default data jika belum terhubung profil pegawai
            $data = [
                'pegawai' => $pegawai,
                'jumlahMuridDiajar' => 0,
                'jumlahMapel' => 0,
                'jumlahEkskul' => 0,
            ];

            if ($pegawai) {
                // Anda bisa menyesuaikan logika perhitungan ini dengan relasi database Anda nanti
                $data['jumlahMapel'] = $pegawai->jadwalPelajaran()->distinct('mata_pelajaran_id')->count() ?? 0;
                $data['jumlahEkskul'] = Ekstrakurikuler::where('pembina_id', $pegawai->id)->count();
            }
            
            return view('dashboard.guru', compact('data'));
        }

        // =========================================================
        // 3. DASHBOARD STAF (TU)
        // =========================================================
        if ($user->hasPermission('view-dashboard-staf')) {
            return view('dashboard.staf');
        }

        // =========================================================
        // 4. DASHBOARD SISWA
        // =========================================================
        if ($user->hasPermission('view-dashboard-siswa')) {
            return view('dashboard.siswa');
        }

        // =========================================================
        // FALLBACK (Jika Role tidak dicentang izin apapun)
        // =========================================================
        return view('dashboard.default');
    }
}