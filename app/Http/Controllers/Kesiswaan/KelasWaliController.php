<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\KelasWali;
use App\Models\Pegawai;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\AnggotaKelasWali;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Pastikan Anda nanti membuat 2 file Export & Import ini
use App\Exports\AnggotaKelasWaliTemplateExport; 
use App\Imports\AnggotaKelasWaliImport;
use Maatwebsite\Excel\Facades\Excel;

class KelasWaliController extends Controller
{
    /**
     * 1. Menampilkan Daftar Semua Kelompok Wali
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = KelasWali::with('waliKelas')
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('tingkat', 'like', "%{$search}%");
            });
        }

        $kelasWali = $query->paginate(15)->withQueryString();

        // Cari pegawai (guru) yang bisa dijadikan Wali Bimbingan
        $guru_list = Pegawai::where('status_keaktifan', 'Aktif')
                            ->whereIn('jenis_ptk', ['Guru', 'Kepala Sekolah'])
                            ->orderBy('nama_lengkap', 'asc')
                            ->get(); 

        return view('kesiswaan.kelas_wali.index', compact('kelasWali', 'guru_list'));
    }

    /**
     * 2. Menyimpan Data Master Kelompok Wali Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas'    => 'required|string|max:50|unique:kelas_wali,nama_kelas', 
            'tingkat'       => 'required|in:7,8,9',
            'wali_kelas_id' => 'nullable|exists:pegawai,id', 
        ], [
            'nama_kelas.required'    => 'Nama Kelompok Wali wajib diisi.',
            'nama_kelas.unique'      => 'Nama Kelompok Wali tersebut sudah ada.',
            'tingkat.required'       => 'Tingkat wajib dipilih.',
        ]);

        try {
            $semesterAktif = Semester::where('is_aktif', true)->first();

            KelasWali::create(array_merge($validated, [
                'semester_id' => $semesterAktif ? $semesterAktif->id : null,
                'wali_kelas_id' => $request->wali_kelas_id ?: null
            ]));

            return redirect()->route('kesiswaan.kelas_wali.index')
                ->with('success', 'Master data Kelompok Wali baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menambah kelompok: ' . $e->getMessage()]);
        }
    }

    /**
     * 3. Menampilkan Detail & Manajemen Anggota Kelompok Wali
     */
    public function show(Request $request, $id)
    {
        $kelas = KelasWali::with('waliKelas')->findOrFail($id);
        $semester_id = $request->input('semester_id');

        $semester_list = Semester::with('tahunAjaran')->orderBy('id', 'desc')->get();
        $semester_aktif = Semester::where('is_aktif', true)->first();

        // Kunci ke semester aktif jika user belum memilih filter semester lain
        $current_semester_id = $semester_id ?? ($semester_aktif->id ?? null);

        // Ambil list anggota pada kelompok wali ini
        $anggota = AnggotaKelasWali::with('siswa')
            ->where('kelas_wali_id', $id)
            ->where('semester_id', $current_semester_id)
            ->get();

        // Ambil daftar siswa AKTIF yang BELUM PUNYA KELOMPOK WALI di semester terpilih ini
        $siswa_tanpa_kelas = Siswa::where('status_siswa', 'Aktif')
            ->whereDoesntHave('anggotaKelasWali', function ($query) use ($current_semester_id) {
                $query->where('semester_id', $current_semester_id); 
            })->orderBy('nama_lengkap', 'asc')->get();

        // Untuk kebutuhan modal opsi tujuan kenaikan kelompok massal
        $all_kelas = KelasWali::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('kesiswaan.kelas_wali.show', compact(
            'kelas', 'anggota', 'semester_list', 'siswa_tanpa_kelas', 'all_kelas', 'current_semester_id'
        ));
    }

