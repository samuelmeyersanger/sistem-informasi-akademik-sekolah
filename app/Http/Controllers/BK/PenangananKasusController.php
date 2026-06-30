<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\PemanggilanOrangTua;
use App\Models\AlihKasus;
use App\Models\Siswa;
use App\Models\WaliSiswa;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class PenangananKasusController extends Controller
{
    /**
     * Halaman Utama Penanganan Kasus (Tab Panggilan Ortu & Tab Alih Kasus)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $currentTab = $request->input('tab', 'panggilan');

        // Mengambil list siswa lengkap dengan Eager Loading relasi kelas dan wali (pivot)
        $listSiswa = Siswa::with(['kelas', 'wali'])->orderBy('nama_lengkap', 'asc')->get();
        
        // Memperbaiki kolom pengurutan berdasarkan 'nama_lengkap' sesuai file migrasi Anda
        $listWali = WaliSiswa::orderBy('nama_lengkap', 'asc')->get(); 
        $listPegawai = Pegawai::orderBy('nama_lengkap', 'asc')->get();

        $panggilans = collect();
        $alihKasusList = collect();

        if ($currentTab === 'panggilan') {
            $query = PemanggilanOrangTua::with(['siswa.kelas', 'wali', 'pegawai']);
            if (!empty($search)) {
                $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%');
                })->orWhere('alasan_panggilan', 'like', '%' . $search . '%');
            }
            $panggilans = $query->latest('tanggal_panggilan')->paginate(10)->appends(['search' => $search, 'tab' => 'panggilan']);
        } else {
            $query = AlihKasus::with(['siswa.kelas']);
            if (!empty($search)) {
                $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%');
                })->orWhere('topik_permasalahan', 'like', '%' . $search . '%')
                  ->orWhere('kepada_siapa', 'like', '%' . $search . '%');
            }
            $alihKasusList = $query->latest('tanggal_alih')->paginate(10)->appends(['search' => $search, 'tab' => 'alih']);
        }

        return view('bk.penanganan.index', compact(
            'panggilans', 'alihKasusList', 'listSiswa', 'listWali', 'listPegawai', 'search', 'currentTab'
        ));
    }

    /**
     * Store Pemanggilan Orang Tua
     */
    public function storePanggilan(Request $request)
    {
        $data = $request->validate([
            'tanggal_panggilan' => 'required|date',
            'siswa_id' => 'required|exists:siswa,id',
            'wali_id' => 'required|exists:wali_siswa,id',
            'alasan_panggilan' => 'required|string',
            'status' => 'required|in:Terpanggil,Tidak Hadir,Dijadwalkan Ulang',
            'tanggal_kehadiran' => 'nullable|date',
            'hasil_pertemuan' => 'nullable|string',
            'pegawai_id' => 'required|exists:pegawai,id',
        ]);

        PemanggilanOrangTua::create($data);

        return redirect()->route('bk.penanganan.index', ['tab' => 'panggilan'])->with('success', 'Surat panggilan orang tua berhasil dicatat.');
    }

    /**
     * Store Alih Kasus (Referral)
     */
    public function storeAlihKasus(Request $request)
    {
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'topik_permasalahan' => 'required|string|max:255',
            'bidang_bimbingan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:255',
            'fungsi_kegiatan' => 'nullable|string|max:255',
            'tujuan_kegiatan' => 'nullable|string',
            'hasil_yang_dicapai' => 'nullable|string',
            'gambaran_ringkas_masalah' => 'required|string',
            'alasan_alih_kasus' => 'required|string',
            'jenis_alih' => 'required|in:Ke Orang Tua,Ke Kepala Sekolah,Ke Instansi Lain,Ke Ahli Lain',
            'kepada_siapa' => 'required|string|max:255',
            'tanggal_alih' => 'required|date',
            'bahan_disertakan' => 'nullable|string',
            'keterkaitan_layanan_terdahulu' => 'nullable|string',
            'rencana_penilaian_tindak_lanjut' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        AlihKasus::create($data);

        return redirect()->route('bk.penanganan.index', ['tab' => 'alih'])->with('success', 'Dokumen alih tangan kasus berhasil dibuat.');
    }

    /**
     * Destroy Panggilan
     */
    public function destroyPanggilan(PemanggilanOrangTua $panggilan)
    {
        $panggilan->delete();
        return redirect()->route('bk.penanganan.index', ['tab' => 'panggilan'])->with('success', 'Data panggilan orang tua berhasil dihapus.');
    }

    /**
     * Destroy Alih Kasus
     */
    public function destroyAlihKasus(AlihKasus $alih)
    {
        $alih->delete();
        return redirect()->route('bk.penanganan.index', ['tab' => 'alih'])->with('success', 'Dokumen alih kasus berhasil dihapus.');
    }
}