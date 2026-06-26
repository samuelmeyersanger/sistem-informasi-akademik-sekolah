<?php

namespace App\Http\Controllers\Sarpras;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GedungController extends Controller
{
    // =========================================================================
    // KELOMPOK GEDUNG
    // =========================================================================

    /**
     * Menampilkan semua daftar gedung (Halaman Utama Sarpras)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $gedung = Gedung::withCount('ruangan')
            ->when($search, function($query) use ($search) {
                $query->where('nama_gedung', 'like', "%{$search}%")
                      ->orWhere('kode_gedung', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('sarpras.gedung.index', compact('gedung'));
    }

    /**
     * Menyimpan gedung baru via popup modal di halaman index
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_gedung' => 'required|string|max:255',
            'kode_gedung' => 'required|string|max:50|unique:gedung,kode_gedung',
            'deskripsi' => 'nullable|string',
            'jumlah_lantai' => 'required|integer|min:1',
        ]);

        Gedung::create($validated);

        return redirect()->route('sarpras.gedung.index')
            ->with('success', 'Gedung baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail Gedung + Daftar Ruangan di dalamnya
     */
    public function show($id)
    {
        $gedung = Gedung::with(['ruangan' => function($query) {
            $query->withCount('inventaris')->latest();
        }])->findOrFail($id);

        return view('sarpras.gedung.show', compact('gedung'));
    }

    /**
     * Memperbarui data gedung dari modal edit
     */
    public function update(Request $request, $id)
    {
        $gedung = Gedung::findOrFail($id);

        $validated = $request->validate([
            'nama_gedung' => 'required|string|max:255',
            'kode_gedung' => 'required|string|max:50|unique:gedung,kode_gedung,' . $id,
            'deskripsi' => 'nullable|string',
            'jumlah_lantai' => 'required|integer|min:1',
        ]);

        $gedung->update($validated);

        return redirect()->route('sarpras.gedung.index')
            ->with('success', "Data gedung {$gedung->nama_gedung} berhasil diperbarui!");
    }

    /**
     * Menghapus data gedung (Soft Delete)
     */
    public function destroy($id)
    {
        $gedung = Gedung::findOrFail($id);
        $gedung->delete();

        return redirect()->route('sarpras.gedung.index')
            ->with('success', "Gedung {$gedung->nama_gedung} berhasil dihapus.");
    }


    // =========================================================================
    // KELOMPOK RUANGAN (Akses via Detail Gedung)
    // =========================================================================

    /**
     * Menyimpan ruangan baru via popup modal di halaman detail gedung
     */
    public function storeRuangan(Request $request, $gedung_id)
    {
        Gedung::findOrFail($gedung_id);

        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kode_ruangan' => 'required|string|max:50|unique:ruangan,kode_ruangan',
            'kapasitas' => 'required|integer|min:0',
        ]);

        $validated['gedung_id'] = $gedung_id;

        Ruangan::create($validated);

        return redirect()->route('sarpras.gedung.show', $gedung_id)
            ->with('success', 'Ruangan baru berhasil ditambahkan ke gedung ini!');
    }

    /**
     * Menampilkan detail Ruangan + Daftar Barang Inventaris di dalamnya
     */
    public function showRuangan($ruangan_id)
    {
        $ruangan = Ruangan::with(['gedung', 'inventaris' => function($query) {
            $query->latest();
        }])->findOrFail($ruangan_id);

        return view('sarpras.gedung.show_ruangan', compact('ruangan'));
    }

    /**
     * Memperbarui data ruangan dari modal edit di detail gedung
     */
    public function updateRuangan(Request $request, $ruangan_id)
    {
        $ruangan = Ruangan::findOrFail($ruangan_id);

        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kode_ruangan' => 'required|string|max:50|unique:ruangan,kode_ruangan,' . $ruangan_id,
            'kapasitas' => 'required|integer|min:0',
        ]);

        $ruangan->update($validated);

        return redirect()->route('sarpras.gedung.show', $ruangan->gedung_id)
            ->with('success', "Data ruangan {$ruangan->nama_ruangan} berhasil diperbarui!");
    }

    /**
     * Menghapus data ruangan (Soft Delete)
     */
    public function destroyRuangan($ruangan_id)
    {
        $ruangan = Ruangan::findOrFail($ruangan_id);
        $gedungIdSebelumnya = $ruangan->gedung_id;
        
        $ruangan->delete();

        return redirect()->route('sarpras.gedung.show', $gedungIdSebelumnya)
            ->with('success', "Ruangan {$ruangan->nama_ruangan} berhasil dihapus.");
    }


    // =========================================================================
    // KELOMPOK INVENTARIS BARANG (Akses via Detail Ruangan)
    // =========================================================================

    /**
     * Menyimpan barang inventaris baru via popup modal di halaman detail ruangan
     */
    public function storeInventaris(Request $request, $ruangan_id)
    {
        $ruangan = Ruangan::findOrFail($ruangan_id);

        $validated = $request->validate([
            'kode_barang' => 'required|string|max:100|unique:inventaris,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'merek' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'tahun_pembelian' => 'nullable|integer|digits:4',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat,Hilang',
            'jumlah' => 'required|integer|min:1',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'lokasi' => 'nullable|string|max:255',
        ]);

        $validated['ruangan_id'] = $ruangan_id;
        $validated['gedung_id'] = $ruangan->gedung_id;

        if ($request->hasFile('foto_barang')) {
            $path = $request->file('foto_barang')->store('inventaris', 'public');
            $validated['foto_barang'] = $path;
        }

        Inventaris::create($validated);

        return redirect()->route('sarpras.gedung.showRuangan', $ruangan_id)
            ->with('success', 'Barang inventaris baru berhasil didaftarkan ke ruangan ini!');
    }

    /**
     * Memperbarui data inventaris barang dari modal edit di detail ruangan
     */
    public function updateInventaris(Request $request, $inventaris_id)
    {
        $inventaris = Inventaris::findOrFail($inventaris_id);

        $validated = $request->validate([
            'kode_barang' => 'required|string|max:100|unique:inventaris,kode_barang,' . $inventaris_id,
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'merek' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'tahun_pembelian' => 'nullable|integer|digits:4',
            'harga_perolehan' => 'nullable|numeric|min:0',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat,Hilang',
            'jumlah' => 'required|integer|min:1',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'lokasi' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('foto_barang')) {
            // Hapus foto lama jika ganti foto baru
            if ($inventaris->foto_barang) {
                Storage::disk('public')->delete($inventaris->foto_barang);
            }
            $path = $request->file('foto_barang')->store('inventaris', 'public');
            $validated['foto_barang'] = $path;
        }

        $inventaris->update($validated);

        return redirect()->route('sarpras.gedung.showRuangan', $inventaris->ruangan_id)
            ->with('success', "Data inventaris barang {$inventaris->nama_barang} berhasil diperbarui!");
    }

    /**
     * Menghapus data barang inventaris (Soft Delete)
     */
    public function destroyInventaris($inventaris_id)
    {
        $inventaris = Inventaris::findOrFail($inventaris_id);
        $ruanganIdSebelumnya = $inventaris->ruangan_id;

        if ($inventaris->foto_barang) {
            Storage::disk('public')->delete($inventaris->foto_barang);
        }

        $inventaris->delete();

        return redirect()->route('sarpras.gedung.showRuangan', $ruanganIdSebelumnya)
            ->with('success', "Barang {$inventaris->nama_barang} berhasil dihapus dari inventaris ruangan.");
    }
}