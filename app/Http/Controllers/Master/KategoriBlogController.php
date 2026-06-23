<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\KategoriBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriBlogController extends Controller
{
    /**
     * Menampilkan daftar semua kategori blog (Dengan Fitur Search)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Menghitung jumlah artikel (blogs) di dalam kategori dengan filter pencarian
        $query = KategoriBlog::withCount('blogs');

        if (!empty($search)) {
            $query->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%');
        }

        // Ambil data dengan urutan alfabetis
        $kategoriBlogs = $query->orderBy('nama', 'asc')->get();

        return view('master.kategori-blog.index', compact('kategoriBlogs', 'search'));
    }

    /**
     * Menyimpan kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:kategori_blog,nama'],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori ini sudah terdaftar.',
        ]);

        KategoriBlog::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama), // Fallback pengaman tambahan
        ]);

        return redirect()->route('master.kategori-blog.index')->with('success', 'Kategori blog baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui kategori blog
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriBlog::findOrFail($id);

        $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:kategori_blog,nama,' . $id],
        ], [
            'nama.required' => 'Nama kategori tidak boleh kosong.',
            'nama.unique' => 'Nama kategori sudah digunakan.',
        ]);

        // Paksa reset slug agar function booted() updating memicu regenerasi slug baru
        $kategori->slug = null; 
        
        $kategori->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('master.kategori-blog.index')->with('success', 'Nama kategori blog berhasil diperbarui.');
    }

    /**
     * Menghapus kategori blog
     */
    public function destroy($id)
    {
        $kategori = KategoriBlog::findOrFail($id);

        // Proteksi Keamanan: Jika ada artikel/blog yang memakai kategori ini, gagalkan penghapusan
        if ($kategori->blogs()->count() > 0) {
            return redirect()->route('master.kategori-blog.index')
                ->with('error', 'Gagal menghapus! Kategori ini masih memiliki artikel aktif di dalamnya. Pindahkan atau hapus artikel terlebih dahulu.');
        }

        $kategori->delete();

        return redirect()->route('master.kategori-blog.index')->with('success', 'Kategori blog berhasil dihapus secara permanen.');
    }
}