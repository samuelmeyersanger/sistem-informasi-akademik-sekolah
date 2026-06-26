<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\AnggotaKelas;
use App\Models\RiwayatKelasSiswa;
use App\Models\RiwayatStatusSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AnggotaKelasTemplateExport;
use App\Imports\AnggotaKelasImport;
use Maatwebsite\Excel\Facades\Excel;

class KelasController extends Controller
{
    /**
     * 1. Menampilkan Daftar Semua Kelas beserta Wali Kelasnya
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Kelas::with('waliKelas')->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('tingkat', 'like', "%{$search}%");
            });
        }

        $kelas = $query->paginate(15)->withQueryString();

        $guru_list = Pegawai::where('status_keaktifan', 'Aktif')
                            ->whereIn('jenis_ptk', ['Guru', 'Kepala Sekolah'])
                            ->orderBy('nama_lengkap', 'asc')
                            ->get(); 

        return view('kesiswaan.kelas.index', compact('kelas', 'guru_list'));
    }

    /**
     * 2. Menyimpan Data Master Kelas Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas'    => 'required|string|max:50|unique:kelas,nama_kelas', 
            'tingkat'       => 'required|in:7,8,9',
            'wali_kelas_id' => 'nullable|exists:pegawai,id|unique:kelas,wali_kelas_id', 
        ], [
            'nama_kelas.required'    => 'Nama kelas wajib diisi.',
            'nama_kelas.unique'      => 'Nama kelas tersebut sudah ada di sistem.',
            'tingkat.required'       => 'Tingkat kelas wajib dipilih.',
            'wali_kelas_id.unique'   => 'Pegawai tersebut sudah ditugaskan menjadi Wali Kelas di kelas lain.'
        ]);

        try {
            $semesterAktif = Semester::where('is_aktif', true)->first();

            Kelas::create(array_merge($validated, [
                'semester_id' => $semesterAktif ? $semesterAktif->id : null,
                'wali_kelas_id' => $request->wali_kelas_id ?: null
            ]));

            return redirect()->route('kesiswaan.kelas.index')
                ->with('success', 'Master data ruang kelas baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menambah kelas: ' . $e->getMessage()]);
        }
    }

    /**
     * 3. Menampilkan Ruang Kelas & Manajemen Anggota Kelas Internal (PENGGANTI /kelas/show)
     */
    public function show(Request $request, $id)
    {
        $kelas = Kelas::with('waliKelas')->findOrFail($id);
        $semester_id = $request->input('semester_id');

        $semester_list = Semester::with('tahunAjaran')->orderBy('id', 'desc')->get();
        $semester_aktif = Semester::where('is_aktif', true)->first();

        // Kunci ke semester aktif jika user belum memilih filter semester lain
        $current_semester_id = $semester_id ?? ($semester_aktif->id ?? null);

        // Ambil list anggota pada kelas ini dan semester terpilih
        $anggota = AnggotaKelas::with('siswa')
            ->where('kelas_id', $id)
            ->where('semester_id', $current_semester_id)
            ->get();

        // Ambil daftar siswa AKTIF yang BELUM PUNYA KELAS di semester terpilih ini
        $siswa_tanpa_kelas = Siswa::where('status_siswa', 'Aktif')
            ->whereDoesntHave('anggotaKelas', function ($query) use ($current_semester_id) {
                $query->where('semester_id', $current_semester_id);
            })->orderBy('nama_lengkap', 'asc')->get();

        // Untuk kebutuhan modal opsi tujuan mutasi/kenaikan kelas massal
        $all_kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('kesiswaan.kelas.show', compact(
            'kelas', 'anggota', 'semester_list', 'siswa_tanpa_kelas', 'all_kelas', 'current_semester_id'
        ));
    }

    /**
     * 4. Memperbarui Data Kelas & Wali Kelas
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas'    => 'required|string|max:50|unique:kelas,nama_kelas,' . $id,
            'tingkat'       => 'required|in:7,8,9',
            'wali_kelas_id' => 'nullable|exists:pegawai,id|unique:kelas,wali_kelas_id,' . $id,
        ], [
            'nama_kelas.required'    => 'Nama kelas wajib diisi.',
            'nama_kelas.unique'      => 'Nama kelas tersebut sudah digunakan.',
            'tingkat.required'       => 'Tingkat kelas wajib dipilih.',
            'wali_kelas_id.unique'   => 'Pegawai tersebut sudah ditugaskan menjadi Wali Kelas di kelas lain.'
        ]);

        try {
            $kelas->update(array_merge($validated, [
                'wali_kelas_id' => $request->wali_kelas_id ?: null
            ]));

            return redirect()->route('kesiswaan.kelas.index')
                ->with('success', 'Data ruang kelas dan wali kelas berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal mengubah data kelas: ' . $e->getMessage()]);
        }
    }

    /**
     * 5. Menghapus Master Data Kelas
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        try {
            $kelas->delete();
            return redirect()->route('kesiswaan.kelas.index')
                ->with('success', 'Data ruang kelas berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus kelas: ' . $e->getMessage()]);
        }
    }

    /* =========================================================================
       LOGIKA EXTRA: MANAJEMEN ANGGOTA KELAS (DIPINDAHKAN KE SINI)
       ========================================================================= */