    /**
     * 4. Memperbarui Data Kelompok Wali
     */
    public function update(Request $request, $id)
    {
        $kelas = KelasWali::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas'    => 'required|string|max:50|unique:kelas_wali,nama_kelas,' . $id,
            'tingkat'       => 'required|in:7,8,9',
            'wali_kelas_id' => 'nullable|exists:pegawai,id',
        ], [
            'nama_kelas.required'    => 'Nama kelompok wajib diisi.',
            'nama_kelas.unique'      => 'Nama kelompok tersebut sudah digunakan.',
        ]);

        try {
            $kelas->update(array_merge($validated, [
                'wali_kelas_id' => $request->wali_kelas_id ?: null
            ]));

            return redirect()->route('kesiswaan.kelas_wali.index')
                ->with('success', 'Data Kelompok Wali berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal mengubah data kelompok: ' . $e->getMessage()]);
        }
    }

    /**
     * 5. Menghapus Master Data Kelompok Wali
     */
    public function destroy($id)
    {
        $kelas = KelasWali::findOrFail($id);
        try {
            $kelas->delete();
            return redirect()->route('kesiswaan.kelas_wali.index')
                ->with('success', 'Data Kelompok Wali berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus kelompok: ' . $e->getMessage()]);
        }
    }

    /* =========================================================================
       LOGIKA EXTRA: MANAJEMEN ANGGOTA KELOMPOK WALI
       ========================================================================= */

    /**
     * Tambah/Plotting Siswa Baru ke dalam Kelompok Wali
     */
    public function storeAnggota(Request $request)
    {
        $request->validate([
            'kelas_wali_id' => 'required|exists:kelas_wali,id',
            'semester_id' => 'required|exists:semester,id',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $kelas = KelasWali::findOrFail($request->kelas_wali_id);

        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswa_id) {
                AnggotaKelasWali::updateOrCreate(
                    ['siswa_id' => $siswa_id, 'semester_id' => $request->semester_id],
                    ['kelas_wali_id' => $kelas->id, 'tingkat' => $kelas->tingkat]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', count($request->siswa_ids) . ' siswa berhasil masuk ke kelompok wali.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Proses Kenaikan / Tinggal Kelas Massal
     */
    public function prosesKenaikan(Request $request)
    {
        $request->validate([
            'dari_kelas_id' => 'required|exists:kelas_wali,id',
            'dari_semester_id' => 'required|exists:semester,id',
            'ke_kelas_id' => 'required|exists:kelas_wali,id',
            'ke_semester_id' => 'required|exists:semester,id',
            'siswa_ids' => 'required|array|min:1',
            'status_aksi' => 'required|in:Naik Kelas,Tinggal Kelas',
        ]);

        $kelas_baru = KelasWali::findOrFail($request->ke_kelas_id);

        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswa_id) {
                AnggotaKelasWali::where('siswa_id', $siswa_id)->where('semester_id', $request->dari_semester_id)->delete();

                AnggotaKelasWali::create([
                    'siswa_id' => $siswa_id, 
                    'kelas_wali_id' => $kelas_baru->id,
                    'tingkat' => $kelas_baru->tingkat, 
                    'semester_id' => $request->ke_semester_id,
                ]);
            }

            DB::commit();
            return redirect()->route('kesiswaan.kelas_wali.show', $request->dari_kelas_id)
                             ->with('success', "Proses {$request->status_aksi} massal sukses dilakukan.");
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
            'dari_kelas_id' => 'required|exists:kelas_wali,id',
            'semester_id' => 'required|exists:semester,id',
            'siswa_ids' => 'required|array|min:1',
            'tahun_lulus' => 'required|digits:4',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswa_id) {
                AnggotaKelasWali::where('siswa_id', $siswa_id)->delete();
                // Mengubah status induk siswa menjadi Lulus dan membebaskannya dari kelas akademik manapun
                Siswa::where('id', $siswa_id)->update(['status_siswa' => 'Lulus', 'kelas_id' => null]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Anggota kelompok diproses menjadi lulus/alumni.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Mengeluarkan Siswa Secara Individu dari Kelompok Wali
     */
    public function removeSiswa($id)
    {
        $anggota = AnggotaKelasWali::findOrFail($id);

        DB::beginTransaction();
        try {
            $anggota->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Siswa dicopot dari Kelompok Wali.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Download Template Anggota Kelompok Wali (.xlsx asli)
     */
    public function downloadTemplateAnggota(Request $request, $id)
    {
        $kelas = KelasWali::findOrFail($id);
        $current_semester_id = $request->input('semester_id');

        if (!$current_semester_id) {
            $semester_aktif = Semester::where('is_aktif', true)->first();
            $current_semester_id = $semester_aktif ? $semester_aktif->id : null;
        }

        $cleanClassName = str_replace(' ', '_', $kelas->nama_kelas);
        $filename = "Template_Plotting_Kelas_Wali_{$cleanClassName}.xlsx";

        return Excel::download(new AnggotaKelasWaliTemplateExport($id, $current_semester_id), $filename);
    }

    /**
     * Proses Import Anggota Kelas Massal dari Berkas Excel
     */
    public function importAnggota(Request $request, $id)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:2048',
            'semester_id' => 'required|exists:semester,id'
        ]);

        KelasWali::findOrFail($id);
        
        try {
            Excel::import(new AnggotaKelasWaliImport($id, $request->semester_id), $request->file('file_excel'));
            
            return redirect()->back()->with('success', "Proses import selesai. Anggota siswa berhasil di-plotting.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal memproses file Excel: ' . $e->getMessage()]);
        }
    }
}