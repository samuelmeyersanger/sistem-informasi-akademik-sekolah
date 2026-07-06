<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\KodeGuru;
use App\Models\JadwalPelajaran; // Sesuaikan jika nama model jadwal Anda berbeda
use App\Models\WaktuKbm;

class JadwalMengajarController extends Controller
{
    /**
     * Menampilkan Jadwal Mengajar Pribadi Guru (Read-Only)
     */
    public function index(Request $request)
    {
        // 1. Ambil data pegawai dari user yang sedang login
        $pegawai = Pegawai::where('user_id', auth()->id())->first();

        $jadwalPelajaran = collect();
        $daftarWaktu = collect();

        if ($pegawai) {
            // 2. Ambil semua ID Kode Guru milik pegawai ini
            $kodeGuruIds = KodeGuru::where('pegawai_id', $pegawai->id)->pluck('id');

            if ($kodeGuruIds->isNotEmpty()) {
                // 3. Tarik jadwal HANYA yang kode_guru_id-nya milik guru ini
                $jadwalPelajaran = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'ruangan'])
                                    ->whereIn('kode_guru_id', $kodeGuruIds)
                                    ->get();

                // 4. Tarik master waktu untuk grid tabel
                $daftarWaktu = WaktuKbm::orderByRaw("
                                    CASE hari 
                                        WHEN 'Senin' THEN 1
                                        WHEN 'Selasa' THEN 2
                                        WHEN 'Rabu' THEN 3
                                        WHEN 'Kamis' THEN 4
                                        WHEN 'Jumat' THEN 5
                                        WHEN 'Sabtu' THEN 6
                                        ELSE 7 
                                    END
                                ")
                                ->orderBy('jam_ke', 'asc')
                                ->get();
            }
        }

        // Return view diarahkan ke folder akademik
        return view('akademik.jadwal_mengajar.index', compact('pegawai', 'jadwalPelajaran', 'daftarWaktu'));
    }
}