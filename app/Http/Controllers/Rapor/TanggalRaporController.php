<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\TanggalRapor;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Http\Request;

class TanggalRaporController extends Controller
{
    /**
     * Menampilkan daftar tanggal rapor (dengan Paginasi & Pencarian)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Mengambil data dengan relasi agar meminimalisir N+1 Query Problem
        $query = TanggalRapor::with(['tahunAjaran', 'semester']);

        // Fitur Pencarian berdasarkan Tahun Ajaran atau Tempat Cetak
        if (!empty($search)) {
            $query->where('tempat_cetak', 'like', '%' . $search . '%')
                  ->orWhereHas('tahunAjaran', function($q) use ($search) {
                      $q->where('nama_tahun_ajaran', 'like', '%' . $search . '%');
                  });
        }

        $tanggalRapors = $query->orderBy('created_at', 'desc')
                               ->paginate(10)
                               ->appends(['search' => $search]);

        // Mengambil master data untuk kebutuhan form dropdown Modal (Tambah/Edit)
        $tahunAjarans = TahunAjaran::orderBy('created_at', 'desc')->get();
        $semesters = Semester::orderBy('created_at', 'desc')->get();

        return view('rapor.tanggal-rapor.index', compact('tanggalRapors', 'search', 'tahunAjarans', 'semesters'));
    }

    /**
     * Menyimpan data tanggal rapor baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'tempat_cetak' => ['required', 'string', 'max:100'],
            'tanggal_cetak' => ['required', 'date'],
            'nama_kepala_sekolah' => ['required', 'string', 'max:150'],
            'nip_kepala_sekolah' => ['nullable', 'string', 'max:50'],
            'label_kepala_sekolah' => ['required', 'string', 'max:50'],
            'label_nip_kepala_sekolah' => ['required', 'string', 'max:20'],
            'label_nip_wali_kelas' => ['required', 'string', 'max:20'],
        ]);

        // Proteksi Logika: Pastikan belum ada setingan rapor di TA & Semester yang sama
        $sudahAda = TanggalRapor::where('tahun_ajaran_id', $request->tahun_ajaran_id)
                                ->where('semester_id', $request->semester_id)
                                ->exists();

        if ($sudahAda) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal! Pengaturan tanggal rapor untuk Tahun Ajaran dan Semester tersebut sudah pernah dibuat.');
        }

        TanggalRapor::create($request->all());

        return redirect()->route('rapor.tanggal-rapor.index')->with('success', 'Pengaturan tanggal rapor berhasil disimpan.');
    }

    /**
     * Memperbarui data tanggal rapor
     */
    public function update(Request $request, $id)
    {
        $tanggalRapor = TanggalRapor::findOrFail($id);

        $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'semester_id' => ['required', 'exists:semester,id'],
            'tempat_cetak' => ['required', 'string', 'max:100'],
            'tanggal_cetak' => ['required', 'date'],
            'nama_kepala_sekolah' => ['required', 'string', 'max:150'],
            'nip_kepala_sekolah' => ['nullable', 'string', 'max:50'],
            'label_kepala_sekolah' => ['required', 'string', 'max:50'],
            'label_nip_kepala_sekolah' => ['required', 'string', 'max:20'],
            'label_nip_wali_kelas' => ['required', 'string', 'max:20'],
        ]);

        // Proteksi Logika: Cek apakah kombinasi TA & Semester sudah dipakai oleh record LAIN
        $sudahAda = TanggalRapor::where('tahun_ajaran_id', $request->tahun_ajaran_id)
                                ->where('semester_id', $request->semester_id)
                                ->where('id', '!=', $id)
                                ->exists();

        if ($sudahAda) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal! Anda tidak bisa menggunakan Tahun Ajaran dan Semester yang sudah dipakai di pengaturan lain.');
        }

        $tanggalRapor->update($request->all());

        return redirect()->route('rapor.tanggal-rapor.index')->with('success', 'Pengaturan tanggal rapor berhasil diperbarui.');
    }

    /**
     * Menghapus tanggal rapor
     */
    public function destroy($id)
    {
        $tanggalRapor = TanggalRapor::findOrFail($id);
        
        $tanggalRapor->delete(); // Otomatis menjadi Soft Delete

        return redirect()->route('rapor.tanggal-rapor.index')
            ->with('success', 'Pengaturan tanggal rapor berhasil dihapus.');
    }
}