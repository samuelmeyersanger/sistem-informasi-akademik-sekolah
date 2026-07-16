<?php

namespace App\Http\Controllers\Rapor;

use App\Http\Controllers\Controller;
use App\Models\TemaKokurikuler;
use Illuminate\Http\Request;

class TemaKokurikulerController extends Controller
{
    /**
     * Menampilkan daftar Tema Kokurikuler (dengan Paginasi & Pencarian)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = TemaKokurikuler::query();

        // Fitur Pencarian berdasarkan nama tema
        if (!empty($search)) {
            $query->where('tema', 'like', '%' . $search . '%');
        }

        $temas = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->appends(['search' => $search]);

        return view('rapor.tema-kokurikuler.index', compact('temas', 'search'));
    }

    /**
     * Menyimpan data Tema Kokurikuler baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tema' => ['required', 'string', 'max:255'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        // Proteksi Logika: Pastikan belum ada tema dengan nama yang persis sama (Mencegah Duplikat)
        $sudahAda = TemaKokurikuler::where('tema', $request->tema)->exists();

        if ($sudahAda) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal! Tema kokurikuler tersebut sudah pernah dibuat.');
        }

        // Catatan: is_aktif biasanya datang dari checkbox HTML. 
        // Jika dicentang maka true, jika tidak dicentang nilainya false.
        TemaKokurikuler::create([
            'tema' => $request->tema,
            'is_aktif' => $request->has('is_aktif') ? true : false,
        ]);

        return redirect()->route('rapor.tema-kokurikuler.index')->with('success', 'Tema kokurikuler berhasil disimpan.');
    }

    /**
     * Memperbarui data Tema Kokurikuler
     */
    public function update(Request $request, $id)
    {
        $temaKokurikuler = TemaKokurikuler::findOrFail($id);

        $request->validate([
            'tema' => ['required', 'string', 'max:255'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        // Proteksi Logika: Cek apakah nama tema sudah dipakai oleh record/data LAIN
        $sudahAda = TemaKokurikuler::where('tema', $request->tema)
                                   ->where('id', '!=', $id)
                                   ->exists();

        if ($sudahAda) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal! Anda tidak bisa menggunakan nama tema yang sudah dipakai di data lain.');
        }

        $temaKokurikuler->update([
            'tema' => $request->tema,
            'is_aktif' => $request->has('is_aktif') ? true : false,
        ]);

        return redirect()->route('rapor.tema-kokurikuler.index')->with('success', 'Tema kokurikuler berhasil diperbarui.');
    }

    /**
     * Menghapus Tema Kokurikuler
     */
    public function destroy($id)
    {
        $temaKokurikuler = TemaKokurikuler::findOrFail($id);
        
        $temaKokurikuler->delete(); // Otomatis menjadi Soft Delete

        return redirect()->route('rapor.tema-kokurikuler.index')
            ->with('success', 'Tema kokurikuler berhasil dihapus.');
    }
}