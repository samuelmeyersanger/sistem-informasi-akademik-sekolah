<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MataPelajaranController extends Controller
{
    /**
     * Menampilkan daftar master data mata pelajaran.
     * Mendukung fitur pencarian berdasarkan nama/singkatan mapel dan fitur pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');

        // Query dasar mengambil data berdasarkan nomor urut terkecil
        $query = MataPelajaran::orderBy('nomor_urut', 'asc');

        // Kondisi jika user melakukan pencarian data
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%")
                  ->orWhere('singkatan_mapel', 'like', "%{$search}%");
            });
        }

        // Ambil data dengan sistem pagination (15 data per halaman)
        $mataPelajaran = $query->paginate(15)->withQueryString();

        return view('akademik.mata_pelajaran.index', compact('mataPelajaran'));
    }

    /**
     * Menyimpan data mata pelajaran baru ke dalam database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nomor_urut'      => 'required|integer|min:0',
            'nama_mapel'      => 'required|string|max:255',
            'singkatan_mapel' => 'nullable|string|max:50',
            'jumlah_jam'      => 'required|integer|min:0',
        ]);

        // Kembalikan ke kode bersih semula
        MataPelajaran::create([
            'nomor_urut'      => $request->nomor_urut,
            'nama_mapel'      => $request->nama_mapel,
            'singkatan_mapel' => $request->singkatan_mapel,
            'jumlah_jam'      => $request->jumlah_jam,
        ]);

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', "Mata pelajaran '{$request->nama_mapel}' berhasil ditambahkan ke dalam sistem.");
    }

    /**
     * Memperbarui data mata pelajaran yang sudah ada di database.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $mapel = MataPelajaran::findOrFail($id);

        $request->validate([
            'nomor_urut'      => 'required|integer|min:0',
            'nama_mapel'      => 'required|string|max:255',
            'singkatan_mapel' => 'nullable|string|max:50',
            'jumlah_jam'      => 'required|integer|min:0',
        ]);

        $mapel->update([
            'nomor_urut'      => $request->nomor_urut,
            'nama_mapel'      => $request->nama_mapel,
            'singkatan_mapel' => $request->singkatan_mapel,
            'jumlah_jam'      => $request->jumlah_jam,
        ]);

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', "Perubahan data mata pelajaran '{$mapel->nama_mapel}' berhasil disimpan.");
    }

    /**
     * Menghapus data mata pelajaran dari database menggunakan sistem Soft Delete.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $mapel = MataPelajaran::findOrFail($id);
        
        $namaMapel = $mapel->nama_mapel;
        $mapel->delete();

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', "Mata pelajaran '{$namaMapel}' berhasil dihapus (Arsip).");
    }
}