<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Menampilkan daftar semua semester beserta data tahun ajaran terkait
     */
    public function index()
    {
        // Ambil data semester, di-load bersama relasi tahun ajaran untuk mencegah N+1 query
        $semesters = Semester::with('tahunAjaran')->orderBy('created_at', 'desc')->get();
        
        // Ambil data tahun ajaran untuk pilihan di dropdown select form
        $tahunAjarans = TahunAjaran::orderBy('nama_tahun_ajaran', 'desc')->get();

        return view('admin.semester.index', compact('semesters', 'tahunAjarans'));
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

        return redirect()->route('admin.semester.index')->with('success', 'Semester baru berhasil ditambahkan.');
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

        return redirect()->route('admin.semester.index')->with('success', 'Data semester berhasil diperbarui.');
    }

    /**
     * Menghapus data semester (Soft Delete)
     */
    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);

        // Proteksi Keamanan: Semester yang sedang aktif tidak boleh dihapus langsung
        if ($semester->is_aktif) {
            return redirect()->route('admin.semester.index')
                ->with('error', 'Gagal menghapus! Nonaktifkan status semester ini terlebih dahulu sebelum dihapus.');
        }

        // Catatan: Jika semester ini sudah terikat dengan tabel nilai/jadwal_pelajaran,
        // Anda bisa menambahkan proteksi hitung data di sini sebelum dihapus.

        $semester->delete(); // Menggunakan Soft Delete sesuai Model Anda

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil dihapus dan dipindahkan ke arsip sampah.');
    }
}