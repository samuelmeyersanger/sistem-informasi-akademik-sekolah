<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\AnggotaKelas;
use App\Models\RiwayatKelasSiswa;
use App\Models\RiwayatStatusSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnggotaKelasController extends Controller
{
    /**
     * 1. Menampilkan Filter Anggota Kelas Berdasarkan Ruang Kelas & Semester
     */
    public function index(Request $request)
    {
        $kelas_id = $request->input('kelas_id');
        $semester_id = $request->input('semester_id');

        $kelas_list = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        $semester_list = Semester::with('tahunAjaran')->orderBy('id', 'desc')->get();
        $semester_aktif = Semester::where('is_aktif', true)->first();

        // Default filter ke semester aktif jika user belum memilih
        $current_semester_id = $semester_id ?? ($semester_aktif->id ?? null);

        $anggota = [];
        if ($kelas_id && $current_semester_id) {
            $anggota = AnggotaKelas::with('siswa')
                ->where('kelas_id', $kelas_id)
                ->where('semester_id', $current_semester_id)
                ->get();
        }

        // Ambil daftar siswa yang AKTIF tapi BELUM PUNYA KELAS di semester ini (untuk di-plotting)
        $siswa_tanpa_kelas = Siswa::where('status_siswa', 'Aktif')
            ->whereDoesntHave('anggotaKelas', function ($query) use ($current_semester_id) {
                $query->where('semester_id', $current_semester_id);
            })->orderBy('nama_lengkap', 'asc')->get();

        return view('kesiswaan.anggota-kelas.index', compact(
            'anggota', 'kelas_list', 'semester_list', 'siswa_tanpa_kelas', 'kelas_id', 'current_semester_id'
        ));
    }

    /**
     * 2. Plotting / Memasukkan Siswa ke dalam Kelas (Bisa Massal)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);

        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswa_id) {
                // 1. Amankan/pastikan siswa tidak double kelas di semester yang sama
                AnggotaKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswa_id,
                        'semester_id' => $request->semester_id
                    ],
                    [
                        'kelas_id' => $kelas->id,
                        'tingkat' => $kelas->tingkat,
                    ]
                );

                // 2. Update kolom kelas_id di tabel master siswa sebagai pointer kelas saat ini
                Siswa::where('id', $siswa_id)->update(['kelas_id' => $kelas->id]);

                // 3. Catat ke Jurnal Riwayat Kelas Siswa
                RiwayatKelasSiswa::create([
                    'siswa_id' => $siswa_id,
                    'kelas_id' => $kelas->id,
                    'tingkat' => $kelas->tingkat,
                    'semester_id' => $request->semester_id,
                    'keterangan' => 'Plotting Awal Kelas'
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', count($request->siswa_ids) . ' Siswa berhasil dimasukkan ke kelas ' . $kelas->nama_kelas);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal melakukan plotting kelas: ' . $e->getMessage()]);
        }
    }

    /**
     * 3. Proses Kenaikan Kelas atau Perpindahan Semester Massal
     */
    public function prosesKenaikan(Request $request)
    {
        $request->validate([
            'dari_kelas_id' => 'required|exists:kelas,id',
            'dari_semester_id' => 'required|exists:semester,id',
            'ke_kelas_id' => 'required|exists:kelas,id',
            'ke_semester_id' => 'required|exists:semester,id',
            'siswa_ids' => 'required|array|min:1',
            'status_aksi' => 'required|in:Naik Kelas,Tinggal Kelas,Mutasi Kelas',
        ]);

        $kelas_baru = Kelas::findOrFail($request->ke_kelas_id);

        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswa_id) {
                
                // 1. Hapus pendaftaran kelas lama di tabel aktif (atau update jika sistem Anda rollover)
                AnggotaKelas::where('siswa_id', $siswa_id)
                    ->where('semester_id', $request->dari_semester_id)
                    ->delete();

                // 2. Daftarkan ke kelas baru untuk semester baru
                AnggotaKelas::create([
                    'siswa_id' => $siswa_id,
                    'kelas_id' => $kelas_baru->id,
                    'tingkat' => $kelas_baru->tingkat,
                    'semester_id' => $request->ke_semester_id,
                ]);

                // 3. Update master siswa ke tingkat & kelas terbarunya
                Siswa::where('id', $siswa_id)->update([
                    'kelas_id' => $kelas_baru->id,
                    'tingkat' => $kelas_baru->tingkat
                ]);

                // 4. Masukkan log permanen kenaikan kelas
                RiwayatKelasSiswa::create([
                    'siswa_id' => $siswa_id,
                    'kelas_id' => $kelas_baru->id,
                    'tingkat' => $kelas_baru->tingkat,
                    'semester_id' => $request->ke_semester_id,
                    'keterangan' => $request->status_aksi // Misal: "Naik Kelas" atau "Tinggal Kelas"
                ]);
            }

            DB::commit();
            return redirect()->route('kesiswaan.anggota-kelas.index', [
                'kelas_id' => $kelas_baru->id,
                'semester_id' => $request->ke_semester_id
            ])->with('success', 'Proses mutasi/kenaikan kelas massal berhasil dieksekusi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal memproses kenaikan kelas: ' . $e->getMessage()]);
        }
    }

    /**
     * 4. Proses Kelulusan Massal (Khusus Tingkat 9)
     */
    public function prosesKelulusan(Request $request)
    {
        $request->validate([
            'dari_kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id', // Semester aktif saat kelulusan
            'siswa_ids' => 'required|array|min:1',
            'tahun_lulus' => 'required|digits:4',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswa_id) {
                // 1. Putus kelas aktif siswa di tabel aktif anggota_kelas
                AnggotaKelas::where('siswa_id', $siswa_id)->delete();

                // 2. Ubah status di tabel utama siswa menjadi 'Lulus' & kosongkan kelas_id nya
                Siswa::where('id', $siswa_id)->update([
                    'status_siswa' => 'Lulus',
                    'kelas_id' => null 
                ]);

                // 3. Catat riwayat status beserta metadata kelulusan secara dinamis ke dalam JSON
                RiwayatStatusSiswa::create([
                    'siswa_id' => $siswa_id,
                    'semester_id' => $request->semester_id,
                    'status' => 'Lulus',
                    'metadata' => [
                        'tahun_lulus' => $request->tahun_lulus,
                        'keterangan' => $request->keterangan ?? 'Lulus jalur reguler akhir tahun ajaran.',
                        'tanggal_kelulusan' => now()->format('Y-m-d')
                    ]
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', count($request->siswa_ids) . ' Siswa tingkat akhir berhasil diproses menjadi Alumni/Lulus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal memproses kelulusan massal: ' . $e->getMessage()]);
        }
    }

    /**
     * 5. Mengeluarkan / Mencopot Siswa dari Kelas Tertentu secara Individual
     */
    public function removeSiswa($id)
    {
        $anggota = AnggotaKelas::findOrFail($id);
        $siswa_id = $anggota->siswa_id;

        DB::beginTransaction();
        try {
            // Kosongkan kelas saat ini di master tabel siswa
            Siswa::where('id', $siswa_id)->update(['kelas_id' => null]);
            
            // Hapus dari plot kelas aktif
            $anggota->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Siswa berhasil dikeluarkan dari daftar kelas ini.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal mencopot siswa: ' . $e->getMessage()]);
        }
    }
}