<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Pegawai;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Ekstrakurikuler;
use App\Models\KodeGuru;         
use App\Models\JadwalPelajaran;  
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // =========================================================
        // 🟢 TARUH KODE RONTGEN-NYA TEPAT DI SINI
        // (Ini akan menghentikan proses ke bawah dan memunculkan layar hitam)
        // =========================================================
        dd([
            'NAMA_AKUN' => $user->name ?? 'Tanpa Nama',
            'ROLE_YANG_TERBACA_DI_PIVOT' => $user->roles()->with('permissions')->get()->toArray(),
            'HASIL_CEK_IZIN_STAF' => method_exists($user, 'hasPermission') ? $user->hasPermission('view-dashboard-staf') : 'Error: Fungsi hasPermission tidak ada!',
        ]);
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
                'totalJam' => 0,
            ];

            if ($pegawai) {
                // 1. Hitung jumlah Mapel unik yang diajarkan dari tabel KodeGuru
                $data['jumlahMapel'] = KodeGuru::where('pegawai_id', $pegawai->id)
                                        ->distinct('mata_pelajaran_id')
                                        ->count('mata_pelajaran_id');
                // 2. Hitung jumlah ekskul
                $data['jumlahEkskul'] = Ekstrakurikuler::where('pembina_id', $pegawai->id)->count();
                // (BONUS) 3. Jika Anda ingin menghitung Total Jam Mengajar per minggu
                // Kita ambil kumpulan ID kode gurunya, lalu hitung ada berapa kotak jadwalnya
                $kumpulanKode = KodeGuru::where('pegawai_id', $pegawai->id)->pluck('id');
                $data['totalJam'] = JadwalPelajaran::whereIn('kode_guru_id', $kumpulanKode)->count();
                // 👇 TAMBAHKAN KODE NOMOR 4 INI 👇
                // 4. Cari Jadwal HARI INI
                $mapHari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
                $hariIni = $mapHari[Carbon::today()->dayOfWeek]; // Sistem mendeteksi nama hari ini
                
                $data['namaHariIni'] = $hariIni;
                $data['jadwalHariIni'] = JadwalPelajaran::with(['waktuKbm', 'kelas', 'ruangan', 'kodeGuru.mataPelajaran'])
                    ->whereIn('kode_guru_id', $kumpulanKode)
                    ->where('hari', $hariIni)
                    ->get()
                    ->sortBy(function ($jadwal) {
                        return $jadwal->waktuKbm->jam_ke ?? 0; // Urutkan dari jam pertama
                    });
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