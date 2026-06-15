<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /**
     * Menampilkan daftar semua tahun ajaran
     */
    public function index()
    {
        // Mengambil semua data tahun ajaran, diurutkan dari yang terbaru (termasuk relasi count jika ada)
        $tahunAjarans = TahunAjaran::orderBy('created_at', 'desc')->get();

        return view('admin.tahun-ajaran.index', compact('tahunAjarans'));
    }

    /**
     * Menyimpan data tahun ajaran baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tahun_ajaran' => ['required', 'string', 'max:50', 'unique:tahun_ajaran,nama_tahun_ajaran'],
            'is_aktif' => ['required', 'boolean'],
        ], [
            'nama_tahun_ajaran.required' => 'Nama tahun ajaran wajib diisi.',
            'nama_tahun_ajaran.unique' => 'Nama tahun ajaran ini sudah terdaftar.',
        ]);

        // Logika Proteksi: Jika diset AKTIF (true), nonaktifkan tahun ajaran lainnya terlebih dahulu
        if ($request->is_aktif == 1) {
            TahunAjaran::where('is_aktif', true)->update(['is_aktif' => false]);
        }

        TahunAjaran::create([
            'nama_tahun_ajaran' => $request->nama_tahun_ajaran,
            'is_aktif' => $request->is_aktif,
        ]);

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran baru berhasil disimpan.');
    }

    /**
     * Memperbarui data tahun ajaran
     */
    public function update(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        $request->validate([
            'nama_tahun_ajaran' => ['required', 'string', 'max:50', 'unique:tahun_ajaran,nama_tahun_ajaran,' . $id],
            'is_aktif' => ['required', 'boolean'],
        ], [
            'nama_tahun_ajaran.required' => 'Nama tahun ajaran tidak boleh kosong.',
            'nama_tahun_ajaran.unique' => 'Nama tahun ajaran tersebut sudah digunakan.',
        ]);

        // Logika Proteksi: Jika status diubah ke AKTIF (true), nonaktifkan yang lain
        if ($request->is_aktif == 1) {
            TahunAjaran::where('id', '!=', $id)->update(['is_aktif' => false]);
        }

        $tahunAjaran->update([
            'nama_tahun_ajaran' => $request->nama_tahun_ajaran,
            'is_aktif' => $request->is_aktif,
        ]);

        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Data tahun ajaran berhasil diperbarui.');
    }

    /**
     * Menghapus tahun ajaran
     */
    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Proteksi: Tahun ajaran yang sedang aktif tidak boleh dihapus langsung
        if ($tahunAjaran->is_aktif) {
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('error', 'Gagal menghapus! Nonaktifkan tahun ajaran ini terlebih dahulu.');
        }

        // Proteksi Tambahan: Cegah hapus jika ada semester yang bergantung padanya
        if ($tahunAjaran->semesters()->count() > 0) {
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('error', 'Gagal menghapus! Tahun ajaran ini masih memiliki data semester aktif di dalamnya.');
        }

        $tahunAjaran->delete(); // Ini otomatis menjadi Soft Delete sesuai Model Anda

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus (Dipindahkan ke sampah/soft delete).');
    }
}