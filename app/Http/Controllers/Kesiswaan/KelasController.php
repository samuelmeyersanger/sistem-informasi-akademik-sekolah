<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User; // Atau App\Models\Guru jika Anda memiliki model Guru terpisah untuk Wali Kelas
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * 1. Menampilkan Daftar Semua Kelas beserta Wali Kelasnya
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Eager load relasi waliKelas (asumsi relasi bernama waliKelas ke model User/Guru)
        $query = Kelas::with('waliKelas')->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc');

        if ($search) {
            $query->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('tingkat', 'like', "%{$search}%");
        }

        $kelas = $query->paginate(15)->withQueryString();

        // Ambil data Guru/User untuk pilihan dropdown Wali Kelas di Form Form
        // Silakan sesuaikan role atau kriteria pencarian akun guru Anda
        $guru_list = User::where('role', 'Guru')->orderBy('name', 'asc')->get(); 

        return view('kesiswaan.kelas.index', compact('kelas', 'guru_list'));
    }

    /**
     * Menyimpan Data Master Kelas Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas', 
            'tingkat' => 'required|in:7,8,9',
            // Disesuaikan dengan kolom 'wali_kelas_id' dan tabel 'pegawai'
            'wali_kelas_id' => 'nullable|exists:pegawai,id|unique:kelas,wali_kelas_id', 
        ], [
            'wali_kelas_id.unique' => 'Pegawai tersebut sudah menjadi Wali Kelas di kelas lain.'
        ]);

        try {
            Kelas::create($validated);

            return redirect()->route('kesiswaan.kelas')
                ->with('success', 'Master data ruang kelas baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menambah kelas: ' . $e->getMessage()]);
        }
    }

    /**
     * Memperbarui Data Kelas & Wali Kelas
     */
    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . $kelas->id,
            'tingkat' => 'required|in:7,8,9',
            // Disesuaikan dengan kolom 'wali_kelas_id' dan tabel 'pegawai'
            'wali_kelas_id' => 'nullable|exists:pegawai,id|unique:kelas,wali_kelas_id,' . $kelas->id,
        ], [
            'wali_kelas_id.unique' => 'Pegawai tersebut sudah menjadi Wali Kelas di kelas lain.'
        ]);

        try {
            $kelas->update($validated);

            return redirect()->route('kesiswaan.kelas')
                ->with('success', 'Data ruang kelas dan wali kelas berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal mengubah data kelas: ' . $e->getMessage()]);
        }
    }

    /**
     * 4. Menghapus Master Data Kelas
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        try {
            // Catatan: Karena di migration anggota_kelas diset onDelete('cascade'), 
            // jika kelas dihapus, maka pemetaan siswa di anggota_kelas akan ikut terhapus.
            $kelas->delete();

            return redirect()->route('kesiswaan.kelas')
                ->with('success', 'Data ruang kelas berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus kelas: ' . $e->getMessage()]);
        }
    }
}