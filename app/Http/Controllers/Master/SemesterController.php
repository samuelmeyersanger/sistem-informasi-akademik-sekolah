<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Menampilkan daftar semua semester beserta data tahun ajaran terkait (Dengan Fitur Search)
     */
    public function index(Request $request) // 👈 Tambahkan parameter Request di sini
    {
        // Tangkap kata kunci pencarian dari input name="search"
        $search = $request->input('search');

        // Buat query dasar dengan Eager Loading relasi tahunAjaran untuk mencegah N+1 query
        $query = Semester::with('tahunAjaran');

        // Jika user mengetikkan sesuatu di kolom pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                // Cari berdasarkan kolom 'nama' di tabel semester
                $q->where('nama', 'like', '%' . $search . '%')
                  // Atau cari berdasarkan nama tahun ajaran di tabel relasinya
                  ->orWhereHas('tahunAjaran', function($relationQuery) use ($search) {
                      $relationQuery->where('nama_tahun_ajaran', 'like', '%' . $search . '%');
                  });
            });
        }

        // Urutkan dari yang terbaru, beri paginasi 10 data, dan kunci parameter pencariannya
        $semesters = $query->orderBy('created_at', 'desc')
                           ->paginate(10)
                           ->appends(['search' => $search]);
        
        // Ambil data tahun ajaran untuk pilihan di dropdown select form modal
        $tahunAjarans = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();

        return view('master.semester.index', compact('semesters', 'tahunAjarans', 'search'));
    }

    /**
     * Menyimpan data semester baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'semester_ke' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'is_aktif' => ['required', 'boolean'],
        ], [
            'nama.required' => 'Nama semester wajib diisi (Misal: Ganjil / Genap).',
            'tahun_ajaran_id.exists' => 'Tahun ajaran yang dipilih tidak valid.',
        ]);

        // Logika Proteksi: Jika diset AKTIF (true), nonaktifkan semua semester lainnya terlebih dahulu
        if ($request->is_aktif == 1) {
            Semester::where('is_aktif', true)->update(['is_aktif' => false]);
        }

        Semester::create([
            'nama' => $request->nama,
            'semester_ke' => $request->semester_ke,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'is_aktif' => $request->is_aktif,
        ]);

        return redirect()->route('master.semester.index')->with('success', 'Semester baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data semester
     */
    public function update(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);

        $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'semester_ke' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'is_aktif' => ['required', 'boolean'],
        ]);

        // Logika Proteksi: Jika diubah ke AKTIF (true), nonaktifkan semester lainnya
        if ($request->is_aktif == 1) {
            Semester::where('id', '!=', $id)->update(['is_aktif' => false]);
        }

        $semester->update([
            'nama' => $request->nama,
            'semester_ke' => $request->semester_ke,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'is_aktif' => $request->is_aktif,
        ]);

        return redirect()->route('master.semester.index')->with('success', 'Data semester berhasil diperbarui.');
    }

    /**
     * Menghapus data semester (Soft Delete)
     */
    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);

        // Proteksi Keamanan: Semester yang sedang aktif tidak boleh dihapus langsung
        if ($semester->is_aktif) {
            return redirect()->route('master.semester.index')
                ->with('error', 'Gagal menghapus! Nonaktifkan status semester ini terlebih dahulu sebelum dihapus.');
        }

        $semester->delete(); // Menggunakan Soft Delete sesuai Model Anda

        return redirect()->route('master.semester.index')
            ->with('success', 'Semester berhasil dihapus dan dipindahkan ke arsip sampah.');
    }
}