    /**
     * Tambah/Plotting Siswa Baru ke dalam Kelas
     */
    public function storeAnggota(Request $request)
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
                AnggotaKelas::updateOrCreate(
                    ['siswa_id' => $siswa_id, 'semester_id' => $request->semester_id],
                    ['kelas_id' => $kelas->id, 'tingkat' => $kelas->tingkat]
                );

                Siswa::where('id', $siswa_id)->update(['kelas_id' => $kelas->id]);

                RiwayatKelasSiswa::create([
                    'siswa_id' => $siswa_id, 'kelas_id' => $kelas->id, 'tingkat' => $kelas->tingkat,
                    'semester_id' => $request->semester_id, 'keterangan' => 'Plotting Awal Kelas'
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', count($request->siswa_ids) . ' siswa berhasil masuk ke kelas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Proses Mutasi / Kenaikan Kelas Massal
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
                AnggotaKelas::where('siswa_id', $siswa_id)->where('semester_id', $request->dari_semester_id)->delete();

                AnggotaKelas::create([
                    'siswa_id' => $siswa_id, 'kelas_id' => $kelas_baru->id,
                    'tingkat' => $kelas_baru->tingkat, 'semester_id' => $request->ke_semester_id,
                ]);

                Siswa::where('id', $siswa_id)->update(['kelas_id' => $kelas_baru->id, 'tingkat' => $kelas_baru->tingkat]);

                RiwayatKelasSiswa::create([
                    'siswa_id' => $siswa_id, 'kelas_id' => $kelas_baru->id, 'tingkat' => $kelas_baru->tingkat,
                    'semester_id' => $request->ke_semester_id, 'keterangan' => $request->status_aksi
                ]);
            }

            DB::commit();
            return redirect()->route('kesiswaan.kelas.show', $request->dari_kelas_id)->with('success', 'Mutasi massal sukses dilakukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Proses Kelulusan Massal
     */
    public function prosesKelulusan(Request $request)
    {
        $request->validate([
            'dari_kelas_id' => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'siswa_ids' => 'required|array|min:1',
            'tahun_lulus' => 'required|digits:4',
            'keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswa_id) {
                AnggotaKelas::where('siswa_id', $siswa_id)->delete();
                Siswa::where('id', $siswa_id)->update(['status_siswa' => 'Lulus', 'kelas_id' => null]);

                RiwayatStatusSiswa::create([
                    'siswa_id' => $siswa_id, 'semester_id' => $request->semester_id, 'status' => 'Lulus',
                    'metadata' => [
                        'tahun_lulus' => $request->tahun_lulus,
                        'keterangan' => $request->keterangan ?? 'Lulus jalur reguler akhir tahun ajaran.',
                        'tanggal_kelulusan' => now()->format('Y-m-d')
                    ]
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Siswa tingkat akhir diproses menjadi lulus/alumni.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Mengeluarkan Siswa Secara Individu dari Kelas
     */
    public function removeSiswa($id)
    {
        $anggota = AnggotaKelas::findOrFail($id);
        $siswa_id = $anggota->siswa_id;

        DB::beginTransaction();
        try {
            Siswa::where('id', $siswa_id)->update(['kelas_id' => null]);
            $anggota->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Siswa dicopot dari kelas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }
    /**
     * Download Template Anggota Kelas (.xlsx asli)
     */
    public function downloadTemplateAnggota(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $current_semester_id = $request->input('semester_id');

        if (!$current_semester_id) {
            $semester_aktif = Semester::where('is_aktif', true)->first();
            $current_semester_id = $semester_aktif ? $semester_aktif->id : null;
        }

        $cleanClassName = str_replace(' ', '_', $kelas->nama_kelas);
        $filename = "Template_Plotting_Kelas_{$cleanClassName}.xlsx";

        return Excel::download(new AnggotaKelasTemplateExport($id, $current_semester_id), $filename);
    }

    /**
     * Proses Import Anggota Kelas Massal dari Berkas Excel
     */
    public function importAnggota(Request $request, $id)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:2048',
            'semester_id' => 'required|exists:semester,id'
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes'    => 'Format file harus berupa excel (.xlsx atau .xls)',
            'file_excel.max'      => 'Ukuran file maksimal adalah 2MB.'
        ]);

        try {
            Excel::import(new AnggotaKelasImport($id, $request->semester_id), $request->file('file_excel'));
            
            return redirect()->back()->with('success', "Proses pembacaan excel selesai. Anggota siswa yang ditandai 'Y' sukses di-plotting ke dalam kelas.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal memproses file Excel. Pastikan data berformat .xlsx dengan sheet bernama "Template". Pesan error: ' . $e->getMessage()]);
        }
    }
}