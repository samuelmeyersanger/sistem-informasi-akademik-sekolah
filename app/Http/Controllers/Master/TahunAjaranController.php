<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /**
     * Menampilkan daftar semua tahun ajaran (dengan fitur Pencarian)
     */
    public function index(Request $request) // 👈 Tambahkan parameter Request $request di sini
    {
        // Ambil kata kunci pencarian dari input text name="search"
        $search = $request->input('search');

        // Buat query dasar pencarian
        $query = TahunAjaran::query();

        // Kondisi jika user mengetik sesuatu di kolom search
        if (!empty($search)) {
            $query->where('nama_tahun_ajaran', 'like', '%' . $search . '%');
        }

        // Urutkan dari yang terbaru dan tambahkan Paginasi (misal: 10 data per halaman)
        // appends() digunakan agar saat pindah halaman paginasi, kata kunci search tidak hilang
        $tahunAjarans = $query->orderBy('created_at', 'desc')
                             ->paginate(10)
                             ->appends(['search' => $search]);

        return view('master.tahun-ajaran.index', compact('tahunAjarans', 'search'));
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

        return redirect()->route('master.tahun-ajaran.index')->with('success', 'Tahun ajaran baru berhasil disimpan.');
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

        return redirect()->route('master.tahun-ajaran.index')->with('success', 'Data tahun ajaran berhasil diperbarui.');
    }

    /**
     * Menghapus tahun ajaran
     */
    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Proteksi: Tahun ajaran yang sedang aktif tidak boleh dihapus langsung
        if ($tahunAjaran->is_aktif) {
            return redirect()->route('master.tahun-ajaran.index')
                ->with('error', 'Gagal menghapus! Nonaktifkan tahun ajaran ini terlebih dahulu.');
        }

        // Proteksi Tambahan: Cegah hapus jika ada semester yang bergantung padanya
        if ($tahunAjaran->semesters()->count() > 0) {
            return redirect()->route('master.tahun-ajaran.index')
                ->with('error', 'Gagal menghapus! Tahun ajaran ini masih memiliki data semester aktif di dalamnya.');
        }

        $tahunAjaran->delete(); // Ini otomatis menjadi Soft Delete sesuai Model Anda

        return redirect()->route('master.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus (Dipindahkan ke sampah/soft delete).');
    }
}