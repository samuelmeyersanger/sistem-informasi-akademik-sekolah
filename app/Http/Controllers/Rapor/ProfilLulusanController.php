<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\ProfilLulusan;
use Illuminate\Http\Request;

class ProfilLulusanController extends Controller
{
    /**
     * Menampilkan daftar Profil Lulusan (dengan Paginasi & Pencarian)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ProfilLulusan::query();

        // Fitur Pencarian: Bisa mencari nama tema atau nama dimensi
        if (!empty($search)) {
            $query->where('tema', 'like', '%' . $search . '%')
                  ->orWhere('dimensi_profil_lulusan', 'like', '%' . $search . '%');
        }

        // Diurutkan berdasarkan kolom 'no' agar berurutan sesuai pedoman
        $profils = $query->orderBy('no', 'asc')
                         ->paginate(10)
                         ->appends(['search' => $search]);

        return view('rapor.profil-lulusan.index', compact('profils', 'search'));
    }

    /**
     * Menyimpan data Profil Lulusan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'no' => ['required', 'string', 'max:50'],
            'tema' => ['required', 'string', 'max:255'],
            'dimensi_profil_lulusan' => ['required', 'string', 'max:255'],
            'subdmensi' => ['required', 'string'], // Sesuai dengan typo di database Anda
        ]);

        ProfilLulusan::create($request->all());

        return redirect()->route('rapor.profil-lulusan.index')
            ->with('success', 'Profil lulusan berhasil disimpan.');
    }

    /**
     * Memperbarui data Profil Lulusan
     */
    public function update(Request $request, $id)
    {
        $profilLulusan = ProfilLulusan::findOrFail($id);

        $request->validate([
            'no' => ['required', 'string', 'max:50'],
            'tema' => ['required', 'string', 'max:255'],
            'dimensi_profil_lulusan' => ['required', 'string', 'max:255'],
            'subdmensi' => ['required', 'string'],
        ]);

        $profilLulusan->update($request->all());

        return redirect()->route('rapor.profil-lulusan.index')
            ->with('success', 'Profil lulusan berhasil diperbarui.');
    }

    /**
     * Menghapus Profil Lulusan
     */
    public function destroy($id)
    {
        $profilLulusan = ProfilLulusan::findOrFail($id);
        
        // Hapus permanen (karena tabel ini tidak menggunakan SoftDeletes)
        $profilLulusan->delete(); 

        return redirect()->route('rapor.profil-lulusan.index')
            ->with('success', 'Profil lulusan berhasil dihapus.');
    }
}